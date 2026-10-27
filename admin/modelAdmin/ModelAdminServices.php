<?php
require_once __DIR__ . '/../../config/database.php';

class ModelAdminServices {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Получить все услуги с названием их категорий
    public function getAllServices() {
        $query = "SELECT s.*, c.name AS category_name 
                  FROM services s 
                  JOIN categories c ON s.category_id = c.id 
                  ORDER BY s.id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Получить конкретную услугу по ID
    public function getServiceById($id) {
        $query = "SELECT * FROM services WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Добавить новую услугу
    public function addService($category_id, $title, $description, $price, $duration, $image) {
        $query = "INSERT INTO services (category_id, title, description, price, duration, image, is_active) 
                  VALUES (:category_id, :title, :description, :price, :duration, :image, 1)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":category_id", $category_id);
        $stmt->bindParam(":title", $title);
        $stmt->bindParam(":description", $description);
        $stmt->bindParam(":price", $price);
        $stmt->bindParam(":duration", $duration);
        $stmt->bindParam(":image", $image);
        return $stmt->execute();
    }

    // Обновить существующую услугу
    public function updateService($id, $category_id, $title, $description, $price, $duration, $image) {
        $query = "UPDATE services 
                  SET category_id = :category_id, title = :title, description = :description, 
                      price = :price, duration = :duration, image = :image 
                  WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->bindParam(":category_id", $category_id);
        $stmt->bindParam(":title", $title);
        $stmt->bindParam(":description", $description);
        $stmt->bindParam(":price", $price);
        $stmt->bindParam(":duration", $duration);
        $stmt->bindParam(":image", $image);
        return $stmt->execute();
    }

    // Удалить услугу
    public function deleteService($id) {
        $query = "DELETE FROM services WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
?>