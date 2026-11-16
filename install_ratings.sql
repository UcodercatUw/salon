-- Таблица оценок и отзывов для услуг салона (Ilusalong)
-- Выполнить один раз в базе salon_db (phpMyAdmin -> SQL) либо через install_ratings.php

CREATE TABLE IF NOT EXISTS `ratings` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `user_id` INT(11) NOT NULL,
  `service_id` INT(11) NOT NULL,
  `rating` TINYINT(4) NOT NULL,
  `review` TEXT DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_user_service` (`user_id`, `service_id`),
  KEY `idx_ratings_service` (`service_id`),
  CONSTRAINT `fk_ratings_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_ratings_service` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- ============================================================
-- НЕОБЯЗАТЕЛЬНО: демонстрационные отзывы.
-- Раскомментируйте блок и выполните, если нужны готовые отзывы
-- для показа статистики (автор — существующий клиент id = 2).
-- ============================================================
-- INSERT INTO `ratings` (`user_id`, `service_id`, `rating`, `review`) VALUES
--   (2, 1, 5, 'Väga hea juuksur, tulemus ületas ootusi!'),
--   (2, 2, 4, 'Hea lõikus, aga ootasin veidi kauem.'),
--   (2, 3, 5, 'Suurepärane värvimine, soovitan!'),
--   (2, 4, 4, 'Kena maniküür, kestab hästi.'),
--   (2, 5, 5, 'Parim pediküür, mida olen saanud.'),
--   (2, 6, 3, 'Ultraheli puhastus oli ok, aga hind tundus kõrge.'),
--   (2, 7, 5, 'Lõõgastav massaaž, tulen kindlasti tagasi.');
