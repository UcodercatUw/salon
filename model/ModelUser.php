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
        $query = "INSERT INTO users (name, email, phone, login, password, pass, status) 
                  VALUES (:name, :email, :phone, :username, :password, :pass, 'client')";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":name", $name);
        $stmt->bindParam(":email", $email);
        $stmt->bindParam(":phone", $phone);
        $stmt->bindParam(":username", $username);
        $stmt->bindParam(":password", $hashedPassword);
        $stmt->bindParam(":pass", $password);
        return $stmt->execute();
    }
}
?>