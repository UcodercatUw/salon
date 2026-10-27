<?php
require_once __DIR__ . '/../config/database.php';

class ModelEnrollment {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function getEnrollmentsByUser($userId) {
        $query = "SELECT e.*, s.title as service_title, s.price, s.duration 
                  FROM enrollments e 
                  JOIN services s ON e.course_id = s.id 
                  WHERE e.user_id = :user_id";
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":user_id", $userId);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    public function createEnrollment($userId, $serviceId, $date) {
        // Форматируем дату для базы данных (убираем 'T' и добавляем секунды, если нужно)
        $date = str_replace('T', ' ', $date);
        if (strlen($date) === 16) {
            $date .= ':00';
        }

        $query = "INSERT INTO enrollments (user_id, course_id, enrollment_date, status) 
                  VALUES (:user_id, :course_id, :date, 'confirmed')";
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":user_id", $userId);
            $stmt->bindParam(":course_id", $serviceId);
            $stmt->bindParam(":date", $date);
            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }
}
?>