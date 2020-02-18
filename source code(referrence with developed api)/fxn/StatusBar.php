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

require_once (_FULLPATH . "/classes/class_myJAM_DB.php");
require_once (_FULLPATH . "/classes/class_myJAM_Architecture.php");
require_once (_FULLPATH . "/classes/class_myJAM_User.php");
require_once (_FULLPATH . "/classes/class_myJAM_Project.php");
require_once (_FULLPATH . "/fxn/is_md5.php");
//o-------------------------------------------------------------------------------o
function StatusBar ($UserID = NULL, $ProjID = NULL, $Appl = NULL, $ArchID = NULL,
$RunningOnly = NULL, $md5 = NULL)
//o-------------------------------------------------------------------------------o
{
  $db = new myJAM_DB();
  $url = "";
  $sql = "select uid, count(pbs_jobnumber), sum(actual_su), job_state from Jobs WHERE job_state!=''";
  if ($UserID)
  {
    $User = new myJAM_User($UserID);
    if (is_object($User) && is_scalar($User->ID) && (integer)$User->ID > 0)
    {
      $sql .= " AND uid='" . (int)$User->ID . "'";
      $url .= "&amp;UserID=" . (int)$User->ID;
    }
    else
    {
      unset($User);
    }
  }
  if ($ProjID)
  {
    $Project = new myJAM_Project($ProjID);
    if (is_object($Project) && is_scalar($Project->ID) &&
     (integer)$Project->ID > 0)
    {
      $sql .= " AND pid='" . (int)$Project->ID . "'";
      $url .= "&amp;ProjID=" . (int)$Project->ID;
    }
    else
    {
      unset($Project);
    }
  }
  if (is_scalar($ArchID))
  {
    $Architectures = new myJAM_Architecture();
    if ($Architectures->Name[$ArchID])
    {
      $sql .= " AND ArchID='" . (int)$ArchID . "'";
      $url .= "&amp;ArchID=" . (int)$ArchID;
    }
  }
  elseif (is_array($ArchID) && (integer)$ArchID > 0)
  {
    $archs = "";
    foreach ($ArchID as $id)
    {
      if ($archs != "")
      {
        $archs .= "' OR ArchID='";
      }
      $archs .= $id;
    }
    $sql .= " AND (ArchID='" . (int)$archs . "')";
  }
  if (!empty($md5))
  {
    $sql .= " AND md5_bin='" . mysql_real_escape_string($md5) . "'";
    $url .= '&amp;md5=' . htmlentities($md5);
  }
  $sql .= " GROUP BY uid, job_state";
  $vCounts = $db->query($sql);
  $nbRunning = 0;
  $nbQueued = 0;
  $nbJobs = 0;
  $UsedSUs = 0.0;
  $vUser = array();
  foreach ($vCounts as $count)
  {
    switch ($count["job_state"])
    {
      case "R":
        $nbRunning += $count["count(pbs_jobnumber)"];
        $UsedSUs += $count["sum(actual_su)"];
        break;
      case "Q":
        $nbQueued += $count["count(pbs_jobnumber)"];
        $UsedSUs += $count["sum(actual_su)"];
        break;
      case "F":
      case "":
        $nbJobs += $count["count(pbs_jobnumber)"];
        $UsedSUs += $count["sum(actual_su)"];
        break;
      case "H":
      case "E":
        break;
      default:
        echo "Unknown JobState :\"" . $count["job_state"] . "\"!<br/>";
        die("myJAM>> FATAL ERROR 0xd8ab in UserInfo");
    }
    if (!$RunningOnly)
    {
      @$vUser[$count["uid"]] += $count["count(pbs_jobnumber)"];
    }
    else
    {
      if ($count["job_state"] == "R")
      {
        @$vUser[$count["uid"]] += $count["count(pbs_jobnumber)"];
      }
    }
  }
  echo "<table class=\"full\">" . "<tr>" .
   "<td><img src=\"images/Utilities-Cut.png\" alt=\"myJAMIcon\"/>" .
   "<a href=\"ShowJobs.php?running=1" . $url . "\"><b>Running:</b> " . $nbRunning .
   "</a></td>" .
   "<td align=\"center\"><img src=\"images/Hourglass_cut.png\" alt=\"myJAMIcon\"/>" .
   "<a href=\"ShowJobs.php?queued=1" . $url . "\"><b>Queued:</b> " . $nbQueued .
   "</a></td>" .
   "<td align=\"center\"><img src=\"images/check-mark-copy-32x32.png\" alt=\"myJAMIcon\"/>" .
   "<a href=\"ShowJobs.php?finished=1" . $url . "\"><b>Finished:</b> " . $nbJobs .
   "</a></td>" .
   "<td align=\"right\"><img src=\"images/Clock-32x32.png\" alt=\"myJAMIcon\"/>";
  printf("<b>Used SUs:</b> %.2f</td>", $UsedSUs);
  echo "</tr></table>";
  arsort($vUser);
  return $vUser;
}
