-- =====================================================================
-- Feature: Source-Player Whitelist (require_source_players)
-- อนุญาตเฉพาะนักเตะที่เคยถูกส่งรายชื่อในทัวร์นาเมนต์ต้นทางที่กำหนด
-- Mirror ของ migrations:
--   2026-07-20-120000_AddRequireSourcePlayersToTournament
--   2026-07-20-121000_CreateTournamentSourceCheckLink
--
-- ใช้สำหรับ deploy บน production ที่ไม่ได้รัน `php spark migrate`
-- รันซ้ำได้ปลอดภัย (idempotent)
-- =====================================================================

-- ---------------------------------------------------------------------
-- 1) เพิ่ม column `require_source_players` ในตาราง `tournament`
--    (idempotent: เพิ่มเฉพาะเมื่อยังไม่มี column)
-- ---------------------------------------------------------------------
SET @col_exists := (
    SELECT COUNT(*) FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'tournament'
      AND COLUMN_NAME = 'require_source_players'
);

SET @ddl := IF(@col_exists = 0,
    'ALTER TABLE `tournament`
        ADD COLUMN `require_source_players` TINYINT(1) NOT NULL DEFAULT 0
        COMMENT ''อนุญาตเฉพาะนักเตะที่เคยถูกส่งรายชื่อในทัวร์นาเมนต์ที่เลือกไว้ (tournament_source_check_link)''
        AFTER `prevent_duplicate_player`',
    'SELECT 1'
);

PREPARE stmt FROM @ddl;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- ---------------------------------------------------------------------
-- 2) สร้างตาราง `tournament_source_check_link`
--    เก็บ mapping: ทัวร์ปลายทาง (tour_id) -> ทัวร์ต้นทาง (source_tour_id)
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `tournament_source_check_link` (
    `id`             INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    `tour_id`        INT(10) UNSIGNED NOT NULL,
    `source_tour_id` INT(10) UNSIGNED NOT NULL,
    `created_at`     DATETIME DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_tour_source` (`tour_id`, `source_tour_id`),
    KEY `idx_tour` (`tour_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================================
-- หมายเหตุ: การตั้งค่า source tournaments ต่อทัวร์นาเมนต์
-- ทำผ่านหน้า admin (/admin/tournament -> แก้ไขทัวร์ -> เปิด "ตรวจสอบ
-- นักเตะต้นทาง" -> เลือกรายการแข่งขันต้นทาง) ไม่ต้อง INSERT ผ่าน SQL
-- =====================================================================
