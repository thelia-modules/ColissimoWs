
# This is a fix for InnoDB in MySQL >= 4.1.x
# It "suspends judgement" for fkey relationships until are tables are set.
SET FOREIGN_KEY_CHECKS = 0;

-- ---------------------------------------------------------------------
-- colissimows_price_slices
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `colissimows_price_slices`;

CREATE TABLE `colissimows_price_slices`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `area_id` INTEGER NOT NULL,
    `max_weight` FLOAT,
    `max_price` FLOAT,
    `shipping` FLOAT NOT NULL,
    PRIMARY KEY (`id`),
    INDEX `FI_colissimows_price_slices_area_id` (`area_id`),
    CONSTRAINT `fk_colissimows_price_slices_area_id`
        FOREIGN KEY (`area_id`)
        REFERENCES `area` (`id`)
        ON UPDATE RESTRICT
        ON DELETE RESTRICT
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- colissimows_freeshipping
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `colissimows_freeshipping`;

CREATE TABLE `colissimows_freeshipping`
(
    `id` INTEGER NOT NULL,
    `active` TINYINT(1) DEFAULT 0,
    `freeshipping_from` DECIMAL(18,2),
    PRIMARY KEY (`id`)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- colissimows_area_freeshipping
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `colissimows_area_freeshipping`;

CREATE TABLE `colissimows_area_freeshipping`
(
    `id` INTEGER NOT NULL,
    `area_id` INTEGER NOT NULL,
    `cart_amount` DECIMAL(18,2) DEFAULT 0.00,
    PRIMARY KEY (`id`),
    INDEX `FI_colissimows_area_freeshipping_area_id` (`area_id`),
    CONSTRAINT `fk_colissimows_area_freeshipping_area_id`
        FOREIGN KEY (`area_id`)
        REFERENCES `area` (`id`)
        ON UPDATE RESTRICT
        ON DELETE RESTRICT
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- colissimows_label
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `colissimows_label`;

CREATE TABLE `colissimows_label`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `order_id` INTEGER NOT NULL,
    `order_ref` VARCHAR(255) NOT NULL,
    `error` TINYINT(1) DEFAULT 0 NOT NULL,
    `error_message` VARCHAR(255) DEFAULT '',
    `tracking_number` VARCHAR(64) DEFAULT '',
    `label_data` LONGTEXT,
    `label_type` VARCHAR(4),
    `weight` FLOAT NOT NULL,
    `signed` TINYINT(1) DEFAULT 0,
    `with_customs_invoice` TINYINT(1) DEFAULT 0 NOT NULL,
    `created_at` DATETIME,
    `updated_at` DATETIME,
    PRIMARY KEY (`id`),
    INDEX `FI_colissimows_label_order` (`order_id`),
    CONSTRAINT `fk_colissimows_label_order`
        FOREIGN KEY (`order_id`)
        REFERENCES `order` (`id`)
        ON UPDATE RESTRICT
        ON DELETE CASCADE
) ENGINE=InnoDB;

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
