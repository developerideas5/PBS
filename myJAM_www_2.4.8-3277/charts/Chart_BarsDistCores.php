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
require_once(_FULLPATH.'/access.php');
require_once(_FULLPATH.'/classes/class_myJAM_Jobs.php');
require_once(_FULLPATH.'/php-ofc-library/open-flash-chart.php');
require_once(_FULLPATH.'/fxn/ParseChartArgs.php');
$args = ParseChartArgs();
if(!isset($args['data']) || empty($args['data']))
{
  $args['data'] = 'JOBS';
}
$links = False;
if(@$_GET['links'] == 1)
{
  $links = true;
}
if(!isset($args['nbTop']))
{
  $args['nbTop'] = 10;
}
$ToolTip = 'Cores: #x_label#<br>#val#';
switch($args['data'])
{
  case 'SUs':
    $ToolTip .= ' SUs';
    break;
  case 'JOBS':
    $ToolTip .= ' Jobs';
    break;
}
if (!empty($args['ArchIDs']))
{
  $ToolTip .= '<br>'.$args['ArchNameList'];
  $title = ' ('.$args['ArchNameList'].')';
}
else
{
  $ToolTip .= '<br>All Architectures';
  $title = '';
}
$jobs = new myJAM_Jobs();
$ret_array = $jobs->GetDistCores($args);
$chart = new graph();
$bar = new bar_outline(50, '#3333CC', '#1010A0');
$bar->key('# Cores', 12);
$vXLab = array();
for($nbcores = 1; $nbcores <= $ret_array['MaxCores']; $nbcores++)
{
  if(!isset($ret_array['data'][$nbcores]))
  {
    $ret_array['data'][$nbcores] = 0.0;
  }
  if ($links && $ret_array['data'][$nbcores] > 0)
  {
    $bar->add_link($ret_array['data'][$nbcores], 'javascript:pop_CoreInfo(\''.$nbcores.'\')');
  }
  else
  {
    $bar->add_link($ret_array['data'][$nbcores], '');
  }
  $vXLab[] = $nbcores;
}
$chart->title($title, '{font-size:12px; color: #000000; margin: 2px; padding:2px;}');
$chart->bg_colour = '#ffffff';
$chart->set_inner_background( '#DEDEDE', '#FFFFFF', 270);
$chart->set_tool_tip($ToolTip);
$chart->data_sets[] = $bar;
$chart->set_x_labels($vXLab);
$chart->set_x_label_style( 10, '#000000', 2 );
$chart->set_x_legend('# Cores', 10, '#909090');
$chart->set_y_max($ret_array['MaxData']);
$chart->y_label_steps(5);
switch($args['data'])
{
  case 'SUs':
    $chart->set_y_legend('Service Units [cpu * h]', 10, '#909090');
    break;
  case 'JOBS':
    $chart->set_y_legend('# Jobs', 10, '#909090');
    break;
}
echo $chart->render();
