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

define('_FULLPATH', realpath(dirname(__FILE__).'/../'));
require_once(_FULLPATH."/access.php");
require_once(_FULLPATH."/classes/class_myJAM_DB.php");
//o-------------------------------------------------------------------------------o
function GenSelectHelper($Entries, $Name)
//o-------------------------------------------------------------------------------o
{
  $out = '<select size="1"'
        .' name="'.$Name.'"'
        .' id="'.$Name.'"'
        .' onchange="SelectMacro(this.options[this.selectedIndex].value, \''.$Name.'\')"'
        .'>';
  $out .= '<option value="">--- SELECT ---</option>';
  foreach($Entries as $entry=>$value)
  {
    $out .= '<option value="'.$value.'">'
           .htmlentities($entry)
           .'</option>';
  }
  $out .= '</select>';
  return $out;
}
//o-------------------------------------------------------------------------------o
function GenVarSelect()
//o-------------------------------------------------------------------------------o
{
  $decorations = array(
                        'hline' => '%%HLINE%%'
                      );
  $metrics = array(
                    'Invoice Number' => '%%INVOICENO%%',
                    'Invoice Date' => '%%NOW%%',
                    'Start Date' => '%%STARTDATE%%',
                    'End Date' => '%%ENDDATE%%',
                    'Used SUs' => '%%USEDSUS%%',
                    'Old SUs' => '%%OLDSUS%%',
                    'New SUs' => '%%NEWSUS%%',
                    'Normal Costmodel' => '%%COSTNORMAL%%',
                    'Overrun Costmodel' => '%%COSTOVER%%',
                    'Total Amount' => '%%AMOUNT%%'
                  );
  $names = array(
                  'Project Name' => '%%PROJECTNAME%%',
                  'Project Owner' => '%%PROJECTOWNER%%',
                  'User List' => '%%USERLIST%%',
                );
  $users = array(
                  'Username' => '%%UL_USERNAME%%',
                  'Full Name' => '%%UL_FULLNAME%%'
                );
  $out = '<table style="width:100%;border-style:solid;border-width:1px;">'
          .'<tr>';
  //Generate Decoration Selector
  $out .= '<td>'
            .'<b>Decorations</b><br/>'
            .GenSelectHelper($decorations, 'DecorationSelect')
         .'</td>';
  //Generate Metric Selector
  $out .= '<td>'
            .'<b>Invoice Properties</b><br/>'
            .GenSelectHelper($metrics, 'MetricsSelect')
         .'</td>';
  //Generate Names Selector
  $out .= '<td>'
            .'<b>Project Properties</b><br/>'
            .GenSelectHelper($names, 'NamesSelect')
         .'</td>';
  //Generate User Selector
  $out .= '<td>'
            .'<b>User List Entries</b><br/>'
            .GenSelectHelper($users, 'UsersSelect')
         .'</td>';
  $out .= '</tr>'
         .'</table>';
  return $out;
}
//o-------------------------------------------------------------------------------o
//o-------------------------------------------------------------------------------o
//o-------------------------------------------------------------------------------o
if($_POST['TemplID'] == 'new')
{
  $TemplID = 'new';
  $vres = array();
  $vres[0]['Address'] = '';
  $vres[0]['Subject'] = '';
  $vres[0]['Body'] = '';
  $vres[0]['UserList_Entry'] = '';
  $vres[0]['Footer'] = '';
  $vres[0]['Name'] = '';
}
else
{
  $TemplID = (int)$_POST['TemplID'];
  $db = new myJAM_db();
  $sql = "SELECT Name, Address, Subject, Body, UserList_Entry, Footer FROM InvoiceTemplates WHERE id='$TemplID'";
  $vres = $db->query($sql);
  if($db->num_rows() != 1)
  {
    die('myJAM>> FATAL ERROR: Internale Error while getting Invoice Template');
  }
}
echo GenVarSelect();
echo
  '<form id="invtemplform" action="main.php?page=in_voice_templates" method="POST"'
  .' onsubmit="return CheckNew();"'
  .'>'
   .'<div style="width:100%;">'
   .'<div style="width:63ex;">'
   .'<p/>'
   .'<div style="float:left;"><b>Address</b></div>'
   .'<div style="float:right;"><a href="" onclick="return InsertMacro(\'invtmpl_header\')">Insert Macro</a></div>'
         .'<textarea name="invtmpl_header" id="invtmpl_header" cols="60" rows="5">'
         .htmlentities($vres[0]['Address'])
         .'</textarea>'
         .'<p/>'
   .'<div style="float:left;"><b>Subject</b></div>'
   .'<div style="float:right;"><a href="" onclick="return InsertMacro(\'invtmpl_subject\')">Insert Macro</a></div>'
         .'<textarea name="invtmpl_subject" id="invtmpl_subject" cols="60" rows="2">'
         .htmlentities($vres[0]['Subject'])
         .'</textarea>'
         .'<p/>'
   .'<div style="float:left;"><b>Body</b></div>'
   .'<div style="float:right;"><a href="" onclick="return InsertMacro(\'invtmpl_body\')">Insert Macro</a></div>'
         .'<textarea name="invtmpl_body" id="invtmpl_body" cols="60" rows="12">'
         .htmlentities($vres[0]['Body'])
         .'</textarea>'
         .'<p/>'
   .'<div style="float:left;"><b>UserList Entry</b></div>'
   .'<div style="float:right;"><a href="" onclick="return InsertMacro(\'invtmpl_userlistentry\')">Insert Macro</a></div>'
         .'<textarea name="invtmpl_userlistentry" id="invtmpl_userlistentry" cols="60" rows="2">'
         .htmlentities($vres[0]['UserList_Entry'])
         .'</textarea>'
         .'<p/>'
   .'<div style="float:left;"><b>Footer</b></div>'
   .'<div style="float:right;"><a href="" onclick="return InsertMacro(\'invtmpl_footer\')">Insert Macro</a></div>'
         .'<textarea name="invtmpl_footer" id="invtmpl_footer" cols="60" rows="2">'
         .htmlentities($vres[0]['Footer'])
         .'</textarea>'
         .'<p/>'
         .'<input type="button" name="preview" value=" Preview" onclick="GenPreview('
         .strtotime('now')
         .');"/>'
         .'&nbsp;&nbsp;<input type="submit" value=" Save Template "/>'
         .'&nbsp;&nbsp;<input type="button" name="cancel" value=" CANCEL" onclick="CancelEdit();"/>'
         .'<input type="hidden" name="templid" id="templid" value="'.$TemplID.'">'
         .'<input type="hidden" name="templname" id="templname" value="'.htmlentities($vres[0]['Name']).'">'
         .'<input type="hidden" name="submit" value="submitted">'
  .'</div>'
  .'</div>'
  .'</form>'
;
