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

    <!-- Средняя оценка услуги -->
    <?php if (isset($ratingStats) && $ratingStats['total_reviews'] > 0): ?>
        <div class="rating-summary" style="display: flex; align-items: center; gap: 12px; margin-bottom: 15px; padding: 12px 16px; background: var(--bg-main); border-radius: 8px;">
            <div class="stars-display" style="display: flex; gap: 2px;">
                <?php 
                $avgRating = round($ratingStats['avg_rating'], 1);
                for ($i = 1; $i <= 5; $i++):
                    if ($i <= floor($avgRating)):
                ?>
                    <span style="color: #f5a623; font-size: 22px;">&#9733;</span>
                <?php elseif ($i - $avgRating < 1): ?>
                    <span style="color: #f5a623; font-size: 22px;">&#9733;</span>
                <?php else: ?>
                    <span style="color: #ddd; font-size: 22px;">&#9733;</span>
                <?php 
                    endif;
                endfor; 
                ?>
            </div>
            <span style="font-size: 20px; font-weight: 700; color: var(--text-main);"><?= $avgRating ?></span>
            <span style="color: var(--text-secondary); font-size: 14px;">(<?= $ratingStats['total_reviews'] ?> <?= $lang['reviews_count'] ?? 'отзывов' ?>)</span>
        </div>
    <?php endif; ?>

    <!-- Мастер процедуры и его рейтинг -->
    <?php if (!empty($service['master_name'])): ?>
        <div class="master-card" style="display: flex; align-items: center; gap: 14px; flex-wrap: wrap; padding: 14px 16px; margin-bottom: 18px; background: var(--bg-main); border-radius: 8px;">
            <div style="display: flex; align-items: center; gap: 8px;">
                <span style="font-size: 26px; line-height: 1;">&#128100;</span>
                <div>
                    <div style="font-size: 13px; color: var(--text-secondary);"><?= $lang['master'] ?? 'Мастер' ?></div>
                    <strong style="color: var(--text-main); font-size: 16px;"><?= htmlspecialchars($service['master_name']) ?></strong>
                </div>
            </div>
            <?php if (!empty($masterStats) && $masterStats['total_reviews'] > 0): ?>
                <?php $mAvg = round($masterStats['avg_rating'], 1); ?>
                <div style="display: flex; align-items: center; gap: 8px;">
                    <span style="display: flex; gap: 2px;">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <span style="color: <?= $i <= round($mAvg) ? '#f5a623' : '#ddd' ?>; font-size: 18px;">&#9733;</span>
                        <?php endfor; ?>
                    </span>
                    <span style="font-weight: 700; color: var(--text-main);"><?= $mAvg ?></span>
                    <span style="color: var(--text-secondary); font-size: 13px;">
                        (<?= $masterStats['total_reviews'] ?> <?= $lang['reviews_count'] ?? 'отзывов' ?>, <?= $masterStats['total_services'] ?> <?= $lang['master_services'] ?? 'услуг' ?>)
                    </span>
                </div>
            <?php else: ?>
                <span style="color: var(--text-secondary); font-size: 14px;">
                    <?= $lang['master_rating'] ?? 'Рейтинг мастера' ?>: <?= $lang['no_reviews_short'] ?? 'Нет оценок' ?>
                </span>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <p style="font-size: 16px; line-height: 1.6; color: var(--text-main);"><?= htmlspecialchars($service['description']) ?></p>
    
    <p style="margin-top: 15px; font-size: 18px;">
        <?= $lang['price'] ?? 'Цена' ?>: <strong><?= $service['price'] ?> €</strong>
    </p>
    <p style="font-size: 18px;">
        <?= $lang['duration'] ?? 'Длительность' ?>: <strong><?= $service['duration'] ?> <?= $lang['min'] ?? 'мин' ?></strong>
    </p>

    <!-- Блок записи отображается только для клиентов и гостей, но скрыт для администратора -->
    <?php if (!isset($_SESSION['status']) || $_SESSION['status'] !== 'admin'): ?>
        <div style="margin-top: 25px;">
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="index.php?action=book&id=<?= $service['id'] ?>" class="btn"><?= $lang['book_btn'] ?? 'Записаться на процедуру' ?></a>
            <?php else: ?>
                <p style="color: var(--text-secondary);">
                    <a href="index.php?action=book&id=<?= $service['id'] ?>" class="btn">
    <?= $lang['book_btn'] ?? 'Записаться на процедуру' ?>
</a>
                </p>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <br>
    <p style="margin-top: 20px;">
        <a href="index.php" style="color: var(--primary); text-decoration: none; font-weight: 500;">&larr; <?= $lang['back_to_services'] ?? 'Назад к списку услуг' ?></a>
    </p>
</div>

