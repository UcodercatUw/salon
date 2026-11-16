-- ============================================================
-- Мастер у процедуры: колонка master_name в таблице services
-- База: salon_db. phpMyAdmin -> вкладка SQL -> вставить -> Выполнить.
--
-- Скрипт безопасен для повторного запуска:
--   * колонка добавляется только если её ещё нет;
--   * UPDATE заполняет только пустые значения (master_name IS NULL),
--     уже проставленные имена и правки не перезаписываются.
-- ============================================================

-- 1. Добавляем колонку с именем мастера (только если её ещё нет)
SET @col_exists := (
  SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME   = 'services'
    AND COLUMN_NAME  = 'master_name'
);
SET @ddl := IF(
  @col_exists = 0,
  'ALTER TABLE `services` ADD COLUMN `master_name` VARCHAR(150) DEFAULT NULL AFTER `title`',
  'SELECT ''column services.master_name already exists - skipped'' AS info'
);
PREPARE stmt FROM @ddl;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;


-- 2. Мастера для существующих процедур (только для пустых значений)
--    1-3 — парикмахер, 4-5 — мастер маникюра, 6 — косметолог, 7 — массажист
UPDATE `services` SET `master_name` = 'Kadri Tamm' WHERE `master_name` IS NULL AND `id` IN (1, 2, 3);
UPDATE `services` SET `master_name` = 'Anni Kask'  WHERE `master_name` IS NULL AND `id` IN (4, 5);
UPDATE `services` SET `master_name` = 'Liis Pärn'  WHERE `master_name` IS NULL AND `id` = 6;
UPDATE `services` SET `master_name` = 'Mart Sepp'  WHERE `master_name` IS NULL AND `id` = 7;


-- 3. Проверка результата
SELECT `id`, `category_id`, `title`, `master_name` FROM `services` ORDER BY `id`;
