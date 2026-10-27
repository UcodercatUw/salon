<?php
require_once __DIR__ . '/../modelAdmin/ModelAdminEnrollments.php';

class ControllerAdminClients {

    // Отображение списка всех записей клиентов
    public function index() {
        $model = new ModelAdminEnrollments();
        $enrollments = $model->getAllEnrollments();
        include __DIR__ . '/../viewAdmin/adminEnrollmentsList.php';
    }

    // Удаление / отмена записи клиента
    public function delete($id) {
        if ($id > 0) {
            $model = new ModelAdminEnrollments();
            $model->deleteEnrollment($id);
        }
        header('Location: index.php?action=clients');
        exit;
    }
}
?>