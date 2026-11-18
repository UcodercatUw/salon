<?php
require_once __DIR__ . '/../model/ModelUser.php';

class ControllerUser {

    public function login() {
        $error = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username'] ?? $_POST['login'] ?? '');
            $password = $_POST['password'] ?? '';

            if (!empty($username) && !empty($password)) {
                $userModel = new ModelUser();
                $user = $userModel->getUserByUsername($username);

                if ($user) {
                    // Пароль проверяется только по хешу password; открытый текст из pass не принимается.
                    if (!empty($user['password']) && password_verify($password, $user['password'])) {
                        // Устаревший или слабый хеш пересчитываем при успешном входе.
                        if (password_needs_rehash($user['password'], PASSWORD_DEFAULT)) {
                            $userModel->updatePassword((int) $user['id'], $password);
                        }

                        $_SESSION['user_id'] = $user['id'];
                        $_SESSION['username'] = $user['login'] ?? $user['username'];
                        $_SESSION['status'] = $user['status'] ?? 'user';
                        
                        header('Location: index.php');
                        exit;
                    } else {
                        $error = 'Неверный пароль.';
                    }
                } else {
                    $error = 'Пользователь не найден.';
                }
            } else {
                $error = 'Пожалуйста, заполните все поля.';
            }
        }

        include __DIR__ . '/../view/formLogin.php';
    }

    public function register() {
        $error = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
        unset($_SESSION['user_id']);
        unset($_SESSION['username']);
        unset($_SESSION['status']);
        session_destroy();
        header('Location: index.php');
        exit;
    }
}
?>