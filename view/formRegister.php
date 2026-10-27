<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$langCode = $_SESSION['lang'] ?? 'et';
$langFile = ROOT_DIR . '/lang/' . $langCode . '.php';
$lang = file_exists($langFile) ? require $langFile : [];
if (!is_array($lang)) {
    $lang = [];
}

ob_start();
?>

<div class="form-container" style="max-width: 450px; margin: 40px auto; background: var(--bg-card); padding: 30px; border-radius: 8px; border: 1px solid var(--border-color); box-shadow: 0 4px 6px rgba(0,0,0,0.02);">
    <h2 style="margin-top: 0; color: var(--primary); text-align: center;"><?= $lang['register'] ?? 'Регистрация' ?></h2>

    <?php if (!empty($error)): ?>
        <div style="background: #ffe6eb; color: #c71585; padding: 10px; border-radius: 4px; margin-bottom: 15px; font-size: 14px;">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="index.php?action=register">
        <div style="margin-bottom: 15px;">
            <label style="display: block; margin-bottom: 5px; font-weight: 500;"><?= $lang['name'] ?? 'Имя' ?>:</label>
            <input type="text" name="name" required style="width: 100%; padding: 10px; border: 1px solid var(--border-color); border-radius: 4px; background: #fff;">
        </div>

        <div style="margin-bottom: 15px;">
            <label style="display: block; margin-bottom: 5px; font-weight: 500;">Email:</label>
            <input type="email" name="email" required style="width: 100%; padding: 10px; border: 1px solid var(--border-color); border-radius: 4px; background: #fff;">
        </div>

        <div style="margin-bottom: 15px;">
            <label style="display: block; margin-bottom: 5px; font-weight: 500;"><?= $lang['phone'] ?? 'Телефон' ?>:</label>
            <input type="text" name="phone" style="width: 100%; padding: 10px; border: 1px solid var(--border-color); border-radius: 4px; background: #fff;">
        </div>

        <div style="margin-bottom: 15px;">
            <label style="display: block; margin-bottom: 5px; font-weight: 500;"><?= $lang['username'] ?? 'Логин' ?>:</label>
            <input type="text" name="username" required style="width: 100%; padding: 10px; border: 1px solid var(--border-color); border-radius: 4px; background: #fff;">
        </div>

        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 5px; font-weight: 500;"><?= $lang['password'] ?? 'Пароль' ?>:</label>
            <input type="password" name="password" required style="width: 100%; padding: 10px; border: 1px solid var(--border-color); border-radius: 4px; background: #fff;">
        </div>

        <button type="submit" class="btn" style="width: 100%;"><?= $lang['register_btn'] ?? 'Зарегистрироваться' ?></button>
    </form>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/templates/layout.php';
?>