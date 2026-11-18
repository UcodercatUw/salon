<?php
/**
 * Общие средства защиты приложения: безопасная сессия и CSRF-токен.
 * Подключается в точках входа (index.php, admin/index.php) до вывода HTML.
 */

/**
 * Запускает сессию с защищёнными параметрами cookie (HttpOnly, SameSite=Lax).
 */
function salon_start_session(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }

    $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');

    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'httponly' => true,
        'samesite' => 'Lax',
        'secure' => $secure,
    ]);

    session_start();
}

/**
 * Возвращает CSRF-токен текущей сессии, создавая его при первом обращении.
 */
function csrf_token(): string
{
    if (empty($_SESSION['csrf_token']) || !is_string($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

/**
 * Скрытое поле с CSRF-токеном для вставки в форму.
 */
function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="'
        . htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8') . '">';
}

/**
 * IP посетителя. Берём только REMOTE_ADDR: X-Forwarded-For подделывается клиентом.
 */
function salon_client_ip(): string
{
    return $_SERVER['REMOTE_ADDR'] ?? 'unknown';
}

/**
 * Ключ счётчика неудачных попыток: логин + IP, в виде хеша фиксированной длины.
 */
function login_attempt_key(string $username): string
{
    return hash('sha256', strtolower(trim($username)) . '|' . salon_client_ip());
}

/**
 * Пропускает только POST-запрос с корректным CSRF-токеном.
 */
function csrf_verify(): void
{
    $sent = $_POST['csrf_token'] ?? '';

    if ($_SERVER['REQUEST_METHOD'] !== 'POST'
        || !is_string($sent)
        || !hash_equals(csrf_token(), $sent)) {
        http_response_code(403);
        exit('CSRF token mismatch.');
    }
}
