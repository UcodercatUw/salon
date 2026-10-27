<?php
session_start();
define('ROOT_DIR', __DIR__);

// 1. Перехват смены языка с сохранением текущей страницы
if (isset($_GET['lang']) && in_array($_GET['lang'], ['et', 'ru', 'en'])) {
    $_SESSION['lang'] = $_GET['lang'];
    
    // Сохраняем остальные GET-параметры (например, action=login или action=show&id=5)
    $params = $_GET;
    unset($params['lang']); // удаляем lang, чтобы не дублировать
    
    $redirectUrl = 'index.php';
    if (!empty($params)) {
        $redirectUrl .= '?' . http_build_query($params);
    }
    
    header('Location: ' . $redirectUrl);
    exit;
}

// 2. Установка языка по умолчанию (эстонский)
if (!isset($_SESSION['lang'])) {
    $_SESSION['lang'] = 'et';
}

// 3. Подключение базы данных и роутера
require_once ROOT_DIR . '/config/database.php';
require_once ROOT_DIR . '/route/routing.php';
?>