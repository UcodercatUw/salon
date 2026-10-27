<?php
require_once __DIR__ . '/../../config/database.php';

class ModelAdminEnrollments {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Получить все записи клиентов
    public function getAllEnrollments() {
        $query = "SELECT e.*, u.username, u.email, s.title AS service_title, s.price, s.duration 
                  FROM enrollments e 
                  JOIN users u ON e.user_id = u.id 
                  JOIN services s ON e.course_id = s.id 
                  ORDER BY e.id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Удалить или отменить запись
    public function deleteEnrollment($id) {
        $query = "DELETE FROM enrollments WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
?>