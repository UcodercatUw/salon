<?php
require_once __DIR__ . '/../config/database.php';

class ModelRating {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Получить все отзывы для конкретной услуги
    public function getRatingsByService($serviceId) {
        $query = "SELECT r.*, u.username as user_name 
                  FROM ratings r 
                  JOIN users u ON r.user_id = u.id 
                  WHERE r.service_id = :service_id 
                  ORDER BY r.created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":service_id", $serviceId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Получить среднюю оценку и количество отзывов для услуги
    public function getAverageRating($serviceId) {
        $query = "SELECT AVG(rating) as avg_rating, COUNT(*) as total_reviews 
                  FROM ratings WHERE service_id = :service_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":service_id", $serviceId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Получить оценку конкретного пользователя для услуги
    public function getUserRating($userId, $serviceId) {
        $query = "SELECT * FROM ratings WHERE user_id = :user_id AND service_id = :service_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $userId, PDO::PARAM_INT);
        $stmt->bindParam(":service_id", $serviceId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Добавить или обновить оценку (один пользователь — одна оценка на услугу)
    public function addOrUpdateRating($userId, $serviceId, $rating, $review) {
        // Проверяем, есть ли уже оценка от этого пользователя
        $existing = $this->getUserRating($userId, $serviceId);
        
        if ($existing) {
            $query = "UPDATE ratings SET rating = :rating, review = :review, created_at = CURRENT_TIMESTAMP 
                      WHERE user_id = :user_id AND service_id = :service_id";
        } else {
            $query = "INSERT INTO ratings (user_id, service_id, rating, review) 
                      VALUES (:user_id, :service_id, :rating, :review)";
        }
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $userId, PDO::PARAM_INT);
        $stmt->bindParam(":service_id", $serviceId, PDO::PARAM_INT);
        $stmt->bindParam(":rating", $rating, PDO::PARAM_INT);
        $stmt->bindParam(":review", $review, PDO::PARAM_STR);
        return $stmt->execute();
    }

    // Средние оценки для всех услуг (для карточек в списке)
    public function getAllAverageRatings() {
        $query = "SELECT service_id, AVG(rating) as avg_rating, COUNT(*) as total_reviews 
                  FROM ratings GROUP BY service_id";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $ratings = [];
        foreach ($results as $row) {
            $ratings[$row['service_id']] = $row;
        }
        return $ratings;
    }

    // Средняя оценка мастера: считается по оценкам всех его процедур
    public function getMasterStats($masterName) {
        $query = "SELECT AVG(r.rating) as avg_rating, 
                          COUNT(r.id) as total_reviews, 
                          COUNT(DISTINCT s.id) as total_services
                  FROM services s 
                  JOIN ratings r ON r.service_id = s.id 
                  WHERE s.master_name = :master_name";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":master_name", $masterName, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Средние оценки всех мастеров (ключ массива — имя мастера)
    public function getAllMasterStats() {
        $query = "SELECT s.master_name, 
                          AVG(r.rating) as avg_rating, 
                          COUNT(r.id) as total_reviews,
                          COUNT(DISTINCT s.id) as total_services
                  FROM services s 
                  JOIN ratings r ON r.service_id = s.id 
                  WHERE s.master_name IS NOT NULL AND s.master_name <> ''
                  GROUP BY s.master_name";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $masters = [];
        foreach ($results as $row) {
            $masters[$row['master_name']] = $row;
        }
        return $masters;
    }
}
?>