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

ob_start();
?>
<div class="form-container">
    <h2><?= $lang['edit_service_heading'] ?? 'Редактировать услугу' ?></h2>
    <?php if (!empty($error)): ?>
        <p class="error-msg" style="color: #ff4d6d; margin-bottom: 15px;"><?= $error ?></p>
    <?php endif; ?>

    <form action="index.php?action=edit&id=<?= $service['id'] ?>" method="POST" enctype="multipart/form-data" class="salon-form">
        <div>
            <label><?= $lang['category_label'] ?? 'Категория:' ?></label>
            <select name="category_id" required>
                <?php foreach ($categories as $cat): ?>
                    <?php 
                        $catKey = 'cat_' . $cat['id'];
                        $catTitle = $lang[$catKey] ?? $cat['name'];
                    ?>
                    <option value="<?= $cat['id'] ?>" <?= $cat['id'] == $service['category_id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($catTitle) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <label><?= $lang['service_title_label'] ?? 'Название услуги:' ?></label>
            <input type="text" name="title" value="<?= htmlspecialchars($service['title']) ?>" required>
        </div>

        <div>
            <label><?= $lang['description_label'] ?? 'Описание:' ?></label>
            <textarea name="description" rows="4"><?= htmlspecialchars($service['description']) ?></textarea>
        </div>

        <div>
            <label><?= $lang['price_label'] ?? 'Цена (€):' ?></label>
            <input type="number" step="0.01" name="price" value="<?= $service['price'] ?>" required>
        </div>

        <div>
            <label><?= $lang['duration_label'] ?? 'Длительность (мин):' ?></label>
            <input type="number" name="duration" value="<?= $service['duration'] ?>" required>
        </div>

        <div>
            <label><?= $lang['image_label'] ?? 'Изображение:' ?></label>
            <?php if (!empty($service['image']) && $service['image'] !== 'default.jpg'): ?>
                <p style="font-size: 0.9rem; color: var(--text-secondary); margin-bottom: 5px;"><?= $lang['current'] ?? 'Текущее' ?>: <?= htmlspecialchars($service['image']) ?></p>
            <?php endif; ?>
            <input type="file" name="image" accept="image/*">
        </div>

        <button type="submit" class="btn" style="margin-top: 10px;"><?= $lang['save_changes'] ?? 'Сохранить изменения' ?></button>
    </form>
    
    <br>
    <a href="index.php" class="back-link">&larr; <?= $lang['back_to_list'] ?? 'Вернуться к списку' ?></a>
</div>
<?php
$content = ob_get_clean();
include __DIR__ . '/templates/adminLayout.php';
?>
