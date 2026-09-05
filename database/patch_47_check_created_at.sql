-- Patch 47 diagnostic: find created_at columns in the verification tables that are NOT NULL and have no DEFAULT
-- Run against the database that the application uses (DB_NAME configured, expected 'original_east')

SELECT TABLE_SCHEMA, TABLE_NAME, COLUMN_NAME, COLUMN_TYPE, IS_NULLABLE, COLUMN_DEFAULT
FROM information_schema.COLUMNS
WHERE TABLE_SCHEMA = 'original_east'
  AND COLUMN_NAME = 'created_at'
  AND TABLE_NAME IN (
    'users','vehicles','bookings','repairs','repair_parts','maintenance_records','products','product_compatibility','vehicle_models','vehicle_brands','services'
  )
ORDER BY TABLE_NAME;

-- To list only problematic columns (NOT NULL and no DEFAULT):

SELECT TABLE_SCHEMA, TABLE_NAME, COLUMN_NAME, COLUMN_TYPE, IS_NULLABLE, COLUMN_DEFAULT
FROM information_schema.COLUMNS
WHERE TABLE_SCHEMA = 'original_east'
  AND COLUMN_NAME = 'created_at'
  AND TABLE_NAME IN (
    'users','vehicles','bookings','repairs','repair_parts','maintenance_records','products','product_compatibility','vehicle_models','vehicle_brands','services'
  )
  AND (COLUMN_DEFAULT IS NULL OR COLUMN_DEFAULT = '')
  AND IS_NULLABLE = 'NO'
ORDER BY TABLE_NAME;
