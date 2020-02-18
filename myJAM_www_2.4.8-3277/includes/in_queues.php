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
require_once(_FULLPATH.'/classes/class_myJAM_Host.php');
require_once(_FULLPATH.'/helper/myJAM_forms/myJAM_Form_SubForm.php');
require_once(_FULLPATH.'/helper/myJAM_forms/myJAM_Form_Element_Button.php');
require_once(_FULLPATH.'/helper/myJAM_forms/myJAM_Form_Element_Caption.php');
require_once(_FULLPATH.'/helper/myJAM_forms/myJAM_Form_Element_Text.php');
?>
<script type="text/javascript" src="js/prototype.js"></script>
<script src="js/queues.js" type="text/javascript"></script>
<?php
//o-------------------------------------------------------------------------------o//
//o MAIN                                                                          o//
//o-------------------------------------------------------------------------------o//
echo genRefreshButtons();
$action = 'view';
switch($action)
{
  default:
    echo genQueueView();
}
?>
<script type="text/javascript">
UpdateQueues();
</script>
<?php
//o-------------------------------------------------------------------------------o//
function genQueueView()
//o-------------------------------------------------------------------------------o//
{
  $queue_table = '<table class="table1" cellpadding="2" cellspacing="2">';
  $hostlist = new myJAM_Host();
  $HostIDList = $hostlist->ID;
  foreach($HostIDList as $hostid)
  {
    $queue_table .= genQueueTableByHost(new myJAM_Host($hostid));
  }
  $queue_table .= '</table>';
  return $queue_table;
}
//o-------------------------------------------------------------------------------o//
function genQueueTableByHost($host)
//o-------------------------------------------------------------------------------o//
{
  global $ActiveUser;
  $admin = $ActiveUser->ADMIN;
  $db = new myJAM_db();
  $sql = 'SELECT qid, queue_descr, queue_adjust FROM Queues WHERE hid=\''.(int)$host->ID.'\'';
  $vQueues = $db->query($sql);
  $out = '<tr>'
          .'<td colspan="0">'
            .'<div style="float:left">';
  if($admin)
  {
       $out .= '<a href="main.php?page=delete_host&hid='.(int)$host->ID.'">'
                .'<img src="images/Trash-Empty-icon_16x16.png" title="delete"/>'
              .'</a>'
              .'&nbsp;&nbsp;&nbsp;';
  }
       $out .= '<b>Host: </b>'
              .$host->Name
              .' ('.$host->BatchSystem.')'
            .'</div>'
            .'<div style="float:right">';
  $out .= '</div>'
          .'</td>'
        .'</tr>';
  //Header of the table;
  $out .= '<tr class="cell3">';
  $out .= '<td>Queue Name</td>'
           .'<td>Queue Weight</td>'
           .'<td>Running Jobs</td>';
  $out .= '<td>Load</td>';
  $out .= '</tr>';
  foreach($vQueues as $queue)
  {
    $out .= '<tr>';
    $out .= '<td>'.$queue['queue_descr'].'</td>'
           .sprintf('<td align="right"><div class="quemargin">%.2f</div></td>',$queue['queue_adjust'])
           .sprintf('<td align="right"><div class="quemargin" id="qvr_%d">&nbsp;</div></td>', $queue['qid'])
           .sprintf('<td id="qvl_%d"></td>', $queue['qid']);
    $out .= '</tr>';
  }
  $out .= '<tr><td colspan="0"><div class="queadmin"></div></td></tr>'
         .'<tr>'
         .'<td><b>Total</b></td>'
         .'<td></td>'
         .sprintf('<td align="right"><div class="quemargin" id="qvr_h%d">&nbsp;</div></td>', $host->ID)
         .sprintf('<td id="qvl_h%d"></td>', $host->ID)
         .'</tr>'
         .'<tr><td>&nbsp;</td></tr>';
  return $out;
}
//o-------------------------------------------------------------------------------o//
function genRefreshButtons()
//o-------------------------------------------------------------------------------o//
{
  $form1 = new myJAM_Form_SubForm();
  $button = new myJAM_Form_Element_Button('refresh', ' Refresh ');
  $button->onclick('UpdateQueues();');
  $form1->addElement($button);
  $form2 = new myJAM_Form_SubForm();
  $form2->addElement(new myJAM_Form_Element_Caption('<span id="auto_fresh" style="color:#ff0000;"><b>Auto Refresh</b></span>'));
  $form2->addElement(new myJAM_Form_Element_Caption('&nbsp;&nbsp;'));
  $button = new myJAM_Form_Element_Button('start', ' Start ');
  $button->onclick('Start_Auto()');
  $form2->addElement($button);
  $form2->addElement(new myJAM_Form_Element_Caption('&nbsp;'));
  $button = new myJAM_Form_Element_Button('stop', ' Stop ');
  $button->onclick('Stop_Auto()');
  $form2->addElement($button);
  $form2->addElement(new myJAM_Form_Element_Caption('&nbsp;'));
  $text = new myJAM_Form_Element_Text('time', 60, 5, 5);
  $text->onchange('ChangeReloadIntervall(this);');
  $form2->addElement($text);
  $form2->addElement(new myJAM_Form_Element_Caption('sec.'));
  $out = '<table style="width:100%">'
          .'<tr>'
            .'<td style="text-align:center;background-color:#e0e0e0">'
              .$form1->renderForm()
            .'</td>'
            .'<td style="text-align:center;background-color:#e0e0e0">'
              .$form2->renderForm()
            .'</td>'
            .'<td style="text-align:center;background-color:#e0e0e0;width:42%">'
            .'</td>';
  $out .= '</tr>'
        .'</table>';
  return $out;
}
