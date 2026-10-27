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

<div class="service-detail" style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 8px; padding: 30px; margin-top: 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.02);">
    <?php if (!empty($service['image'])): ?>
        <img src="public/images/<?= htmlspecialchars($service['image']) ?>" alt="" style="width: 100%; max-height: 350px; object-fit: cover; border-radius: 6px; margin-bottom: 20px;">
    <?php endif; ?>

    <h2 style="color: var(--primary); margin-top: 0;"><?= htmlspecialchars($service['title']) ?></h2>
    <p style="font-size: 16px; line-height: 1.6; color: var(--text-main);"><?= htmlspecialchars($service['description']) ?></p>
    
    <p style="margin-top: 15px; font-size: 18px;">
        Цена: <strong><?= $service['price'] ?> €</strong>
    </p>
    <p style="font-size: 18px;">
        Длительность: <strong><?= $service['duration'] ?> мин</strong>
    </p>

    <!-- Блок записи отображается только для клиентов и гостей, но скрыт для администратора -->
    <?php if (!isset($_SESSION['status']) || $_SESSION['status'] !== 'admin'): ?>
        <div style="margin-top: 25px;">
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="index.php?action=book&id=<?= $service['id'] ?>" class="btn"><?= $lang['book_btn'] ?? 'Записаться на процедуру' ?></a>
            <?php else: ?>
                <p style="color: var(--text-secondary);">
                    <a href="index.php?action=login" style="color: var(--primary); font-weight: 600;"><?= $lang['login'] ?? 'Войдите' ?></a>, чтобы записаться на процедуру.
                </p>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <br>
    <p style="margin-top: 20px;">
        <a href="index.php" style="color: var(--primary); text-decoration: none; font-weight: 500;">&larr; <?= $lang['back_to_services'] ?? 'Назад к списку услуг' ?></a>
    </p>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/templates/layout.php';
?>