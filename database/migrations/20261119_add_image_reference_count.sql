-- Run once against the lost_and_found database before deploying the PHP changes.
ALTER TABLE images
    ADD COLUMN image_count INT UNSIGNED NOT NULL DEFAULT 1;

-- Bring existing rows in line with the number of reports that reference them.
UPDATE images AS i
LEFT JOIN (
    SELECT image_id, COUNT(*) AS reference_count
    FROM reports
    WHERE image_id IS NOT NULL
    GROUP BY image_id
) AS r ON r.image_id = i.id
SET i.image_count = COALESCE(r.reference_count, 0);
