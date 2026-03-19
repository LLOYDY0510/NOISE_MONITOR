START TRANSACTION;
INSERT INTO users (name, email, password, role, status) VALUES
('Admin User', 'admin@library.com', MD5('admin123'), 'Administrator', 'active'),
('Manager User', 'manager@library.com', MD5('manager123'), 'Library Manager', 'active'),
('Staff User', 'staff@library.com', MD5('staff123'), 'Library Staff', 'active');
INSERT INTO noise_events (user_id, zone, noise_level, alert_level) VALUES
(1, 'Reference', 35, 'Low'),
(1, 'Corner 1', 60, 'Average'),
(1, 'Center', 85, 'High'),
(1, 'Corner 2', 70, 'Average');

INSERT INTO zone_status (zone_name, latest_noise) VALUES
('Reference', 0),
('Corner 1', 0),
('Center', 0),
('Corner 2', 0);
 