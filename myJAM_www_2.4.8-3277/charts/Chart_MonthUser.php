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
require_once(_FULLPATH.'/access.php');
require_once(_FULLPATH.'/classes/class_myJAM_Architecture.php');
require_once(_FULLPATH.'/classes/class_myJAM_User.php');
require_once(_FULLPATH.'/classes/class_myJAM_Jobs.php');
require_once(_FULLPATH.'/helper/myJAM_JSON/myJAM_JSON.php');
require_once(_FULLPATH.'/php-ofc-library/open-flash-chart.php');
require_once(_FULLPATH.'/includes/Colors.php');
require_once(_FULLPATH.'/fxn/ParseChartArgs.php');
$args = ParseChartArgs();
if(empty($args['ArchIDs']))
{
  $Archs = new myJAM_Architecture();
  $ArchIDList = $Archs->ID;
  foreach($ArchIDList as $ArchID)
  {
    $args['ArchIDs'][] = $ArchID;
    $args['ArchNames'][$ArchID] = $Archs->Name[$ArchID];
  }
   unset($Archs);
}
if(!isset($args['UserIDs']) || count($args['UserIDs']) < 1)
{
  $args['UserIDs'] = array();
  $args['UserNames'] = array();
  $Users = new myJAM_User();
  foreach($Users->ID as $UserID)
  {
    $args['UserIDs'][] = $UserID;
    $args['UserNames'][$UserID] = $Users->UserName[$UserID];
  }
  unset($Users);
}
if(empty($args['EndTime']))
  {$args['EndTime'] = time();}
if(empty($args['StartTime']) || $args['StartTime'] >= $args['EndTime'])
{
  $args['StartTime'] = strtotime('-5 month', $args['EndTime']);
  $args['StartTime'] = strtotime('01-'.date('m', $args['StartTime']).'-'.date('Y', $args['StartTime']));
}
if(!isset($args['data']) || empty($args['data']))
  {$args['data'] = 'SUs';}
$ToolTip = '#key#<br>#val#';
switch($args['data'])
{
  case 'SUs':
    $title = 'Service Units';
    $ToolTip .= ' SUs';
    break;
  case 'JOBS':
    $title = '# of Jobs';
    $ToolTip .= ' Jobs';
    break;
}
//create a JOBS-Object and get the data-array!
$jobs = new myJAM_Jobs();
$data = $jobs->GetUserMonths($args);
//create new chart object;
$chart = new graph();
$chart->bg_colour = '#ffffff';
$chart->set_inner_background( '#DEDEDE', '#FFFFFF', 270);
$pColor = 0;
$admin = $ActiveUser->ADMIN;
foreach($args['UserNames'] as $UserID=>$UserName)
{
  switch($args['type'])
  {
    case 'BARS':
      $bar = new bar_3d(75, $vColors[$pColor]);
      $bar->key($UserName, 12);
      for($i = 0; $i < $data['nbMonths']; $i++)
      {
        $link = '';
        if($admin && $data['data'][$UserID][$i] > 0)
        {
          $tmp = new myJAM_JSON();
          $tmp->JobState = 'F';
          $tmp->UserID = $UserID;
          $tmp->month = $data['pMonth'][$UserID][$i];
          $tmp->year = $data['pYear'][$UserID][$i];
          $link = 'javascript:pop_ShowJobs(\''
                 .$tmp->toBase64()
                 .'\');';
        }
        $bar->add_link($data['data'][$UserID][$i], $link);
      }
      $chart->data_sets[] = $bar;
      unset($bar);
      break;
    case 'LINES':
      $line = new line_hollow(3, 5, $vColors[$pColor]);
      $line->key($UserName, 12);
      for($i = 0; $i < $data['nbMonths']; $i++)
      {
        $link = '';
        if($admin && $data['data'][$UserID][$i] > 0)
        {
          $tmp = new myJAM_JSON();
          $tmp->JobState = 'F';
          $tmp->UserID = $UserID;
          $tmp->month = $data['pMonth'][$UserID][$i];
          $tmp->year = $data['pYear'][$UserID][$i];
          $link = 'javascript:pop_ShowJobs(\''
                 .$tmp->toBase64()
                 .'\');';
        }
        $line->add_link($data['data'][$UserID][$i], $link);
      }
      $chart->data_sets[] = $line;
      unset($line);
      break;
  }
  $pColor = (++$pColor)%17;
}
//set x-Axis
$chart->set_x_axis_3d($data['nbMonths']);
$chart->x_axis_colour('#909090', '#ADB5C7');
$chart->set_x_labels($data['MonthNames']);
$ToolTip .= '<br>#x_label#';
//set y-Axis
$chart->y_axis_colour('#909090', '#ADB5C7');
$chart->y_label_steps(5);
$chart->set_y_legend($title, 12, '#736AFF');
$chart->set_y_max($data['MaxData']);
//set Tool-Tips
$chart->set_tool_tip($ToolTip);
echo $chart->render();
