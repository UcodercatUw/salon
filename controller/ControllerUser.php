<?php
require_once __DIR__ . '/../model/ModelUser.php';
require_once __DIR__ . '/../model/ModelLoginAttempt.php';

class ControllerUser {

    public function login() {
        $error = '';
        $lockRemaining = 0;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            csrf_verify();
            $username = trim($_POST['username'] ?? $_POST['login'] ?? '');
            $password = $_POST['password'] ?? '';

            if (!empty($username) && !empty($password)) {
                $userModel = new ModelUser();
                $attemptModel = new ModelLoginAttempt();
                $attemptKey = login_attempt_key($username);

                // Пока блокировка активна, пароль не проверяем вообще.
                $lockedSeconds = $attemptModel->isLocked($attemptKey);

                if ($lockedSeconds > 0) {
                    $lockRemaining = $lockedSeconds;
                    $_SESSION['login_locked_login'] = $username;
                    // Абсолютное время снятия блокировки, чтобы таймер пережил перезагрузку страницы.
                    $_SESSION['login_locked_until'] = time() + $lockedSeconds;
                } else {
                    $user = $userModel->getUserByUsername($username);

                    // Пароль проверяется только по хешу password; открытый текст из pass не принимается.
                    if ($user && !empty($user['password']) && password_verify($password, $user['password'])) {
                        // Устаревший или слабый хеш пересчитываем при успешном входе.
                        if (password_needs_rehash($user['password'], PASSWORD_DEFAULT)) {
                            $userModel->updatePassword((int) $user['id'], $password);
                        }

                        $attemptModel->clear($attemptKey);
                        unset($_SESSION['login_locked_login']);

                        $_SESSION['user_id'] = $user['id'];
                        $_SESSION['username'] = $user['login'] ?? $user['username'];
                        $_SESSION['status'] = $user['status'] ?? 'user';
                        
                        header('Location: index.php');
                        exit;
                    } else {
                        $failures = $attemptModel->registerFailure($attemptKey);

                        if ($failures >= ModelLoginAttempt::MAX_ATTEMPTS) {
                            $lockRemaining = max(1, $attemptModel->isLocked($attemptKey));
                            $_SESSION['login_locked_login'] = $username;
                            $_SESSION['login_locked_until'] = time() + $lockRemaining;
                        } else {
                            $error = $user ? 'Неверный пароль.' : 'Пользователь не найден.';
                        }
                    }
                }
            } else {
                $error = 'Пожалуйста, заполните все поля.';
            }
        }

        // После перезагрузки формы баннер с таймером тоже должен быть виден,
        // иначе обновление страницы сбрасывает обратный отсчёт на полные 15 минут.
        if ($lockRemaining <= 0 && !empty($_SESSION['login_locked_login'])) {
            $attemptModel = $attemptModel ?? new ModelLoginAttempt();
            $lockRemaining = $attemptModel->isLocked(login_attempt_key((string) $_SESSION['login_locked_login']));

            if ($lockRemaining > 0) {
                $attemptModelIsNew = !empty($attemptModel) && !($attemptModel instanceof ModelLoginAttempt);
                // Восстановим остаток и абсолютное время окончания блокировки.
                $_SESSION['login_locked_until'] = time() + $lockRemaining;
            } else {
                unset($_SESSION['login_locked_login']);
                unset($_SESSION['login_locked_until']);
            }
        }

        include __DIR__ . '/../view/formLogin.php';
    }

    public function register() {
        $error = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            csrf_verify();
            $name = trim($_POST['name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $phone = trim($_POST['phone'] ?? '');
            $username = trim($_POST['username'] ?? $_POST['login'] ?? '');
            $password = $_POST['password'] ?? '';

            if (!empty($name) && !empty($email) && !empty($username) && !empty($password)) {
                $userModel = new ModelUser();
                
                if ($userModel->getUserByUsername($username)) {
                    $error = 'Такой логин уже занят.';
                } else {
                    if ($userModel->createUser($name, $email, $phone, $username, $password)) {
                        header('Location: index.php?action=login');
                        exit;
                    } else {
                        $error = 'Ошибка при регистрации.';
                    }
                }
            } else {
                $error = 'Пожалуйста, заполните обязательные поля.';
            }
        }

        include __DIR__ . '/../view/formRegister.php';
    }

    public function logout() {
        csrf_verify();
        unset($_SESSION['user_id']);
        unset($_SESSION['username']);
        unset($_SESSION['status']);
        session_destroy();
        header('Location: index.php');
        exit;
    }
}
?>