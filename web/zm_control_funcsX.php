<?php
//
// ZoneMinder web control function library, $Date$, $Revision$
// Copyright (C) 2003, 2004, 2005, 2006  Philip Coombes
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

function getControlCommands( $monitor )
{
	$cmds = array();

	$cmds['Wake'] = "wake";
	$cmds['Sleep'] = "sleep";
	$cmds['Reset'] = "reset";

	$cmds['PresetSet'] = "presetSet";
	$cmds['PresetGoto'] = "presetGoto";
	$cmds['PresetHome'] = "presetHome";

	if ( $monitor['CanZoomCon'] )
		$cmds['ZoomRoot'] = "zoomCon";
	elseif ( $monitor['CanZoomRel'] )
		$cmds['ZoomRoot'] = "zoomRel";
	elseif ( $monitor['CanZoomAbs'] )
		$cmds['ZoomRoot'] = "zoomAbs";
	$cmds['ZoomTele'] = $cmds['ZoomRoot']."Tele";
	$cmds['ZoomWide'] = $cmds['ZoomRoot']."Wide";
	$cmds['ZoomStop'] = "zoomStop";
	$cmds['ZoomAuto'] = "zoomAuto";
	$cmds['ZoomMan'] = "zoomMan";

	if ( $monitor['CanFocusCon'] )
		$cmds['FocusRoot'] = "focusCon";
	elseif ( $monitor['CanFocusRel'] )
		$cmds['FocusRoot'] = "focusRel";
	elseif ( $monitor['CanFocusAbs'] )
		$cmds['FocusRoot'] = "focusAbs";
	$cmds['FocusFar'] = $cmds['FocusRoot']."Far";
	$cmds['FocusNear'] = $cmds['FocusRoot']."Near";
	$cmds['FocusStop'] = "focusStop";
	$cmds['FocusAuto'] = "focusAuto";
	$cmds['FocusMan'] = "focusMan";

	if ( $monitor['CanIrisCon'] )
		$cmds['IrisRoot'] = "irisCon";
	elseif ( $monitor['CanIrisRel'] )
		$cmds['IrisRoot'] = "irisRel";
	elseif ( $monitor['CanIrisAbs'] )
		$cmds['IrisRoot'] = "irisAbs";
	$cmds['IrisOpen'] = $cmds['IrisRoot']."Open";
	$cmds['IrisClose'] = $cmds['IrisRoot']."Close";
	$cmds['IrisStop'] = "irisStop";
	$cmds['IrisAuto'] = "irisAuto";
	$cmds['IrisMan'] = "irisMan";

	if ( $monitor['CanWhiteCon'] )
		$cmds['WhiteRoot'] = "whiteCon";
	elseif ( $monitor['CanWhiteRel'] )
		$cmds['WhiteRoot'] = "whiteRel";
	elseif ( $monitor['CanWhiteAbs'] )
		$cmds['WhiteRoot'] = "whiteAbs";
	$cmds['WhiteIn'] = $cmds['WhiteRoot']."In";
	$cmds['WhiteOut'] = $cmds['WhiteRoot']."Out";
	$cmds['WhiteAuto'] = "whiteAuto";
	$cmds['WhiteMan'] = "whiteMan";

	if ( $monitor['CanGainCon'] )
		$cmds['GainRoot'] = "gainCon";
	elseif ( $monitor['CanGainRel'] )
		$cmds['GainRoot'] = "gainRel";
	elseif ( $monitor['CanGainAbs'] )
		$cmds['GainRoot'] = "gainAbs";
	$cmds['GainUp'] = $cmds['GainRoot']."Up";
	$cmds['GainDown'] = $cmds['GainRoot']."Down";
	$cmds['GainAuto'] = "gainAuto";
	$cmds['GainMan'] = "gainMan";

	if ( $monitor['CanMoveCon'] )
	{
		$cmds['MoveRoot'] = "moveCon";
		$cmds['Center'] = "moveStop";
	}
	elseif ( $monitor['CanMoveRel'] )
	{
		$cmds['MoveRoot'] = "moveRel";
		$cmds['Center'] = $cmds['PresetHome'];
	}
	elseif ( $monitor['CanMoveAbs'] )
	{
		$cmds['MoveRoot'] = "moveAbs";
		$cmds['Center'] = $cmds['PresetHome'];
	}

	$cmds['MoveUp'] = $cmds['MoveRoot']."Up";
	$cmds['MoveDown'] = $cmds['MoveRoot']."Down";
	$cmds['MoveLeft'] = $cmds['MoveRoot']."Left";
	$cmds['MoveRight'] = $cmds['MoveRoot']."Right";
	$cmds['MoveUpLeft'] = $cmds['MoveRoot']."UpLeft";
	$cmds['MoveUpRight'] = $cmds['MoveRoot']."UpRight";
	$cmds['MoveDownLeft'] = $cmds['MoveRoot']."DownLeft";
	$cmds['MoveDownRight'] = $cmds['MoveRoot']."DownRight";

	return( $cmds );
}

