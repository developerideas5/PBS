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
?>
<script type="text/javascript" src="js/Pop_UserInfo.js"></script>
<script type="text/javascript" src="js/Pop_ProjectInfo.js"></script>
<script type="text/javascript" src="js/Pop_JobsInfo.js"></script>
<script type="text/javascript" src="js/HistoryController.js"></script>
<?php
?>
<script type="text/javascript" src="js/tabcontent.js">
/***********************************************
* Dynamic Countdown script-  Dynamic Drive (http://www.dynamicdrive.com)
* This notice MUST stay intact for legal use
* Visit http://www.dynamicdrive.com/ for this script and 100s more.
***********************************************/
</script>
<script type="text/javascript">
//function for toggling pulldown-menus
//o-------------------------------------------------------------------------------o
function ToggleDiv(DivID)
//o-------------------------------------------------------------------------------o
{
  var el = window.document.getElementById(DivID);
  if (el.style.display == "")
  {
    el.style.display = "none";
    if(el.id=='ArchContent')
    {
     var iframeobj=window.document.getElementById("objoverflash");
     if(iframeobj)
     {
       iframeobj.style.display="none";
     }
    }
  }
  else
  {
     el.style.display = "";
     /* Architecture-Div */
    if(el.id=='ArchContent')
     {
      var iframeobj=window.document.getElementById("objoverflash");
      if(iframeobj)
      {
        iframeobj.style.display="block";
      }
    }
  }
}
//o-------------------------------------------------------------------------------o
function UpdateChartMonth()
//o-------------------------------------------------------------------------------o
{
  var ChartMonthForm = window.document.getElementById("ChartMonthSelector");
  if (ChartMonthForm.JobChartSel[0].checked)
  {
    window.document.getElementById("TabSUs").style.display="";
    ReDrawCharts();
  }
  else
    {window.document.getElementById("TabSUs").style.display="none";}
  if (ChartMonthForm.JobChartSel[1].checked)
  {
    window.document.getElementById("TabJobs").style.display="";
    ReDrawCharts();
  }
  else
    {window.document.getElementById("TabJobs").style.display="none";}
}
//init image-objects for buttons
//o-------------------------------------------------------------------------------o
var button_left_norm = new Image();
button_left_norm.src = "images/Button_left_norm.png";
var button_left_active = new Image();
button_left_active.src = "images/Button_left_active.png";
var button_right_norm = new Image();
button_right_norm.src = "images/Button_right_norm.png";
var button_right_active = new Image();
button_right_active.src = "images/Button_right_active.png";
//o-------------------------------------------------------------------------------o
</script>
<script type="text/javascript">
if(document.getElementById("objoverflash"))
{
 document.getElementById("objoverflash").style.display="none";
}
</script>
<?php
require_once("php-ofc-library/open_flash_chart_object.php");
require_once("classes/class_myJAM_Architecture.php");
require_once("classes/class_myJAM_Project.php");
require_once("classes/class_myJAM_User.php");
require_once("classes/class_myJAM_DB.php");
//o-------------------------------------------------------------------------------o
function GenArchButtons()
//o-------------------------------------------------------------------------------o
{
  $Archs = new myJAM_Architecture();
  echo "<form action=\"\">"
  ."<table><tr><td><span class=\"fat\">Architectures</span></td></tr>"
  ."<tr>"
  ."<td>"
  ."<select size=\"6\" multiple=\"multiple\" onchange=\"ArchSelect();\" name=\"ArchSelector\" id=\"ArchSelector\" class=\"inputfont\">"
  ;
  $ArchIDList = $Archs->IDList;
  foreach($ArchIDList as $ArchID)
  {
    echo "<option selected=\"selected\" class=\"inputfont\" value=\"".$ArchID."\">"
          .$Archs->Name[$ArchID]
          ."</option>"
          ;
  }
  echo "</select>"
       ."</td>"
       ."</tr>"
       ."</table>"
       ."</form>"
       ;
}
//o-------------------------------------------------------------------------------o
function GenProjSelect()
//o-------------------------------------------------------------------------------o
{
  $Projects = new myJAM_Project();
  echo "<form action=\"\">"
      ."<table><tr><td><span class=\"fat\">Projects</span></td></tr>"
      ."<tr><td><select class=\"inputfont\" name=\"ProjSelector\" id=\"ProjSelector\" size=\"1\" onchange=\"ProjSelect();\">"
      ."<option class=\"inputfont\" value=\"\">---ALL---</option>"
      ;
  $ProjNameList = $Projects->Name;
  foreach($ProjNameList as $ProjID => $ProjName)
  {
    echo "<option class=\"inputfont\" value=\""
          .$ProjID . "\">"
          .$ProjName . "</option>"
          ;
  }
  echo "</select></td></tr>"
  . "</table>"
  ."</form>"
  ;
}
//o-------------------------------------------------------------------------------o
function GenSortedProjSelect()
//o-------------------------------------------------------------------------------o
{
  $Projects = new myJAM_Project();
  $ProjList = array();
  $ProjIDList = $Projects->ID;
  foreach($ProjIDList as $pid)
  {
    $ProjList[$pid] = 0;
  }
  $db = new myJAM_DB();
  $epoch = mktime(date('H'), date('i'), date('s'), date('m')-6 , date('d'), date('Y'));
  $sql = "SELECT pid, count(pid)".
         " FROM Jobs".
         " WHERE job_state='F'".
         " AND pid!=''".
         " AND date>=FROM_UNIXTIME(".(int)$epoch.")".
         " GROUP BY pid".
         " ORDER BY count(pid) DESC;";
  $vCounts = $db->query($sql);
  if (!preg_match('/Firefox(?:\/| )(\S+)/', $_SERVER['HTTP_USER_AGENT']))
  {
    $selectsize = 21;
  }
  else
  {
    $selectsize = 27;
  }
  $out = '<div>'
          .'<select class="inputfont"'
                 .' id="ProjMonthSelector"'
                 .' onchange="ReDrawCharts(\'Chart_MonthProject\')"'
                 .' size="' . $selectsize . '"'
                 .' multiple="multiple"'
                 .' style="width:200px">';
  $nbSelect = 5;
  $ProjSelectStr = '';
  foreach($vCounts as $counts)
  {
    $out .= '<option class="inputfont" value="'.$counts['pid'].'"';
    if($nbSelect > 0)
    {
      $out .= ' selected="selected"';
      $ProjSelectStr .= $counts['pid'].'p';
      $nbSelect--;
    }
    $out .= '>'
             .$Projects->Name[$counts['pid']]
             .' (' . $counts['count(pid)'] . ')'
           .'</option>';
    unset($ProjList[$counts['pid']]);
  }
  foreach($ProjList as $pid => $count)
  {
    $out .= '<option class="inputfont"'
                  .' value="'.$pid.'">'
             .$Projects->Name[$pid].'(0)'
           .'</option>';
  }
  $out .= '</select></div>';
  return array('ProjSelectStr' => $ProjSelectStr,
               'Selector' => $out);
}
//o-------------------------------------------------------------------------------o
function GenSortedUserSelect()
//o-------------------------------------------------------------------------------o
{
  $Users = new myJAM_User();
  $UserList = array();
  $UserIDList = $Users->ID;
  foreach($UserIDList as $uid)
  {
    $UserList[$uid] = 0;
  }
  $db = new myJAM_DB();
  $epoch = mktime(date('H'), date('i'), date('s'), date('m')-6 , date('d'), date('Y'));
  $sql ="SELECT uid, count(uid)".
        " FROM Jobs".
        " WHERE job_state='F'".
        " AND uid!=''".
        " AND date>=FROM_UNIXTIME(".(int)$epoch.")".
        " AND uid NOT IN (SELECT uid FROM Users WHERE real_username LIKE 'formerUser%')". //MYJAM-490
        " GROUP BY uid".
        " ORDER BY count(uid) DESC;";
  $vCounts = $db->query($sql);
  if (!preg_match('/Firefox(?:\/| )(\S+)/', $_SERVER['HTTP_USER_AGENT']))
  {
    $selectsize = 21;
  }
  else
  {
    $selectsize = 27;
  }
  $out = '<div>'
          .'<select class="inputfont"'
                 .' id="UserMonthSelector"'
                 .' onchange="ReDrawCharts(\'Chart_MonthUser\')"'
                 .' size="' . $selectsize . '"'
                 .' multiple="multiple"'
                 .' style="width:200px">';
  $nbSelect = 5;
  $UserSelectStr = '';
  foreach($vCounts as $counts)
  {
    $out .= '<option class="inputfont"'
                  .' value="'.$counts['uid'].'"';
    if($nbSelect > 0)
    {
      $out .= ' selected="selected"';
      $UserSelectStr .= $counts['uid'].'u';
      $nbSelect--;
    }
    $out .= '>'
              .$Users->FullName[$counts['uid']].' (' . $counts['count(uid)'] . ')'
           .'</option>';
    unset($UserList[$counts['uid']]);
  }
  foreach($UserList as $uid=>$count)
  {
    $out .= '<option class="inputfont"'
                  .' value="'.$uid.'">'
              .$Users->FullName[$uid].' (0)'
           .'</option>';
  }
  $out .= '</select></div>';
  return array('UserSelectStr' => $UserSelectStr,
               'Selector' => $out);
}
//o-------------------------------------------------------------------------------o
function GenUserSelect()
//o-------------------------------------------------------------------------------o
{
  $Users = new myJAM_User();
  echo "<form action=\"\">"
  ."<table><tr><td><span class=\"fat\">Users</span></td></tr>"
  ."<tr><td><select class=\"inputfont\" name=\"UserSelector\" id=\"UserSelector\" size=\"1\" onchange=\"UserSelect();\">"
  ."<option class=\"inputfont\" value=\"\">---ALL---</option>"
  ;
  $UserFullNameList = $Users->FullName;
  foreach($UserFullNameList as $UserID => $FullName)
  {
    echo "<option class=\"inputfont\" value=\"" . $UserID . "\">" . $FullName . "</option>";
  }
  echo "</select></td></tr>"
  . "</table>"
  . "</form>"
  ;
}
?>
<div id="Calender" style="width:290px;position:absolute;top:80px;left:180px;z-index:10;background:#ffffff">
<div id="CalHeader" onclick="ToggleDiv('CalContent');"
 class="calheaderdiv" style="cursor: pointer;"><span class="fat">Date
