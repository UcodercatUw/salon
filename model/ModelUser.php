<?php
require_once __DIR__ . '/../config/database.php';

class ModelUser {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function getUserByUsername($username) {
        // Ищем пользователя, у которого login ИЛИ username равен введенному значению
        $query = "SELECT * FROM users WHERE login = :username OR username = :username LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":username", $username);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function createUser($name, $email, $phone, $username, $password) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // В таблице users: username — отображаемое имя, login — логин для входа.
        // Значения status ограничены enum('user','admin').
        $query = "INSERT INTO users (username, email, phone, login, password, pass, status, registration_date) 
                  VALUES (:username, :email, :phone, :login, :password, :pass, 'user', CURRENT_DATE)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":username", $name);
        $stmt->bindParam(":email", $email);
        $stmt->bindParam(":phone", $phone);
        $stmt->bindParam(":login", $username);
        $stmt->bindParam(":password", $hashedPassword);
        $stmt->bindParam(":pass", $password);

        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            // Например, логин или email уже заняты (UNIQUE-ограничения)
            return false;
        }
    }
}
?>