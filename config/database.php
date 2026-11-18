<?php
class Database {
    private $host;
    private $db_name;
    private $username;
    private $password;
    private $dsn;
    public $conn;

    public function __construct() {
        // Значения по умолчанию совпадают с локальной XAMPP-конфигурацией.
        // Переменные окружения позволяют запускать приложение и тесты на другой базе.
        $this->host = getenv('SALON_DB_HOST') ?: 'localhost';
        $this->db_name = getenv('SALON_DB_NAME') ?: 'salon_db';
        $this->username = getenv('SALON_DB_USER') ?: 'root';
        $this->password = getenv('SALON_DB_PASS') ?: '';
        $this->dsn = getenv('SALON_DB_DSN') ?: null;
    }

    public function getConnection() {
        $this->conn = null;
        try {
            $dsn = $this->dsn ?: ("mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8");
            $this->conn = new PDO($dsn, $this->username, $this->password);
            if (strpos($dsn, 'sqlite:') === 0) {
                $this->conn->exec('PRAGMA foreign_keys = ON');
            }
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch(PDOException $exception) {
            echo "Ошибка подключения: " . $exception->getMessage();
        }
        return $this->conn;
    }
}
?>
