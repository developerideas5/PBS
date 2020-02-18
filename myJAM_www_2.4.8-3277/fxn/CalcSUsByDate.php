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

require_once(_FULLPATH."/classes/class_myJAM_DB.php");
require_once(_FULLPATH."/classes/class_myJAM_Project.php");
require_once(_FULLPATH."/fxn/CalcNormOver.php");
//o-------------------------------------------------------------------------------o
function CalcSUsByDate($Project, $date)
//o-------------------------------------------------------------------------------o
//IN
//$Project: myJAM_Project Object of Project to be calculated
//$date: date in EPOCHE
{
  if(!is_object($Project) || !is_scalar($Project->ID) || (integer)$Project->ID < 1)
  {
    die("myJAM>> FATAL ERROR 0x1686 in CalcSUsByDate");
  }
  $day = date("d", $date);
  $month = date("m", $date);
  $year = date("Y", $date);
  if(!checkdate($month, $day, $year))
  {
    die("myJAM>> FATAL ERROR 0x8d8d in CalcSUsByDate");
  }
  $db = new myJAM_DB();
  $sql = "SELECT tid, date, payment_amount, norm_costs, over_costs FROM Transactions";
  $sql .= " WHERE pid='".(int)$Project->ID."'";
  $sql .= " AND date<=FROM_UNIXTIME(".(int)$date.")";
  $sql .= " ORDER BY date";
  $res = $db->query($sql);
  $actual_su = 0.0;
  $old_date = "";
  foreach($res as $line)
  {
    $sql = "SELECT sum(actual_su) FROM Jobs WHERE pid='".(int)$Project->ID."'";
    $sql .= " AND (job_state='F' OR job_state='R')";
    $sql .= " AND date<='".mysql_real_escape_string($line["date"])."'";
    $sql .= " AND date<=FROM_UNIXTIME(".(int)$date.")";
    if($old_date != "")
    {
      $sql .= " AND date>'".mysql_real_escape_string($old_date)."'";
    }
    $his_sus = $db->query($sql);
    if ($db->num_rows() == 1 &&
        isset($his_sus[0]["sum(actual_su)"]) &&
        (float)$his_sus[0]["sum(actual_su)"] > 0.0)
    {
      $usedSUs = (float)$his_sus[0]["sum(actual_su)"];
    }
    else
    {
      $usedSUs = 0.0;
    }
    if($Project->Billable)
    {
      $actual_su -= $usedSUs;
      $result = CalcNormOver(-$usedSUs, $line["payment_amount"], $line["norm_costs"], $line["over_costs"]);
      $actual_su += sprintf("%.2f", $result["overrun_sus"]);
      $actual_su += sprintf("%.2f", $result["norm_sus"]);
    }
    else
    {
      $actual_su += $usedSUs;
    }
    $old_date = $line["date"];
  }
  $sql = "SELECT sum(actual_su) FROM Jobs WHERE pid='".(int)$Project->ID."'";
  $sql .= " AND (job_state='F' OR job_state='R')";
  $sql .= " AND date>'".mysql_real_escape_string($old_date)."'";
  $sql .= " AND date<=FROM_UNIXTIME(".(int)$date.")";
  $res = $db->query($sql);
  if($db->num_rows()==1)
  {
    if($Project->Billable)
    {
      $actual_su -= (float)$res[0]["sum(actual_su)"];
    }
    else
    {
      $actual_su += (float)$res[0]["sum(actual_su)"];
    }
  }
  return $actual_su;
}
