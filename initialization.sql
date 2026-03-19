CREATE DATABASE libraryquiet;
USE libraryquiet;

CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100),
  email VARCHAR(100) UNIQUE,
  password VARCHAR(255),
  role ENUM('Administrator','Library Manager','Library Staff'),
  status ENUM('active','inactive') DEFAULT 'active'
);

CREATE TABLE IF NOT EXISTS noise_events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,            
    zone VARCHAR(50) NOT NULL,                
    noise_level INT NOT NULL,                   
    alert_level ENUM('Low','Average','High') NOT NULL,
    recorded_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS zone_status (
    id INT AUTO_INCREMENT PRIMARY KEY,
    zone_name VARCHAR(50) NOT NULL,   
    latest_noise INT NOT NULL,     
    last_updated DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);