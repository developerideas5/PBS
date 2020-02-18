<?php
/*
   _                          _____     _       ____    ____      __ _
  / /                        |_   _|   / \     |_   \  /   _|    / /\ \
 / /  _ .--..--.    _   __     | |    / _ \      |   \/   |     / /  \ \
< <  [ `.-. .-. |  [ \ [  ]_   | |   / ___ \     | |\  /| |    / /    > >
 \ \  | | | | | |   \ '/ /| |__' | _/ /   \ \_  _| |_\/_| |_  / /    / /
  \_\[___||__||__][\_:  / `.____.'|____| |____||_____||_____|/_/    /_/ Community Edition
                   \__.' o---------------------------------------------------------------
                         |
                         o--------o Project Coordinator, CTO: Dr. rer. nat. Stephan Raub
                                  o B.Sc. Ingo Breuer
                                  o Michael Schlapa
                                  o Dennis-Bendert Schramm (retired)
                                  o Marcus Ihde-Meister
                                  o Christoph Gierling (retired)

Copyright (C) 2010,2011 The <myJAM/> Team, Heinrich-Heine-University Duesseldorf, Germany.

https://sourceforge.net/projects/myjam

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307,
USA.
*/

define('_FULLPATH', realpath(dirname(__FILE__).'/../'));
$vColors = array();
$ActiveUser = NULL;
require_once(_FULLPATH.'/classes/class_myJAM_Architecture.php');
require_once(_FULLPATH.'/classes/class_myJAM_Jobs.php');
require_once(_FULLPATH.'/php-ofc-library/open-flash-chart.php');
require_once(_FULLPATH.'/includes/Colors.php');
require_once(_FULLPATH.'/fxn/ParseChartArgs.php');
$args = ParseChartArgs();
$title = '';
$ToolTip2 = '';
if(empty($args['ArchIDs']))
{
  $Archs = new myJAM_Architecture();
  $ArchIDList = $Archs->ID;
  foreach($ArchIDList as $ArchID)
  {
    $args['ArchIDs'][] = $ArchID;
    $args['ArchNames'][$ArchID] = $Archs->Name[$ArchID];
  }
}
if(empty($args['EndTime']))
{
  $args['EndTime'] = time();
}
if(empty($args['StartTime']) || $args['StartTime'] >= $args['EndTime'])
{
  $args['StartTime'] = strtotime('-5 month', $args['EndTime']);
  $args['StartTime'] = strtotime('01-'.date('m', $args['StartTime']).'-'.date('Y', $args['StartTime']));
}
if(!isset($args['data']) || empty($args['data']))
{
  $args['data'] = 'SUS MONTHLY';
}
if (isset($args['UserIDs']))
{
  $user = new myJAM_User($args['UserIDs'][0]);
  $title .= ' (User '.$user->UserName.')';
  $ToolTip2 .= '<br>User: '.$user->UserName;
}
if (isset($args['ProjIDs']))
{
  $Proj = new myJAM_Project($args['ProjIDs'][0]);
  $title .= ' (Project '.$Proj->Name.')';
  $ToolTip2 .= '<br>Project: '.$Proj->Name;
}
//get the data:
$jobs = new myJAM_Jobs($args);
$jobs->getDistArchs();
$ToolTip = '#x_label#<br>#val#';
if($jobs->SUsReq)
{
  $title = 'Service Units'.$title;
  $ToolTip .= ' SUs';
}
elseif($jobs->JobsReq)
{
  $title = '# of Jobs'.$title;
  $ToolTip .= ' Jobs';
}
$ToolTip .= '<br>Arch: #key#'.$ToolTip2;
//create new chart object;
$chart = new graph();
$chart->title($title, '{font-size:12px; font-weight:bolder; color: #000000; margin: 5px; padding:5px;}');
$chart->bg_colour = '#ffffff';
$chart->set_inner_background('#dedede', '#ffffff', 270);
//$nbArchs = $jobs->nbLines;
//$vArchNames = $jobs->ArchNames;
foreach($args['ArchNames'] as $ArchID=>$ArchName)
{
  switch($args['type'])
  {
    case 'BARS':
      $bar = new bar_3d(75, $vColors[$ArchID]);
      $bar->key($ArchName, 12);
      $bar->data = $jobs->Data['data'][$ArchID];
      $chart->data_sets[] = $bar;
      unset($bar);
      break;
    case 'LINES':
      $chart->set_data($jobs->Data['data'][$ArchID]);
      $chart->line_dot( 3, 5, $vColors[$ArchID], $ArchName, 12);
      break;
  }
}
//set x-Axis
$chart->set_x_axis_3d($jobs->Data['nbMonths']);
$chart->x_axis_colour( '#909090', '#ADB5C7' );
$chart->set_x_labels($jobs->Data['MonthNames']);
//set y-Axis
$chart->y_axis_colour( '#909090', '#ADB5C7' );
$chart->y_label_steps(5);
$chart->set_y_legend($title, 12, '#736AFF' );
$chart->set_y_max($jobs->Data['MaxData']);
//set Tool-Tips
$chart->set_tool_tip($ToolTip);
echo $chart->render();