<!-- ============== БЛОК ОЦЕНКИ И ОТЗЫВОВ ============== -->
<div id="reviews" style="margin-top: 30px;">

    <!-- Форма добавления оценки (только для авторизованных клиентов, не админов) -->
    <?php if (isset($_SESSION['user_id']) && (!isset($_SESSION['status']) || $_SESSION['status'] !== 'admin')): ?>
        <div class="rating-form-card" style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 8px; padding: 25px; margin-bottom: 25px; box-shadow: 0 4px 6px rgba(0,0,0,0.02);">
            <h3 style="color: var(--primary); margin-top: 0; margin-bottom: 15px;">
                <?php if ($userRating): ?>
                    <?= $lang['edit_your_review'] ?? 'Изменить вашу оценку' ?>
                <?php else: ?>
                    <?= $lang['leave_review'] ?? 'Оставить отзыв' ?>
                <?php endif; ?>
            </h3>
            
            <form method="POST" action="index.php?action=submit_rating" class="salon-form">
                <input type="hidden" name="service_id" value="<?= $service['id'] ?>">
                
                <!-- Звёздочный рейтинг -->
                <div style="margin-bottom: 15px;">
                    <label style="font-weight: 600; display: block; margin-bottom: 8px; color: var(--text-main);">
                        <?= $lang['your_rating'] ?? 'Ваша оценка' ?>:
                    </label>
                    <div class="star-rating" style="display: flex; gap: 5px; direction: rtl; justify-content: flex-end;">
                        <?php for ($i = 5; $i >= 1; $i--): ?>
                            <input type="radio" name="rating" value="<?= $i ?>" id="star<?= $i ?>" 
                                   style="display: none;" 
                                   <?= ($userRating && $userRating['rating'] == $i) ? 'checked' : '' ?>
                                   required>
                            <label for="star<?= $i ?>" class="star-label" 
                                   style="cursor: pointer; font-size: 32px; color: <?= ($userRating && $userRating['rating'] >= $i) ? '#f5a623' : '#ddd' ?>; transition: color 0.2s;"
                                   onmouseover="highlightStars(<?= $i ?>)" 
                                   onmouseout="resetStars()">&#9733;</label>
                        <?php endfor; ?>
                    </div>
                </div>
                
                <!-- Текст отзыва -->
                <div style="margin-bottom: 15px;">
                    <label for="review" style="font-weight: 600; display: block; margin-bottom: 8px; color: var(--text-main);">
                        <?= $lang['your_review'] ?? 'Ваш отзыв' ?> (<?= $lang['optional'] ?? 'необязательно' ?>):
                    </label>
                    <textarea name="review" id="review" rows="3" 
                              style="width: 100%; padding: 10px 12px; border: 1px solid var(--border-color); border-radius: 6px; background-color: var(--bg-main); color: var(--text-main); font-size: 14px; resize: vertical; font-family: inherit;"
                              placeholder="<?= $lang['review_placeholder'] ?? 'Напишите ваш отзыв...' ?>"><?= $userRating ? htmlspecialchars($userRating['review']) : '' ?></textarea>
                </div>
                
                <button type="submit" class="btn">
                    <?php if ($userRating): ?>
                        <?= $lang['update_review'] ?? 'Обновить отзыв' ?>
                    <?php else: ?>
                        <?= $lang['submit_review'] ?? 'Отправить отзыв' ?>
                    <?php endif; ?>
                </button>
            </form>
        </div>
    <?php elseif (!isset($_SESSION['user_id'])): ?>
        <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 8px; padding: 20px; margin-bottom: 25px; text-align: center;">
            <p style="color: var(--text-secondary); margin: 0;">
                <a href="index.php?action=login" style="color: var(--primary); font-weight: 600; text-decoration: none;">
                    <?= $lang['login_to_review'] ?? 'Войдите, чтобы оставить отзыв' ?>
                </a>
            </p>
        </div>
    <?php endif; ?>

    <!-- Список отзывов -->
    <?php if (!empty($reviews)): ?>
        <h3 style="color: var(--primary); margin-bottom: 15px;"><?= $lang['all_reviews'] ?? 'Отзывы' ?> (<?= count($reviews) ?>)</h3>
        
        <?php foreach ($reviews as $rev): ?>
            <div class="review-card" style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 8px; padding: 20px; margin-bottom: 12px; box-shadow: 0 2px 4px rgba(0,0,0,0.02);">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <strong style="color: var(--text-main);"><?= htmlspecialchars($rev['user_name']) ?></strong>
                        <div style="display: flex; gap: 1px;">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <span style="color: <?= $i <= $rev['rating'] ? '#f5a623' : '#ddd' ?>; font-size: 16px;">&#9733;</span>
                            <?php endfor; ?>
                        </div>
                    </div>
                    <span style="color: var(--text-secondary); font-size: 13px;">
                        <?= date('d.m.Y H:i', strtotime($rev['created_at'])) ?>
                    </span>
                </div>
                <?php if (!empty($rev['review'])): ?>
                    <p style="color: var(--text-main); margin: 0; line-height: 1.5;"><?= nl2br(htmlspecialchars($rev['review'])) ?></p>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p style="color: var(--text-secondary); text-align: center; padding: 20px;">
            <?= $lang['no_reviews_yet'] ?? 'Отзывов пока нет. Будьте первым!' ?>
        </p>
    <?php endif; ?>
</div>

<!-- JavaScript для интерактивных звёздочек -->
<script>
function highlightStars(rating) {
    const labels = document.querySelectorAll('.star-label');
    labels.forEach(label => {
        const starFor = label.getAttribute('for');
        const starValue = parseInt(starFor.replace('star', ''));
        label.style.color = starValue <= rating ? '#f5a623' : '#ddd';
    });
}

function resetStars() {
    const checked = document.querySelector('.star-rating input:checked');
    const labels = document.querySelectorAll('.star-label');
    
    if (checked) {
        const checkedVal = parseInt(checked.value);
        labels.forEach(label => {
            const starFor = label.getAttribute('for');
            const starValue = parseInt(starFor.replace('star', ''));
            label.style.color = starValue <= checkedVal ? '#f5a623' : '#ddd';
        });
    } else {
        labels.forEach(label => {
            label.style.color = '#ddd';
        });
    }
}

// Обработка клика по звёздочкам
document.querySelectorAll('.star-rating input').forEach(input => {
    input.addEventListener('change', function() {
        resetStars();
    });
});
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/templates/layout.php';
?>