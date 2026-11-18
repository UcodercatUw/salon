<?php
/**
 * Создаёт таблицу login_attempts в рабочей базе.
 *
 * Запуск из папки salon:
 *   php scripts/migrate_login_attempts.php
 *
 * Таблица нужна для блокировки входа после 5 неудачных попыток.
 * Модель ModelLoginAttempt создаёт её и сама, скрипт полезен для
 * явной подготовки базы (например, если у пользователя БД нет прав на CREATE).
 */
require_once __DIR__ . '/../config/database.php';

$database = new Database();
$conn = $database->getConnection();

$conn->exec(
    "CREATE TABLE IF NOT EXISTS login_attempts (
        login_key VARCHAR(64) NOT NULL PRIMARY KEY,
        attempts INTEGER NOT NULL DEFAULT 0,
        last_attempt DATETIME NOT NULL
    )"
);

echo "Таблица login_attempts готова." . PHP_EOL;
