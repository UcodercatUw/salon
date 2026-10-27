<?php
require_once __DIR__ . '/../controller/ControllerServices.php';

$serviceController = new ControllerServices();
$action = isset($_GET['action']) ? $_GET['action'] : 'index';
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

switch ($action) {
    case 'show':
        if ($id > 0) {
            $serviceController->show($id);
        } else {
            $serviceController->index();
        }
        break;

    case 'book':
        if ($id > 0) {
            $serviceController->book($id);
        } else {
            header('Location: index.php');
        }
        break;

    case 'my_bookings':
        $serviceController->myBookings();
        break;

    case 'login':
    case 'register':
    case 'logout':
        require_once __DIR__ . '/../controller/ControllerUser.php';
        $userController = new ControllerUser();
        if ($action === 'login') {
            $userController->login();
        } elseif ($action === 'register') {
            $userController->register();
        } elseif ($action === 'logout') {
            $userController->logout();
        }
        break;

    case 'index':
    default:
        $serviceController->index();
        break;
}
?>