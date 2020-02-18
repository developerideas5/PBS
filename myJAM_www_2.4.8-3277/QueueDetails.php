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

define('_FULLPATH', realpath(dirname(__FILE__)));
require_once(_FULLPATH."/access.php");
require_once(_FULLPATH."/CloseWindow.php");
require_once(_FULLPATH."/classes/class_myJAM_DB.php");
require_once(_FULLPATH."/classes/class_myJAM_Queue.php");
require_once(_FULLPATH."/classes/class_myJAM_User.php");
require_once(_FULLPATH."/includes/cmn_header.php");
echo '<head>'
.'<link rel="shortcut icon" href="images/myJAM-icon.png" type="image/png"/>'
.'<title>Queue Info</title>'
.'<link rel="stylesheet" type="text/css" href="css/style.css" />'
.'</head>'
.'<body class="apopbody">'
.'<table cellspacing="3" cellpadding="0" class="tabhead">'
.'<tr>'
.'<td>'
.'<table cellspacing="0" cellpadding="0" class="tabhead2">'
.'<tr>'
.'<td class="gradhead1">'
.'<img src="images/multigradient_left.png" alt="gradientGFX" />'
.'</td>'
.'<td class="gradhead4">'
.'<div class="toolgrad3">Queue Details</div>'
.'</td>'
.'<td class="gradhead5">'
.'<img src="images/multigradient_middle.png" alt="gradientGFX" />'
.'</td>'
.'<td class="gradhead6"></td>'
.'<td class="gradhead1">'
.'<img src="images/multigradient_right.png" alt="gradientGFX" />'
.'</td>'
.'</tr>'
.'</table>'
.'</td>'
.'</tr>'
.'</table>'
.'<p></p>'
;
$db = new myJAM_DB();
$queue = new myJAM_Queue($_GET["queue"]);
$SQL = "SELECT uid, job_state, comment FROM Jobs WHERE (job_state!='F')";
if (isset($queue) && is_object($queue) && is_scalar($queue->ID))
{
  $SQL .= " AND qid='".(int)$queue->ID."'";
}
$vJobs = $db->query($SQL);
//echo $SQL ." ". $vJobs;
$stat = array();
$global_stat = array();
$nbRunning = 0;
$nbQueued = 0;
foreach($vJobs as $job)
{
  if($job["job_state"] == "R")
  {
    @$stat[$job["uid"]]["Running"]++;
    $nbRunning++;
  }
  elseif($job["job_state"] == "Q")
  {
    @$stat[$job["uid"]]["Queued"]++;
    if ($job["comment"])
    {
      @$stat[$job["uid"]]["comment"][$job["comment"]]++;
      @$global_stat[$job["comment"]]++;
      $nbQueued++;
    }
  }
}
echo '<table class="quetable" cellpadding="2" cellspacing="2">';
if(isset($_GET["queue"]) && !empty($_GET["queue"]))
{
  $queue = new myJAM_Queue($_GET["queue"]);
  if (!is_scalar($queue->ID)){
    die("myJAM>> FATAL ERROR NWO2 in module QueueDetails: ILLEGAL ARGUMENT");
  }
  echo "<tr><td><span class=\"fat\">Queue</span>: ".$queue->Name."</td></tr>";
}
echo '<tr>'
.'<td>'
.'<div class="apopbody">'
.'<table class="full">'
.'<tr>'
.'<td class="cell3">User</td>'
.'<td class="cell3">Running</td>'
.'<td class="cell3">Queued</td>'
.'</tr>';
foreach($stat as $uid=>$s)
{
  echo "<tr>";
  $user = new myJAM_User($uid);
  if(is_array($user->ID))
  {die("myJAM>> FATAL ERROR ZUM4 in module QueueDetails: ILLEGAL VALUE: ".$uid);}
  echo "<td>".$user->FullName."</td>"
  ."<td class=\"right\">";
  if(isset($s["Running"]))
  {
    echo $s["Running"];
  }
  echo '</td>'
  ;
  if (!empty($s["comment"]))
  {
    echo '<td><table>';
    foreach($s["comment"] as $comment=>$count)
    {
      echo "<tr><td class=\"right\"><a href=\"ShowJobs.php?queued=1";
      if (isset($queue))
      {
        echo "&amp;queue=".$queue->Name;
      }
      echo "&amp;UserID=".$user->ID."\">".$count."</a></td>"
      ."<td>".$comment."</td>"
      ."</tr>"
      ;
    }
    echo '</table></td>';
  }
  echo "</tr>";
}
echo '<tr><td colspan="3">'
.'<div class="queadmin">'
.'</div>'
.'</td>'
.'</tr>'
.'<tr>'
.'<td>'
.'<span class="fat">Total</span>'
.'</td>'
.'<td class="right">'.$nbRunning.'</td>';
if(!empty($global_stat))
{
  echo '<td><table>';
  foreach($global_stat as $comment=>$count)
  {
    echo '<tr><td class="right">'.$count.'</td><td>'.$comment.'</td></tr>';
  }
  echo "</table></td>";
}
echo "</tr>"
."</table>"
."</div>"
."</td>"
."</tr>"
.'</table><p />';
echo CloseWindow();
echo '</body></html>';
