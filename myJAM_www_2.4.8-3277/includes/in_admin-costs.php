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

require_once(_FULLPATH."/access.php");
require_once(_FULLPATH."/classes/class_myJAM_User.php");
require_once(_FULLPATH."/classes/class_myJAM_CostModel.php");
$User = new myJAM_User($_SESSION["uid"]);
if (is_array($User->ID))
{
die("myJAM>> FATAL ERROR CJ16 in module ADMIN COSTS");
}
if (! $User->ADMIN)
{
  die("myJAM>> ERROR K3CB: Access not allowed");
}
$CostModelList = new myJAM_CostModel();
if(isset($_GET['action']) && $_GET['action'] == "modify")
{
  echo "<form action=\"main.php?page=admin-costs&amp;action=update\" method=\"post\">";
}
if(isset($_GET['action']) && $_GET['action'] == "new")
{
  echo "<form action=\"main.php?page=admin-costs&amp;action=insert\" method=\"post\">";
}
echo "<table class=\"table1\" cellpadding=\"2\" cellspacing=\"2\">";
//o-------------------------------------------------------------------------------o
if(isset($_GET['action']) && $_GET['action'] == "delete")
//o-------------------------------------------------------------------------------o
{
  $cm = new myJAM_CostModel($_GET["cid"]);
  if(is_array($cm->ID))
  {
    die("myJAM>> FATAL ERROR FXBK in module ADMIN-COSTS: Unknown Cost Model");
  }
  if($cm->NbProjects > 0)
  {
    echo "<span class=\"error\"><h3>ERROR! You can't remove a cost model when projects are associated with it.</h3></span>";
  }
  else
  {
    $cm->DELETE();
  }
}
//o-------------------------------------------------------------------------------o
if(isset($_GET['action']) && $_GET['action'] == "update")
//o-------------------------------------------------------------------------------o
{
  $cm = new myJAM_CostModel($_POST["cid"]);
  if (is_array($cm->ID))
  {
    die("myJAM>> FATAL ERROR HM9S in module ADMIN COSTS: Unknown Cost Model");
  }
  if (is_numeric($_POST['cost_su_norm']))
  {
    $cm->Norm = $_POST['cost_su_norm'];
  }
  else
  {
    die("myJAM>> ERROR YBSS: INPUT NOT NUMERIC in module ADMIN COSTS");
  }
  if (is_numeric($_POST['cost_su_over']))
  {
    $cm->Over = $_POST['cost_su_over'];
  }
  else
  {
    die("myJAM>> ERROR 6S8J: INPUT NOT NUMERIC in module ADMIN COSTS");
  }
  $cm->Description = $_POST['cost_descr'];
}
//o-------------------------------------------------------------------------------o
if(isset($_GET['action']) && $_GET['action']=="insert")
//o-------------------------------------------------------------------------------o
{
  myJAM_CostModel::Create($_POST['cost_descr'], $_POST['cost_su_norm'], $_POST['cost_su_over']);
  $CostModelList = new myJAM_CostModel();
}
//o-------------------------------------------------------------------------------o
//o Main Table                                                                    o
//o-------------------------------------------------------------------------------o
echo "                            <tr>";
echo "                                <td class=\"cell3\">";
echo "                                    Cost Description";
echo "                                </td>";
echo "                                <td class=\"cell3\">";
echo "                                    Cost Service Unit (Normal)";
echo "                                </td>";
echo "                                <td class=\"cell3\">";
echo "                                    Cost Service Unit (Overrun)";
echo "                                </td>";
echo "                                <td class=\"cell3\">";
echo "                                    # Projects";
echo "                                </td>";
echo "                                <td class=\"cell3\"></td>";
echo "                            </tr>";
$CMIDList = $CostModelList->ID;
foreach($CMIDList as $cid)
{
  if((isset($_GET['cid']) && $_GET["cid"] == $cid)
  && (isset($_GET['cid']) && $_GET["action"] == "modify"))
  {
    echo "                            <tr>";
    echo "                                <td class=\"cell2\">";
    echo "                                    <input class=\"inputfont\" type=\"hidden\" name=\"cid\" value=\"".$cid."\">";
    echo "                                    <input class=\"inputfont\" type=\"text\" name=\"cost_descr\" value=\"".$CostModelList->Description[$cid]."\">";
    echo "                                </td>";
    echo "                                <td class=\"cell2\">";
    echo "                                     &euro;";
    echo "                                    <input class=\"inputfont\" type=\"text\" name=\"cost_su_norm\" value=\"".$CostModelList->Norm[$cid]."\">";
    echo "                                </td>";
    echo "                                <td class=\"cell2\">";
    echo "                                    &euro;";
    echo "                                    <input class=\"inputfont\" type=\"text\" name=\"cost_su_over\" value=\"".$CostModelList->Over[$cid]."\">";
    echo "                                </td>";
// hier das tr-Tag vielleicht noch schliessen?!?!?
  }
  else
  {
    echo "                            <tr>";
    echo "                                <td class=\"cell2\">";
    echo "                                    <a href=\"main.php?page=admin-costs&amp;action=modify&amp;cid=".$cid."\">".$CostModelList->Description[$cid]."</a>";
    echo "                                </td>";
    echo "                                <td class=\"cell2\">";
    echo "                                     &euro; ".sprintf('%.3f', $CostModelList->Norm[$cid])."";
    echo "                                </td>";
    echo "                                <td class=\"cell2\">";
    echo "                                     &euro; ".sprintf('%.3f', $CostModelList->Over[$cid])."";
    echo "                                </td>";
  }
  echo "                                <td class=\"cell2\">";
  echo "                                    ".$CostModelList->NbProjects[$cid]."";
  echo "                                </td>";
  echo "                                <td class=\"cell2\">";
  echo "                                  <a href=\"main.php?page=admin-costs&amp;action=delete&amp;cid=".$cid."\" onclick=\"return confirm('Are you sure you want to delete the cost model: ".$CostModelList->Description[$cid]."?')\">Delete</a>";
  echo "                                </td>";
  echo "                              </tr>";
} // of while
if(isset($_GET['action']) && ($_GET['action']=="modify" || $_GET['action'] =="new"))
{
  if($_GET['action']=="new")
  {
    echo "                            <tr>";
    echo "                                <td class=\"cell2\">";
    echo "                                    <input class=\"inputfont\" type=\"text\" name=\"cost_descr\" />";
    echo "                                </td>";
    echo "                                <td class=\"cell2\">";
    echo "                                    <span class=\"euroclass\">&euro;</span><input class=\"inputfont\" type=\"text\" name=\"cost_su_norm\" />";
    echo "                                </td>";
    echo "                                <td class=\"cell2\">";
    echo "                                    <span class=\"euroclass\">&euro;</span><input class=\"inputfont\" type=\"text\" name=\"cost_su_over\" />";
    echo "                                </td>";
    echo "                            </tr>";
  }
  echo "                            <tr>";
  echo "                                <td colspan=\"3\">";
  echo "                                    <div class=\"btnclass\">";
  echo "                                        <input class=\"inputfont\" type=\"submit\" value=\"Add/Update\" onclick=\"return confirm('Are you sure you want to Add/Update these settings?')\" />";
  echo "                                    </div>";
  echo "                                </td>";
  echo "                            </tr>";
//echo "                            </form>";
  echo "                        </table>";
  echo "                        </form>";
}
else
{
  echo "                            <tr>";
  echo "                                <td colspan=\"3\">";
  echo "                                    <input class=\"inputfont\" type=\"button\" value=\"Add new cost group\" onclick=\"javascript:window.location.href='main.php?page=admin-costs&amp;action=new'\" />";
  echo "                                </td>";
  echo "                            </tr>";
  echo "                        </table>";
}
