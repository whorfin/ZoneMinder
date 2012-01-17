--
-- This updates a 1.25.0 database to 1.25.1
--

--
-- Add protocol column for monitors
--
alter table Monitors add column `Colours` tinyint(3) unsigned NOT NULL default '1' after `Height`;

--
-- These are optional, but we might as well do it now
--
optimize table Frames;
optimize table Events;
optimize table Filters;
optimize table Zones;
optimize table Monitors;
optimize table Stats;
