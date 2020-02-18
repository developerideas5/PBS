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
require_once(_FULLPATH."/classes/class_myJAM_DB.php");
$db = new myJAM_DB();
if(! $ActiveUser->ADMIN)
{
  die("myJAM>> FATAL ERROR I4AT in module ADMIN EXIT STATUS: You are not authorized to view these page");
}
echo "<div class=\"simpleborder2\">";
if(isset($_GET['action']) && $_GET['action'] == "modify")
{
  echo "<form action=\"main.php?page=admin-exit-status&amp;action=update\" method=\"post\">";
}
if(isset($_GET['action']) && $_GET['action'] == "new")
{
  echo "<form action=\"main.php?page=admin-exit-status&amp;action=insert\" method=\"post\">";
}
echo "<table class=\"table3\" cellpadding=\"2\" cellspacing=\"2\">";
if(isset($_GET['action']) && $_GET['action'] == "delete" && is_numeric($_GET['eid']))
{
  $db->DoSQL("DELETE FROM Exit_Status WHERE eid='".(int)$_GET['eid']."'");
}
if(isset($_GET['action']) && $_GET['action'] == "update" && is_numeric($_POST['eid']))
{
  $db->DoSQL("UPDATE Exit_Status SET"
            ." exit_descr='".mysql_real_escape_string(htmlentities($_POST['exit_descr']))."'"
            ." WHERE eid='".(int)$_POST['eid']."'");}
if(isset($_GET['action']) && $_GET['action']=="insert")
{
  $db->DoSQL("INSERT INTO Exit_Status (exit_descr) VALUES ('".mysql_real_escape_string(htmlentities($_POST['exit_descr']))."')");
}
echo "                            <tr>";
echo "                                <td class=\"cell3\">";
echo "                                    Error (Exit) Number";
echo "                                </td>";
echo "                                <td class=\"cell3\">";
echo "                                    Exit Description";
echo "                                </td>";
echo "                            </tr>";
$result = $db->query("SELECT * FROM Exit_Status");
foreach($result as $row)
{
  if(isset($_GET['eid']) && ($_GET['eid'] == $row['eid'] && $_GET['action'] == "modify"))
  {
    echo "                            <tr>";
    echo "                                <td class=\"cell2\">";
    echo "                                    <input class=\"inputfont\" type=\"text\" readonly name=\"eid\" value=\"".$row['eid']."\" />";
    echo "                                </td>";
    echo "                                <td class=\"cell2\">";
    echo "                                    <input class=\"inputfont\" type=\"text\" name=\"exit_descr\" value=\"".$row['exit_descr']."\" />";
    echo "                                </td>";
  }
  else
  {
    echo "                            <tr>";
    echo "                                <td class=\"cell2\">";
    echo "                                    ".$row['eid'];
    echo "                                </td>";
    if ($row['eid'] <= 4)
    {
      echo "                                 <td class=\"cell2\">";
      echo "                                     ".$row['exit_descr'];
      echo "                                 </td>";
    }
    else
    {
      echo "                                 <td class=\"cell2\">";
      echo "                                     <a href=\"main.php?page=admin-exit-status&amp;action=modify&amp;eid=".$row['eid']."\">".$row['exit_descr']."</a>";
      echo "                                 </td>";
    }
  }
  echo "                            </tr>";
}
if(isset($_GET['action']) && ($_GET['action']=="modify" || $_GET['action'] =="new"))
{
  if(isset($_GET['action']) && $_GET['action']=="new")
  {
    echo "                            <tr>";
    echo "                                <td>";
    echo "                                </td>";
    echo "                                <td class=\"cell2\">";
    echo "                                    <input class=\"inputfont\" type=\"text\" name=\"exit_descr\" />";
    echo "                                </td>";
    echo "                            </tr>";
  }
  echo "                            <tr>";
  echo "                                <td colspan=\"3\">";
  echo "                                    <div class=\"centr\">";
  echo "                                        <input class=\"inputfont\" type=\"submit\" value=\"Add/Update\" onclick=\"return confirm('Are you sure you want to update these settings?')\" />";
  echo "                                    </div>";
  echo "                                </td>";
  echo "                            </tr>";
  //echo "                            <tr>";
  //echo "                                <td colspan=\"3\">";
  //echo "                                    <div class=\"centr\">";
  //echo "                                        <form action=\"main.php?page=admin-exit-status&amp;action=delete&amp;eid=".$_GET['eid']."\" method=\"post\">";
  //echo "                                            <input class=\"inputfont\" type=\"submit\" value=\"Delete\" onclick=\"return confirm('Are you sure you want to delete this exit status')\" />";
  //echo "                                        </form>";
  //echo "                                    </div>";
  //echo "                                </td>";
  //echo "                            </tr>";
  echo "                        </table>";
  echo "                            </form>";
  echo "                        <table class=\"full\">";
  echo "                          <tr>";
  echo "                            <td colspan=\"3\">";
  echo "                                    <div class=\"centr\">";
  echo "                                        <form action=\"main.php?page=admin-exit-status&amp;action=delete&amp;eid=".$_GET['eid']."\" method=\"post\">";
  echo "                                            <div><input class=\"inputfont\" type=\"submit\" value=\"Delete\" onclick=\"return confirm('Are you sure you want to delete this exit status')\" /></div>";
  echo "                                        </form>";
  echo "                                    </div>";
  echo "                            </td>";
  echo "                          </tr>";
  echo "                        </table>";
}
else
{
  echo "                            <tr>";
  echo "                                <td colspan=\"3\">";
  echo "                                    <div class=\"centr\"><a href=\"main.php?page=admin-exit-status&amp;action=new\">Add exit condition</a></div>";
  echo "                                </td>";
  echo "                            </tr>";
  echo "                        </table>";
}
  echo "                        </div>";
