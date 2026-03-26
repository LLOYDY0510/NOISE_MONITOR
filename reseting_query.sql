-- ============================================================
--  LibraryQuiet – resetingquery.sql
--  Resets all data back to default values
--  Run this in phpMyAdmin or terminal to do a full reset
-- ============================================================

USE libraryquiet;

-- ── DISABLE FOREIGN KEY CHECKS ───────────────────────────────
SET FOREIGN_KEY_CHECKS = 0;

-- ── CLEAR ALL TABLES ─────────────────────────────────────────
TRUNCATE TABLE alert_messages;
TRUNCATE TABLE alerts;
TRUNCATE TABLE sensor_overrides;
TRUNCATE TABLE reports;
TRUNCATE TABLE users;
TRUNCATE TABLE zones;

-- ── RE-ENABLE FOREIGN KEY CHECKS ─────────────────────────────
SET FOREIGN_KEY_CHECKS = 1;

-- ── INSERT DEFAULT USERS ─────────────────────────────────────
INSERT INTO users (id, name, email, password, role, status, last_login) VALUES
('U-001', 'Johnlloyd P.',     'admin@library.edu', 'admin123', 'Administrator',   'active', NULL),
('U-002', 'James Anticamars', 'james@library.edu', 'james123', 'Library Manager', 'active', NULL),
('U-003', 'Dimavier',         'staff@library.edu', 'staff123', 'Library Staff',   'active', NULL);

-- ── INSERT DEFAULT ZONES ─────────────────────────────────────
INSERT INTO zones (id, name, floor, capacity, occupied, level, warn_threshold, crit_threshold, sensor, battery, manual_override, description) VALUES
('Z-001', 'Reading AREA',  '1F', 80, 45, 28.00, 40, 60, 'SNS-001', 85, 0, 'Main reading area on the ground floor.'),
('Z-002', 'Study AREA',    '2F', 20, 18, 52.00, 40, 60, 'SNS-002', 72, 0, 'Private study room for small groups.'),
('Z-003', 'Computer AREA', '1F', 40, 12, 18.00, 35, 55, 'SNS-003', 91, 0, 'Computer laboratory section.');

-- ── VERIFY ───────────────────────────────────────────────────
SELECT 'USERS' AS table_name, COUNT(*) AS total FROM users
UNION ALL
SELECT 'ZONES',   COUNT(*) FROM zones
UNION ALL
SELECT 'ALERTS',  COUNT(*) FROM alerts
UNION ALL
SELECT 'REPORTS', COUNT(*) FROM reports;