<?php
declare(strict_types=1);

/**
 * Подготовка тестового окружения.
 *
 * По умолчанию тесты работают на отдельной файловой базе SQLite
 * (tests/.tmp/salon_test.sqlite), поэтому MySQL для прогона не требуется.
 *
 * Чтобы прогнать те же тесты на реальной структуре MySQL, запустите:
 *   SALON_TEST_USE_MYSQL=1 vendor/bin/phpunit
 * Тогда используется база salon_db_test, которая пересоздаётся при каждом запуске.
 * Рабочая база salon_db не затрагивается ни в одном режиме.
 */

const TEST_DB_NAME = 'salon_db_test';

$useMysql = getenv('SALON_TEST_USE_MYSQL') === '1';
$sqliteFile = __DIR__ . '/.tmp/salon_test.sqlite';

if (!$useMysql) {
    $dir = dirname($sqliteFile);
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }
    if (file_exists($sqliteFile)) {
        unlink($sqliteFile);
    }
    putenv('SALON_DB_DSN=sqlite:' . $sqliteFile);
    putenv('SALON_DB_USER=');
    putenv('SALON_DB_PASS=');
} else {
    putenv('SALON_DB_DSN');
    putenv('SALON_DB_HOST=' . (getenv('SALON_DB_HOST') ?: 'localhost'));
    putenv('SALON_DB_NAME=' . TEST_DB_NAME);
    putenv('SALON_DB_USER=' . (getenv('SALON_DB_USER') ?: 'root'));
    putenv('SALON_DB_PASS=' . (getenv('SALON_DB_PASS') ?: ''));
}

require_once __DIR__ . '/../model/ModelRating.php';
require_once __DIR__ . '/../model/ModelUser.php';
require_once __DIR__ . '/../model/ModelEnrollment.php';
require_once __DIR__ . '/../model/ModelServices.php';

final class TestDb
{
    private static ?PDO $pdo = null;
    private static bool $mysql = false;

    public static function conn(): PDO
    {
        if (self::$pdo === null) {
            if (getenv('SALON_TEST_USE_MYSQL') === '1') {
                self::$pdo = self::connectMysql();
                self::$mysql = true;
            } else {
                self::$pdo = self::connectSqlite();
            }
        }

        return self::$pdo;
    }

    private static function connectSqlite(): PDO
    {
        $pdo = new PDO('sqlite:' . __DIR__ . '/.tmp/salon_test.sqlite');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        $pdo->exec('PRAGMA foreign_keys = ON');

        $schema = file_get_contents(__DIR__ . '/schema.sqlite.sql');
        foreach (array_filter(array_map('trim', explode(';', $schema))) as $statement) {
            $pdo->exec($statement);
        }

        return $pdo;
    }

    private static function connectMysql(): PDO
    {
        $host = getenv('SALON_DB_HOST') ?: 'localhost';
        $user = getenv('SALON_DB_USER') ?: 'root';
        $pass = getenv('SALON_DB_PASS') ?: '';

        try {
            $server = new PDO("mysql:host={$host};charset=utf8", $user, $pass);
            $server->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            fwrite(STDERR, PHP_EOL . 'MySQL недоступен: ' . $e->getMessage() . PHP_EOL
                . 'Запустите MySQL (XAMPP Control Panel) или уберите SALON_TEST_USE_MYSQL=1.' . PHP_EOL . PHP_EOL);
            exit(1);
        }

        $server->exec('DROP DATABASE IF EXISTS `' . TEST_DB_NAME . '`');
        $server->exec('CREATE DATABASE `' . TEST_DB_NAME . '` CHARACTER SET utf8');
        $server->exec('USE `' . TEST_DB_NAME . '`');

        $schema = file_get_contents(__DIR__ . '/schema.sql');
        foreach (array_filter(array_map('trim', explode(';', $schema))) as $statement) {
            $server->exec($statement);
        }

        return $server;
    }

    /**
     * Очищает все таблицы перед каждым тестом, сохраняя структуру.
     */
    public static function reset(): void
    {
        $pdo = self::conn();
        $tables = ['ratings', 'enrollments', 'services', 'users', 'categories'];

        if (self::$mysql) {
            $pdo->exec('SET FOREIGN_KEY_CHECKS = 0');
            foreach ($tables as $table) {
                $pdo->exec("TRUNCATE TABLE `{$table}`");
            }
            $pdo->exec('SET FOREIGN_KEY_CHECKS = 1');
            return;
        }

        $pdo->exec('PRAGMA foreign_keys = OFF');
        foreach ($tables as $table) {
            $pdo->exec("DELETE FROM {$table}");
        }
        $pdo->exec("DELETE FROM sqlite_sequence");
        $pdo->exec('PRAGMA foreign_keys = ON');
    }

    /**
     * Базовый набор данных: 2 категории, 2 клиента, 3 услуги (2 у одного мастера).
     */
    public static function seedBasics(): void
    {
        $pdo = self::conn();

        $pdo->exec("INSERT INTO categories (id, name) VALUES
            (1, 'Juuksuriteenused'),
            (2, 'Küünehooldus')");

        $pdo->exec("INSERT INTO users (id, username, email, phone, login, password, status, registration_date) VALUES
            (1, 'Mari Maasikas', 'mari@example.com', '55500001', 'mari', 'hash1', 'user', CURRENT_DATE),
            (2, 'Jaan Kuusk', 'jaan@example.com', '55500002', 'jaan', 'hash2', 'user', CURRENT_DATE)");

        $pdo->exec("INSERT INTO services (id, category_id, title, master_name, price, duration, is_active) VALUES
            (1, 1, 'Juukselõikus', 'Kadri Tamm', 25.00, 60, 1),
            (2, 1, 'Värvimine', 'Kadri Tamm', 45.00, 90, 1),
            (3, 2, 'Maniküür', 'Anni Kask', 20.00, 45, 1)");
    }
}
