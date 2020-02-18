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
require_once(_FULLPATH.'/classes/class_myJAM_DB.php');
$PixelWidth = 120;
//cache the admin-flag
$admin = $ActiveUser->ADMIN;
//get the results from the QueueDetails-View
$db = new myJAM_db();
$sql = 'SELECT id,name,nbRunning,nbQueued,open,running,HostID FROM QueueDetails WHERE name!=\'<deleted queue>\'';
$vRes = $db->query($sql);
//Init of the vars we need
$qJSON = array();
$QMax = 0;
$nbRunningTot = array();
$nbQueuedTot = array();
//determine the maximum of jobs
foreach($vRes as $result)
{
  $QMax = max($QMax, $result['nbRunning']+$result['nbQueued']);
  $nbRunningTot[$result['HostID']] = 0;
  $nbQueuedTot[$result['HostID']] = 0;
}
if($QMax > 0)
{
  $QFac = $PixelWidth/$QMax;
}
else
{
  $QFac = 0;
}
//Iterate through the Queues and collect the data
foreach($vRes as $result)
{
  $nbRunning = (int)$result['nbRunning'];
  $nbQueued = (int)$result['nbQueued'];
  $nbRunningTot[$result['HostID']] += $nbRunning;
  $nbQueuedTot[$result['HostID']] += $nbQueued;
  $qJSON[] = genDataElement($result['id'], $result['name'], $nbRunning, $nbQueued, $result);
}
//Now the per-host summation
foreach($nbRunningTot as $HostID => $running)
{
  $all = $running+$nbQueuedTot[$HostID];
  if($all > 0)
  {
    $QFac = $PixelWidth/$all;
  }
  else
  {
    $QFac = 0;
  }
  $qJSON[] = genDataElement('h'.$HostID, '', $running, $nbQueuedTot[$HostID], NULL);
}
echo json_encode($qJSON);
//o-------------------------------------------------------------------------------o//
//o-------------------------------------------------------------------------------o//
//o-------------------------------------------------------------------------------o//
//o-------------------------------------------------------------------------------o//
function genDataElement($id, $QueueName, $nbRunning, $nbQueued, $result)
//o-------------------------------------------------------------------------------o//
{
  global $PixelWidth;
  global $QFac;
  global $admin;
  $tmp = array('id' => $id);
  if($nbRunning > 0)
  {
    if($admin)
    {
      $tmp['nbR'] = '<a href="javascript:pop_ShowJobs(\''.$QueueName.'\', \'R\');">'.$nbRunning.'</a>';
    }
    else
    {
      $tmp['nbR'] = $nbRunning;
    }
  }
  else
  {
    $tmp['nbR'] = '';
  }
  if($nbRunning+$nbQueued > 0)
  {
    $tmp['load'] = genDivBar($PixelWidth, $QFac, $nbRunning, 0);
  }
  else
  {
    $tmp['load'] = '';
  }
  return $tmp;
}
//o-------------------------------------------------------------------------------o//
function genDivBar($PixelWidth, $f, $nbr, $nbq)
//o-------------------------------------------------------------------------------o//
{
  $running = round($f*$nbr, 0);
  $queued = round($f*$nbq, 0);
//  $out = '<div style="height:20px;width:'.$PixelWidth.'px;border-style:solid;border-width:1px;background-color:#d0d0d0">'
//          .'<div style="position:relative;top:0px;left:0px;height:20px;width:'.$queued.'px;background-color:#ff0000"></div>'
//          .'<div style="position:relative;top:-20px;left:'.$queued.'px;height:20px;width:'.$running.'px;background-color:#00ff00"></div>'
//        .'</div>'
//        ;
  $out = '<div style="height:20px;width:'.$PixelWidth.'px;border-style:solid;border-width:1px;background-image:url(images/bar_bg.png);background-repeat:repeat-x;">'
          .'<div style="position:relative;top:0px;left:0px;height:20px;width:'.$queued.'px;background-image:url(images/bar_red.png);background-repeat:repeat-x;"></div>'
          .'<div style="position:relative;top:-20px;left:'.$queued.'px;height:20px;width:'.$running.'px;background-image:url(images/bar_green.png);background-repeat:repeat-x;"></div>'
        .'</div>'
        ;
  return $out;
}
