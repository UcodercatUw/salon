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

// Общее количество отзывов для распределения
$totalReviews = array_sum($distribution);

ob_start();
?>

<h2 style="color: var(--primary); margin-bottom: 25px;"><?= $lang['admin_ratings_title'] ?? 'Статистика отзывов' ?></h2>

<!-- ============ ОБЩАЯ СТАТИСТИКА ============ -->
<div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 20px; margin-bottom: 30px;">
    <!-- Всего отзывов -->
    <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 8px; padding: 25px; text-align: center; box-shadow: 0 4px 6px rgba(0,0,0,0.02);">
        <div style="font-size: 36px; font-weight: 700; color: var(--primary);">
            <?= $overallStats['total_reviews'] ?>
        </div>
        <div style="color: var(--text-secondary); margin-top: 5px; font-size: 14px;">
            <?= $lang['stat_total_reviews'] ?? 'Всего отзывов' ?>
        </div>
    </div>
    
    <!-- Средняя оценка -->
    <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 8px; padding: 25px; text-align: center; box-shadow: 0 4px 6px rgba(0,0,0,0.02);">
        <div style="font-size: 36px; font-weight: 700; color: #f5a623;">
            <?= $overallStats['total_reviews'] > 0 ? round($overallStats['overall_avg'], 1) : '—' ?>
        </div>
        <div style="display: flex; justify-content: center; gap: 2px; margin-top: 3px;">
            <?php 
            $ovAvg = round($overallStats['overall_avg']);
            for ($i = 1; $i <= 5; $i++): ?>
                <span style="color: <?= $i <= $ovAvg ? '#f5a623' : '#ddd' ?>; font-size: 16px;">&#9733;</span>
            <?php endfor; ?>
        </div>
        <div style="color: var(--text-secondary); margin-top: 5px; font-size: 14px;">
            <?= $lang['stat_avg_rating'] ?? 'Средняя оценка' ?>
        </div>
    </div>
    
    <!-- Уникальные пользователи -->
    <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 8px; padding: 25px; text-align: center; box-shadow: 0 4px 6px rgba(0,0,0,0.02);">
        <div style="font-size: 36px; font-weight: 700; color: var(--primary);">
            <?= $overallStats['unique_reviewers'] ?>
        </div>
        <div style="color: var(--text-secondary); margin-top: 5px; font-size: 14px;">
            <?= $lang['stat_unique_reviewers'] ?? 'Клиентов оставили отзыв' ?>
        </div>
    </div>
    
    <!-- Оцененные услуги -->
    <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 8px; padding: 25px; text-align: center; box-shadow: 0 4px 6px rgba(0,0,0,0.02);">
        <div style="font-size: 36px; font-weight: 700; color: var(--primary);">
            <?= $overallStats['reviewed_services'] ?>
        </div>
        <div style="color: var(--text-secondary); margin-top: 5px; font-size: 14px;">
            <?= $lang['stat_reviewed_services'] ?? 'Услуг с отзывами' ?>
        </div>
    </div>
</div>

<!-- ============ РАСПРЕДЕЛЕНИЕ ОЦЕНОК ============ -->
<?php if ($totalReviews > 0): ?>
<div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 8px; padding: 25px; margin-bottom: 30px; box-shadow: 0 4px 6px rgba(0,0,0,0.02);">
    <h3 style="color: var(--primary); margin-top: 0; margin-bottom: 20px;">
        <?= $lang['rating_distribution'] ?? 'Распределение оценок' ?>
    </h3>
    
    <?php for ($stars = 5; $stars >= 1; $stars--): ?>
        <?php 
            $count = $distribution[$stars];
            $percentage = $totalReviews > 0 ? round(($count / $totalReviews) * 100) : 0;
        ?>
        <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 10px;">
            <div style="min-width: 30px; text-align: right; font-weight: 600; color: var(--text-main);">
                <?= $stars ?> &#9733;
            </div>
            <div style="flex: 1; background: var(--bg-main); border-radius: 10px; height: 20px; overflow: hidden;">
                <div style="width: <?= $percentage ?>%; background: linear-gradient(90deg, #f5a623, #ffd700); height: 100%; border-radius: 10px; transition: width 0.5s ease;">
                </div>
            </div>
            <div style="min-width: 70px; text-align: right; color: var(--text-secondary); font-size: 13px;">
                <?= $count ?> (<?= $percentage ?>%)
            </div>
        </div>
    <?php endfor; ?>
