<?php
require_once __DIR__ . '/../model/ModelServices.php';
require_once __DIR__ . '/../model/ModelEnrollment.php';

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
        include __DIR__ . '/../view/servicesList.php';
    }

    public function show($id) {
        $serviceModel = new ModelServices();
        $service = $serviceModel->getServiceById($id);

        if (!$service) {
            header('Location: index.php');
            exit;
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
}
?>