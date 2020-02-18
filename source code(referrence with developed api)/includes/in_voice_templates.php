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
require_once(_FULLPATH.'/classes/class_myJAM_DB.php');
require_once(_FULLPATH.'/classes/class_myJAM_Project.php');
require_once(_FULLPATH.'/fxn/GenTemplateSelect.php');
?>
<script type="text/javascript" src="js/prototype.js"></script>
<script type="text/javascript" src="js/TestUserInput.js"></script>
<script type="text/javascript" src="js/InvoiceTemplateController.js"></script>
<?php
//o-------------------------------------------------------------------------------o
function GenProjectSelect()
//o-------------------------------------------------------------------------------o
{
  $Projects = new myJAM_Project();
  $out = '<select size="1"'
        .' name="ProjSelect"'
        .' id="ProjSelect"'
        .'>';
  $out .= '<option selected value="0">--- Choose Project ---</option>';
  $ProjIDList = $Projects->ID;
  foreach($ProjIDList as $name=>$pid)
  {
    $out .= '<option value="'.$pid.'">'
              .$name
            .'</option>';
  }
  $out .= '</select>';
  return $out;
}
//o-------------------------------------------------------------------------------o
function UpdateTemplate_Action()
//o-------------------------------------------------------------------------------o
{
  $db = new myJAM_db();
  $sql = "SELECT Name FROM InvoiceTemplates WHERE id='".(int)$_POST['templid']."'";
  $db->query($sql);
  if($db->num_rows() != 1)
  {
    die('myJAM>> FATAL ERROR: Could not update template in database...');
  }
  $sql = "UPDATE InvoiceTemplates SET"
        ." Address='".mysql_real_escape_string($_POST['invtmpl_header'])."'"
        .",Subject='".mysql_real_escape_string($_POST['invtmpl_subject'])."'"
        .",Body='".mysql_real_escape_string($_POST['invtmpl_body'])."'"
        .",UserList_Entry='".mysql_real_escape_string($_POST['invtmpl_userlistentry'])."'"
        .",Footer='".mysql_real_escape_string($_POST['invtmpl_footer'])."'";
  $db->DoSQL($sql);
}
//o-------------------------------------------------------------------------------o
function CreateTemplate_Action()
//o-------------------------------------------------------------------------------o
{
  $db = new myJAM_db();
  $sql = 'INSERT INTO InvoiceTemplates'
        .' (Name, Address, Subject, Body, UserList_Entry, Footer)'
        .' VALUES'
        ." ('".mysql_real_escape_string($_POST['templname'])."'"
        .",'".mysql_real_escape_string($_POST['invtmpl_header'])."'"
        .",'".mysql_real_escape_string($_POST['invtmpl_subject'])."'"
        .",'".mysql_real_escape_string($_POST['invtmpl_body'])."'"
        .",'".mysql_real_escape_string($_POST['invtmpl_userlistentry'])."'"
        .",'".mysql_real_escape_string($_POST['invtmpl_footer'])."'"
        .')';
  $db->DoSQL($sql);
}
//o-------------------------------------------------------------------------------o
//o-------------------------------------------------------------------------------o
//o-------------------------------------------------------------------------------o
if(isset($_POST['submit']) && $_POST['submit'] == 'submitted')
{
  if($_POST['templid'] == 'new')
  {
    CreateTemplate_Action();
  }
  if((int)$_POST['templid'] > 0)
  {
    UpdateTemplate_Action();
  }
}
echo
'<div style="width:100%;height:2ex">'
  .'<div style="float:left">'
    .'<b>Invoice Template</b>'
    .'&nbsp;'
    .GenTemplateSelect()
    .'&nbsp;'
    .'<input type="button" name="NewTempl" value=" New " onclick="GetInvoiceTemplate(\'new\');"/>'
    .'&nbsp;'
    .'<input type="button" name="DelTempl" value=" Delete " onclick="DelInvoiceTemplate();"/>'
  .'</div>'
  .'<div style="float:right">'
    .'<b>Project for Preview</b>'
    .'&nbsp;'
    .GenProjectSelect()
  .'</div>'
.'</div>'
.'<br/>'
.'<hr/>';
echo '<div id="template"></div>';