Control</span></div>
<div id="CalContent"
 style="width: 100%; display: none; border-width: 1px; border-color:#ff0000; border-style: solid;">
<table class="full">
 <tr>
  <td>
  <form action="" id="FromCalener">
  <table class="calform" cellspacing="0" cellpadding="0">
   <tr>
    <td align="center" colspan="3"><span class="fat">From:</span></td>
   </tr>
   <tr>
    <td><img alt="leftbutton" src="images/Button_left_norm.png"
     onclick="FromYear--;UpdateYear();"
     onmouseover="this.src = button_left_active.src;"
     onmouseout="this.src = button_left_norm.src;" /></td>
    <td id="FromYear" align="center" valign="middle" class="fromyearid">
    </td>
    <td><img alt="rightbutton" src="images/Button_right_norm.png"
     onclick="FromYear++;UpdateYear();"
     onmouseover="this.src = button_right_active.src;"
     onmouseout="this.src = button_right_norm.src;" /></td>
   </tr>
   <tr>
    <td><img alt="leftbutton" src="images/Button_left_norm.png"
     onclick="FromMonth--;UpdateMonth();"
     onmouseover="this.src = button_left_active.src;"
     onmouseout="this.src = button_left_norm.src;" /></td>
    <td id="FromMonth" align="center" valign="middle" class="fromyearid">
    </td>
    <td><img alt="rightbutton" src="images/Button_right_norm.png"
     onclick="FromMonth++;UpdateMonth();"
     onmouseover="this.src = button_right_active.src;"
     onmouseout="this.src = button_right_norm.src;" /></td>
   </tr>
  </table>
  </form>
  </td>
  <td>&nbsp;</td>
  <td>
  <form action="" id="ToCalener">
  <table class="tocalendartable" cellspacing="0" cellpadding="0">
   <tr>
    <td align="center" colspan="3"><span class="fat">To:</span></td>
   </tr>
   <tr>
    <td><img alt="leftbutton" src="images/Button_left_norm.png"
     onclick="ToYear--;UpdateYear();"
     onmouseover="this.src = button_left_active.src;"
     onmouseout="this.src = button_left_norm.src;" /></td>
    <td id="ToYear" align="center" valign="middle" class="fromyearid"></td>
    <td><img alt="rightbutton" src="images/Button_right_norm.png"
     onclick="ToYear++;UpdateYear();"
     onmouseover="this.src = button_right_active.src;"
     onmouseout="this.src = button_right_norm.src;" /></td>
   </tr>
   <tr>
    <td><img alt="leftbutton" src="images/Button_left_norm.png"
     onclick="ToMonth--;UpdateMonth();"
     onmouseover="this.src = button_left_active.src;"
     onmouseout="this.src = button_left_norm.src;" /></td>
    <td id="ToMonth" align="center" valign="middle" class="fromyearid">
    </td>
    <td><img alt="rightbutton" src="images/Button_right_norm.png"
     onclick="ToMonth++;UpdateMonth();"
     onmouseover="this.src = button_right_active.src;"
     onmouseout="this.src = button_right_norm.src;" /></td>
   </tr>
  </table>
  </form>
  </td>
 </tr>
