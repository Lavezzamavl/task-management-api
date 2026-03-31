-- ============================================================
-- Task Management API — MySQL Dump
-- Author: Nathanael Kamau
-- Generated: 2026-04-01
-- Compatible with: MySQL 8.0+
-- ============================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS `tasks`;

CREATE TABLE `tasks` (
  `id`         BIGINT UNSIGNED  NOT NULL AUTO_INCREMENT,
  `title`      VARCHAR(255)     NOT NULL,
  `due_date`   DATE             NOT NULL,
  `priority`   ENUM('low','medium','high') NOT NULL,
  `status`     ENUM('pending','in_progress','done') NOT NULL DEFAULT 'pending',
  `created_at` TIMESTAMP        NULL DEFAULT NULL,
  `updated_at` TIMESTAMP        NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tasks_title_due_date_unique` (`title`, `due_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Sample data
INSERT INTO `tasks` (`title`, `due_date`, `priority`, `status`, `created_at`, `updated_at`) VALUES
('Design system architecture', '2026-04-05', 'high',   'pending',     NOW(), NOW()),
('Write unit tests',           '2026-04-06', 'high',   'in_progress', NOW(), NOW()),
('Update documentation',       '2026-04-07', 'medium', 'pending',     NOW(), NOW()),
('Code review for PR #42',     '2026-04-05', 'medium', 'done',        NOW(), NOW()),
('Fix typos in README',        '2026-04-10', 'low',    'pending',     NOW(), NOW()),
('Set up CI/CD pipeline',      '2026-04-06', 'high',   'done',        NOW(), NOW());

SET FOREIGN_KEY_CHECKS = 1;
