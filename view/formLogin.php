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

<div class="form-container" style="max-width: 400px; margin: 40px auto; background: var(--bg-card); padding: 30px; border-radius: 8px; border: 1px solid var(--border-color); box-shadow: 0 4px 6px rgba(0,0,0,0.02);">
    <h2 style="margin-top: 0; color: var(--primary); text-align: center;"><?= $lang['login'] ?? 'Войти' ?></h2>
    
    <?php if (!empty($error)): ?>
        <div style="background: #ffe6eb; color: #c71585; padding: 10px; border-radius: 4px; margin-bottom: 15px; font-size: 14px;">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($lockRemaining)): ?>
        <?php
        $lockTitle = $lang['login_locked_title'] ?? 'Вход временно заблокирован';
        $lockText = $lang['login_locked_text'] ?? 'Слишком много неудачных попыток. Повторите через {time}.';
        $lockDone = $lang['login_locked_done'] ?? 'Блокировка снята — можно войти снова.';
        // {time} заменяем на элемент, в который скрипт пишет обратный отсчёт.
        $lockText = str_replace('{time}', '<strong id="login-lock-time">--:--</strong>', $lockText);
        // Серверный абсолютный момент снятия блокировки может быть в сессии (остаётся
        // после перезагрузки страницы). Если он есть и ещё не наступил — используем его,
        // чтобы обратный отсчёт не сбрасывался на полные 15 минут при каждом обновлении.
        $lockUntil = isset($_SESSION['login_locked_until']) ? (int) $_SESSION['login_locked_until'] : 0;
        $secondsLeft = ($lockUntil > time()) ? ($lockUntil - time()) : (int) $lockRemaining;
        $secondsLeft = max(0, $secondsLeft);
        ?>
        <div id="login-lock-banner" data-seconds="<?= $secondsLeft ?>" data-done-text="<?= htmlspecialchars($lockDone, ENT_QUOTES, 'UTF-8') ?>" style="background: #fff4e5; color: #8a4b00; border-left: 4px solid #e8a33d; padding: 12px 14px; border-radius: 4px; margin-bottom: 15px; font-size: 14px;">
            <div style="font-weight: 600; margin-bottom: 4px;"><?= htmlspecialchars($lockTitle) ?></div>
            <div class="login-lock__text"><?= $lockText ?></div>
        </div>
    <?php endif; ?>

    <form method="POST" action="index.php?action=login">
        <?= csrf_field() ?>
        <div style="margin-bottom: 15px;">
            <label style="display: block; margin-bottom: 5px; font-weight: 500;"><?= $lang['username'] ?? 'Логин' ?>:</label>
            <input type="text" name="username" required style="width: 100%; padding: 10px; border: 1px solid var(--border-color); border-radius: 4px; background: #fff;">
        </div>

        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 5px; font-weight: 500;"><?= $lang['password'] ?? 'Пароль' ?>:</label>
            <input type="password" name="password" required style="width: 100%; padding: 10px; border: 1px solid var(--border-color); border-radius: 4px; background: #fff;">
        </div>

        <button type="submit" id="login-submit" class="btn" style="width: 100%;"><?= $lang['login_btn'] ?? 'Войти' ?></button>
    </form>

    <!-- Ссылка для перехода на форму регистрации -->
    <p style="text-align: center; margin-top: 20px; color: var(--text-secondary); font-size: 14px;">
        <?= $lang['no_account'] ?? 'Нет аккаунта?' ?> 
        <a href="index.php?action=register" style="color: var(--primary); font-weight: 600; text-decoration: none;">
            <?= $lang['register_btn'] ?? 'Зарегистрироваться' ?>
        </a>
    </p>
</div>

<?php if (!empty($lockRemaining)): ?>
    <?php // Скрипт в конце страницы: к этому моменту кнопка "Войти" уже есть в DOM. ?>
    <script>
    (function () {
        var banner = document.getElementById('login-lock-banner');
        if (!banner) { return; }
        var label = document.getElementById('login-lock-time');
        var text = banner.querySelector('.login-lock__text');
        var button = document.getElementById('login-submit');
        var left = parseInt(banner.getAttribute('data-seconds'), 10) || 0;

        function pad(value) { return value < 10 ? '0' + value : String(value); }

        function render() {
            label.textContent = Math.floor(left / 60) + ':' + pad(left % 60);
        }

        function setButtonDisabled(disabled) {
            if (!button) { return; }
            button.disabled = disabled;
            button.style.opacity = disabled ? '0.6' : '1';
            button.style.cursor = disabled ? 'not-allowed' : '';
        }

        setButtonDisabled(true);
        render();

        var timer = setInterval(function () {
            left -= 1;
            if (left > 0) { render(); return; }

            clearInterval(timer);
            label.textContent = '0:00';
            text.textContent = banner.getAttribute('data-done-text') || '';
            banner.style.background = '#eef8ee';
            banner.style.color = '#1e6b2f';
            banner.style.borderLeftColor = '#4caf50';
            setButtonDisabled(false);
        }, 1000);
    })();
    </script>
<?php endif; ?>

<?php
$content = ob_get_clean();
include __DIR__ . '/templates/layout.php';
?>