</table>
</div>
</div>
<div id="Archs" style="width:150px;position:absolute;top:80px;left:480px;z-index:10;background:#ffffff">
<div id="ArchHeader" class="clustheader" style="cursor: pointer;"
 onclick="ToggleDiv('ArchContent');"><span class="fat">Architectures</span></div>
<!-- iframe src="javascript:return;"
                style="background-color:#aaaaaa;position: absolute; top: 110px; left: 120px; display: none; width: 102px; height: 32px; z-index: 5;"
                id="iframe"
                frameborder="0"
                scrolling="no">
  </iframe --> <object data="empty.php" type="text/html"
 declare="declare"
 style="background-color: #ffffff; position: absolute; top: 16px; left: 0px; display: none; width: 152px; height: 130px;"
 id="objoverflash"> </object>
<div id="ArchContent"
 style="width: 100%; display: none; border-width: 1px; border-color: #ff0000; position: absolute; top: 16px; left: 0px; height: 128px; width: 150px; border-style: solid;">
    <?php GenArchButtons(); ?>
  </div>
</div>
<div id="Projects" style="width:200px;position:absolute;top:80px;left:640px;z-index:10;background:#ffffff">
<div id="ProjHeader" class="clustheader" style="cursor: pointer;"
 onclick="ToggleDiv('ProjContent');"><span class="fat">Projects</span></div>
