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

<div class="form-container" style="max-width: 450px; margin: 40px auto; background: var(--bg-card); padding: 30px; border-radius: 8px; border: 1px solid var(--border-color); box-shadow: 0 4px 6px rgba(0,0,0,0.02);">
    <h2 style="margin-top: 0; color: var(--primary); text-align: center;"><?= $lang['book_title'] ?? 'Запись на услугу' ?></h2>

    <?php if (!empty($error)): ?>
        <div style="background: #ffe6eb; color: #c71585; padding: 10px; border-radius: 4px; margin-bottom: 15px; font-size: 14px;">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <?php if (isset($service)): ?>
        <div style="margin-bottom: 20px; padding: 15px; background: #fff; border: 1px solid var(--border-color); border-radius: 6px;">
            <h3 style="margin: 0 0 10px 0; color: var(--primary);"><?= htmlspecialchars($service['title']) ?></h3>
            <p style="margin: 5px 0;">Цена: <strong><?= $service['price'] ?> €</strong></p>
            <p style="margin: 5px 0;">Длительность: <strong><?= $service['duration'] ?> мин</strong></p>
        </div>
    <?php endif; ?>

    <form method="POST" action="index.php?action=book&id=<?= $service['id'] ?? '' ?>">
        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 5px; font-weight: 500;"><?= $lang['booking_date'] ?? 'Дата и время записи' ?>:</label>
            <input type="datetime-local" name="enrollment_date" required style="width: 100%; padding: 10px; border: 1px solid var(--border-color); border-radius: 4px; background: #fff;">
        </div>

        <button type="submit" class="btn" style="width: 100%;"><?= $lang['confirm_booking'] ?? 'Подтвердить запись' ?></button>
    </form>

    <br>
    <p style="text-align: center; margin-top: 15px;">
        <a href="index.php?action=show&id=<?= $service['id'] ?? '' ?>" style="color: var(--primary); text-decoration: none; font-weight: 500;">&larr; Назад к услуге</a>
    </p>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/templates/layout.php';
?>