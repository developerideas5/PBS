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
require_once(_FULLPATH."/classes/class_myJAM_DB.php");
require_once(_FULLPATH."/classes/class_myJAM_Project.php");
require_once(_FULLPATH."/CloseWindow.php");
require_once(_FULLPATH."/fxn/CalcNormOver.php");
require_once(_FULLPATH."/includes/cmn_header.php");
echo '<html>'
.'<head>'
.'<link rel="shortcut icon" href="images/myJAM-icon.png" type="image/png" />'
.'<title>Application Info</title>'
.'<link rel="stylesheet" type="text/css" href="css/style.css">'
.'</head>'
.'<body style="margin: 5px;" onunload="opener.window.location.reload();">'
.'<table cellspacing="3" border="0" cellpadding="0" class="tabhead">'
.'<tr>'
.'<td>'
.'<table border="0" cellspacing="0" cellpadding="0" class="tabhead2">'
.'<tr>'
.'<td class="gradhead1"><img src="images/multigradient_left.png" alt="gradientGFX" /></td>'
.'<td style="width: 150px; height: 20px; background-image: url(images/gradient_slice_1.png);">'
.'<b>Application Info</b></td>'
.'<td class="gradhead5"><img src="images/multigradient_middle.png" alt="gradientGFX" /></td>'
.'<td style="background-image: url(images/gradient_slice_2.png); background-repeat: repeat-x;"></td>'
.'<td class="gradhead1"><img src="images/multigradient_right.png" alt="gradientGFX" /></td>'
.'</tr>'
.'</table>'
.'</td>'
.'</table>'
.'<p />'
.'<div style="border-style: solid; border-width: 1px;">';
if(!$ActiveUser->ADMIN){
  die("myJAM>> You are not allowed to access this page!!!");
}
if(!isset($_REQUEST["pid"]) || !is_numeric($_REQUEST["pid"]) || (integer)$_REQUEST["pid"] < 1){
  die("myJAM>> FATAL ERROR 0xb4fe in ReCalcSUs");
}
$Project = new myJAM_Project((integer)$_REQUEST["pid"]);
if(!is_object($Project) || !is_scalar($Project->ID) || $Project->ID < 1){
  die("myJAM>> FATAL ERROR 0x8780 in ReCalcSUs");
}
echo "<h3>Recalculating SUs for Project <b>".$Project->ID."::".$Project->Name."</b></h3></br>"
."<hr><p>";
//o-------------------------------------------------------------------------------o
//o Monthly List of used SUs                                                      o
//o-------------------------------------------------------------------------------o
echo '<b>Consumed SUs:</b><br>'
.'<table style="width:100%;">'
.'<thead>'
.'<tr bgcolor="#C0C0C0">'
.'<th>Date</th><th>SUs</th>'
.'</tr>'
.'</thead>'
.'<tbody>';
$db = new myJAM_DB();
$sql = "SELECT sum(actual_su), month(date), year(date) FROM Jobs WHERE pid='".(int)$Project->ID."'";
$sql .= " AND (job_state='F' OR date>0)";
$sql .= " GROUP BY year(date),month(date) ORDER BY year(date),month(date)";
$res = $db->query($sql);
$actual_su = 0.0;
foreach($res as $line)
{
  echo '<tr><td>';
  if(isset($line["month(date)"]) && isset($line["year(date)"]))
  {
    echo date("F Y", strtotime("01-".$line["month(date)"]."-".$line["year(date)"]));
  }
  else
  {
    echo "<i>Running</i>";
  }
  echo "</td>"
  ."<td style=\"text-align:right;\">";
  printf('%.2f', $line["sum(actual_su)"]);
  $actual_su += $line["sum(actual_su)"];
  echo "</td>"
  ."</tr>";
}
echo "<tr></tr>";
printf("<tr><td><b>Total</b></td><td style=\"text-align:right;\"><b>%.2f</b></td></tr>", $actual_su);
echo "</tbody>"
."</table>"
."<hr><p>";
$sql = "UPDATE Projects SET history_su='".(float)$actual_su."' WHERE pid='".(int)$Project->ID."'";
$db->query($sql);
//o-------------------------------------------------------------------------------o
//o Go through all Transactions and recalculate SUs before and after
//o-------------------------------------------------------------------------------o
if($Project->Billable)
{
  echo '<b>Transactions:</b><br>'
  .'<table style="width:100%;">'
  .'<thead>'
  .'<tr bgcolor="#C0C0C0">'
  .'<th>Date</th>'
  .'<th>Amount</th>'
  .'<th>Cost Model</th>'
  .'<th>SUs substracted</th>'
  .'<th>SUs added</th>'
  .'<th>Old SUs</th>'
  .'<th>New SUs</th>'
  .'</tr>'
  .'</thead>'
  .'<tbody>';
  $sql = "SELECT tid, date, payment_amount, norm_costs, over_costs FROM Transactions";
  $sql .= " WHERE pid='".(int)$Project->ID."'";
  $sql .= " ORDER BY date";
  $res = $db->query($sql);
  $actual_su = 0.0;
  $old_date = "";
  foreach($res as $line)
  {
    echo "<tr>"
    ."<td>".date("d.m.Y", strtotime($line["date"]))."</td>"
    ."<td style=\"text-align:right;\">".$line["payment_amount"]."</td>";
    $sql = "SELECT sum(actual_su) FROM Jobs WHERE pid='".(int)$Project->ID."'";
    $sql .= " AND (job_state='F' OR job_state='R')";
    $sql .= " AND date<='".mysql_real_escape_string($line["date"])."'";
    if($old_date != "")
    {
      $sql .= " AND date>'".mysql_real_escape_string($old_date)."'";
    }
    $his_sus = $db->query($sql);
    if ($db->num_rows() == 1 && isset($his_sus[0]["sum(actual_su)"]) && (float)$his_sus[0]["sum(actual_su)"] > 0.0)
    {
      $usedSUs = (float)$his_sus[0]["sum(actual_su)"];
    }
    else
    {
      $usedSUs = 0.0;
    }
    $actual_su -= $usedSUs;
    $sql = "UPDATE Transactions set old_sus='".sprintf("%.2f", (float)$actual_su)."'";
    echo "<td style=\"text-align:right;\">"
    ."Norm SUs: ".$line["norm_costs"]."<br>"
    ."Overrun SUs: ".$line["over_costs"]."<br>"
    ."</td>";
    $result = CalcNormOver(-$usedSUs, $line["payment_amount"], $line["norm_costs"], $line["over_costs"]);
    printf("<td style=\"text-align:right;\">%.2f<br>Costs: %.2f</td>", $result["overrun_sus"], $result["overrun_costs"]);
    printf("<td style=\"text-align:right;\">%.2f<br>Costs: %.2f</td>", $result["norm_sus"], $result["norm_costs"]);
    $sql .= ",over_su_substracted='".sprintf("%.2f", $result["overrun_sus"])."'";
    $sql .= ",norm_su_added='".sprintf("%.2f", $result["norm_sus"])."'";
    echo "<td style=\"text-align:right;\">".$actual_su."</td>";
    $actual_su += sprintf("%.2f", $result["overrun_sus"]);
    $actual_su += sprintf("%.2f", $result["norm_sus"]);
    echo "<td style=\"text-align:right;\">".$actual_su."</td>";
    $sql .= ",new_sus='".sprintf("%.2f", (float)$actual_su)."' WHERE tid='".(int)$line["tid"]."'";
    $db->query($sql);
    echo "</tr>";
    $old_date = $line["date"];
  }
  echo '</tbody>'
  .'</table>'
  .'<hr><p>';
} //if billable
echo '<b>Statement Of Account:</b><br>'
.'<table style="width:100%;">'
.'<thead>'
.'<tr bgcolor="#C0C0C0">'
.'<th>'
.'</th>'
.'<th>SUs</th>'
.'</tr>'
.'</thead>'
.'<tbody>'
;
if($Project->Billable)
{
  echo "<tr><td>SU credit after last transaction</td><td style=\"text-align:right;\">".$actual_su."</td></tr>";
  $sql = "SELECT sum(actual_su) FROM Jobs WHERE pid='".(int)$Project->ID."'";
  $sql .= " AND (job_state='F' OR job_state='R')";
  $sql .= " AND date>'".mysql_real_escape_string($old_date)."'";
  $res = $db->query($sql);
  if($db->num_rows()==1)
  {
    echo "<tr><td>SUs used after last transaction</td><td style=\"text-align:right;\">".$res[0]["sum(actual_su)"]."</td></tr>";
    $actual_su -= $res[0]["sum(actual_su)"];
  }
  echo "<tr></tr>";
}
echo "<tr><td><b>Actual SUs</b></td><td style=\"text-align:right;\"><b>".sprintf("%.2f",$actual_su)."</b></td><tr>"
."</tbody>
</table>";
$sql = "UPDATE Projects SET su='".(float)$actual_su."' WHERE pid='".(int)$Project->ID."'";
$db->query($sql);
echo '</div><p/>';
echo CloseWindow();
echo '</body></html>';
