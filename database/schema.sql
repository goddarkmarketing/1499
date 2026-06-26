-- BOYINSURE Backend Schema
CREATE DATABASE IF NOT EXISTS boyinsure CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE boyinsure;

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS reward_claims;
DROP TABLE IF EXISTS spin_logs;
DROP TABLE IF EXISTS prizes;
DROP TABLE IF EXISTS campaigns;
DROP TABLE IF EXISTS games;
DROP TABLE IF EXISTS leads;
DROP TABLE IF EXISTS agent_applications;
DROP TABLE IF EXISTS agents;
DROP TABLE IF EXISTS agent_tiers;
DROP TABLE IF EXISTS members;
DROP TABLE IF EXISTS member_tiers;
DROP TABLE IF EXISTS articles;
DROP TABLE IF EXISTS article_categories;
DROP TABLE IF EXISTS admin_users;
DROP TABLE IF EXISTS insurance_plans;
DROP TABLE IF EXISTS insurance_categories;
DROP TABLE IF EXISTS site_content;
DROP TABLE IF EXISTS activity_logs;
DROP TABLE IF EXISTS member_game_quotas;
DROP TABLE IF EXISTS settings;
SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE admin_users (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  email VARCHAR(190) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  name VARCHAR(120) NOT NULL,
  role ENUM('super_admin','ops','hr','sales_manager','support') NOT NULL DEFAULT 'ops',
  status ENUM('active','suspended') NOT NULL DEFAULT 'active',
  last_login_at DATETIME NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE member_tiers (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  code VARCHAR(20) NOT NULL UNIQUE,
  name VARCHAR(80) NOT NULL,
  spin_quota TINYINT UNSIGNED NOT NULL DEFAULT 1,
  min_premium DECIMAL(12,2) NULL,
  description TEXT NULL,
  sort_order TINYINT UNSIGNED NOT NULL DEFAULT 0,
  status ENUM('active','inactive') NOT NULL DEFAULT 'active',
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE members (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  phone VARCHAR(20) NOT NULL,
  login_id VARCHAR(60) NULL,
  email VARCHAR(190) NULL,
  name VARCHAR(120) NOT NULL,
  password_hash VARCHAR(255) NULL,
  tier_id INT UNSIGNED NULL,
  assigned_agent_id INT UNSIGNED NULL,
  status ENUM('pending','active','suspended','closed') NOT NULL DEFAULT 'active',
  spins_remaining TINYINT UNSIGNED NOT NULL DEFAULT 0,
  points INT UNSIGNED NOT NULL DEFAULT 0,
  notes TEXT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY uk_members_phone (phone),
  UNIQUE KEY uk_members_login_id (login_id),
  KEY idx_members_tier (tier_id),
  KEY idx_members_status (status),
  CONSTRAINT fk_members_tier FOREIGN KEY (tier_id) REFERENCES member_tiers(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE agent_tiers (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  code VARCHAR(20) NOT NULL UNIQUE,
  name VARCHAR(80) NOT NULL,
  description TEXT NULL,
  min_policies INT UNSIGNED NULL,
  min_premium DECIMAL(12,2) NULL,
  sort_order TINYINT UNSIGNED NOT NULL DEFAULT 0,
  status ENUM('active','inactive') NOT NULL DEFAULT 'active',
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE agents (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  member_id INT UNSIGNED NULL,
  code VARCHAR(30) NULL UNIQUE,
  name VARCHAR(120) NOT NULL,
  phone VARCHAR(20) NOT NULL,
  email VARCHAR(190) NULL,
  tier_id INT UNSIGNED NULL,
  upline_id INT UNSIGNED NULL,
  license_no VARCHAR(60) NULL,
  status ENUM('active','suspended','inactive') NOT NULL DEFAULT 'active',
  joined_at DATE NULL,
  notes TEXT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  KEY idx_agents_tier (tier_id),
  KEY idx_agents_status (status),
  CONSTRAINT fk_agents_tier FOREIGN KEY (tier_id) REFERENCES agent_tiers(id) ON DELETE SET NULL,
  CONSTRAINT fk_agents_member FOREIGN KEY (member_id) REFERENCES members(id) ON DELETE SET NULL,
  CONSTRAINT fk_agents_upline FOREIGN KEY (upline_id) REFERENCES agents(id) ON DELETE SET NULL
) ENGINE=InnoDB;

ALTER TABLE members
  ADD CONSTRAINT fk_members_agent FOREIGN KEY (assigned_agent_id) REFERENCES agents(id) ON DELETE SET NULL;

CREATE TABLE agent_applications (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  phone VARCHAR(20) NOT NULL,
  email VARCHAR(190) NULL,
  education VARCHAR(120) NULL,
  experience TEXT NULL,
  status ENUM('submitted','contact_pending','interview','training','exam','approved','rejected','cancelled') NOT NULL DEFAULT 'submitted',
  agent_id INT UNSIGNED NULL,
  assigned_admin_id INT UNSIGNED NULL,
  notes TEXT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  KEY idx_app_status (status),
  CONSTRAINT fk_app_admin FOREIGN KEY (assigned_admin_id) REFERENCES admin_users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE leads (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  phone VARCHAR(20) NOT NULL,
  interest VARCHAR(120) NULL,
  message TEXT NULL,
  source VARCHAR(80) NULL DEFAULT 'contact_form',
  plan_ref VARCHAR(80) NULL,
  status ENUM('new','contacted','following','closed_won','closed_lost') NOT NULL DEFAULT 'new',
  assigned_agent_id INT UNSIGNED NULL,
  notes TEXT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  KEY idx_leads_status (status),
  CONSTRAINT fk_leads_agent FOREIGN KEY (assigned_agent_id) REFERENCES agents(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE campaigns (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  description TEXT NULL,
  start_date DATE NULL,
  end_date DATE NULL,
  status ENUM('draft','active','ended') NOT NULL DEFAULT 'draft',
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE games (
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

CREATE TABLE prizes (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  game_id INT UNSIGNED NULL,
  campaign_id INT UNSIGNED NULL,
  name VARCHAR(160) NOT NULL,
  short_name VARCHAR(40) NOT NULL,
  detail TEXT NULL,
  logo_path VARCHAR(255) NULL,
  color VARCHAR(20) NULL,
  prize_type ENUM('voucher','insurance','physical','other') NOT NULL DEFAULT 'voucher',
  value_amount DECIMAL(12,2) NULL,
  weight INT UNSIGNED NOT NULL DEFAULT 10,
  stock INT NULL,
  wheel_enabled TINYINT(1) NOT NULL DEFAULT 1,
  status ENUM('active','inactive','out_of_stock') NOT NULL DEFAULT 'active',
  sort_order SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  KEY idx_prizes_campaign (campaign_id),
  KEY idx_prizes_game (game_id),
  KEY idx_prizes_status (status),
  CONSTRAINT fk_prizes_campaign FOREIGN KEY (campaign_id) REFERENCES campaigns(id) ON DELETE SET NULL,
  CONSTRAINT fk_prizes_game FOREIGN KEY (game_id) REFERENCES games(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE spin_logs (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  member_id INT UNSIGNED NOT NULL,
  game_id INT UNSIGNED NULL,
  prize_id INT UNSIGNED NOT NULL,
  campaign_id INT UNSIGNED NULL,
  ip_address VARCHAR(45) NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY idx_spin_member (member_id),
  KEY idx_spin_game (game_id),
  KEY idx_spin_prize (prize_id),
  KEY idx_spin_created (created_at),
  CONSTRAINT fk_spin_member FOREIGN KEY (member_id) REFERENCES members(id) ON DELETE CASCADE,
  CONSTRAINT fk_spin_game FOREIGN KEY (game_id) REFERENCES games(id) ON DELETE RESTRICT,
  CONSTRAINT fk_spin_prize FOREIGN KEY (prize_id) REFERENCES prizes(id) ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE reward_claims (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  member_id INT UNSIGNED NOT NULL,
  prize_id INT UNSIGNED NOT NULL,
  spin_log_id INT UNSIGNED NULL,
  status ENUM('won','pending_verify','approved','shipping','sent','redeemed','rejected','expired') NOT NULL DEFAULT 'won',
  selected TINYINT(1) NOT NULL DEFAULT 0,
  delivery_method VARCHAR(30) NULL,
  insurance_interest VARCHAR(120) NULL,
  recipient_name VARCHAR(120) NULL,
  recipient_phone VARCHAR(20) NULL,
  address_line VARCHAR(255) NULL,
  subdistrict VARCHAR(120) NULL,
  district VARCHAR(120) NULL,
  province VARCHAR(120) NULL,
  postal_code VARCHAR(10) NULL,
  contact_line VARCHAR(120) NULL,
  contact_note VARCHAR(255) NULL,
  claimed_at DATETIME NULL,
  consent_at DATETIME NULL,
  notes TEXT NULL,
  approved_at DATETIME NULL,
  sent_at DATETIME NULL,
  redeemed_at DATETIME NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  KEY idx_claim_member (member_id),
  KEY idx_claim_status (status),
  CONSTRAINT fk_claim_member FOREIGN KEY (member_id) REFERENCES members(id) ON DELETE CASCADE,
  CONSTRAINT fk_claim_prize FOREIGN KEY (prize_id) REFERENCES prizes(id) ON DELETE RESTRICT,
  CONSTRAINT fk_claim_spin FOREIGN KEY (spin_log_id) REFERENCES spin_logs(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE article_categories (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  slug VARCHAR(40) NOT NULL UNIQUE,
  title VARCHAR(120) NOT NULL,
  tagline VARCHAR(255) NULL,
  icon VARCHAR(40) NULL,
  sort_order TINYINT UNSIGNED NOT NULL DEFAULT 0,
  status ENUM('active','inactive') NOT NULL DEFAULT 'active'
) ENGINE=InnoDB;

CREATE TABLE articles (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  category_id INT UNSIGNED NULL,
  slug VARCHAR(80) NOT NULL UNIQUE,
  title VARCHAR(200) NOT NULL,
  excerpt TEXT NULL,
  body_html MEDIUMTEXT NULL,
  body_json MEDIUMTEXT NULL,
  image_path VARCHAR(255) NULL,
  read_time VARCHAR(20) NULL,
  featured TINYINT(1) NOT NULL DEFAULT 0,
  status ENUM('draft','published','archived') NOT NULL DEFAULT 'draft',
  published_at DATETIME NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  KEY idx_articles_cat (category_id),
  KEY idx_articles_status (status),
  CONSTRAINT fk_articles_cat FOREIGN KEY (category_id) REFERENCES article_categories(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE insurance_categories (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  slug VARCHAR(40) NOT NULL UNIQUE,
  title VARCHAR(120) NOT NULL,
  tagline VARCHAR(255) NULL,
  icon VARCHAR(40) NULL DEFAULT 'shield',
  detail_json MEDIUMTEXT NULL,
  sort_order TINYINT UNSIGNED NOT NULL DEFAULT 0,
  status ENUM('active','inactive') NOT NULL DEFAULT 'active',
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE insurance_plans (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  category_id INT UNSIGNED NOT NULL,
  slug VARCHAR(80) NOT NULL UNIQUE,
  name VARCHAR(200) NOT NULL,
  description TEXT NULL,
  image_path VARCHAR(255) NULL,
  features_json MEDIUMTEXT NULL,
  detail_json MEDIUMTEXT NULL,
  featured TINYINT(1) NOT NULL DEFAULT 0,
  sort_order TINYINT UNSIGNED NOT NULL DEFAULT 0,
  status ENUM('active','inactive') NOT NULL DEFAULT 'active',
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  KEY idx_ins_plan_cat (category_id),
  KEY idx_ins_plan_status (status),
  CONSTRAINT fk_ins_plan_cat FOREIGN KEY (category_id) REFERENCES insurance_categories(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE site_content (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  content_key VARCHAR(60) NOT NULL UNIQUE,
  title VARCHAR(120) NULL,
  body_json MEDIUMTEXT NOT NULL,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE settings (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  setting_key VARCHAR(80) NOT NULL UNIQUE,
  setting_value TEXT NULL,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE member_game_quotas (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  member_id INT UNSIGNED NOT NULL,
  game_id INT UNSIGNED NOT NULL,
  plays_remaining TINYINT UNSIGNED NOT NULL DEFAULT 0,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY uk_member_game (member_id, game_id),
  KEY idx_mgq_member (member_id),
  CONSTRAINT fk_mgq_member FOREIGN KEY (member_id) REFERENCES members(id) ON DELETE CASCADE,
  CONSTRAINT fk_mgq_game FOREIGN KEY (game_id) REFERENCES games(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE activity_logs (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  admin_id INT UNSIGNED NULL,
  action VARCHAR(80) NOT NULL,
  entity_type VARCHAR(40) NULL,
  entity_id INT UNSIGNED NULL,
  detail TEXT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY idx_log_admin (admin_id)
) ENGINE=InnoDB;
