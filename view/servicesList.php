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
                <div class="service-card" onclick="window.location.href='index.php?action=show&id=<?= $service['id'] ?>'" style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 8px; padding: 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.02); cursor: pointer;">
                    <?php if (!empty($service['image'])): ?>
                        <img src="public/images/<?= htmlspecialchars($service['image']) ?>" alt="" style="width: 100%; height: 180px; object-fit: cover; border-radius: 6px; margin-bottom: 15px;">
                    <?php endif; ?>
                    <h3 style="margin-top: 0; color: var(--primary);"><?= htmlspecialchars($service['title']) ?></h3>

                    <!-- Мастер процедуры и его рейтинг -->
                    <?php if (!empty($service['master_name'])): ?>
                        <div class="service-master" style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap; margin-bottom: 8px; color: var(--text-secondary); font-size: 14px;">
                            <span>&#128100; <?= $lang['master'] ?? 'Мастер' ?>: <strong style="color: var(--text-main);"><?= htmlspecialchars($service['master_name']) ?></strong></span>
                            <?php if (isset($masterRatings[$service['master_name']])): ?>
                                <?php
                                    $mRating = $masterRatings[$service['master_name']];
                                    $mAvg = round($mRating['avg_rating'], 1);
                                ?>
                                <span style="display: flex; align-items: center; gap: 3px;">
                                    <span style="display: flex; gap: 1px;">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <span style="color: <?= $i <= round($mAvg) ? '#f5a623' : '#ddd' ?>; font-size: 14px;">&#9733;</span>
                                        <?php endfor; ?>
                                    </span>
                                    <span style="font-weight: 600;"><?= $mAvg ?></span>
                                    <span>(<?= $mRating['total_reviews'] ?>)</span>
                                </span>
                            <?php else: ?>
                                <span><?= $lang['no_reviews_short'] ?? 'Нет оценок' ?></span>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <p style="color: var(--text-main);"><?= htmlspecialchars($service['description']) ?></p>
                    
                    <!-- Рейтинг на карточке -->
                    <?php if (isset($allRatings[$service['id']])): ?>
                        <?php 
                            $sRating = $allRatings[$service['id']];
                            $sAvg = round($sRating['avg_rating'], 1);
                        ?>
                        <div style="display: flex; align-items: center; gap: 6px; margin: 8px 0;">
                            <div style="display: flex; gap: 1px;">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <span style="color: <?= $i <= round($sAvg) ? '#f5a623' : '#ddd' ?>; font-size: 16px;">&#9733;</span>
                                <?php endfor; ?>
                            </div>
                            <span style="font-weight: 600; color: var(--text-main); font-size: 14px;"><?= $sAvg ?></span>
                            <span style="color: var(--text-secondary); font-size: 13px;">(<?= $sRating['total_reviews'] ?>)</span>
                        </div>
                    <?php else: ?>
                        <div style="display: flex; align-items: center; gap: 6px; margin: 8px 0;">
                            <div style="display: flex; gap: 1px;">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <span style="color: #ddd; font-size: 16px;">&#9733;</span>
                                <?php endfor; ?>
                            </div>
                            <span style="color: var(--text-secondary); font-size: 13px;"><?= $lang['no_reviews_short'] ?? 'Нет оценок' ?></span>
                        </div>
                    <?php endif; ?>

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