</div>
<?php endif; ?>

<!-- ============ РЕЙТИНГ ПО УСЛУГАМ ============ -->
<div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 8px; padding: 25px; margin-bottom: 30px; box-shadow: 0 4px 6px rgba(0,0,0,0.02); overflow-x: auto;">
    <h3 style="color: var(--primary); margin-top: 0; margin-bottom: 20px;">
        <?= $lang['ratings_by_service'] ?? 'Оценки по услугам' ?>
    </h3>
    
    <table style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr style="background: var(--bg-main);">
                <th style="text-align: left; padding: 12px; font-weight: 600; color: var(--text-main); border-bottom: 2px solid var(--border-color);"><?= $lang['service'] ?? 'Услуга' ?></th>
                <th style="text-align: center; padding: 12px; font-weight: 600; color: var(--text-main); border-bottom: 2px solid var(--border-color);"><?= $lang['stat_avg_rating'] ?? 'Средняя оценка' ?></th>
                <th style="text-align: center; padding: 12px; font-weight: 600; color: var(--text-main); border-bottom: 2px solid var(--border-color);"><?= $lang['stat_total_reviews'] ?? 'Кол-во отзывов' ?></th>
                <th style="text-align: center; padding: 12px; font-weight: 600; color: var(--text-main); border-bottom: 2px solid var(--border-color);">Min / Max</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($serviceStats as $ss): ?>
                <tr style="border-bottom: 1px solid var(--border-color);">
                    <td style="padding: 12px; color: var(--text-main);">
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <?php if (!empty($ss['image'])): ?>
                                <img src="../public/images/<?= htmlspecialchars($ss['image']) ?>" 
                                     style="width: 40px; height: 40px; object-fit: cover; border-radius: 6px;">
                            <?php endif; ?>
                            <strong><?= htmlspecialchars($ss['title']) ?></strong>
                        </div>
                    </td>
                    <td style="text-align: center; padding: 12px;">
                        <?php if ($ss['total_reviews'] > 0): ?>
                            <div style="display: flex; align-items: center; justify-content: center; gap: 6px;">
                                <div style="display: flex; gap: 1px;">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <span style="color: <?= $i <= round($ss['avg_rating']) ? '#f5a623' : '#ddd' ?>; font-size: 14px;">&#9733;</span>
                                    <?php endfor; ?>
                                </div>
                                <span style="font-weight: 600;"><?= round($ss['avg_rating'], 1) ?></span>
                            </div>
                        <?php else: ?>
                            <span style="color: var(--text-secondary);">—</span>
                        <?php endif; ?>
                    </td>
                    <td style="text-align: center; padding: 12px; color: var(--text-main); font-weight: 500;">
                        <?= $ss['total_reviews'] ?>
                    </td>
                    <td style="text-align: center; padding: 12px; color: var(--text-secondary);">
                        <?php if ($ss['total_reviews'] > 0): ?>
                            <?= $ss['min_rating'] ?> / <?= $ss['max_rating'] ?>
                        <?php else: ?>
                            —
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- ============ РЕЙТИНГ ПО МАСТЕРАМ ============ -->
<?php if (!empty($masterStats)): ?>
<div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 8px; padding: 25px; margin-bottom: 30px; box-shadow: 0 4px 6px rgba(0,0,0,0.02); overflow-x: auto;">
    <h3 style="color: var(--primary); margin-top: 0; margin-bottom: 20px;">
        <?= $lang['stats_by_master'] ?? 'Рейтинг мастеров' ?>
    </h3>

    <table style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr style="background: var(--bg-main);">
                <th style="text-align: left; padding: 12px; font-weight: 600; color: var(--text-main); border-bottom: 2px solid var(--border-color);"><?= $lang['master'] ?? 'Мастер' ?></th>
                <th style="text-align: center; padding: 12px; font-weight: 600; color: var(--text-main); border-bottom: 2px solid var(--border-color);"><?= $lang['stat_avg_rating'] ?? 'Средняя оценка' ?></th>
                <th style="text-align: center; padding: 12px; font-weight: 600; color: var(--text-main); border-bottom: 2px solid var(--border-color);"><?= $lang['stat_total_reviews'] ?? 'Кол-во отзывов' ?></th>
                <th style="text-align: center; padding: 12px; font-weight: 600; color: var(--text-main); border-bottom: 2px solid var(--border-color);"><?= $lang['master_services'] ?? 'Услуг' ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($masterStats as $ms): ?>
                <tr style="border-bottom: 1px solid var(--border-color);">
                    <td style="padding: 12px; color: var(--text-main);"><strong><?= htmlspecialchars($ms['master_name']) ?></strong></td>
                    <td style="text-align: center; padding: 12px;">
                        <?php if ($ms['total_reviews'] > 0): ?>
                            <div style="display: flex; align-items: center; justify-content: center; gap: 6px;">
                                <div style="display: flex; gap: 1px;">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <span style="color: <?= $i <= round($ms['avg_rating']) ? '#f5a623' : '#ddd' ?>; font-size: 14px;">&#9733;</span>
                                    <?php endfor; ?>
                                </div>
                                <span style="font-weight: 600;"><?= round($ms['avg_rating'], 1) ?></span>
                            </div>
                        <?php else: ?>
                            <span style="color: var(--text-secondary);">—</span>
                        <?php endif; ?>
                    </td>
                    <td style="text-align: center; padding: 12px; color: var(--text-main); font-weight: 500;"><?= $ms['total_reviews'] ?></td>
                    <td style="text-align: center; padding: 12px; color: var(--text-secondary);"><?= $ms['total_services'] ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>

