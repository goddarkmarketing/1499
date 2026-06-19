-- Migration: multi-game support (run once on existing databases)
USE boyinsure;

CREATE TABLE IF NOT EXISTS games (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  code VARCHAR(40) NOT NULL UNIQUE,
  name VARCHAR(120) NOT NULL,
  type ENUM('wheel','scratch','quiz','other') NOT NULL DEFAULT 'wheel',
  description TEXT NULL,
  status ENUM('active','inactive') NOT NULL DEFAULT 'active',
  sort_order SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

INSERT IGNORE INTO games (id, code, name, type, description, status, sort_order) VALUES
(1, 'lucky_wheel', 'วงล้อโชคดี', 'wheel', 'เกมหมุนวงล้อลุ้นรางวัลสำหรับลูกค้า BoyInsure', 'active', 1);

-- prizes.game_id
SET @col := (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'prizes' AND COLUMN_NAME = 'game_id');
SET @sql := IF(@col = 0,
  'ALTER TABLE prizes ADD COLUMN game_id INT UNSIGNED NULL AFTER id, ADD KEY idx_prizes_game (game_id)',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

UPDATE prizes SET game_id = 1 WHERE game_id IS NULL;

-- spin_logs.game_id
SET @col := (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'spin_logs' AND COLUMN_NAME = 'game_id');
SET @sql := IF(@col = 0,
  'ALTER TABLE spin_logs ADD COLUMN game_id INT UNSIGNED NULL AFTER member_id, ADD KEY idx_spin_game (game_id), ADD KEY idx_spin_created (created_at)',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

UPDATE spin_logs SET game_id = 1 WHERE game_id IS NULL;
