SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL';

CREATE SCHEMA IF NOT EXISTS `twx` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ;

USE `twx`;

CREATE  TABLE IF NOT EXISTS `twx`.`keys` (
  `id` VARCHAR(32) NOT NULL ,
  `status` TINYINT(4) NULL DEFAULT NULL ,
  `valid_thru` DATE NULL DEFAULT NULL ,
  `owner_name` TEXT NULL DEFAULT NULL ,
  `last_login` DATETIME NULL DEFAULT NULL ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `id_UNIQUE` (`id` ASC) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE  TABLE IF NOT EXISTS `twx`.`tasks` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `key_id` VARCHAR(32) NOT NULL ,
  `mask` VARCHAR(45) NOT NULL ,
  `task_name` VARCHAR(255) NULL DEFAULT 'noname' ,
  `task_data` TEXT NULL DEFAULT NULL ,
  `task_content` TEXT NULL DEFAULT NULL ,
  `task_cron_intval` SMALLINT(6) NULL DEFAULT 0 ,
  `task_progress` TINYINT(4) NULL DEFAULT 0 ,
  `task_status` ENUM('start', 'stop') NULL DEFAULT 'stop' ,
  PRIMARY KEY (`id`, `key_id`, `mask`) ,
  UNIQUE INDEX `id_UNIQUE` (`id` ASC) ,
  UNIQUE INDEX `mask_UNIQUE` (`mask` ASC) ,
  INDEX `key_id` (`key_id` ASC) ,
  INDEX `keys-tasks` (`key_id` ASC) ,
  CONSTRAINT `keys-tasks`
    FOREIGN KEY (`key_id` )
    REFERENCES `twx`.`keys` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci
ROW_FORMAT = DEFAULT;

CREATE  TABLE IF NOT EXISTS `twx`.`logs` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `mask` VARCHAR(45) NOT NULL ,
  `record` TEXT NULL DEFAULT NULL ,
  `date` DATETIME NULL DEFAULT NULL ,
  `source` ENUM('script', 'cron') NULL DEFAULT NULL ,
  `loglevel` SMALLINT(6) NULL DEFAULT NULL ,
  PRIMARY KEY (`id`, `mask`) ,
  INDEX `tasks-logs` (`mask` ASC) ,
  CONSTRAINT `tasks-logs`
    FOREIGN KEY (`mask` )
    REFERENCES `twx`.`tasks` (`mask` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE  TABLE IF NOT EXISTS `twx`.`threads` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `mask` VARCHAR(45) NOT NULL ,
  `cur_pos` INT(5) NULL DEFAULT NULL ,
  `range` VARCHAR(45) NULL DEFAULT NULL ,
  PRIMARY KEY (`id`, `mask`) ,
  UNIQUE INDEX `id_UNIQUE` (`id` ASC) ,
  INDEX `tasks-threads` (`mask` ASC) ,
  CONSTRAINT `tasks-threads`
    FOREIGN KEY (`mask` )
    REFERENCES `twx`.`tasks` (`mask` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE  TABLE IF NOT EXISTS `twx`.`accounts` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `pair` VARCHAR(255) NULL DEFAULT NULL ,
  `service` VARCHAR(255) NULL DEFAULT NULL ,
  `error` VARCHAR(255) NULL DEFAULT NULL ,
  `key_id` VARCHAR(32) NOT NULL ,
  PRIMARY KEY (`id`, `key_id`) ,
  INDEX `keys-accounts` (`key_id` ASC) ,
  CONSTRAINT `keys-accounts`
    FOREIGN KEY (`key_id` )
    REFERENCES `twx`.`keys` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE  TABLE IF NOT EXISTS `twx`.`user_config` (
  `id` INT(8) UNSIGNED ZEROFILL NOT NULL AUTO_INCREMENT ,
  `parameter` VARCHAR(255) NULL DEFAULT NULL ,
  `value` VARCHAR(255) NULL DEFAULT NULL ,
  `key_id` VARCHAR(32) NOT NULL ,
  PRIMARY KEY (`id`, `key_id`) ,
  INDEX `keys-configs` (`key_id` ASC) ,
  UNIQUE INDEX `id_UNIQUE` (`id` ASC) ,
  CONSTRAINT `keys-configs`
    FOREIGN KEY (`key_id` )
    REFERENCES `twx`.`keys` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE  TABLE IF NOT EXISTS `twx`.`proxy` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `pair` VARCHAR(45) NULL DEFAULT NULL ,
  `error` VARCHAR(45) NULL DEFAULT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE  TABLE IF NOT EXISTS `twx`.`drips` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `mask` VARCHAR(45) NOT NULL ,
  `content` TEXT NULL DEFAULT NULL ,
  `cur_pos` INT(8) NULL DEFAULT 0 ,
  `per_request` INT(4) NULL DEFAULT 50 ,
  PRIMARY KEY (`id`, `mask`) ,
  INDEX `tasks-drips` (`mask` ASC) ,
  CONSTRAINT `tasks-drips`
    FOREIGN KEY (`mask` )
    REFERENCES `twx`.`tasks` (`mask` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;