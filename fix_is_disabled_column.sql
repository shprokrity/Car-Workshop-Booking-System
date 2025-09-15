-- Fix the is_disabled column type and default value
ALTER TABLE users MODIFY COLUMN is_disabled TINYINT(1) NOT NULL DEFAULT 0;

-- Update any NULL values to 0
UPDATE users SET is_disabled = 0 WHERE is_disabled IS NULL;