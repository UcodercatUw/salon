<?php
require_once __DIR__ . '/../controllerAdmin/ControllerAdminServices.php';

// Проверка прав доступа (доступ только для зарегистрированных пользователей с ролью admin)
if (!isset($_SESSION['status']) || $_SESSION['status'] !== 'admin') {
    header('Location: ../index.php');
    exit;
}

$controller = new ControllerAdminServices();

// Получаем действие и ID из GET-параметров
$action = isset($_GET['action']) ? $_GET['action'] : 'index';
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

switch ($action) {
    case 'add':
        $controller->add();
        break;
        
    case 'edit':
        if ($id > 0) {
            $controller->edit($id);
        } else {
            header('Location: index.php');
        }
        break;
        
    case 'delete':
        if ($id > 0) {
            $controller->delete($id);
        } else {
            header('Location: index.php');
        }
        break;

    case 'index':
    default:
        $controller->index();
        break;
}
?>