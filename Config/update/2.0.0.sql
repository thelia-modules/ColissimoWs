SET FOREIGN_KEY_CHECKS = 0;
-- ---------------------------------------------------------------------
-- colissimows_area_freeshipping
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `colissimows_area_freeshipping`;

CREATE TABLE `colissimows_area_freeshipping`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `area_id` INTEGER NOT NULL,
    `cart_amount` DECIMAL(18,2) DEFAULT 0.00 NULL,
    PRIMARY KEY (`id`),
    INDEX `FI_colissimows_area_freeshipping_area_id` (`area_id`),
    CONSTRAINT `fk_colissimows_area_freeshipping_area_id`
        FOREIGN KEY (`area_id`)
        REFERENCES `area` (`id`)
        ON UPDATE RESTRICT
        ON DELETE RESTRICT
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- colissimows_freeshipping
-- ---------------------------------------------------------------------

ALTER TABLE `colissimows_freeshipping` ADD `freeshipping_from` DECIMAL(18,2) DEFAULT NULL;

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;