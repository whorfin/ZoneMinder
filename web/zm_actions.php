<?php
//
// ZoneMinder web action file, $Date$, $Revision$
// Copyright (C) 2003  Philip Coombes
//
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with this program; if not, write to the Free Software
// Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
//

if ( isset($action) )
{
	//phpinfo( INFO_VARIABLES );
	if ( $action == "login" && $username && $password )
	{
		userLogin( $username, $password );
	}
	elseif ( $action == "logout" )
	{
		userLogout();
		$refresh_parent = true;
		$view = 'none';
	}
	elseif ( $action == "bandwidth" && $new_bandwidth )
	{
		$bandwidth = $new_bandwidth;
		setcookie( "bandwidth", $new_bandwidth, time()+3600*24*30*12*10 );
		$refresh_parent = true;
		$view = 'none';
	}
	if ( canEdit( 'Events' ) )
	{
		if ( $action == "rename" && $event_name && $eid )
		{
			simpleQuery( "update Events set Name = '$event_name' where Id = '$eid'" );
		}
		elseif ( $action == "archive" && $eid )
		{
			simpleQuery( "update Events set Archived = 1 where Id = '$eid'" );
		}
		elseif ( $action == "unarchive" && $eid )
		{
			simpleQuery( "update Events set Archived = 0 where Id = '$eid'" );
		}
		elseif ( $action == "filter" )
		{
			if ( $filter_name || $new_filter_name )
			{
				if ( $new_filter_name )
					$filter_name = $new_filter_name;
				$filter_query = array();
				$filter_query['trms'] = $trms;
				for ( $i = 1; $i <= $trms; $i++ )
				{
					$conjunction_name = "cnj$i";
					$obracket_name = "obr$i";
					$cbracket_name = "cbr$i";
					$attr_name = "attr$i";
					$op_name = "op$i";
					$value_name = "val$i";
					if ( $i > 1 )
					{
						$filter_query[$conjunction_name] = $$conjunction_name;
					}
					$filter_query[$obracket_name] = $$obracket_name;
					$filter_query[$cbracket_name] = $$cbracket_name;
					$filter_query[$attr_name] = $$attr_name;
					$filter_query[$op_name] = $$op_name;
					$filter_query[$value_name] = $$value_name;
				}
				$filter_parms = array();
				while( list( $key, $value ) = each( $filter_query ) )
				{
					$filter_parms[] = "$key=$value";
				}
				$filter_parms[] = "sort_field=$sort_field";
				$filter_parms[] = "sort_asc=$sort_asc";
				$filter_parms[] = "limit=$limit";
				$filter_query_string = join( '&', $filter_parms );
				simpleQuery( "replace into Filters set Name = '$filter_name', Query = '$filter_query_string', AutoArchive = '$auto_archive', AutoDelete = '$auto_delete', AutoUpload = '$auto_upload', AutoEmail = '$auto_email', AutoMessage = '$auto_message', AutoExecute = '$auto_execute'" );
				$refresh_parent = true;
			}
		}
		elseif ( $action == "delete" )
		{
			if ( !$mark_eids && $mark_eid )
			{
				$mark_eids[] = $mark_eid;
				$refresh_parent = true;
			}
			if ( $mark_eids )
			{
				foreach( $mark_eids as $mark_eid )
				{
					deleteEvent( $mark_eid );
				}
			}
			if ( $fid )
			{
				simpleQuery( "delete from Filters where Name = '$fid'" );
				//$refresh_parent = true;
			}
		}
	}
	if ( canEdit( 'Monitors', $mid ) )
	{
		if ( $action == "function" && isset( $mid ) )
		{
			$sql = "select * from Monitors where Id = '$mid'";
			$result = mysql_query( $sql );
			if ( !$result )
				die( mysql_error() );
			$monitor = mysql_fetch_assoc( $result );

			$old_function = $monitor['Function'];
			if ( $new_function != $old_function )
			{
				simpleQuery( "update Monitors set Function = '$new_function' where Id = '$mid'" );

				$monitor['Function'] = $new_function;
				if ( $cookies ) session_write_close();
				zmcControl( $monitor, true );
				zmaControl( $monitor, true );
				$refresh_parent = true;
			}
		}
		elseif ( $action == "zone" && isset( $mid ) && isset( $zid ) )
		{
			$result = mysql_query( "select * from Monitors where Id = '$mid'" );
			if ( !$result )
				die( mysql_error() );
			$monitor = mysql_fetch_assoc( $result );

			if ( $zid > 0 )
			{
				$result = mysql_query( "select * from Zones where MonitorId = '$mid' and Id = '$zid'" );
				if ( !$result )
					die( mysql_error() );
				$zone = mysql_fetch_assoc( $result );
			}
			else
			{
				$zone = array();
			}

			$types = array();
			$changes = getFormChanges( $zone, $new_zone, $types );

			if ( count( $changes ) )
			{
				if ( $zid > 0 )
				{
					$sql = "update Zones set ".implode( ", ", $changes )." where MonitorId = '$mid' and Id = '$zid'";
				}
				else
				{
					$sql = "insert into Zones set MonitorId = '$mid', ".implode( ", ", $changes );
					$view = 'none';
				}
				//echo "<html>$sql</html>";
				simpleQuery( $sql );
				if ( $cookies ) session_write_close();
				zmaControl( $mid, true );
				$refresh_parent = true;
			}
		}
		elseif ( $action == "monitor" && isset( $mid ) )
		{
			if ( $mid > 0 )
			{
				$result = mysql_query( "select * from Monitors where Id = '$mid'" );
				if ( !$result )
					die( mysql_error() );
				$monitor = mysql_fetch_assoc( $result );

				if ( ZM_OPT_X10 )
				{
					$result = mysql_query( "select * from TriggersX10 where MonitorId = '$mid'" );
					if ( !$result )
						die( mysql_error() );
					$x10_monitor = mysql_fetch_assoc( $result );
				}
			}
			else
			{
				$monitor = array();
				if ( ZM_OPT_X10 )
				{
					$x10_monitor = array();
				}
			}

			// Define a field type for anything that's not simple text equivalent
			$types = array(
				'Triggers' => 'set' 
			);

			$changes = getFormChanges( $monitor, $new_monitor, $types );

			if ( count( $changes ) )
			{
				if ( $mid > 0 )
				{
					simpleQuery( "update Monitors set ".implode( ", ", $changes )." where Id = '$mid'" );
					if ( $changes['Name'] )
					{
						exec( escapeshellcmd( "mv ".EVENTS_PATH."/".$monitor['Name']." ".EVENTS_PATH."/".$new_monitor['Name'] ) );
					}
				}
				elseif ( !$user['MonitorIds'] )
				{
					$sql = "insert into Monitors set ".implode( ", ", $changes );
					$result = mysql_query( $sql );
					if ( !$result )
						die( mysql_error() );
					$mid = mysql_insert_id();
					$sql = "insert into Zones set MonitorId = $mid, Name = 'All', Type = 'Active', Units = 'Percent', LoX = 0, LoY = 0, HiX = 100, HiY = 100, AlarmRGB = 0xff0000, CheckMethod = 'Blobs', MinPixelThreshold = 25, MaxPixelThreshold = 0, MinAlarmPixels = 3, MaxAlarmPixels = 75, FilterX = 3, FilterY = 3, MinFilterPixels = 3, MaxFilterPixels = 75, MinBlobPixels = 2, MaxBlobPixels = 0, MinBlobs = 1, MaxBlobs = 0";
					$result = mysql_query( $sql );
					if ( !$result )
						die( mysql_error() );
					//$view = 'none';
					mkdir( ZM_DIR_EVENTS."/".$mid, 0755 );
					chdir( ZM_DIR_EVENTS );
					symlink( $mid, $new_monitor['Name'] );
					chdir( ".." );
				}
				$restart = true;
			}

			if ( ZM_OPT_X10 )
			{
				$x10_changes = getFormChanges( $x10_monitor, $new_x10_monitor );

				if ( count( $x10_changes ) )
				{
					if ( $x10_monitor && $new_x10_monitor )
					{
						$sql = "update TriggersX10 set ".implode( ", ", $x10_changes )." where MonitorId = '$mid'";
						$result = mysql_query( $sql );
						if ( !$result )
							die( mysql_error() );
					}
					elseif ( !$user['MonitorIds'] )
					{
						if ( !$x10_monitor )
						{
							$sql = "insert into TriggersX10 set MonitorId = '$mid', ".implode( ", ", $x10_changes );
							$result = mysql_query( $sql );
							if ( !$result )
								die( mysql_error() );
						}
						else
						{
							$sql = "delete from TriggersX10 where MonitorId = '$mid'";
							$result = mysql_query( $sql );
							if ( !$result )
								die( mysql_error() );
						}
					}
					$restart = true;
				}
			}

			if ( $restart )
			{
				$result = mysql_query( "select * from Monitors where Id = '$mid'" );
				if ( !$result )
					die( mysql_error() );
				$monitor = mysql_fetch_assoc( $result );
				fixDevices();
				if ( $cookies ) session_write_close();
				zmcControl( $monitor, true );
				zmaControl( $monitor, true );
				//daemonControl( 'restart', 'zmwatch.pl' );
				$refresh_parent = true;
			}
		}
		elseif ( $action == "settings" && isset( $mid ) )
		{
			$zmu_command = getZmuCommand( " -m $mid -B$new_brightness -C$new_contrast -H$new_hue -O$new_colour" );
			$zmu_output = exec( escapeshellcmd( $zmu_command ) );
			list( $brightness, $contrast, $hue, $colour ) = split( ' ', $zmu_output );
			$sql = "update Monitors set Brightness = '$brightness', Contrast = '$contrast', Hue = '$hue', Colour = '$colour' where Id = '$mid'";
			$result = mysql_query( $sql );
				if ( !$result )
					die( mysql_error() );
		}
		elseif ( $action == "delete" )
		{
			if ( $mark_zids )
			{
				$deleted_zid = 0;
				foreach( $mark_zids as $mark_zid )
				{
					$result = mysql_query( "delete from Zones where Id = '$mark_zid'" );
					if ( !$result )
						die( mysql_error() );
					$deleted_zid = 1;
				}
				if ( $deleted_zid )
				{
					if ( $cookies ) session_write_close();
					zmaControl( $mid, true );
					$refresh_parent = true;
				}
			}
			if ( $mark_mids && !$user['MonitorIds'] )
			{
				foreach( $mark_mids as $mark_mid )
				{
					$sql = "select * from Monitors where Id = '$mark_mid'";
					$result = mysql_query( $sql );
					if ( !$result )
						die( mysql_error() );
					if ( !($monitor = mysql_fetch_assoc( $result )) )
					{
						continue;
					}

					$sql = "select Id from Events where MonitorId = '$mark_mid'";
					$result = mysql_query( $sql );
					if ( !$result )
						die( mysql_error() );

					$mark_eids = array();
					while( $row = mysql_fetch_assoc( $result ) )
					{
						$mark_eids[] = $row['Id'];
					}
					foreach( $mark_eids as $mark_eid )
					{
						deleteEvent( $mark_eid );
					}
					unlink( ZM_DIR_EVENTS."/".$monitor['Name'] );
					system( "rm -rf ".ZM_DIR_EVENTS."/".$monitor['Id'] );

					$result = mysql_query( "delete from Zones where MonitorId = '$mark_mid'" );
					if ( !$result )
						die( mysql_error() );
					if ( ZM_OPT_X10 )
					{
						$result = mysql_query( "delete from TriggersX10 where MonitorId = '$mark_mid'" );
						if ( !$result )
							die( mysql_error() );
					}
					$result = mysql_query( "delete from Monitors where Id = '$mark_mid'" );
					if ( !$result )
						die( mysql_error() );

				}
			}
		}
	}
	if ( canView( 'System' ) )
	{
		if ( $action == "group" )
		{
			if ( count($mark_gids) )
			{
				setcookie( "cgroup", $mark_gids[0], time()+3600*24*30*12*10 );
			}
			else
			{
				setcookie( "cgroup", "", time()-3600*24*2 );
			}
			$refresh_parent = true;
		}
	}
	if ( canEdit( 'System' ) )
	{
		if ( $action == "version" && isset($option) )
		{
			switch( $option )
			{
				case 'go' :
				{
					// Ignore this, the caller will open the page itself
					break;
				}
				case 'ignore' :
				{
					$sql = "update Config set Value = '".ZM_DYN_LAST_VERSION."' where Name = 'ZM_DYN_CURR_VERSION'";
					$result = mysql_query( $sql );
					if ( !$result )
						die( mysql_error() );
					break;
				}
				case 'hour' :
				case 'day' :
				case 'week' :
				{
					$next_reminder = time();
					if ( $option == 'hour' )
					{
						$next_reminder += 60*60;
					}
					elseif ( $option == 'day' )
					{
						$next_reminder += 24*60*60;
					}
					elseif ( $option == 'week' )
					{
						$next_reminder += 7*24*60*60;
					}
					$sql = "update Config set Value = '".$next_reminder."' where Name = 'ZM_DYN_NEXT_REMINDER'";
					$result = mysql_query( $sql );
					if ( !$result )
						die( mysql_error() );
					break;
				}
				case 'never' :
				{
					$sql = "update Config set Value = '0' where Name = 'ZM_CHECK_FOR_UPDATES'";
					$result = mysql_query( $sql );
					if ( !$result )
						die( mysql_error() );
					break;
				}
			}
		}
		if ( $action == "options" && isset( $tab ) )
		{
			$config_cat = $config_cats[$tab];
			$changed = false;
			foreach ( $config_cat as $name=>$value )
			{
				if ( $value['Type'] == "boolean" && !$new_config[$name] )
				{
					 $new_config[$name] = 0;
				}
				else
				{
					 $new_config[$name] = preg_replace( "/\r\n/", "\n", stripslashes( $new_config[$name] ) );
				}
				if ( $value['Value'] != $new_config[$name] )
				{
					$sql = "update Config set Value = '".$new_config[$name]."' where Name = '".$name."'";
					//echo $sql;
					$result = mysql_query( $sql );
					if ( !$result )
						die( mysql_error() );
					$changed = true;
				}
			}
			if ( $changed )
			{
				switch( $tab )
				{
					case "system" :
					case "paths" :
					case "config" :
					case "network" :
					case "x10" :
					case "mail" :
					case "ftp" :
						$restart = true;
						break;
					case "web" :
					case "video" :
					case "tools" :
					case "highband" :
					case "medband" :
					case "lowband" :
					case "phoneband" :
						break;
				}
			}
			loadConfig();
		}
		elseif ( $action == "user" && isset( $uid ) )
		{
			if ( $uid > 0 )
			{
				$result = mysql_query( "select * from Users where Id = '$uid'" );
				if ( !$result )
					die( mysql_error() );
				$row = mysql_fetch_assoc( $result );
			}
			else
			{
				$zone = array();
			}

			$changes = array();
			if ( $new_username != $row['Username'] ) $changes[] = "Username = '$new_username'";
			if ( $new_password != $row['Password'] ) $changes[] = "Password = password('$new_password')";
			if ( $new_username != $row['Language'] ) $changes[] = "Language = '$new_language'";
			if ( $new_enabled != $row['Enabled'] ) $changes[] = "Enabled = '$new_enabled'";
			if ( $new_stream != $row['Stream'] ) $changes[] = "Stream = '$new_stream'";
			if ( $new_events != $row['Events'] ) $changes[] = "Events = '$new_events'";
			if ( $new_monitors != $row['Monitors'] ) $changes[] = "Monitors = '$new_monitors'";
			if ( $new_system != $row['System'] ) $changes[] = "System = '$new_system'";
			if ( $new_monitor_ids != $row['MonitorIds'] ) $changes[] = "MonitorIds = '$new_monitor_ids'";

			if ( count( $changes ) )
			{
				if ( $uid > 0 )
				{
					$sql = "update Users set ".implode( ", ", $changes )." where Id = '$uid'";
				}
				else
				{
					$sql = "insert into Users set ".implode( ", ", $changes );
				}
				$result = mysql_query( $sql );
				if ( !$result )
					die( mysql_error() );
				$view = 'none';
				$refresh_parent = true;
				if ( $row['Username'] == $user['Username'] )
				{
					userLogin( $row['Username'], $row['Password'] );
				}
			}
		}
		elseif ( $action == "state" )
		{
			if ( $run_state )
			{
				if ( $cookies ) session_write_close();
				packageControl( $run_state );
				$refresh_parent = true;
			}
		}
		elseif ( $action == "save" )
		{
			if ( $run_state || $new_state )
			{
				$sql = "select Id,Function from Monitors order by Id";
				$result = mysql_query( $sql );
				if ( !$result )
					die( mysql_error() );

				$definitions = array();
				while( $monitor = mysql_fetch_assoc( $result ) )
				{
					$definitions[] = $monitor['Id'].":".$monitor['Function'];
				}
				$definition = join( ',', $definitions );
				if ( $new_state )
					$run_state = $new_state;
				simpleQuery( "replace into States set Name = '$run_state', Definition = '$definition'" );
			}
		}
		elseif ( $action == "groups" )
		{
			if ( $names )
			{
				foreach ( array_keys( $names ) as $id )
				{
					simpleQuery( "update Groups set Name = '".$names[$id]."', MonitorIds = '".$monitor_ids[$id]."' where Id = '$id'" );
				}
			}
			if ( $new_monitor_ids )
			{
				simpleQuery( "insert into Groups set Name = '".$new_name."', MonitorIds = '".$new_monitor_ids."'" );
			}
			$refresh_parent = true;
		}
		elseif ( $action == "delete" )
		{
			if ( $run_state )
			{
				simpleQuery( "delete from States where Name = '$run_state'" );
			}
			if ( $mark_uids )
			{
				foreach( $mark_uids as $mark_uid )
				{
					simpleQuery( "delete from Users where Id = '$mark_uid'" );
				}
				if ( $row['Username'] == $user['Username'] )
				{
					userLogout();
				}
			}
			if ( $mark_gids )
			{
				foreach( $mark_gids as $mark_gid )
				{
					simpleQuery( "delete from Groups where Id = '$mark_gid'" );
					if ( $mark_gid == $cgroup )
					{
						unset( $cgroup );
						setcookie( "cgroup", "", time()-3600*24*2 );
						$refresh_parent = true;
					}
				}
			}
		}
	}
	if ( $action == "learn" )
	{
		if ( !$mark_eids && $mark_eid )
		{
			$mark_eids[] = $mark_eid;
			$refresh_parent = true;
		}
		if ( $mark_eids )
		{
			foreach( $mark_eids as $mark_eid )
			{
				$result = mysql_query( "update Events set LearnState = '$learn_state' where Id = '$mark_eid'" );
				if ( !$result )
					die( mysql_error() );
			}
		}
	}
	elseif ( $action == "reset" )
	{
		$_SESSION['event_reset_time'] = strftime( "%Y-%m-%d %H:%M:%S" );
		setcookie( "event_reset_time", $_SESSION['event_reset_time'], time()+3600*24*30*12*10 );
		if ( $cookies ) session_write_close();
	}
}

?>
