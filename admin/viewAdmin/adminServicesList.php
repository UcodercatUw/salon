<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$langCode = $_SESSION['lang'] ?? 'et';
// Верный путь для admin/viewAdmin/ — ровно два уровня вверх (../../)
$langFile = __DIR__ . '/../../lang/' . $langCode . '.php';
$lang = file_exists($langFile) ? require $langFile : [];
if (!is_array($lang)) {
    $lang = [];
}

ob_start();
?>
<div class="admin-services">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2><?= $lang['admin_services_title'] ?? 'Управление услугами салона' ?></h2>
        <a href="index.php?action=add" class="btn"><?= $lang['add_new_service'] ?? 'Добавить новую услугу' ?></a>
    </div>

    <table class="admin-table" style="width: 100%; border-collapse: collapse; background: var(--bg-card); border-radius: 8px; overflow: hidden; border: 1px solid var(--border-color);">
        <thead>
            <tr style="background: var(--border-color); text-align: left;">
                <th style="padding: 12px;"><?= $lang['image'] ?? 'Картинка' ?></th>
                <th style="padding: 12px;">ID</th>
                <th style="padding: 12px;"><?= $lang['category'] ?? 'Категория' ?></th>
                <th style="padding: 12px;"><?= $lang['title'] ?? 'Название' ?></th>
                <th style="padding: 12px;"><?= $lang['price'] ?? 'Цена' ?></th>
                <th style="padding: 12px;"><?= $lang['duration'] ?? 'Длительность' ?></th>
                <th style="padding: 12px;"><?= $lang['actions'] ?? 'Действия' ?></th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($services)): ?>
                <?php foreach ($services as $service): ?>
                    <tr style="border-bottom: 1px solid var(--border-color);">
                        <td style="padding: 12px;">
                            <?php 
                                $imgSrc = !empty($service['image']) ? '../public/images/' . $service['image'] : '../public/images/default.jpg';
                            ?>
                            <img src="<?= htmlspecialchars($imgSrc) ?>" alt="Service" style="width: 50px; height: 50px; object-fit: cover; border-radius: 6px; border: 1px solid var(--border-color);">
                        </td>
                        <td style="padding: 12px;"><?= $service['id'] ?></td>
                        <td style="padding: 12px;">
                            <?php 
                                $catKey = 'cat_' . ($service['category_id'] ?? $service['id_category'] ?? 1);
                                $catTitle = $lang[$catKey] ?? $service['category_name'];
                                echo htmlspecialchars($catTitle);
                            ?>
                        </td>
                        <td style="padding: 12px;"><strong><?= htmlspecialchars($service['title']) ?></strong></td>
                        <td style="padding: 12px;"><?= $service['price'] ?> €</td>
                        <td style="padding: 12px;"><?= $service['duration'] ?> <?= $lang['min'] ?? 'мин' ?></td>
                        <td style="padding: 12px;">
                            <a href="index.php?action=edit&id=<?= $service['id'] ?>" style="color: var(--primary); text-decoration: none; font-weight: 600; margin-right: 12px;"><?= $lang['edit'] ?? 'Редактировать' ?></a>
                            <a href="index.php?action=delete&id=<?= $service['id'] ?>" onclick="return confirm('<?= $lang['confirm_delete'] ?? 'Вы уверены?' ?>');" style="color: #ff4d6d; text-decoration: none; font-weight: 600;"><?= $lang['delete'] ?? 'Удалить' ?></a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" style="padding: 20px; text-align: center; color: var(--text-secondary);"><?= $lang['no_services'] ?? 'Услуг пока нет' ?></td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<?php
$content = ob_get_clean();
include __DIR__ . '/templates/adminLayout.php';
?>