<?php
require_once __DIR__ . '/../config/database.php';

class ModelServices {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function getAllServices() {
        $query = "SELECT s.*, c.name AS category_name 
                  FROM services s 
                  JOIN categories c ON s.category_id = c.id 
                  ORDER BY s.id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getServicesByCategory($categoryId) {
        $query = "SELECT s.*, c.name AS category_name 
                  FROM services s 
                  JOIN categories c ON s.category_id = c.id 
                  WHERE s.category_id = :category_id 
                  ORDER BY s.id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":category_id", $categoryId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCategories() {
        $query = "SELECT * FROM categories ORDER BY id ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getServiceById($id) {
        $query = "SELECT s.*, c.name AS category_name 
                  FROM services s 
                  JOIN categories c ON s.category_id = c.id 
                  WHERE s.id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>