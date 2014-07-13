--
-- This updates a 1.27.1 database to 1.27.2
--

--
-- Add V4LMultiBuffer and V4LCapturesPerFrame to Monitor
--

-- Add extend alarm frame count to zone definition and Presets
SET @s = (SELECT IF(
	(SELECT COUNT(*)
	FROM INFORMATION_SCHEMA.COLUMNS
	WHERE table_name = 'Monitors'
	AND table_schema = DATABASE()
	AND column_name = 'V4LMultiBuffer'
	) > 0,
"SELECT 'Column V4LMultiBuffer exists in Monitors'",
"ALTER TABLE `Monitors` ADD `V4LMultiBuffer` tinyint(1) unsigned not null default 0 AFTER `Format`"
));

PREPARE stmt FROM @s;
EXECUTE stmt;

SET @s = (SELECT IF(
	(SELECT COUNT(*)
	FROM INFORMATION_SCHEMA.COLUMNS
	WHERE table_name = 'Monitors'
	AND table_schema = DATABASE()
	AND column_name = 'V4LCapturesPerFrame'
	) > 0,
"SELECT 'Column CapturesPerFrame exists in Monitors'",
"ALTER TABLE `Monitors` ADD `V4LCapturesPerFrame` tinyint(3) unsigned not null default 0 AFTER `V4LMultiBuffer`"
));

PREPARE stmt FROM @s;
EXECUTE stmt;