<div id="ProjContent"
 style="width: 100%; display: none; border-width: 1px; border-color: #ff0000; border-style: solid;">
    <?php GenProjSelect(); ?>
  </div>
</div>
<div id="User" style="width:200px;position:absolute;top:80px;left:850px;z-index:10;background:#ffffff">
<div id="UserHeader" class="clustheader" style="cursor: pointer;"
 onclick="ToggleDiv('UserContent');"><span class="fat">Users</span></div>
<div id="UserContent"
 style="width: 100%; display: none; border-width: 1px; border-color: #ff0000; border-style: solid;">
    <?php GenUserSelect(); ?>
  </div>
</div>
<script type="text/javascript">
InitCal();
</script>
<div style="width:100%;height:30px;"></div>
<div id="TabbedCharts" style="width:98%;z-index:1;">
<ul id="charttabs" class="shadetabs">
 <li><a href="#" rel="ChartCont1" class="selected">Jobs / SUs</a></li>
 <li><a href="#" rel="ChartCont2">Projects</a></li>
 <li><a href="#" rel="ChartCont3">Users</a></li>
 <li><a href="#" rel="ChartCont4">Departments</a></li>
 <li><a href="#" rel="ChartCont5">Institutes</a></li>
<?php
?>
</ul>
<div
 style="border: 1px solid gray; width: 100%; margin-bottom: 1em; padding: 10px; height: 500px;">
<div id="ChartCont1" class="tabcontent" style="height: 500px;">
<table class="full">
 <tr>
  <td colspan="2">
  <form action="" id="ChartMonthSelector">
  <div><input type="checkbox" name="JobChartSel" checked="checked"
   onchange="UpdateChartMonth();" />Show SUs <input type="checkbox"
   name="JobChartSel" checked="checked" onchange="UpdateChartMonth();" />Show
  Jobs</div>
  </form>
  </td>
 </tr>
 <tr>
  <td id="TabSUs">
            <?php open_flash_chart_object("100%", "470px", "charts/Chart_DistArchs.php?data=1&type=1", false, "", "Chart_MonthSUs"); ?>
          </td>
  <td id="TabJobs">
            <?php open_flash_chart_object("100%", "470px", "charts/Chart_DistArchs.php?data=2&type=2", false, "", "Chart_MonthJobs"); ?>
          </td>
 </tr>
