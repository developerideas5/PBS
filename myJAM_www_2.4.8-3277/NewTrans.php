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
//o-------------------------------------------------------------------------------o
function ProjSelect()
//o-------------------------------------------------------------------------------o
{
  $ProjectList = new myJAM_Project();
  echo "<select class=\"inputfont\" size=\"1\" id=\"ProjSelect\" onchange=\"ProjSelected();\">"
  ."<option value=\"0\">--- SELECT PROJECT ---</option>"
  ;
  foreach($ProjectList->ID as $pid)
  {
    if($ProjectList->CostModel[$pid]->Over > 0 || $ProjectList->CostModel[$pid]->Norm > 0)
    {
      echo "<option value=\"".$pid."\">".$ProjectList->Name[$pid]."</option>";
    }
  }
  echo "</select>";
}
//o-------------------------------------------------------------------------------o
function CalcNormOver($pid, $payment)
//o-------------------------------------------------------------------------------o
{
  if(!isset($payment) || !is_numeric($payment) || (float)$payment < 0.00)
  {die("myJAM>> FATAL ERROR 0x167c in Module NewTrans!");}
  if(!isset($pid) || !is_numeric($pid) || (integer)$pid < 1)
  {die("myJAM>> FATAL ERROR 0x7eb4 in Module NewTrans!");}
  $Project = new myJAM_Project($_POST["pid"]);
  if(!is_object($Project) || !is_scalar($Project->ID) || $Project->ID < 1)
  {die("myJAM>> FATAL ERROR 0x8dc2 in Module NewTrans!");}
  $tmpSUs = $Project->SUs;
  $result = array();
  if($tmpSUs < 0)
  {
    $overrun_costs = -$tmpSUs * $Project->CostModel->Over;
    if($overrun_costs <= $payment)
    {
      $result["overrun_sus"] = $tmpSUs;
      $result["overrun_costs"] = $overrun_costs;
      $tmpSUs = 0.0;
      $payment -= $overrun_costs;
    }
    else
    {
      $afford = $payment / $Project->CostModel->Over;
      $result["overrun_sus"] = $afford;
      $result["overrun_costs"] = $payment;
      $tmpSUs += $afford;
      $payment = 0.0;
    }
  }
  else
  {
    $result["overrun_sus"] = 0.0;
    $result["overrun_costs"] = 0.0;
  }
  if($payment > 0)
  {
    $afford = $payment / $Project->CostModel->Norm;
    $result["norm_sus"] = $afford;
    $result["norm_costs"] = $payment;
    $tmpSUs += $afford;
  }
  else
  {
    $result["norm_sus"] = 0.0;
    $result["norm_costs"] = 0.0;
  }
  $result["cm_norm"] = $Project->CostModel->Norm;
  $result["cm_over"] = $Project->CostModel->Over;
  $result["old_sus"] = $Project->SUs;
  $result["new_sus"] = $tmpSUs;
  return $result;
}
//o-------------------------------------------------------------------------------o
//o-------------------------------------------------------------------------------o
//o-------------------------------------------------------------------------------o
if(! $ActiveUser->ADMIN || $ActiveUser->UserName=="admin")
{die("myJAM>> FATAL ERROR 0x6a86 in Module NewTrans!");}
if(isset($_POST["action"]))
{
  //o-------------------------------------------------------------------------------o
  if($_POST["action"] == "ProjReq")
  //o-------------------------------------------------------------------------------o
  {
    if(!isset($_POST["pid"]) || !is_numeric($_POST["pid"]) || (integer)$_POST["pid"] < 1)
    {die("myJAM>> FATAL ERROR 0x3785 in Module NewTrans!");}
    $Project = new myJAM_Project($_POST["pid"]);
    if(!is_object($Project) || !is_scalar($Project->ID) || $Project->ID < 1)
    {die("myJAM>> FATAL ERROR 0x38f2 in Module NewTrans!");}
    echo "<i>".$Project->CostModel->Description."</i><br>"
    ."Norm: ".$Project->CostModel->Norm."<br>"
    ."Overrun: ".$Project->CostModel->Over."<br>"
    ."&#&#"
    .$Project->SUs
    ;
    unset($Project);
  }
  //o-------------------------------------------------------------------------------o
  elseif($_POST["action"] == "PaymentReq")
  //o-------------------------------------------------------------------------------o
  {
    if(!isset($_POST["payment"]) || !is_numeric($_POST["payment"]) || (float)$_POST["payment"] < 0.00)
    {die("myJAM>> FATAL ERROR 0x3341 in Module NewTrans!");}
    if(!isset($_POST["pid"]) || !is_numeric($_POST["pid"]) || (integer)$_POST["pid"] < 1)
    {die("myJAM>> FATAL ERROR 0xa00f in Module NewTrans!");}
    $result = CalcNormOver((integer)$_POST["pid"], (float)$_POST["payment"]);
    if($result["overrun_sus"]!=0)
    {printf("%.2f<br>Costs: %.2f&#&#", $result["overrun_sus"], $result["overrun_costs"]);}
    else
    {echo "---&#&#";}
    if($result["norm_sus"]!=0)
    {printf("%.2f<br>Costs: %.2f&#&#", $result["norm_sus"], $result["norm_costs"]);}
    else
    {echo "---&#&#";}
    printf("%.2f&#&#", $result["old_sus"]);
    printf("%.2f&#&#", $result["new_sus"]);
    printf("%.2f", (float)$_POST["payment"]);
  }
  //o-------------------------------------------------------------------------------o
  elseif($_POST["action"] == "DoTransaction")
  //o-------------------------------------------------------------------------------o
  {
    if(!isset($_POST["payment"]) || !is_numeric($_POST["payment"]) || (float)$_POST["payment"] < 0.00)
    {die("myJAM>> FATAL ERROR 0x3ee6 in Module NewTrans!");}
    if(!isset($_POST["pid"]) || !is_numeric($_POST["pid"]) || (integer)$_POST["pid"] < 1)
    {die("myJAM>> FATAL ERROR 0xef9d in Module NewTrans!");}
    $result = CalcNormOver((integer)$_POST["pid"], (float)$_POST["payment"]);
    $db = new myJAM_DB();
    $sql = "INSERT INTO Transactions SET pid='".(integer)$_POST["pid"]."'";
    $sql .= ", payment_amount='".(float)$_POST["payment"]."'";
    $sql .= ", old_sus='".(float)$result["old_sus"]."'";
    $sql .= ", over_su_substracted='".(float)$result["overrun_sus"]."'";
    $sql .= ", norm_su_added='".(float)$result["norm_sus"]."'";
    $sql .= ", date=now()";
    $sql .= ", depositor='".mysql_real_escape_string(utf8_decode($_POST["depositor"]))."'";
    $sql .= ", Organisation='".mysql_real_escape_string(utf8_decode($_POST["organisation"]))."'";
    $sql .= ", internal_account='".mysql_real_escape_string(utf8_decode($_POST["internal_account"]))."'";
    $sql .= ", invoice_number='".mysql_real_escape_string(utf8_decode($_POST["invoice_number"]))."'";
    $sql .= ", uid='".(int)$ActiveUser->ID."'";
    $sql .= ", new_sus='".(float)$result["new_sus"]."'";
    $sql .= ", norm_costs='".(float)$result["cm_norm"]."'";
    $sql .= ", over_costs='".(float)$result["cm_over"]."'";
    $db->query($sql);
    if($db->affected_rows()==1)
    {echo "OK";}
  }
  die("");
}
header('Content-Type: text/html; charset=ISO-8859-1');
echo '<?xml version="1.0" encoding="ISO-8859-1"?>'
.'<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">'
.'<html xmlns="http://www.w3.org/1999/xhtml">'
.'<html>'
.'<head>'
.'<link rel="shortcut icon" href="images/myJAM-icon.png" type="image/png" />'
.'<title>New Transaction</title>'
.'<link rel="stylesheet" type="text/css" href="css/style.css">'
;
?>
<script
 type="text/javascript" src="js/prototype.js">
