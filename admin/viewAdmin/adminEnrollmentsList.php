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
<div class="admin-enrollments">
    <h2><?= $lang['admin_enrollments_title'] ?? 'Управление записями клиентов' ?></h2>
    <br>

    <table class="admin-table" style="width: 100%; border-collapse: collapse; background: var(--bg-card); border-radius: 8px; overflow: hidden; border: 1px solid var(--border-color);">
        <thead>
            <tr style="background: var(--border-color); text-align: left;">
                <th style="padding: 12px;">ID</th>
                <th style="padding: 12px;"><?= $lang['client'] ?? 'Клиент' ?></th>
                <th style="padding: 12px;"><?= $lang['email'] ?? 'Email' ?></th>
                <th style="padding: 12px;"><?= $lang['service'] ?? 'Услуга' ?></th>
                <th style="padding: 12px;"><?= $lang['price'] ?? 'Цена' ?></th>
                <th style="padding: 12px;"><?= $lang['enrollment_date'] ?? 'Дата записи' ?></th>
                <th style="padding: 12px;"><?= $lang['status'] ?? 'Статус' ?></th>
                <th style="padding: 12px;"><?= $lang['actions'] ?? 'Действия' ?></th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($enrollments)): ?>
                <?php foreach ($enrollments as $item): ?>
                    <tr style="border-bottom: 1px solid var(--border-color);">
                        <td style="padding: 12px;"><?= $item['id'] ?></td>
                        <td style="padding: 12px;"><strong><?= htmlspecialchars($item['username']) ?></strong></td>
                        <td style="padding: 12px;"><?= htmlspecialchars($item['email']) ?></td>
                        <td style="padding: 12px;"><?= htmlspecialchars($item['service_title']) ?></td>
                        <td style="padding: 12px;"><?= $item['price'] ?> €</td>
                        <td style="padding: 12px;"><?= $item['enrollment_date'] ?></td>
                        <td style="padding: 12px;"><?= htmlspecialchars($item['status']) ?></td>
                        <td style="padding: 12px;">
                            <a href="index.php?action=delete_enrollment&id=<?= $item['id'] ?>" onclick="return confirm('<?= $lang['confirm_delete_enrollment'] ?? 'Вы уверены, что хотите удалить эту запись?' ?>');" style="color: #ff4d6d; text-decoration: none; font-weight: 600;"><?= $lang['delete'] ?? 'Удалить' ?></a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8" style="padding: 20px; text-align: center; color: var(--text-secondary);"><?= $lang['no_enrollments'] ?? 'Записей пока нет' ?></td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<?php
$content = ob_get_clean();
include __DIR__ . '/templates/adminLayout.php';
?>