</table>
</div>
<div id="ChartCont2" class="tabcontent"
 style="height: 500px; position: relative; top: -500px;">
<table class="seventy">
 <tr>
  <td class="middle"><input type="radio" name="MonthProject_Select"
   value="MonthProject_SUs" checked="checked"
   onclick="ProjMonthsData=1;ReDrawCharts('Chart_MonthProject');" /> SUs
  <input type="radio" name="MonthProject_Select"
   value="MonthProject_Jobs"
   onclick="ProjMonthsData=2;ReDrawCharts('Chart_MonthProject');" />
  Jobs</td>
  <td>&nbsp;&nbsp;&nbsp;</td>
  <td class="middle"><input type="radio" name="MonthProject_Style"
   value="MonthProject_Bars"
   onclick="ProjMonthsType=1;ReDrawCharts('Chart_MonthProject');" />
  Bars <input type="radio" name="MonthProject_Style"
   value="MonthProject_Lines" checked="checked"
   onclick="ProjMonthsType=2;ReDrawCharts('Chart_MonthProject');" />
  Lines <input type="radio" name="MonthProject_Style"
   value="MonthProject_Cumulative_Pie"
   onclick="ProjMonthsType=3;ReDrawCharts('Chart_MonthProject');" />
  Cumulative Pie <input type="radio" name="MonthProject_Style"
   value="MonthProject_Cumulative_Bars"
   onclick="ProjMonthsType=4;ReDrawCharts('Chart_MonthProject');" />
  Cumulative Bars</td>
  <td>&nbsp;</td>
  <td class="middle">Show Top: <input class="inputfont" type="text"
   id="Proj_nbTops" size="4" maxlength="3" value="10"
   disabled="disabled" onchange="ReDrawCharts('Chart_MonthProject');" />
  </td>
 </tr>
</table>
<?php
$proj = GenSortedProjSelect();
echo '<table class="full">'
      .'<tr>'
        .'<td class="eighty" id="here_comes_the_chart">';
open_flash_chart_object("98%", "470px",
                        "charts/Chart_MonthProject.php?data=1&type=2&projids=".$proj['ProjSelectStr'],
                        false,
                        "",
                        "Chart_MonthProject");
echo '</td>'
        .'<td valign="top"><b>Project Select-O-Mat</b><br />'
          .$proj['Selector']
        .'</td>'
      .'</tr>'
    .'</table>';
?>
</div>
<div id="ChartCont3" class="tabcontent"
 style="height: 500px; position: relative; top: -1000px;">
<table class="seventy">
 <tr>
  <td class="middle"><input type="radio" name="MonthUser_Select"
   value="MonthUser_SUs" checked="checked"
   onclick="UserMonthsData=1;ReDrawCharts('Chart_MonthUser');" /> SUs <input
   type="radio" name="MonthUser_Select" value="MonthUser_Jobs"
   onclick="UserMonthsData=2;ReDrawCharts('Chart_MonthUser');" /> Jobs</td>
  <td>&nbsp;&nbsp;&nbsp;</td>
  <td class="middle"><input type="radio" name="MonthUser_Style"
   value="MonthUser_Bars"
   onclick="UserMonthsType=1;ReDrawCharts('Chart_MonthUser');" /> Bars <input
   type="radio" name="MonthUser_Style" value="MonthUser_Lines"
   checked="checked"
   onclick="UserMonthsType=2;ReDrawCharts('Chart_MonthUser');" /> Lines
  <input type="radio" name="MonthUser_Style"
   value="MonthUser_Cumulative_Pie"
   onclick="UserMonthsType=3;ReDrawCharts('Chart_MonthUser');" />
  Cumulative Pie <input type="radio" name="MonthUser_Style"
   value="MonthUser_Cumulative_Bars"
   onclick="UserMonthsType=4;ReDrawCharts('Chart_MonthUser');" />
  Cumulative Bars</td>
  <td>&nbsp;</td>
  <td class="middle">Show Top:<input class="inputfont" type="text"
   id="User_nbTops" size="4" maxlength="3" value="10"
   disabled="disabled" onchange="ReDrawCharts('Chart_MonthUser');" /></td>
 </tr>
