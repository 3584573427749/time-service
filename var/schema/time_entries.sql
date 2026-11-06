CREATE TABLE `time_entries`
(
    `id`            CHAR(36) NOT NULL COLLATE 'utf8mb4_unicode_ci',
    `user_id`       CHAR(36) NULL DEFAULT NULL COLLATE 'utf8mb4_unicode_ci',
    `date`          DATE NULL DEFAULT NULL,
    `duration`      TIME NULL DEFAULT NULL,
    `activity_role` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8mb4_unicode_ci',
    `group_id`      CHAR(36) NULL DEFAULT NULL COLLATE 'utf8mb4_unicode_ci',
    `comment`       TEXT NULL DEFAULT NULL COLLATE 'utf8mb4_unicode_ci',
    `approved_by`   CHAR(36) NULL DEFAULT NULL COLLATE 'utf8mb4_unicode_ci',
    `approved_at`   DATETIME NULL DEFAULT NULL,
    `exported_by`   CHAR(36) NULL DEFAULT NULL COLLATE 'utf8mb4_unicode_ci',
    `exported_at`   DATETIME NULL DEFAULT NULL,
    `created_at`    DATETIME NULL DEFAULT NULL,
    `updated_at`    DATETIME NULL DEFAULT NULL,
    PRIMARY KEY (`id`) ,
    INDEX           `user_id` (`user_id`) ,
    INDEX           `date` (`date`) ,
    INDEX           `group_id` (`group_id`) ,
    INDEX           `approved_at` (`approved_at`) ,
    INDEX           `exported_at` (`exported_at`) 
) COLLATE='utf8mb4_unicode_ci'
ENGINE=InnoDB
;