<?php
require_once __DIR__ . '/../config/database.php';

/**
 * Учёт неудачных попыток входа.
 *
 * После MAX_ATTEMPTS неудач вход для пары «логин + IP» блокируется
 * на LOCKOUT_SECONDS секунд. Счётчик хранится в базе, поэтому очистка
 * cookie или новая сессия блокировку не снимают.
 */
class ModelLoginAttempt {
    public const MAX_ATTEMPTS = 5;
    public const LOCKOUT_SECONDS = 900; // 15 минут

    private $conn;
    private static $tableReady = false;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
        $this->ensureTable();
    }

    /**
     * Таблица создаётся при первом обращении, чтобы приложение работало
     * и на базе, где миграция ещё не запускалась.
     */
    private function ensureTable() {
        if (self::$tableReady) {
            return;
        }

        try {
            $this->conn->exec(
                "CREATE TABLE IF NOT EXISTS login_attempts (
                    login_key VARCHAR(64) NOT NULL PRIMARY KEY,
                    attempts INTEGER NOT NULL DEFAULT 0,
                    last_attempt DATETIME NOT NULL
                )"
            );
            self::$tableReady = true;
        } catch (PDOException $e) {
            // Без таблицы ограничение не работает, но вход не ломаем.
            error_log('login_attempts table is not available: ' . $e->getMessage());
        }
    }

    /**
     * Сколько секунд осталось до конца блокировки; 0 — вход разрешён.
     */
    public function isLocked($loginKey) {
        $row = $this->find($loginKey);
        if (!$row || (int) $row['attempts'] < self::MAX_ATTEMPTS) {
            return 0;
        }

        $remaining = self::LOCKOUT_SECONDS - (time() - (int) strtotime((string) $row['last_attempt']));
        return $remaining > 0 ? $remaining : 0;
    }

    /**
     * Засчитывает неудачную попытку и возвращает их количество в текущем окне.
     */
    public function registerFailure($loginKey) {
        $row = $this->find($loginKey);
        $attempts = 0;

        if ($row) {
            $elapsed = time() - (int) strtotime((string) $row['last_attempt']);
            if ($elapsed < self::LOCKOUT_SECONDS) {
                $attempts = (int) $row['attempts'];
            }
        }

        $attempts++;

        try {
            $stmt = $this->conn->prepare(
                'REPLACE INTO login_attempts (login_key, attempts, last_attempt) VALUES (:key, :attempts, :last)'
            );
            $stmt->execute([
                ':key' => $loginKey,
                ':attempts' => $attempts,
                ':last' => date('Y-m-d H:i:s'),
            ]);
        } catch (PDOException $e) {
            error_log('Failed to store login attempt: ' . $e->getMessage());
        }

        return $attempts;
    }

    /**
     * Сбрасывает счётчик после успешного входа.
     */
    public function clear($loginKey) {
        try {
            $stmt = $this->conn->prepare('DELETE FROM login_attempts WHERE login_key = :key');
            $stmt->execute([':key' => $loginKey]);
        } catch (PDOException $e) {
            error_log('Failed to clear login attempts: ' . $e->getMessage());
        }
    }

    private function find($loginKey) {
        try {
            $stmt = $this->conn->prepare('SELECT attempts, last_attempt FROM login_attempts WHERE login_key = :key');
            $stmt->execute([':key' => $loginKey]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('Failed to read login attempts: ' . $e->getMessage());
            return false;
        }
    }
}
?>
