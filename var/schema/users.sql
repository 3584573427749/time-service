CREATE TABLE `users`
(
    `id`         CHAR(36) NOT NULL COLLATE 'utf8mb4_unicode_ci',
    `first_name` VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8mb4_unicode_ci',
    `last_name`  VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8mb4_unicode_ci',
    `active`     TINYINT(1) NULL DEFAULT '1',
    `created_at` DATETIME NULL DEFAULT NULL,
    `updated_at` DATETIME NULL DEFAULT NULL,
    PRIMARY KEY (`id`) ,
    INDEX        `active` (`active`)
) COLLATE='utf8mb4_unicode_ci'
ENGINE=InnoDB
;