<!-- ============ ПОСЛЕДНИЕ ОТЗЫВЫ ============ -->
<?php if (!empty($recentRatings)): ?>
<div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 8px; padding: 25px; margin-bottom: 30px; box-shadow: 0 4px 6px rgba(0,0,0,0.02);">
    <h3 style="color: var(--primary); margin-top: 0; margin-bottom: 20px;">
        <?= $lang['recent_reviews'] ?? 'Последние отзывы' ?>
    </h3>
    
    <?php foreach ($recentRatings as $rr): ?>
        <div style="border-bottom: 1px solid var(--border-color); padding: 15px 0; <?= $rr === end($recentRatings) ? 'border-bottom: none;' : '' ?>">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 10px;">
                <div>
                    <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 5px;">
                        <strong style="color: var(--text-main);"><?= htmlspecialchars($rr['user_name']) ?></strong>
                        <span style="color: var(--text-secondary); font-size: 13px;">→</span>
                        <span style="color: var(--primary); font-weight: 500;"><?= htmlspecialchars($rr['service_title']) ?></span>
                    </div>
                    <div style="display: flex; gap: 1px; margin-bottom: 5px;">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <span style="color: <?= $i <= $rr['rating'] ? '#f5a623' : '#ddd' ?>; font-size: 16px;">&#9733;</span>
                        <?php endfor; ?>
                    </div>
                    <?php if (!empty($rr['review'])): ?>
                        <p style="color: var(--text-main); margin: 5px 0 0; line-height: 1.5; font-size: 14px;">
                            <?= nl2br(htmlspecialchars($rr['review'])) ?>
                        </p>
                    <?php endif; ?>
                </div>
                <div style="display: flex; align-items: center; gap: 10px;">
                    <span style="color: var(--text-secondary); font-size: 13px; white-space: nowrap;">
                        <?= date('d.m.Y H:i', strtotime($rr['created_at'])) ?>
                    </span>
                    <a href="index.php?action=delete_rating&id=<?= $rr['id'] ?>" 
                       onclick="return confirm('<?= $lang['confirm_delete_review'] ?? 'Удалить этот отзыв?' ?>')" 
                       style="color: #e74c3c; text-decoration: none; font-size: 13px; font-weight: 600;">
                        <?= $lang['delete'] ?? 'Удалить' ?>
                    </a>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<?php
$content = ob_get_clean();
include __DIR__ . '/templates/adminLayout.php';
?>