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
require_once(_FULLPATH.'/classes/class_myJAM_Jobs.php');
require_once(_FULLPATH.'/php-ofc-library/open-flash-chart.php');
//o-------------------------------------------------------------------------------o
//o-------------------------------------------------------------------------------o
//o MAIN OF CHART RUNNING
//o-------------------------------------------------------------------------------o
//o-------------------------------------------------------------------------------o
$links = False;
if (isset($_GET['links']) && $_GET['links'] == '1')
  {$links = True;}
$ToolTip = '#x_label#<br>#val# ';
switch($_GET['data'])
{
  case '2':
    $DataMode = 'CORES';
    $title = 'Cores in Use';
    $Color = '#f0c327';
    $ToolTip .= ' Cores';
    break;
  case '3':
    $DataMode = 'LOAD';
    $title = 'Current Load';
    $Color = 'ec4f4f';
    $ToolTip .= '% Load';
    break;
  default:
    $DataMode = 'JOBS';
    $title = 'Jobs Running';
    $Color = '#5cdd9f';
    $ToolTip .= ' Jobs';
    break;
}
$jobs = new myJAM_Jobs();
$jobs->GetRunning($DataMode);
//create new bar-object
$bars = new bar_glass( 55, $Color, '#a0a0a0' );
$pArch = 0;
$data = $jobs->data;
foreach($data as $val)
{
  $link = '';
  if ($links)
  {
    $pArch++;
    $link = 'javascript:';
    switch($DataMode)
    {
      case 'JOBS':
        $link .= 'ReDraw_Jobs(\''.$pArch.'\');';
        break;
      case 'CORES':
        $link .= 'ReDraw_Cores(\''.$pArch.'\');';
        break;
    }
  }
  $bars->add_link($val, $link);
}
//create graph
$chart = new graph();
$chart->title($title, '{font-size:18px; color: #000000; margin: 5px; padding:5px; padding-left: 18px; padding-right: 18px;}');
$chart->bg_colour = '#FFFFFF';
$chart->set_inner_background( '#DEDEDE', '#FFFFFF', 270);
$chart->data_sets[] = $bars;
$chart->set_tool_tip($ToolTip);
$chart->set_x_labels($jobs->ArchNames);
$chart->x_axis_colour('#909090', '#d0d0d0' );
$chart->set_y_min(0);
if ($DataMode == 'LOAD')
{
  $chart->set_y_max(100);
}
else
{
  $chart->set_y_max($jobs->MaxData);
}
$chart->y_label_steps(10);
$chart->y_axis_colour('#909090', '#d0d0d0' );
echo $chart->render();
