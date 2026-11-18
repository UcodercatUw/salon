<?php
require_once __DIR__ . '/../modelAdmin/ModelAdminRatings.php';

class ControllerAdminRatings {

    // Отображение страницы статистики отзывов
    public function index() {
        $model = new ModelAdminRatings();
        $overallStats = $model->getOverallStats();
        $serviceStats = $model->getServiceStats();
        $masterStats = $model->getMasterStats();
        $distribution = $model->getRatingDistribution();
        $recentRatings = $model->getRecentRatings(10);
        include __DIR__ . '/../viewAdmin/adminRatingsStats.php';
    }

    // Удаление отзыва
    public function delete($id) {
        csrf_verify();
        if ($id > 0) {
            $model = new ModelAdminRatings();
            $model->deleteRating($id);
        }
        header('Location: index.php?action=ratings');
        exit;
    }
}
?>