function controlFocus( $monitor )
{
	global $cmds, $zmSlangFocus, $zmSlangNear, $zmSlangFar, $zmSlangAuto, $zmSlangMan;

	ob_start();
?>
<div id="focusControls">
  <div><?= $zmSlangNear ?></div>
  <div class="longArrowBtn upBtn" onclick="controlCmd('<?= $cmds['FocusNear'] ?>',event,0,-1)"></div>
  <div<?php if ( $monitor['CanFocusCon'] ) { ?> onclick="controlCmd('<?= $cmds['FocusStop'] ?>')"<?php } ?>><?= $zmSlangFocus ?></div>
  <div class="longArrowBtn downBtn" onclick="controlCmd('<?= $cmds['FocusFar'] ?>',event,0,1)"></div>
  <div><?= $zmSlangFar ?></div>
<?php
	if ( $monitor['CanAutoFocus'] )
	{
?>
  <div><input type="button" class="textbutton" value="<?= $zmSlangAuto ?>" onclick="controlCmd('<?= $cmds['FocusAuto'] ?>')"/></div>
  <div><input type="button" class="textbutton" value="<?= $zmSlangMan ?>" onclick="controlCmd('<?= $cmds['FocusMan'] ?>')"/></div>
<?php
	}
?>
</div>
<?php
	return( ob_get_clean() );
}

function controlZoom( $monitor )
{
	global $cmds, $zmSlangZoom, $zmSlangTele, $zmSlangWide, $zmSlangAuto, $zmSlangMan;

	ob_start();
?>
<div id="zoomControls">
  <div><?= $zmSlangTele ?></div>
  <div class="longArrowBtn upBtn" onclick="controlCmd('<?= $cmds['ZoomTele'] ?>',event,0,-1)"></div>
  <div<?php if ( $monitor['CanZoomCon'] ) { ?> onclick="controlCmd('<?= $cmds['ZoomStop'] ?>')"<?php } ?>><?= $zmSlangZoom ?></div>
  <div class="longArrowBtn downBtn" onclick="controlCmd('<?= $cmds['ZoomWide'] ?>',event,0,1)"></div>
  <div><?= $zmSlangWide ?></div>
<?php
	if ( $monitor['CanAutoZoom'] )
	{
?>
  <div><input type="button" class="textbutton" value="<?= $zmSlangAuto ?>" onclick="controlCmd('<?= $cmds['ZoomAuto'] ?>')"/></div>
  <div><input type="button" class="textbutton" value="<?= $zmSlangMan ?>" onclick="controlCmd('<?= $cmds['ZoomMan'] ?>')"/></div>
<?php
	}
?>
</div><?php
	return( ob_get_clean() );
}