</table>
<?php
$user = GenSortedUserSelect();
echo '<table class="full">'
      .'<tr>'
        .'<td class="eighty" id="Chart_Users">';
open_flash_chart_object('98%', '470px',
                        'charts/Chart_MonthUser.php?data=1&type=2&userids='.$user['UserSelectStr'],
                        false,
                        '',
                        'Chart_MonthUser');
echo '</td>'
        .'<td valign="top"><span class="fat">User Select-O-Mat</span><br />'
          .$user['Selector']
        .'</td>'
      .'</tr>'
    .'</table>';
?>
</div>
<div id="ChartCont4" class="tabcontent"
  style="height: 500px; position: relative; top: -1500px;">
<table class="seventy">
 <tr>
  <td style="vertical-align: middle;"><input type="radio"
   name="Dep_Select" value="Dep_SUs" checked="checked"
   onclick="DepData=1;ReDrawCharts('Chart_Dep');" /> SUs <input
   type="radio" name="Dep_Select" value="Dep_Jobs"
   onclick="DepData=2;ReDrawCharts('Chart_Dep');" /> Jobs</td>
  <td>&nbsp;&nbsp;&nbsp;</td>
  <td class="middle"><input type="radio" name="Dep_Style"
   value="Dep_Cumulative_Pie" checked="checked"
   onclick="DepType=3;ReDrawCharts('Chart_Dep');" /> Cumulative Pie <input
   type="radio" name="Dep_Style" value="Dep_Cumulative_Bars"
   onclick="DepType=4;ReDrawCharts('Chart_Dep');" /> Cumulative Bars</td>
  <td>&nbsp;</td>
  <td class="middle">Show Top:<input class="inputfont" type="text"
   id="Dep_nbTops" size="4" maxlength="3" value="10"
   onchange="ReDrawCharts('Chart_Dep');" /></td>
 </tr>
</table>
<div id="Chart_DepChart">
        <?php open_flash_chart_object("100%", "470px", "charts/Chart_DistDeps.php?data=1&type=3", false, "", "Chart_Dep"); ?>
      </div>
</div>
<div id="ChartCont5" class="tabcontent"
  style="height: 500px; position: relative; top: -2000px;">
<table class="seventy">
 <tr>
  <td style="vertical-align: middle;"><input type="radio"
   name="Inst_Select" value="Inst_SUs" checked="checked"
   onclick="InstData=1;ReDrawCharts('Chart_Inst');" /> SUs <input
   type="radio" name="Inst_Select" value="Inst_Jobs"
   onclick="InstData=2;ReDrawCharts('Chart_Inst');" /> Jobs</td>
  <td>&nbsp;&nbsp;&nbsp;</td>
  <td class="middle"><input type="radio" name="Inst_Style"
   value="Inst_Cumulative_Pie" checked="checked"
   onclick="InstType=3;ReDrawCharts('Chart_Inst');" /> Cumulative Pie <input
   type="radio" name="Inst_Style" value="Inst_Cumulative_Bars"
   onclick="InstType=4;ReDrawCharts('Chart_Inst');" /> Cumulative Bars</td>
  <td>&nbsp;</td>
  <td class="middle">Show Top:<input class="inputfont" type="text"
   id="Inst_nbTops" size="4" maxlength="3" value="10"
   onchange="ReDrawCharts('Chart_Inst');" /></td>
 </tr>
</table>
<div id="Chart_InstChart">
        <?php open_flash_chart_object("100%", "470px", "charts/Chart_DistInst.php?data=1&type=3", false, "", "Chart_Inst"); ?>
      </div>
</div>
<?php
?>
</div>
</div>
<script type="text/javascript">
//Init the Tabs
var TCharts=new ddtabcontent("charttabs");
TCharts.setpersist(true);
TCharts.setselectedClassTarget("link");
TCharts.init();
</script>
<div id="tabpos"></div>
