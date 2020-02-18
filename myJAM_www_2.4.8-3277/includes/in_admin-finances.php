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
require_once(_FULLPATH."/classes/class_myJAM_Project.php");
require_once(_FULLPATH."/classes/class_myJAM_CostModel.php");
?>
<script type="text/javascript">
//o-------------------------------------------------------------------------------o
function popNewTrans()
//o-------------------------------------------------------------------------------o
{
  w=window.open("NewTrans.php",
                "New_Transaction",
                "width=600,height=500,location=no,menubar=no,status=yes,toolbar=no,scrollbars=no");
  if (w.blur)
  {
    w.focus();
  }
}
</script>
<?php
//o-------------------------------------------------------------------------------o
if(! $ActiveUser->ADMIN)
//o-------------------------------------------------------------------------------o
{
  die("myJAM>> FATAL ERROR IHQ8 in module ADMIN FINANCES: You are not authorized to view this page!");
}
?>
<div class="transmaintable">
    <table>
      <tr>
        <td><input class="inputfont"
                   type="button"
                   id="NewTrans"
                   value="New Transaction"
                   onclick="popNewTrans();" /></td>
        <td class="right">&nbsp;&nbsp;&nbsp;&nbsp;<b>Search</b></td>
        <td><input class="inputfont"
                   type="text"
                   id="TransSearch"
                   disabled="disabled"
                   maxlength="30" /></td>
      </tr>
    </table>
</div>
<p/>
<?php
echo '<table class="transmaintable">';
?>
 <tr>
  <td class="transhead">Trans-ID</td>
  <td class="transhead">Date</td>
  <td class="transhead">Project</td>
  <td class="transhead">Amount</td>
  <td class="transhead">CostModel</td>
  <td class="transhead">Overrun SUs</td>
  <td class="transhead">Added SUs</td>
  <td class="transhead">Admin</td>
  <td class="transhead"></td>
 </tr>
<?php
$db = new myJAM_DB();
$Transactions = $db->query("SELECT * FROM Transactions ORDER BY date");
foreach($Transactions as $trans)
{
  echo "<tr>"
  ."<td class=\"trans1\">".$trans["tid"]."</td>"
  ."<td class=\"trans2\">".date("d.m.Y",strtotime($trans["date"]))."</td>"
  ;
  $Project = new myJAM_Project($trans["pid"]);
  if(!is_object($Project) || !is_scalar($Project->ID) || $Project->ID < 1)
  {
    die("myJAM>> FATAL ERROR 0x4242 in AdminFinances!");
  }
  echo "<td class=\"trans3\">".$Project->Name."</td>"
  . "<td class=\"trans4\">".$trans["payment_amount"]."</td>"
  ."<td class=\"trans5\"><span class=\"transcostmodel\">".$Project->CostModel->Description."</span><br/>"
  ."Norm SUs: ".$Project->CostModel->Norm."<br/>"
  ."Overrun SUs: ".$Project->CostModel->Over."<br/>"
  ."</td>"
  ;
  unset($Project);
  echo "<td class=\"trans2\">".$trans["over_su_substracted"]."</td>"
  ."<td class=\"trans1\">".$trans["norm_su_added"]."</td>"
  ;
  $admin = new myJAM_User($trans["uid"]);
  if (!is_object($admin) || !is_scalar($admin->ID) || $admin->ID < 1)
  {
    die("myJAM>> FATAL ERROR 0x4243 in AdminFinances!");
  }
  echo "<td class=\"trans6\">".$admin->FullName."</td>";
  unset($admin);
  echo "<td class=\"outertranstable\">"
  ."<table class=\"innertranstable\">"
  ."<tr>"
  ."<td class=\"tabletd1\">Depositor</td>"
  ."<td class=\"tabletd2\">".nl2br($trans["depositor"])."</td>"
  ."</tr>"
  ."<tr>"
  ."<td class=\"tabletd1\">Organisation</td>"
  ."<td class=\"tabletd2\">".nl2br($trans["Organisation"])."</td>"
  ."</tr>"
  ."<tr>"
  ."<td class=\"tabletd1\">Internal Account</td>"
  ."<td class=\"tabletd2\">".($trans["internal_account"])."</td>"
  ."</tr>"
  ."<tr>"
  ."<td class=\"tabletd1\">Invoice Number</td>"
  ."<td class=\"tabletd2\">".($trans["invoice_number"])."</td>"
  ."</tr>"
  ."</table>"
  ."</td>"
  ."</tr>"
  ."<tr><td colspan=\"9\"><div class=\"bottomborder\"></div></td></tr>"
  ;
}
echo '</table>';
