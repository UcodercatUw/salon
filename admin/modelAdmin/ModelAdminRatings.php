<?php
require_once __DIR__ . '/../../config/database.php';

class ModelAdminRatings {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Общая статистика по всем отзывам
    public function getOverallStats() {
        $query = "SELECT 
                    COUNT(*) as total_reviews, 
                    COALESCE(AVG(rating), 0) as overall_avg,
                    COUNT(DISTINCT user_id) as unique_reviewers,
                    COUNT(DISTINCT service_id) as reviewed_services
                  FROM ratings";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Статистика по каждой услуге
    public function getServiceStats() {
        $query = "SELECT s.id, s.title, s.image, s.price,
                    COALESCE(AVG(r.rating), 0) as avg_rating, 
                    COUNT(r.id) as total_reviews,
                    COALESCE(MIN(r.rating), 0) as min_rating,
                    COALESCE(MAX(r.rating), 0) as max_rating
                  FROM services s 
                  LEFT JOIN ratings r ON s.id = r.service_id 
                  GROUP BY s.id, s.title, s.image, s.price
                  ORDER BY avg_rating DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Распределение оценок (сколько 1-звездочных, 2-звездочных и т.д.)
    public function getRatingDistribution() {
        $query = "SELECT rating, COUNT(*) as count FROM ratings GROUP BY rating ORDER BY rating DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $distribution = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0];
        foreach ($results as $row) {
            $distribution[(int)$row['rating']] = (int)$row['count'];
        }
        return $distribution;
    }

    // Статистика по мастерам (рейтинг считается по оценкам всех его процедур)
    public function getMasterStats() {
        $query = "SELECT s.master_name,
                    COALESCE(AVG(r.rating), 0) as avg_rating,
                    COUNT(r.id) as total_reviews,
                    COUNT(DISTINCT s.id) as total_services
                  FROM services s
                  LEFT JOIN ratings r ON r.service_id = s.id
                  WHERE s.master_name IS NOT NULL AND s.master_name <> ''
                  GROUP BY s.master_name
                  ORDER BY avg_rating DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Последние отзывы
    public function getRecentRatings($limit = 10) {
        $query = "SELECT r.*, u.username as user_name, s.title as service_title 
                  FROM ratings r 
                  JOIN users u ON r.user_id = u.id 
                  JOIN services s ON r.service_id = s.id 
                  ORDER BY r.created_at DESC 
                  LIMIT :lim";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":lim", $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Удалить отзыв
    public function deleteRating($id) {
        $query = "DELETE FROM ratings WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
?>