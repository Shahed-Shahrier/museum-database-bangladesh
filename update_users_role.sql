ALTER TABLE `users` ADD COLUMN `Museum_ID` INT(11) NULL;
ALTER TABLE `users` ADD CONSTRAINT `fk_users_museum` FOREIGN KEY (`Museum_ID`) REFERENCES `museum` (`Museum_ID`) ON DELETE SET NULL ON UPDATE CASCADE;
ALTER TABLE `users` MODIFY COLUMN `Role` ENUM('admin', 'guest', 'museum_admin') NOT NULL DEFAULT 'guest';