</script>
<script type="text/javascript">
//o-------------------------------------------------------------------------------o
function ProjSelected()
//o-------------------------------------------------------------------------------o
{
  var pid = $("ProjSelect").value;
  $("Div_CostModel_Header").innerHTML = "";
  $("Div_CostModel").innerHTML = "";
  $("Div_SUs_Header").innerHTML = "";
  $("Div_SUs").innerHTML = "";
  $("Div_Overrun_Header").innerHTML = "";
  $("Div_Norm_Header").innerHTML = "";
  $("Div_Overrun").innerHTML = "";
  $("Div_Norm").innerHTML = "";
  $("payment_amount").value = "0.00";
  if(pid == 0)
  {
    window.alert("Please, select a valid project!");
    return false;
  }
  var ProjReq = new Ajax.Request("NewTrans.php",
                                 {
                                   method: "POST",
                                   parameters: "action=ProjReq&pid="+pid,
                                   onComplete: ProjReqComplete
                                 }
                                );
}
//o-------------------------------------------------------------------------------o
function ProjReqComplete(answer)
//o-------------------------------------------------------------------------------o
{
  var tmp = answer.responseText.split("&#&#");
  $("Div_CostModel_Header").innerHTML = "<b>CostModel</b>";
  $("Div_CostModel").innerHTML = tmp[0];
  $("Div_SUs_Header").innerHTML = "<b>SUs</b>";
  $("Div_SUs").innerHTML = tmp[1];
  if (tmp[1] < 0)
  {
    $("Div_SUs").style.color="#ff0000";
  }
  else
  {
    $("Div_SUs").style.color="#000000";
  }
}
//o-------------------------------------------------------------------------------o
function ChangePayment()
//o-------------------------------------------------------------------------------o
{
  var payment = $("payment_amount").value;
  var pid = $("ProjSelect").value;
  if(pid == 0)
  {
    window.alert("Please, select a valid project!");
    return false;
  }
  if(!(/^[0-9\.]+$/.test(payment)))
  {
    window.alert("Please, enter a valid payment amount");
    $("payment_amount").value = "0.00";
    ProjSelected();
    return false;
  }
  var PayReq = new Ajax.Request("NewTrans.php",
                                {
                                  method: "POST",
                                  parameters: "action=PaymentReq&pid="+pid+"&payment="+payment,
                                  onComplete: PaymentReqComplete
                                }
                               );
}
//o-------------------------------------------------------------------------------o
function PaymentReqComplete(answer)
//o-------------------------------------------------------------------------------o
{
  var tmp=answer.responseText.split("&#&#");
  $("Div_Overrun_Header").innerHTML = "<b>SUs substracted<br>for Overrun</b>";
  $("Div_Overrun").innerHTML = tmp[0];
  $("Div_Norm_Header").innerHTML = "<b>SUs added</b>";
  $("Div_Norm").innerHTML = tmp[1];
  $("Div_SUs").innerHTML = tmp[3]+"<br>(was "+tmp[2]+")";
  if (tmp[3] < 0)
  {
    $("Div_SUs").style.color="#ff0000";
  }
  else
  {
    $("Div_SUs").style.color="#000000";
  }
  $("payment_amount").value = tmp[4];
}
//o-------------------------------------------------------------------------------o
function SubmitTrans()
//o-------------------------------------------------------------------------------o
{
  var pid = $("ProjSelect").value;
  if (pid == 0)
  {
    window.alert("Please, select a valid Project");
    return false;
  }
  var payment = $("payment_amount").value;
  if(!(/^[0-9\.]+$/.test(payment)))
  {
    window.alert("Please, enter a valid payment amount");
    $("payment_amount").value = "0.00";
    ProjSelected();
    return false;
  }
  CheckText('depositor','depositor_header','Depositor');
  CheckText('organisation','organisation_header','Organisation');
  CheckText('internal_account','internal_account_header','Internal Account');
  CheckText('invoice_number','invoice_number_header','Invoice Number');
  var TransReq = new Ajax.Request("NewTrans.php",
                                  {
                                     method: "POST",
                                     encoding: "UTF-8",
                                     parameters: "action=DoTransaction&pid=" + pid +
                                                 "&payment=" + payment +
                                                 "&depositor=" + $("depositor").value +
                                                 "&organisation="+$("organisation").value+
                                                 "&internal_account="+$("internal_account").value+
                                                 "&invoice_number="+$("invoice_number").value,
                                     onComplete: TransactionComplete
                                   }
                                  );
}
//o-------------------------------------------------------------------------------o
function TransactionComplete(answer)
//o-------------------------------------------------------------------------------o
{
  if (answer.responseText == "OK")
  {
    window.alert("Transaction added succesfully!");
    opener.window.location.reload();
    window.close();
  }
  else
  {
    window.alert("UNKNOWN ERROR WHILE ADDING TRANSACTION!!");
  }
}
//o-------------------------------------------------------------------------------o
function CheckText(id, header, name)
//o-------------------------------------------------------------------------------o
{
  if(document.getElementById(id).value == "")
    {return true;}
  if(!(/^[a-zA-Z0-9�������\-\@\.\+ \/,\s']+$/.test(document.getElementById(id).value)))
  {
    window.alert("Illegal character in "+name);
    document.getElementById(header).style.color = "#ff0000";
  }
  else
    {document.getElementById(header).style.color = "#000000";}
}
</script>
<?php
echo '</head>'
.'<body style="margin: 5px;">'
.'<table cellspacing="3" border="0" cellpadding="0" class="tabhead">'
.'<tr>'
.'<td>'
.'<table border="0" cellspacing="0" cellpadding="0" class="tabhead2">'
.'<tr>'
.'<td class="gradhead1"><img src="images/multigradient_left.png" alt="gradientGFX" /></td>'
.'<td style="width: 150px; height: 20px; background-image: url(images/gradient_slice_1.png);">'
.'<b>New Transaction</b></td>'
.'<td class="gradhead5"><img src="images/multigradient_middle.png" alt="gradientGFX" /></td>'
.'<td style="background-image: url(images/gradient_slice_2.png); background-repeat: repeat-x;"></td>'
.'<td class="gradhead1"><img src="images/multigradient_right.png" alt="gradientGFX" /></td>'
.'</tr>'
.'</table>'
.'</td>'
.'</table>'
.'<p />'
.'<div style="border-style: solid; border-width: 1px;">'
.'<table style="width: 100%;">'
.'<tr>'
.'<td style="width: 100%">'
.'<table style="width: 100%;">'
.'<tr>'
.'<td style="background-color: #c0c0c0;"><b>Project</b></td>'
.'<td style="background-color: #c0c0c0;">'
.'<div id="Div_CostModel_Header"></div>'
.'</td>'
.'<td style="background-color: #c0c0c0;">'
.'<div id="Div_SUs_Header"></div>'
.'</td>'
.'</tr>'
.'<tr>'
.'<td>'.ProjSelect().'</td>'
.'<td>'
.'<div id="Div_CostModel"></div>'
.'</td>'
.'<td>'
.'<div id="Div_SUs"></div>'
.'</td>'
.'</tr>'
.'</table>'
.'</td>'
.'</tr>'
.'<tr>'
.'<td style="width: 100%">'
.'<table style="width: 100%;">'
.'<tr>'
.'<td style="background-color: #c0c0c0;"><b>Payment Amount</b></td>'
.'<td style="background-color: #c0c0c0;">'
.'<div id="Div_Overrun_Header"></div>'
.'</td>'
.'<td style="background-color: #c0c0c0;">'
.'<div id="Div_Norm_Header"></div>'
.'</td>'
.'</tr>'
.'<tr>'
.'<td><input class="inputfont" type="text" id="payment_amount" maxlength="10" onchange="ChangePayment();" value="0.00"></td>'
.'<td>'
.'<div id="Div_Overrun"></div>'
.'</td>'
.'<td>'
.'<div id="Div_Norm"></div>'
.'</td>'
.'</tr>'
.'</table>'
.'</td>'
.'</tr>'
.'<tr>'
.'<td style="width: 100%">'
.'<table style="width: 100%;">'
.'<tr>'
.'<td style="background-color: #c0c0c0;" id="depositor_header"><b>Depositor</b></td>'
.'<td style="background-color: #c0c0c0;" id="organisation_header"><b>Organisation</b></td>'
.'</tr>'
.'<tr>'
.'<td><textarea class="inputfont" cols="30" rows="6" id="depositor" onchange="CheckText(\'depositor\',\'depositor_header\',\'Depositor\');">'
.'</textarea></td>'
.'<td><textarea class="inputfont" cols="30" rows="6" id="organisation" onchange="CheckText(\'organisation\',\'organisation_header\',\'Organisation\');">'
.'</textarea></td>'
.'</tr>'
.'<tr>'
.'<td style="background-color: #c0c0c0;" id="internal_account_header"><b>Internal Account</b></td>'
.'<td style="background-color: #c0c0c0;" id="invoice_number_header"><b>Invoice Number</b></td>'
.'</tr>'
.'<tr>'
.'<td><input class="inputfont" type="text" id="internal_account" maxlength="30" onchange="CheckText(\'internal_account\',\'internal_account_header\',\'Internal Account\');"></td>'
.'<td><input class="inputfont" type="text" id="invoice_number" maxlength="30" 	onchange="CheckText(\'invoice_number\',\'invoice_number_header\',\'Invoice Number\');"></td>'
.'</tr>'
.'</table>'
.'</td>'
.'</tr>'
.'<tr>'
.'<td style="width: 100%">'
.'<hr>'
.'</td>'
.'</tr>'
.'<tr>'
.'<td style="width: 100%">'
.'<table style="width: 100%;">'
.'<tr>'
.'<td><input class="inputfont" type="button" value="Submit" onclick="SubmitTrans();"></td>'
.'<td><input class="inputfont" type="button" value="CANCEL" onclick="window.alert(\'Do you really want to cancel?\');window.close();"></td>'
.'</tr>'
.'</table>'
.'</td>'
.'</tr>'
.'</table>'
.'</div>'
.'</body>'
.'</html>'
;
