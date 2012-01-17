--
-- This updates a 1.25.0 database to 1.25.1
--

--
-- Add protocol column for monitors
--
alter table Monitors add column `Colours` tinyint(3) unsigned NOT NULL default '1' after `Height`;

insert into Config set Id = 183, Name = 'ZM_REDUCTION_FPS_RESET_TIME', Value = '0', Type = 'integer', DefaultValue = '0', Hint = 'integer', Pattern = '(?-xism:^(\d+)$)', Format = ' $1 ', Prompt = 'After how many seconds should the maxfps of a stream go back to normal after it has been throttled', Help = 'When a stream cannot send frames as fast as requested (generally due to too slow of a connection) it will throttle it back, unfortunately if the cause of this was only temporary the FPS will never pickup again.  By setting this to a value greater than 0 it will automatically go back to the original FPS after X seconds from when the last throttle took place.', Category = 'image', Readonly = '0', Requires = '';

--
-- These are optional, but we might as well do it now
--
optimize table Frames;
optimize table Events;
optimize table Filters;
optimize table Zones;
optimize table Monitors;
optimize table Stats;
