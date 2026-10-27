<?
session_start();

// Установка языка по умолчанию (эстонский или русский)
if (!isset($_SESSION['lang'])) {
    $_SESSION['lang'] = 'et'; // По умолчанию эстонский
}

// Переключение языка через GET-параметр
if (isset($_GET['lang']) &&in_array($_GET['lang'], ['et', 'ru', 'en'])) {
    $_SESSION['lang'] = $_GET['lang'];
}

$lang_file = __DIR__ . '/../lang/' . $_SESSION['lang'] . '.php';

if (file_exists($lang_file)) {
    $lang = include($lang_file);
} else {
    $lang = include(__DIR__ . '/../lang/et.php'); // Резервный файл
}
?>