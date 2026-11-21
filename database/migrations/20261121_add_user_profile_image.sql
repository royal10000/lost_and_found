-- Run once against the lost_and_found database. Safe to run again if the column exists.
SET @profile_image_column_exists = (
    SELECT COUNT(*)
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'users'
      AND COLUMN_NAME = 'profile_image'
);

SET @profile_image_ddl = IF(
    @profile_image_column_exists = 0,
    'ALTER TABLE users ADD COLUMN profile_image VARCHAR(255) NULL',
    'SELECT 1'
);

PREPARE profile_image_stmt FROM @profile_image_ddl;
EXECUTE profile_image_stmt;
DEALLOCATE PREPARE profile_image_stmt;
