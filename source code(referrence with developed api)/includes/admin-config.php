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
if (! $ActiveUser->ADMIN)
{
  die("myJAM>> FATAL ERROR. ACCESS DENIED !!!");
}
?>
<script type="text/javascript" src="js/TestUserInput.js"></script>
<script type="text/javascript" src="js/AdminConfig.js"></script>
<?php
//o-------------------------------------------------------------------------------o
function Action_Update()
//o-------------------------------------------------------------------------------o
{
  $db = new myJAM_DB();
  $vals = array();
  if (is_numeric($_POST["maxrespp"]))
  {
    $vals[] = "max_results_per_page='" . (int)$_POST["maxrespp"] . "'";
  }
  if (is_numeric($_POST["maxindexpp"]))
  {
    $vals[] = "max_index_page='" . (int)$_POST["maxindexpp"] . "'";
  }
  $vals[] = "SiteAddress='" . mysql_real_escape_string($_POST["site_addr"]) . "'";
  $vals[] = "SiteName='" . mysql_real_escape_string($_POST["sitename"]) . "'";
  $sql = "UPDATE Configuration SET " . implode(", ", $vals);
  $db->DoSQL($sql);
  echo '<b>Configuration updated!</b><br/>';
}
//o-------------------------------------------------------------------------------o
//o-------------------------------------------------------------------------------o
//o-------------------------------------------------------------------------------o
if(isset($_GET["action"]) && $_GET["action"]=="submit")
{
  Action_Update();
}
$db = new myJAM_db();
$vConfig = $db->query("SELECT * FROM Configuration");
?>
<div class="usermaintable" style="width: 600px">
<form action="main.php?page=admin-config&action=submit" method="POST"
 onsubmit="return CheckAll();">
<table cellpadding="2" cellspacing="2">
 <tr>
  <td><b>Max. results per page</b></td>
  <td><input type="text" id="maxrespp" name="maxrespp" maxlength="4"
   size="10" value="<?php echo $vConfig[0]["max_results_per_page"]?>"
   onchange="CheckMaxResPage();" /></td>
 </tr>
 <tr>
  <td><b>Max. indices per page</b></td>
  <td><input type="text" id="maxindexpp" name="maxindexpp" maxlength="4"
   size="10" value="<?php echo $vConfig[0]["max_index_page"]?>"
   onchange="CheckMaxIndPage();" /></td>
 </tr>
 <tr>
  <td><b>Site Name</b></td>
  <td><input type="text" id="sitename" name="sitename" maxlength="32"
   size="32" value="<?php echo $vConfig[0]["SiteName"]?>"
   onchange="CheckSiteName();" /></td>
 </tr>
 <tr>
  <td><b>Site address</b></td>
  <td><textarea id="site_addr" name="site_addr" rows="3" cols="60"
   onchange="CheckSiteAddress();"><?php echo $vConfig[0]["SiteAddress"]?></textarea>
 </tr>
 <tr>
  <td colspan="2" style="text-align: center"><input type="submit"
   value="  submit  " /> <input type="button" value="  cancel  " /></td>
 </tr>
</table>
</form>
</div>
