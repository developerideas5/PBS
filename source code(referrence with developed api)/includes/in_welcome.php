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

require_once(_FULLPATH.'/access.php');
require_once(_FULLPATH.'/ColorGrd.php');
require_once(_FULLPATH.'/MultiGrd.php');
require_once(_FULLPATH.'/fxn/CalcWaiting.php');
require_once(_FULLPATH.'/classes/class_myJAM_User.php');
require_once(_FULLPATH.'/classes/class_myJAM_Project.php');
require_once(_FULLPATH.'/classes/class_myJAM_Jobs.php');
require_once(_FULLPATH.'/classes/class_myJAM_Announcement.php');
require_once(_FULLPATH.'/php-ofc-library/open_flash_chart_object.php');
?>
<script type="text/javascript" src="js/Pop_JobDetails.js"></script>
<?php
$ProjectTables = "<p></p>";
$ProjectList = $ActiveUser->Projects;
foreach($ProjectList as $project)
{
  if($ActiveUser->ID == $project->Owner->ID || $ActiveUser->ADMIN)
  {
    $user_statement = '';
  }
  else
  {
    $user_statement = ",".$ActiveUser->ID;
  }
  $ProjectTables .= "<table class=\"full\" cellpadding=\"0\" cellspacing=\"0\">";
  $ProjectTables .= "<tr>";
  $ProjectTables .= "<td colspan=\"2\">";
  $ProjectTables .= "<div class=\"leftie\"><a class=\"stat\" href=\"main.php?page=projects&amp;mode=info&amp;pid=".$project->ID."&amp;module=user\"><img src=\"images/Project-24x24.png\" alt=\"project\" /></a> <a href=\"main.php?page=projects&amp;mode=info&amp;pid=".$project->ID."&amp;module=user\"><span class=\"fat\">".$project->Name."</span></a></div>";
  $ProjectTables .= "<hr/>";
  $ProjectTables .= "</td>";
  $ProjectTables .= "</tr>";
  $ProjectTables .= "<tr>";
  $ProjectTables .= "<td class=\"welc2\">";
  $ProjectTables .= "<div class=\"leftie\"><img src=\"images/Aqua-Ball-Green-8x8.png\" alt=\"aquaball\" /> Used SUs: ".$project->SUs."<br/>";
  $ProjectTables .= "<img src=\"images/Aqua-Ball-Green-8x8.png\" alt=\"aquaball\" /> Jobs finished: ";
  $ProjectTables .= "<a href=\"\" onclick=\"pop_JobDetails('".$project->ID."','F'".$user_statement.");\">".$project->Finished."</a><br/>";
  $ProjectTables .= "</td>";
  $ProjectTables .= "<td class=\"welc2\">";
  $ProjectTables .= "<div class=\"leftie\"><img src=\"images/Aqua-Ball-Green-8x8.png\" alt=\"aquaball\" /> Jobs running: ";
  $ProjectTables .= "<a href=\"\" onclick=\"pop_JobDetails('".$project->ID."','R'".$user_statement.");\">".$project->Running."</a><br/>";
  $ProjectTables .= "</td>";
  $ProjectTables .= "</tr>";
  $ProjectTables .= "<tr>";
  $ProjectTables .= "<td colspan=\"2\"><div class=\"right\">";
  $ProjectTables .= "<a class=\"stat\" href=\"main.php?page=history&amp;pid=".$project->ID."&amp;action=displaygraphs\"><img src=\"images/Column-Chart-24x24.png\" alt=\"coloumnchart\" /></a> <a href=\"main.php?page=history&amp;pid=".$project->ID."&amp;action=displaygraphs\"><span class=\"fat\">Statistics</span></a></div>";
  $ProjectTables .= "</td>";
  $ProjectTables .= "</tr>";
  $ProjectTables .= "</table>";
}
$announcement = '';
$Ann = new myJAM_Announcement(5);
$first=true;
foreach($Ann->ID as $id)
{
  $announcement .= '('.date('Y-m-d H:i',$Ann->Date[$id]).')';
  $announcement .= ' <a onclick="myJAM_toggle_display(\'ann_'.$id.'\');" href="#" style="margin-right:15px;margin-top:10px;">'.$Ann->Title[$id];
  $announcement .= '</a>';
  $announcement .= '<p/>';
  $announcement .= '<p id="ann_'.$id.'" style="display:'.(($first)?'block':'none').';background-color:lightgray;margin-left:5px;margin-right:5px;border:1px solid black;padding:5px;">';
  $announcement .= html_entity_decode(nl2br($Ann->Content[$id]))."</p>";
  $first=false;
}
echo '<table class="tabhead" cellspacing="3" cellpadding="0">'
.'<tr>'
.'<td>'
.'<table cellspacing="0" cellpadding="0" class="tabhead2">'
.'<tr>'
.'<td class="gradhead1">'
.'<img src="images/multigradient_left.png" alt="gradientGFX"/>'
.'</td>'
.'<td class="gradhead4">'
.'<span class="fat">Announcements</span>'
.'</td>'
.'<td class="gradhead5">'
.'<img src="images/multigradient_middle.png" alt="gradientGFX"/>'
.'</td>'
.'<td class="gradhead6">'
.'</td>'
.'<td class="gradhead1">'
.'<img src="images/multigradient_right.png" alt="gradientGFX"/>'
.'</td>'
.'</tr>'
.'</table>'
.$announcement
.'</td>'
.'</tr>'
.'</table>'
.'<p/>'
.'<table class="tabhead" cellspacing="3" cellpadding="0">'
.'<tr>'
.'<td align="center"> '
.'<table cellspacing="0" cellpadding="0" class="tabhead2">'
.'<tr>'
.'<td class="gradhead1">'
.'<img src="images/multigradient_left.png" alt="gradientGFX"/>'
.'</td>'
.'<td class="gradhead4">'
.'<span class="fat">'
;
if ($ActiveUser->UserName != 'admin')
{
  echo "Active Projects";
}
else
{
  echo "Current Activities";
}
echo '</span>'
.'</td>'
.'<td class="gradhead5">'
.'<img src="images/multigradient_middle.png" alt="gradientGFX"/>'
.'</td>'
.'<td class="gradhead6">'
.'</td>'
.'<td class="gradhead1">'
.'<img src="images/multigradient_right.png" alt="gradientGFX" />'
.'</td>'
.'</tr>'
.'</table>'
;
//< This is the main table which contains all other tables -->
if($ActiveUser->UserName!='admin')
{
  echo "<table class=\"full\" cellspacing=\"0\">"
  ."<tr>"
  ."<td class=\"welc2\">"
  ."<table id=\"welcome\" cellpadding=\"0\" cellspacing=\"0\" class=\"full\">"
  ."<tr>"
  ."<td>"
  .$ProjectTables
  ."</td>"
  ."</tr>"
  ."</table>"
  ."</td>"
  ."<td class=\"tabletd2\">"
  ."<table class=\"full\">"
  ."<tr>"
  ."<td class=\"tabletd2\">"
  ."<div class=\"centr\">"
  ;
  open_flash_chart_object("75%","200","charts/Chart_DistArchs.php?data=1&type=1&userids=".$ActiveUser->ID,false,"","userchart1");
  echo "</div>"
  ."<hr/>"
  ."</td>"
  ."</tr>"
  ."<tr>"
  ."<td class=\"tabletd2\">"
  ."<div class=\"centr\">"
  ;
  open_flash_chart_object("75%","200","charts/Chart_DistArchs.php?data=2&type=2&userids=".$ActiveUser->ID,false,"","userchart2");
  echo "</div>"
  ."</td>"
  ."</tr>"
  ."</table>"
  ."</td>"
  ."</tr>"
  ."</table>"
  ;
 } else {
  echo "<div class=\"welc3\">"
  ;
  open_flash_chart_object("75%","250","charts/Chart_DistArchs.php?data=1&nbmonths=6&type=1&select=1",false,"","adminchart1");
  echo "</div>"
  ."<hr/>"
  ."<div class=\"welc3\">"
  ;
  open_flash_chart_object("75%","250","charts/Chart_DistArchs.php?data=2&nbmonths=6&type=2&select=5",false,"","adminchart2");
  echo "</div>"
  ;
 }
echo '</td></tr></table><div id="tabpos" class="tabposstyle"></div>';
