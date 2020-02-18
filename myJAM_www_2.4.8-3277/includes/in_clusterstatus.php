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

//require_once(_FULLPATH."/access.php");
require_once(_FULLPATH."/classes/class_myJAM_Architecture.php");
require_once(_FULLPATH."/php-ofc-library/open_flash_chart_object.php");
require_once(_FULLPATH."/classes/class_myJAM_User.php");
require_once(_FULLPATH."/fxn/PrintOverview.php");
echo '<script type="text/javascript">';
if ($ActiveUser->ADMIN)
{
  echo 'var CST_ADMIN = 1;';
}
else
{
  echo 'var CST_ADMIN = 0;';
}
echo "var CST_JobsArchID = \"\";"
    ."var CST_CoresArchID = \"\";"
    ."</script>"
    ;
?>
<script type="text/javascript" src="js/tabcontent.js">
/***********************************************
* Dynamic Countdown script-  Dynamic Drive (http://www.dynamicdrive.com)
* This notice MUST stay intact for legal use
* Visit http://www.dynamicdrive.com/ for this script and 100s more.
***********************************************/
</script>
<script type="text/javascript" src="js/Pop_UserInfo.js"></script>
<script type="text/javascript" src="js/Pop_ProjectInfo.js"></script>
<?php
?>
<script type="text/javascript" src="js/clusterstatus.js"></script>
<?php
$Archs = new myJAM_Architecture();
$Chart_Running_Width = '';
$Chart_Load_Width = '';
if($Archs->nb == 1)
{
  $Chart_Running_Width .= '120px';
  $Chart_Load_Width .= '165px';
}
else if($Archs->nb == 2)
{
  $Chart_Running_Width .= '150px';
  $Chart_Load_Width .= '195px';
}
else if($Archs->nb == 3)
{
  $Chart_Running_Width .= '190px';
  $Chart_Load_Width .= '225px';
}
else if($Archs->nb == 4)
{
  $Chart_Running_Width .= '220px';
  $Chart_Load_Width .= '250px';
}
else if($Archs->nb >= 5)
{
  $Chart_Running_Width .= '230px';
  $Chart_Load_Width .= '260px';
}
?>
<div id="upper" class="transmaintable">
 <table class="full" cellpadding="0" cellspacing="0">
   <tr>
     <td class="leftie">
       <?php echo PrintOverview();?>
     </td>
     <td style="text-align:center;">
       <?php open_flash_chart_object($Chart_Running_Width, "300px", "charts/Chart_Running.php?data=1&links=1", false, "", "ChartRunning1" );?>
     </td>
     <td style="text-align:center;">
       <?php open_flash_chart_object($Chart_Running_Width, "300px", "charts/Chart_Running.php?data=2&links=1", false, "", "ChartRunning2" );?>
     </td>
     <td style="text-align:center;">
       <?php open_flash_chart_object($Chart_Load_Width, "300px", "charts/Chart_Running.php?data=3&links=1", false, "", "ChartRunning3" );?>
     </td>
   </tr>
 </table>
</div>
<div id="TabbedCharts" style="margin-top:8px">
  <ul id="charttabs" class="shadetabs">
    <li><a href="#" rel="ChartCont1">Top Users</a></li>
    <li><a href="#" rel="ChartCont2">Top Projects</a></li>
    <li><a href="#" rel="ChartCont3">Core Distribution</a></li>
<?php
?>
  </ul>
  <div class="tabvisual" style="width:1000px">
    <div id="ChartCont1" class="tabcontent" style="height:300px;">
      <div>
      <table class="full">
      <tr>
      <td class="tabletd2">
        <?php
          open_flash_chart_object("600px", "300px", "charts/Chart_DistUser.php?jobstate=R&type=3&data=2", false, "", "ChartUserDist" );
        ?>
      </td>
      <td class="tabletd2">
        <table>
          <tr>
          <td>
            <input type="radio"
                   name="UserDataSelect"
                  value="1"
                  onclick="CST_UserDistData=1;ReDraw_Jobs(CST_JobsArchID);" /> SUs &nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio"
                   name="UserDataSelect"
                  value="2"
                  checked="checked"
                  onclick="CST_UserDistData=2;ReDraw_Jobs(CST_JobsArchID);" /> Jobs
          </td>
          </tr>
          <tr>
            <td>
              <input type="button" class="inputfont" value="All Architectures" onclick="ReDraw_Jobs('');" />
            </td>
          </tr>
        </table>
      </td>
      </tr>
      </table>
      </div>
    </div>
    <div id="ChartCont2" class="tabcontent" style="height:300px;position:relative;top:-300px;">
      <div>
      <table class="full">
      <tr>
      <td class="tabletd2">
        <?php
          open_flash_chart_object("600px", "300px", "charts/Chart_DistProj.php?jobstate=R&data=2&type=3", false, "", "ChartProjDist" );
        ?>
      </td>
      <td class="tabletd2">
        <table>
          <tr>
          <td>
            <input type="radio"
                   name="ProjDataSelect"
                  value="1"
                  onclick="CST_ProjDistData=1;ReDraw_Jobs(CST_JobsArchID);" /> SUs &nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio"
                   name="ProjDataSelect"
                  value="2"
                  checked="checked"
                  onclick="CST_ProjDistData=2;ReDraw_Jobs(CST_JobsArchID);" /> Jobs
          </td>
          </tr>
          <tr>
            <td>
              <input type="button" class="inputfont" value="All Architectures" onclick="ReDraw_Jobs('');" />
            </td>
          </tr>
        </table>
      </td>
      </tr>
      </table>
      </div>
    </div>
    <div id="ChartCont3" class="tabcontent" style="height:300px;position:relative;top:-600px;">
      <div>
      <table class="full">
      <tr>
      <td class="tabletd2">
        <?php
          if ($ActiveUser->ADMIN)
            {open_flash_chart_object("800px", "300px", "charts/Chart_BarsDistCoresLOG2.php?jobstate=R&links=1", false, "", "ChartCoreDist" );}
          else
            {open_flash_chart_object("800px", "300px", "charts/Chart_BarsDistCoresLOG2.php?jobstate=R", false, "", "ChartCoreDist" );}
        ?>
      </td>
      <td class="tabletd2">
        <table>
          <tr>
          <td>
            <input type="radio"
                   name="CoresDataSelect"
                  value="1"
                  onclick="CST_CoresDistData=1;ReDraw_Cores(CST_CoresArchID);" /> SUs &nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio"
                   name="CoresDataSelect"
                  value="2"
                  checked="checked"
                  onclick="CST_CoresDistData=2;ReDraw_Cores(CST_CoresArchID);" /> Jobs
          </td>
          </tr>
          <tr>
            <td>
              <input type="button" class="inputfont" value="All Architectures" onclick="ReDraw_Cores('');" />
            </td>
          </tr>
        </table>
      </td>
      </tr>
      </table>
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
var TabbedCharts=document.getElementById("TabbedCharts");
TabbedCharts.style.width=window.innerWidth-215+"px";
</script>
<div id="tabpos">
</div>
