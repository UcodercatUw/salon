<?php
// Включаем отображение ошибок для отладки
ini_set('display_errors', 1);
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Обработка переключения языков (ET, RU, EN)
if (isset($_GET['lang']) && in_array($_GET['lang'], ['et', 'ru', 'en'])) {
    $_SESSION['lang'] = $_GET['lang'];
    
    $params = $_GET;
    unset($params['lang']);
    $query = http_build_query($params);
    $redirect = 'index.php' . ($query ? '?' . $query : '');
    
    header('Location: ' . $redirect);
    exit;
}

// Установка языка по умолчанию
$langCode = $_SESSION['lang'] ?? 'et';

// 2. Проверка прав администратора[cite: 17]
if (!isset($_SESSION['status']) || $_SESSION['status'] !== 'admin') {
    header('Location: ../index.php');
    exit;
}

// 3. Получение действия и ID из GET-параметров[cite: 17]
$action = isset($_GET['action']) ? $_GET['action'] : 'services';
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// 4. Маршрутизация запросов в админ-панели
switch ($action) {
    case 'services':
    case 'add':
    case 'edit':
    case 'delete':
        require_once __DIR__ . '/controllerAdmin/ControllerAdminServices.php';
        $controller = new ControllerAdminServices();
        
        if ($action === 'add') {
            $controller->add();
        } elseif ($action === 'edit' && $id > 0) {
            $controller->edit($id);
        } elseif ($action === 'delete' && $id > 0) {
            $controller->delete($id);
        } else {
            $controller->index();
        }
        break;

    case 'clients':
    case 'delete_enrollment':
        require_once __DIR__ . '/controllerAdmin/ControllerAdminClients.php';
        $controller = new ControllerAdminClients();
        
        if ($action === 'delete_enrollment' && $id > 0) {
            $controller->delete($id);
        } else {
            $controller->index();
        }
        break;

    default:
        require_once __DIR__ . '/controllerAdmin/ControllerAdminServices.php';
        $controller = new ControllerAdminServices();
        $controller->index();
        break;
}
?>