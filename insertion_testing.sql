START TRANSACTION;
INSERT IGNORE INTO users (id, name, email, password, role, status, last_login) VALUES
('U-001', 'Johnlloyd P.',     'admin@library.edu', 'admin123', 'Administrator',   'active', NULL),
('U-002', 'James Anticamars', 'james@library.edu', 'james123', 'Library Manager', 'active', NULL),
('U-003', 'Dimavier',         'staff@library.edu', 'staff123', 'Library Staff',   'active', NULL);

INSERT IGNORE INTO zones (id, name, floor, capacity, occupied, level, warn_threshold, crit_threshold, sensor, battery, description) VALUES
('Z-001', 'Reading AREA',  '1F', 80, 45, 28.00, 40, 60, 'SNS-001', 85, 'Main reading area on the ground floor.'),
('Z-002', 'Study AREA',    '2F', 20, 18, 52.00, 40, 60, 'SNS-002', 72, 'Private study room for small groups.'),
('Z-003', 'Computer AREA', '1F', 40, 12, 18.00, 35, 55, 'SNS-003', 91, 'Computer laboratory section.');