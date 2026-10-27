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
    <h2><?= $lang['add_service_heading'] ?? 'Добавить новую услугу' ?></h2>
    <?php if (!empty($error)): ?>
        <p class="error-msg" style="color: #ff4d6d; margin-bottom: 15px;"><?= $error ?></p>
    <?php endif; ?>

    <form action="index.php?action=add" method="POST" enctype="multipart/form-data" class="salon-form">
        <div>
            <label><?= $lang['category_label'] ?? 'Категория:' ?></label>
            <select name="category_id" required>
                <?php foreach ($categories as $cat): ?>
                    <?php 
                        $catKey = 'cat_' . $cat['id'];
                        $catTitle = $lang[$catKey] ?? $cat['name'];
                    ?>
                    <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($catTitle) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <label><?= $lang['service_title_label'] ?? 'Название услуги:' ?></label>
            <input type="text" name="title" required>
        </div>

        <div>
            <label><?= $lang['description_label'] ?? 'Описание:' ?></label>
            <textarea name="description" rows="4"></textarea>
        </div>

        <div>
            <label><?= $lang['price_label'] ?? 'Цена (€):' ?></label>
            <input type="number" step="0.01" name="price" required>
        </div>

        <div>
            <label><?= $lang['duration_label'] ?? 'Длительность (мин):' ?></label>
            <input type="number" name="duration" value="60" required>
        </div>

        <div>
            <label><?= $lang['image_label'] ?? 'Изображение услуги:' ?></label>
            <input type="file" name="image" accept="image/*">
        </div>

        <button type="submit" class="btn" style="margin-top: 10px;"><?= $lang['save_service'] ?? 'Сохранить услугу' ?></button>
    </form>

    <br>
    <a href="index.php" class="back-link">&larr; <?= $lang['back_to_list'] ?? 'Вернуться к списку' ?></a>
</div>
<?php
$content = ob_get_clean();
include __DIR__ . '/templates/adminLayout.php';
?>
```[cite: 9, 12]

Сохраните этот код в файл **`admin/viewAdmin/adminServiceAdd.php`**[cite: 9] — теперь форма добавления новых услуг будет полностью синхронизирована с выбранным языком интерфейса!