<?php
/**
 * Одноразовая миграция: переносит пароли из легаси-колонки `users.pass`
 * (открытый текст) в хеш `users.password`.
 *
 * Запуск только из консоли, из корня проекта:
 *
 *   php scripts/migrate_passwords.php
 *   php scripts/migrate_passwords.php --drop-column
 *
 * Без --drop-column колонка `pass` остаётся (для проверки результата),
 * с --drop-column она удаляется после успешного переноса.
 * Значения паролей в вывод не попадают.
 */

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('CLI only');
}

require_once __DIR__ . '/../config/database.php';

$dropColumn = in_array('--drop-column', $argv, true);

$database = new Database();
$conn = $database->getConnection();

if (!$conn) {
    fwrite(STDERR, "Нет соединения с базой данных.\n");
    exit(1);
}

try {
    $hasPass = (bool) $conn->query("SHOW COLUMNS FROM users LIKE 'pass'")->fetch();
} catch (PDOException $e) {
    fwrite(STDERR, 'Не удалось прочитать схему таблицы users: ' . $e->getMessage() . "\n");
    exit(1);
}

if (!$hasPass) {
    echo "Колонка users.pass отсутствует — миграция не требуется.\n";
    exit(0);
}

$users = $conn->query("SELECT id, password, pass FROM users")->fetchAll(PDO::FETCH_ASSOC);
$update = $conn->prepare("UPDATE users SET password = :password WHERE id = :id");

$migrated = 0;
foreach ($users as $user) {
    $plain = (string) ($user['pass'] ?? '');
    if ($plain === '') {
        continue;
    }

    // Переносим только если в password нет корректного хеша.
    $info = password_get_info((string) ($user['password'] ?? ''));
    if ($info['algo'] !== null) {
        continue;
    }

    $update->execute([
        ':password' => password_hash($plain, PASSWORD_DEFAULT),
        ':id' => (int) $user['id'],
    ]);
    $migrated++;
}

echo "Перенесено паролей: {$migrated} из " . count($users) . " пользователей.\n";

// Проверяем, что открытых паролей в password больше не осталось.
$remaining = 0;
foreach ($conn->query("SELECT password FROM users")->fetchAll(PDO::FETCH_ASSOC) as $row) {
    $info = password_get_info((string) $row['password']);
    if ($info['algo'] === null) {
        $remaining++;
    }
}
if ($remaining > 0) {
    fwrite(STDERR, "Внимание: у {$remaining} пользователей password не является хешем — им нужен сброс пароля.\n");
    exit(2);
}

if ($dropColumn) {
    $conn->exec("ALTER TABLE users DROP COLUMN pass");
    echo "Колонка users.pass удалена.\n";
} else {
    echo "Колонка users.pass оставлена. Удалить её: php scripts/migrate_passwords.php --drop-column\n";
}
