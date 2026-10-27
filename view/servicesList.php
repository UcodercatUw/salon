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

$currentCategory = isset($_GET['category']) ? intval($_GET['category']) : null;

ob_start();
?>

<div class="services-page">
    <div class="categories-list">
        <a href="index.php" class="category-pill <?= $currentCategory === null ? 'active' : '' ?>">
            <?= $lang['all_services'] ?? 'Kõik teenused' ?>
        </a>
        <?php if (!empty($categories)): ?>
            <?php foreach ($categories as $cat): ?>
                <?php 
                    $catKey = 'cat_' . $cat['id'];
                    $catTitle = $lang[$catKey] ?? $cat['name'];
                ?>
                <a href="index.php?action=index&category=<?= $cat['id'] ?>" class="category-pill <?= $currentCategory === $cat['id'] ? 'active' : '' ?>">
                    <?= htmlspecialchars($catTitle) ?>
                </a>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <h2><?= $lang['services_title'] ?? 'Meie teenused' ?></h2>
    <br>

    <div class="services-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px;">
        <?php if (!empty($services)): ?>
            <?php foreach ($services as $service): ?>
                <div class="service-card" style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 8px; padding: 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.02);">
                    <?php if (!empty($service['image'])): ?>
                        <img src="public/images/<?= htmlspecialchars($service['image']) ?>" alt="" style="width: 100%; height: 180px; object-fit: cover; border-radius: 6px; margin-bottom: 15px;">
                    <?php endif; ?>
                    <h3 style="margin-top: 0; color: var(--primary);"><?= htmlspecialchars($service['title']) ?></h3>
                    <p style="color: var(--text-main);"><?= htmlspecialchars($service['description']) ?></p>
                    <p><?= $lang['price'] ?? 'Цена' ?>: <strong><?= $service['price'] ?> €</strong></p>
                    <p><?= $lang['duration'] ?? 'Длительность' ?>: <strong><?= $service['duration'] ?> <?= $lang['min'] ?? 'мин' ?></strong></p>
                    <br>
                    <a href="index.php?action=show&id=<?= $service['id'] ?>" class="btn"><?= $lang['details'] ?? 'Rohkem' ?></a>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p style="color: var(--text-secondary);"><?= $lang['no_services'] ?? 'Teenuseid hetkel pole.' ?></p>
        <?php endif; ?>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/templates/layout.php';
?>