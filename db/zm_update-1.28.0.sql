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
"SELECT 'Column V4LCapturesPerFrame exists in Monitors'",
"ALTER TABLE `Monitors` ADD `V4LCapturesPerFrame` tinyint(3) unsigned not null default 0 AFTER `V4LMultiBuffer`"
));

PREPARE stmt FROM @s;
EXECUTE stmt;

SET @s = (SELECT IF(
	(SELECT COUNT(*)
	FROM INFORMATION_SCHEMA.COLUMNS
	WHERE table_name = 'Monitors'
	AND table_schema = DATABASE()
	AND column_name = 'ServerHost'
	) > 0,
"SELECT 'Column ServerHost exists in Monitors'",
"ALTER TABLE `Monitors` ADD `ServerHost` varchar(64) AFTER `Name`"
));

PREPARE stmt FROM @s;
EXECUTE stmt;

SET @s = (SELECT IF(
	(SELECT COUNT(*)
	FROM INFORMATION_SCHEMA.COLUMNS
	WHERE table_name = 'Monitors'
	AND table_schema = DATABASE()
	AND column_name = 'JPGPath'
	) > 0,
"SELECT 'Column JPGPath exists in Monitors'",
"ALTER TABLE `Monitors` ADD `JPGPath` varchar(255) NOT NULL default '' AFTER `SubPath`"
));

PREPARE stmt FROM @s;
EXECUTE stmt;

SET @s = (SELECT IF(
	(SELECT COUNT(*)
	FROM INFORMATION_SCHEMA.COLUMNS
	WHERE table_name = 'Monitors'
	AND table_schema = DATABASE()
	AND column_name = 'MJPGPath'
	) > 0,
"SELECT 'Column MJPGPath exists in Monitors'",
"ALTER TABLE `Monitors` ADD `MJPGPath` varchar(255) NOT NULL default '' AFTER `JPGPath`"
));

PREPARE stmt FROM @s;
EXECUTE stmt;


SET @s = (SELECT IF(
	(SELECT COUNT(*)
	FROM INFORMATION_SCHEMA.COLUMNS
	WHERE table_name = 'Monitors'
	AND table_schema = DATABASE()
	AND column_name = 'SaveJPEGs'
	) > 0,
"SELECT 'Column SaveJPEGs exists in Monitors'",
"ALTER TABLE `Monitors` ADD `SaveJPEGs` TINYINT NOT NULL DEFAULT '3' AFTER `Deinterlacing`"
));

PREPARE stmt FROM @s;
EXECUTE stmt;

SET @s = (SELECT IF(
	(SELECT COUNT(*)
	FROM INFORMATION_SCHEMA.COLUMNS
	WHERE table_name = 'Monitors'
	AND table_schema = DATABASE()
	AND column_name = 'VideoWriter'
	) > 0,
"SELECT 'Column VideoWriter exists in Monitors'",
"ALTER TABLE `Monitors` ADD `VideoWriter` TINYINT NOT NULL DEFAULT '0' AFTER `SaveJPEGs`"
));

PREPARE stmt FROM @s;
EXECUTE stmt;

SET @s = (SELECT IF(
	(SELECT COUNT(*)
	FROM INFORMATION_SCHEMA.COLUMNS
	WHERE table_name = 'Monitors'
	AND table_schema = DATABASE()
	AND column_name = 'EncoderParameters'
	) > 0,
"SELECT 'Column EncoderParameters exists in Monitors'",
"ALTER TABLE `Monitors` ADD `EncoderParameters` TEXT NOT NULL AFTER `VideoWriter`"
));

PREPARE stmt FROM @s;
EXECUTE stmt;

SET @s = (SELECT IF(
	(SELECT COUNT(*)
	FROM INFORMATION_SCHEMA.COLUMNS
	WHERE table_name = 'Events'
	AND table_schema = DATABASE()
	AND column_name = 'DefaultVideo'
	) > 0,
"SELECT 'Column DefaultVideo exists in Events'",
"ALTER TABLE `Events` ADD `DefaultVideo` VARCHAR( 64 ) NOT NULL AFTER `AlarmFrames`"
));

PREPARE stmt FROM @s;
EXECUTE stmt;
