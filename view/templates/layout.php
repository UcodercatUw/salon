<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$langCode = $_SESSION['lang'] ?? 'et';
$langFile = __DIR__ . '/../../lang/' . $langCode . '.php';
$lang = file_exists($langFile) ? require $langFile : [];
if (!is_array($lang)) {
    $lang = [];
}

$getEt = $_GET; $getEt['lang'] = 'et'; $urlEt = 'index.php?' . http_build_query($getEt);
$getRu = $_GET; $getRu['lang'] = 'ru'; $urlRu = 'index.php?' . http_build_query($getRu);
$getEn = $_GET; $getEn['lang'] = 'en'; $urlEn = 'index.php?' . http_build_query($getEn);
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($langCode) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ilusalong</title>
    <link rel="stylesheet" href="public/css/style.css">
    <!-- Скрипт предотвращения мигания темы при загрузке -->
    <script>
        const savedTheme = localStorage.getItem('theme') || 'light';
        if (savedTheme === 'dark') {
            document.documentElement.setAttribute('data-theme', 'dark');
        }
    </script>
</head>
<body>
    <header>
        <div class="nav-container">
            <a href="index.php" class="logo">Ilusalong</a>
            <div class="nav-right">
                <!-- Аккуратная круглая кнопка с иконкой темы -->
                <button id="theme-toggle" type="button" style="background: transparent; border: 1px solid var(--border-color); color: var(--text-main); width: 38px; height: 38px; border-radius: 50%; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.2s ease;">
                    <svg id="theme-icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>
                    </svg>
                </button>

                <div class="lang-switcher">
                    <a href="<?= htmlspecialchars($urlEt) ?>">ET</a> | 
                    <a href="<?= htmlspecialchars($urlRu) ?>">RU</a> | 
                    <a href="<?= htmlspecialchars($urlEn) ?>">EN</a>
                </div>
                <div class="user-menu">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <span><?= htmlspecialchars($_SESSION['username'] ?? '') ?></span>
                        
                        <!-- Показываем «Мои записи» ТОЛЬКО если пользователь НЕ администратор -->
                        <?php if (!isset($_SESSION['status']) || $_SESSION['status'] !== 'admin'): ?>
                            <a href="index.php?action=my_bookings"><?= $lang['my_bookings'] ?? 'Мои записи' ?></a>
                        <?php endif; ?>

                        <!-- Показываем ссылку на админ-панель, если это админ -->
                        <?php if (isset($_SESSION['status']) && $_SESSION['status'] === 'admin'): ?>
                            <a href="admin/index.php"><?= $lang['admin_panel'] ?? 'Админ-панель' ?></a>
                        <?php endif; ?>

                        <a href="index.php?action=logout"><?= $lang['logout'] ?? 'Выйти' ?></a>
                    <?php else: ?>
                        <a href="index.php?action=login"><?= $lang['login'] ?? 'Войти' ?></a>
                        <a href="index.php?action=register"><?= $lang['register'] ?? 'Регистрация' ?></a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </header>

    <main class="container">
        <?= $content ?? '' ?>
    </main>

    <footer>
        <p>&copy; 2026 Ilusalong. Kõik õigused kaitstud.</p>
    </footer>

    <!-- Скрипт переключения темы и иконок -->
    <script>
        const themeToggleBtn = document.getElementById('theme-toggle');
        const themeIcon = document.getElementById('theme-icon');
        
        const sunSvg = '<circle cx="12" cy="12" r="5"></circle><line x1="12" y1="1" x2="12" y2="3"></line><line x1="12" y1="21" x2="12" y2="23"></line><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line><line x1="1" y1="12" x2="3" y2="12"></line><line x1="21" y1="12" x2="23" y2="12"></line><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line>';
        const moonSvg = '<path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>';

        if (localStorage.getItem('theme') === 'dark') {
            if (themeIcon) themeIcon.innerHTML = sunSvg;
        }

        if (themeToggleBtn) {
            themeToggleBtn.addEventListener('click', () => {
                const currentTheme = document.documentElement.getAttribute('data-theme');
                if (currentTheme === 'dark') {
                    document.documentElement.removeAttribute('data-theme');
                    localStorage.setItem('theme', 'light');
                    if (themeIcon) themeIcon.innerHTML = moonSvg;
                } else {
                    document.documentElement.setAttribute('data-theme', 'dark');
                    localStorage.setItem('theme', 'dark');
                    if (themeIcon) themeIcon.innerHTML = sunSvg;
                }
            });
        }
    </script>
</body>
</html>