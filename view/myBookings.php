<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$langCode = $_SESSION['lang'] ?? 'et';
$langFile = __DIR__ . '/../lang/' . $langCode . '.php';
$lang = file_exists($langFile) ? require $langFile : [];
if (!is_array($lang)) {
    $lang = [];
}

ob_start();
?>

<div class="bookings-page">
    <h2><?= $lang['my_bookings'] ?? 'Мои записи' ?></h2>
    <br>

    <div class="services-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px; margin-bottom: 30px;">
        <?php if (!empty($bookings)): ?>
            <?php foreach ($bookings as $booking): ?>
                <div class="service-card" style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 8px; padding: 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.02);">
                    <h3 style="margin-top: 0; color: var(--primary);"><?= htmlspecialchars($booking['service_title']) ?></h3>
                    <p><?= $lang['price'] ?? 'Цена' ?>: <strong><?= $booking['price'] ?> €</strong></p>
                    <p><?= $lang['duration'] ?? 'Длительность' ?>: <strong><?= $booking['duration'] ?> <?= $lang['min'] ?? 'мин' ?></strong></p>
                    <p><?= $lang['date'] ?? 'Дата' ?>: <strong><?= $booking['enrollment_date'] ?></strong></p>
                    <p><?= $lang['status'] ?? 'Статус' ?>: <strong><?= htmlspecialchars($booking['status']) ?></strong></p>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p style="color: var(--text-secondary);"><?= $lang['no_bookings'] ?? 'У вас пока нет записей.' ?></p>
        <?php endif; ?>
    </div>

    <a href="index.php" class="btn"><?= $lang['back_home'] ?? '← На главную' ?></a>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/templates/layout.php';
?>