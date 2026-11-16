<?php
require_once __DIR__ . '/../model/ModelServices.php';
require_once __DIR__ . '/../model/ModelEnrollment.php';
require_once __DIR__ . '/../model/ModelRating.php';

class ControllerServices {

    public function index() {
        $serviceModel = new ModelServices();
        $categoryId = isset($_GET['category']) ? intval($_GET['category']) : null;
        
        if ($categoryId) {
            $services = $serviceModel->getServicesByCategory($categoryId);
        } else {
            $services = $serviceModel->getAllServices();
        }
        
        $categories = $serviceModel->getCategories();

        // Получаем средние оценки для всех услуг
        $ratingModel = new ModelRating();
        $allRatings = $ratingModel->getAllAverageRatings();
        // Рейтинги мастеров по имени мастера (для карточек услуг)
        $masterRatings = $ratingModel->getAllMasterStats();

        include __DIR__ . '/../view/servicesList.php';
    }

    public function show($id) {
        $serviceModel = new ModelServices();
        $service = $serviceModel->getServiceById($id);

        if (!$service) {
            header('Location: index.php');
            exit;
        }

        // Получаем рейтинг и отзывы для этой услуги
        $ratingModel = new ModelRating();
        $ratingStats = $ratingModel->getAverageRating($id);
        $reviews = $ratingModel->getRatingsByService($id);
        $userRating = null;
        if (isset($_SESSION['user_id'])) {
            $userRating = $ratingModel->getUserRating($_SESSION['user_id'], $id);
        }

        // Рейтинг мастера, который выполняет эту процедуру
        $masterStats = null;
        if (!empty($service['master_name'])) {
            $masterStats = $ratingModel->getMasterStats($service['master_name']);
        }

        include __DIR__ . '/../view/serviceRead.php';
    }

    public function book($id) {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit;
        }

        $serviceModel = new ModelServices();
        $service = $serviceModel->getServiceById($id);

        if (!$service) {
            header('Location: index.php');
            exit;
        }

        $error = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $date = $_POST['enrollment_date'] ?? '';
            
            if (!empty($date)) {
                $enrollmentModel = new ModelEnrollment();
                // Передаем 3 аргумента: ID пользователя, ID услуги и выбранную дату
                if ($enrollmentModel->createEnrollment($_SESSION['user_id'], $id, $date)) {
                    header('Location: index.php?action=my_bookings');
                    exit;
                } else {
                    $error = 'Ошибка при оформлении записи.';
                }
            } else {
                $error = 'Пожалуйста, выберите дату и время.';
            }
        }

        include __DIR__ . '/../view/serviceBook.php';
    }

    public function myBookings() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit;
        }

        $enrollmentModel = new ModelEnrollment();
        $bookings = $enrollmentModel->getEnrollmentsByUser($_SESSION['user_id']);

        include __DIR__ . '/../view/myBookings.php';
    }

    // Обработка отправки оценки
    public function submitRating() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit;
        }

        $serviceId = isset($_POST['service_id']) ? intval($_POST['service_id']) : 0;
        $rating = isset($_POST['rating']) ? intval($_POST['rating']) : 0;
        $review = trim($_POST['review'] ?? '');

        if ($serviceId <= 0 || $rating < 1 || $rating > 5) {
            header('Location: index.php');
            exit;
        }

        $ratingModel = new ModelRating();
        $ratingModel->addOrUpdateRating($_SESSION['user_id'], $serviceId, $rating, $review);

        header('Location: index.php?action=show&id=' . $serviceId . '#reviews');
        exit;
    }
}
?>