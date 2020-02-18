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

require_once(_FULLPATH.'/classes/class_myJAM_User.php');
require_once(_FULLPATH.'/classes/class_myJAM_Project.php');
require_once(_FULLPATH.'/classes/class_myJAM_Architecture.php');
//o-------------------------------------------------------------------------------o
function ParseChartArgs()
//o-------------------------------------------------------------------------------o
{
  $ret_array = array();
  // EndDate
  if(isset($_GET['etime']) && (integer)$_GET['etime'] > 0)
  {
    $ret_array['EndTime'] = (integer)$_GET['etime'];
  }
  elseif(isset($_GET['eyear']) && (integer)$_GET['eyear'] >= 1900
      && isset($_GET['emonth']) && (integer)$_GET['emonth'] >= 1 && (integer)$_GET['emonth'] <=12)
  {
    $ret_array['EndTime'] = strtotime(sprintf('%04u-%02u-01', (integer)$_GET['eyear'],
                                                               (integer)$_GET['emonth']));
    $lastday = date('t', $ret_array['EndTime']);
    $ret_array['EndTime'] = strtotime(sprintf('%04u-%02u-%02u', (integer)$_GET['eyear'],
                                                                (integer)$_GET['emonth'],
                                                                $lastday));
  }
  // StartDate
  if(isset($_GET['stime']) && is_numeric($_GET['stime']) && (integer)$_GET['stime'] > 0)
  {
    $ret_array['StartTime'] = (integer)$_GET['stime'];
  }
  elseif(isset($_GET['syear']) && (integer)$_GET['syear'] >= 1900
      && isset($_GET['smonth']) && (integer)$_GET['smonth'] >= 1 && (integer)$_GET['smonth'] <=12)
  {
    $ret_array['StartTime'] = strtotime(sprintf('%04u-%02u-01', (integer)$_GET['syear'], $_GET['smonth']));
  }
//  if(empty($ret_array["StartTime"]) || $ret_array["StartTime"] >= $ret_array["EndTime"])
//  {
//    $ret_array["StartTime"] = strtotime("-5 month", $ret_array["EndTime"]);
//  }
  // Architectures
  if(isset($_GET['archids']) && !empty($_GET['archids']))
  {
    $ret_array['ArchIDs'] = array();
    $ret_array['ArchNames'] = array();
    $ArchList = new myJAM_Architecture();
    $vArgs = explode('a',$_GET['archids']);
    if ($vArgs)
    {
      $ret_array['ArchNameList'] = '';
      foreach($vArgs as $ArchID)
      {
        if(@$ArchList->Name[$ArchID])
        {
          $ret_array['ArchIDs'][] = $ArchID;
          $ret_array['ArchNames'][$ArchID] = $ArchList->Name[$ArchID];
          if($ret_array['ArchNameList'] != '')
            {$ret_array['ArchNameList'] .= ', ';}
          $ret_array['ArchNameList'] .= $ArchList->Name[$ArchID];
        }
      }
    }
    else
    {
      if($ArchList->Name[(integer)$_GET['archids']])
      {
        $ret_array['ArchIDs'][0] = (integer)$_GET['archids'];
        $ret_array['ArchNames'][(integer)$_GET['archids']] = $ArchList->Name[(integer)$_GET['archids']];
        $ret_array['ArchNameList'] = $ArchList->Name[(integer)$_GET['archids']];
      }
    }
    unset($ArchList);
  }
  // Projects
  if(isset($_GET['projids']) && !empty($_GET['projids']))
  {
    $ret_array['ProjIDs'] = array();
    $ProjList = new myJAM_Project();
    $vArgs = explode('p',$_GET['projids']);
    if ($vArgs)
    {
      foreach($vArgs as $ProjID)
      {
        if(!empty($ProjID) && $ProjList->Name[$ProjID])
        {
          $ret_array['ProjIDs'][] = $ProjID;
          $ret_array['ProjNames'][$ProjID] = $ProjList->Name[$ProjID];
        }
      }
    }
    else
    {
      if($ProjList->Name[(integer)$_GET['projids']])
      {
        $ret_array['ProjIDs'][0] = (integer)$_GET['projids'];
        $ret_array['ProjNames'][$ProjID] = $ProjList->Name[(integer)$_GET['projids']];
      }
    }
    unset($ProjList);
  }
  // User
  if(isset($_GET['userids']) && !empty($_GET['userids']))
  {
    $ret_array['UserIDs'] = array();
    $UserList = new myJAM_User();
    $vArgs = explode('u',$_GET['userids']);
    if ($vArgs)
    {
      foreach($vArgs as $UserID)
      {
        if(!empty($UserID) && $UserList->UserName[$UserID])
        {
          $ret_array['UserIDs'][] = $UserID;
          $ret_array['UserNames'][$UserID] = $UserList->UserName[$UserID];
        }
      }
    }
    else
    {
      if($UserList->UserName[(integer)$_GET['userids']])
      {
        $ret_array['UserIDs'][0] = (integer)$_GET['userids'];
        $ret_array['UserNames'][$UserID] = $UserList->UserName[(integer)$_GET['userids']];
      }
    }
    unset($UserList);
  }
  // ChartType
  if(isset($_GET['type']))
  {
    switch($_GET['type'])
    {
      case '1':
        $ret_array['type'] = 'BARS';
        break;
      case '2':
        $ret_array['type'] = 'LINES';
        break;
      case '3':
        $ret_array['type'] = 'PIE';
        break;
      default:
        $ret_array['type'] = '';
    }
  }
  // ChartData
  if(isset($_GET['data']))
  {
    switch($_GET['data'])
    {
      case '1':
        $ret_array['data'] = 'SUs';
        break;
      case '2':
        $ret_array['data'] = 'JOBS';
        break;
      case '3':
        $ret_array['data'] = 'LOAD';
        break;
      default:
        $ret_array['data'] = '';
    }
  }
  // Top-N
  if(isset($_GET['nbtop']) && (integer)$_GET['nbtop'] > 0)
  {
    $ret_array['nbTop'] = (integer)$_GET['nbtop'];
  }
  // JobState
  if(isset($_GET['jobstate']) && in_array($_GET['jobstate'], array('F','Q','R')))
  {
    $ret_array['JobState'] = $_GET['jobstate'];
  }
  // Give it back
  return $ret_array;
}
