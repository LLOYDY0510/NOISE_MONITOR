
USE `libraryquiet`;

START TRANSACTION;

SET foreign_key_checks = 1;

TRUNCATE TABLE `users`;
TRUNCATE TABLE `noise_events`;
TRUNCATE TABLE `zones_status`;

SET foreign_key_checks = 1;

COMMIT;