function controlIris( $monitor )
{
	global $cmds, $zmSlangIris, $zmSlangOpen, $zmSlangClose, $zmSlangAuto, $zmSlangMan;

	ob_start();
?>
<div id="irisControls">
  <div><?= $zmSlangOpen ?></div>
  <div class="longArrowBtn upBtn" onclick="controlCmd('<?= $cmds['IrisOpen'] ?>',event,0,-1)"></div>
  <div<?php if ( $monitor['CanIrisCon'] ) { ?> onclick="controlCmd('<?= $cmds['IrisStop'] ?>')"<?php } ?>><?= $zmSlangIris ?></div>
  <div class="longArrowBtn downBtn" onclick="controlCmd('<?= $cmds['IrisClose'] ?>',event,0,1)"></div>
  <div><?= $zmSlangClose ?></div>
<?php
	if ( $monitor['CanAutoIris'] )
	{
?>
  <div><input type="button" class="textbutton" value="<?= $zmSlangAuto ?>" onclick="controlCmd('<?= $cmds['IrisAuto'] ?>')"/></div>
  <div><input type="button" class="textbutton" value="<?= $zmSlangMan ?>" onclick="controlCmd('<?= $cmds['IrisMan'] ?>')"/></div>
<?php
	}
?>
</div>
<?php
	return( ob_get_clean() );
}

function controlWhite( $monitor )
{
	global $cmds, $zmSlangWhite, $zmSlangIn, $zmSlangOut, $zmSlangAuto, $zmSlangMan;

	ob_start();
?>
<div id="whiteControls">
  <div><?= $zmSlangIn ?></div>
  <div class="longArrowBtn upBtn" onclick="controlCmd('<?= $cmds['WhiteIn'] ?>',event,0,-1)"></div>
  <div<?php if ( $monitor['CanWhiteCon'] ) { ?> onclick="controlCmd('<?= $cmds['WhiteStop'] ?>')"<?php } ?>><?= $zmSlangWhite ?></div>
  <div class="longArrowBtn downBtn" onclick="controlCmd('<?= $cmds['WhiteOut'] ?>',event,0,1)"></div>
  <div><?= $zmSlangOut ?></div>
<?php
	if ( $monitor['CanAutoWhite'] )
	{
?>
  <div><input type="button" class="textbutton" value="<?= $zmSlangAuto ?>" onclick="controlCmd('<?= $cmds['WhiteAuto'] ?>')"/></div>
  <div><input type="button" class="textbutton" value="<?= $zmSlangMan ?>" onclick="controlCmd('<?= $cmds['WhiteMan'] ?>')"/></div>
<?php
	}
?>
</div>
<?php
	return( ob_get_clean() );
}

function controlPanTilt( $monitor )
{
	global $cmds, $zmSlangPanTilt;

	ob_start();
?>
<div id="pantiltControls">
  <div><?= $zmSlangPanTilt ?></div>
  <div id="pantiltButtons">
<?php
	if ( $monitor['CanTilt'] )
	{
		if ( $monitor['CanPan'] )
		{
			if ( $monitor['CanMoveDiag'] )
			{
?>
      <div id="upLeftBtn" class="arrowBtn" onclick="controlCmd('<?= $cmds['MoveUpLeft'] ?>',event,-1,-1)"></div>
<?php
			}
		}
?>
      <div id="upBtn" class="arrowBtn" onclick="controlCmd('<?= $cmds['MoveUp'] ?>',event,0,-1)"></div>
<?php
		if ( $monitor['CanPan'] )
		{
			if ( $monitor['CanMoveDiag'] )
			{
?>
      <div id="upRightBtn" class="arrowBtn" onclick="controlCmd('<?= $cmds['MoveUpRight'] ?>',event,1,-1)"></div>
<?php
			}
		}
    }
	if ( $monitor['CanPan'] )
	{
?>
      <div id="leftBtn" class="arrowBtn" onclick="controlCmd('<?= $cmds['MoveLeft'] ?>',event,-1,0)"></div>
<?php
	}
?>
      <div id="centerBtn" class="arrowBtn" onclick="controlCmd('<?= $cmds['Center'] ?>')"></div>
<?php
	if ( $monitor['CanPan'] )
	{
?>
      <div id="rightBtn" class="arrowBtn" onclick="controlCmd('<?= $cmds['MoveRight'] ?>',event,1,0)"></div>
<?php
	}
	if ( $monitor['CanTilt'] )
	{
		if ( $monitor['CanPan'] )
		{
			if ( $monitor['CanMoveDiag'] )
			{
?>
      <div id="downLeftBtn" class="arrowBtn" onclick="controlCmd('<?= $cmds['MoveDownLeft'] ?>',event,-1,1)"></div>
<?php
			}
		}
?>
      <div id="downBtn" class="arrowBtn" onclick="controlCmd('<?= $cmds['MoveDown'] ?>',event,0,1)"></div>
<?php
		if ( $monitor['CanPan'] )
		{
			if ( $monitor['CanMoveDiag'] )
			{
?>
      <div id="downRightBtn" class="arrowBtn" onclick="controlCmd('<?= $cmds['MoveDownRight'] ?>',event,1,1)"></div>
<?php
			}
		}
	}
?>
  </div>
</div>
<?php
	return( ob_get_clean() );
}

function controlPresets( $monitor )
{
	global $cmds, $jws, $zmSlangPresets, $zmSlangHome, $zmSlangSet;

	define( "MAX_PRESETS", "12" );

    $sql = "select * from ControlPresets where MonitorId = '".$monitor['Id']."'";
    $result = mysql_query( $sql );
    if ( !$result )
        die( mysql_error() );
    $labels = array();
    while( $row = mysql_fetch_assoc( $result ) )
    {
        $labels[$row['Preset']] = $row['Label'];
    }
    mysql_free_result( $result );

	$preset_break = (int)(($monitor['NumPresets']+1)/((int)(($monitor['NumPresets']-1)/MAX_PRESETS)+1));

	ob_start();
?>
<div id="presetControls">
  <div><?= $zmSlangPresets ?></div>
  <div>
<?php
	for ( $i = 1; $i <= $monitor['NumPresets']; $i++ )
	{
?><input type="button" class="numbutton" title="<?= $labels[$i]?$labels[$i]:"" ?>" value="<?= $i ?>" onclick="controlCmd('<?= $cmds['PresetGoto'] ?><?=$i?>');"/><?php (($i%$preset_break)==0)?"<br/>":"&nbsp;&nbsp;" ?><?php
		if ( $i && (($i%$preset_break) == 0) )
		{
?><br/><?php
		}
	}
?>
  </div>
  <div>
<?php
	if ( $monitor['HasHomePreset'] )
	{
?>
    <span><input type="button" class="textbutton" value="<?= $zmSlangHome ?>" onclick="controlCmd('<?= $cmds['PresetHome'] ?>');"/></span>
<?php
	}
	if ( canEdit( 'Monitors') && $monitor['CanSetPresets'] )
	{
?>
    <span><input type="button" class="textbutton" value="<?= $zmSlangSet ?>" onclick="newWindow('<?= $PHP_SELF ?>?view=controlpreset&mid=<?= $monitor['Id'] ?>', 'zmPreset', <?= $jws['preset']['w'] ?>, <?= $jws['preset']['h'] ?> );"/></span>
<?php
	}
?>
  </div>
</div>
<?php
	return( ob_get_clean() );
}

function controlPower( $monitor )
{
	global $cmds, $zmSlangControl, $zmSlangWake, $zmSlangSleep, $zmSlangReset;

	ob_start();
?>
<div id="powerControls">
  <div><?= $zmSlangControl ?></div>
  <div>
<?php
	if ( $monitor['CanWake'] )
	{
?>
    <span><input type="button" class="textbutton" value="<?= $zmSlangWake ?>" onclick="controlCmd('<?= $cmds['Wake'] ?>')"/></span>
<?php
	}
	if ( $monitor['CanSleep'] )
	{
?>
    <span><input type="button" class="textbutton" value="<?= $zmSlangSleep ?>" onclick="controlCmd('<?= $cmds['Sleep'] ?>')"/></span>
<?php
	}
	if ( $monitor['CanReset'] )
	{
?>
    <span><input type="button" class="textbutton" value="<?= $zmSlangReset ?>" onclick="controlCmd('<?= $cmds['Reset'] ?>')"/></span>
<?php
	}
?>
  </div>
</div>
<?php
	return( ob_get_clean() );
}
