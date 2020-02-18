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
require_once(_FULLPATH.'/helper/myJAM_forms/myJAM_Form.php');
require_once(_FULLPATH.'/helper/myJAM_forms/myJAM_Form_Element_SubmitButton.php');
require_once(_FULLPATH.'/fxn/getDummyQueueID.php');
require_once(_FULLPATH.'/fxn/getDummyHostID.php');
//o-------------------------------------------------------------------------------o//
function getActiveJobs($host)
//o-------------------------------------------------------------------------------o//
{
  $db = new myJAM_db();
  $sql = 'SELECT count(pbs_jobnumber), job_state'
        .' FROM Jobs'
        .' WHERE hid='.(int)$host->ID
        .' AND (job_state="R" OR job_state="Q")';
  $res = $db->query($sql);
  $tmp = array();
  $tmp['running'] = 0;
  $tmp['queued'] = 0;
  foreach ($res as $row)
  {
    if($row['job_state'] == 'R')
    {
      $tmp['running'] += (int)$row['count(pbs_jobnumber)'];
    }
    elseif ($row['job_state'] == 'Q')
    {
      $tmp['queued'] += (int)$row['count(pbs_jobnumber)'];
    }
  }
  return $tmp;
}
//o-------------------------------------------------------------------------------o//
function DH_show_Pre($host)
//o-------------------------------------------------------------------------------o//
{
  echo '<b>Deleting host: ' . $host->Name . '</b></br>';
  $nbJobs = getActiveJobs($host);
  $nbTotalJobs = $nbJobs['running'] + $nbJobs['queued'];
  // Test for active Jobs
  echo '<p></p>';
  echo '<b>Active jobs:</b> ' . $nbTotalJobs . '<br/>';
  if ($nbTotalJobs > 0)
  {
    echo '&nbsp;&nbsp;running: ' . $nbJobs['running'] . '<br/>';
    echo '&nbsp;&nbsp;queued: ' . $nbJobs['queued'] . '<br/>';
    echo ('<p></p><span style="color:red"><b>Host can not be deleted while there are active jobs on this host!</b></span>');
  }
  // Test for associated queues
  $Queues = $host->Queues;
  echo '<p></p>';
  echo '<b>Queues of this host:</b> ' . count($Queues) . '<br/>';
  if (count($Queues) > 0)
  {
    foreach ($Queues as $queue)
    {
      echo '&nbsp;' . $queue->Name . '<br/>';
    }
    echo '<p></p>';
    echo ('<p></p><span style="color:red"><b>These queues will also be deleted!!!</b></span>');
  }
  $form = new myJAM_Form('main.php?page=delete_host&hid=' . $host->ID . '&action=delete');
  $form->addElement(new myJAM_Form_Element_SubmitButton('submitbutton', ' DELETE HOST '));
  echo '<p></p>';
  echo $form->renderForm();
}
//o-------------------------------------------------------------------------------o//
function DH_transcribe_Jobs($host)
//o-------------------------------------------------------------------------------o//
{
  $dummyQueueID = getDummyQueueID();
  $dummyHostID = getDummyHostID();
  $db = new myJAM_db();
  $sql = 'UPDATE Jobs SET'
        .' hid='.(int)$dummyHostID
        .',qid='.(int)$dummyQueueID
        .' WHERE hid='.(int)$host->ID;
  $db->DoSQL($sql);
  echo $db->affected_rows().' jobs transcribed.<br/><p></p>';
}
//o-------------------------------------------------------------------------------o//
function DH_deleteQueues($host)
//o-------------------------------------------------------------------------------o//
{
  $db = new myJAM_db();
  //get Queues from this host
  $sql = 'SELECT qid from Queues WHERE hid='.(int)$host->ID;
  $vQueues = $db->query($sql);
  $qStatements = array();
  foreach($vQueues as $row)
  {
    $qStatements[] = 'qid='.(int)$row['qid'];
  }
  if(count($qStatements) > 0)
  {
    $sql = 'DELETE FROM Meta_ProjectsQueues WHERE'
          .' ('
          .implode(' OR ', $qStatements)
          .')';
    $db->DoSQL($sql);
    echo $db->affected_rows().' project/queues relations deleted.<br/><p></p>';
  }
  $sql = 'DELETE FROM Queues WHERE hid='.(int)$host->ID;
  $db->DoSQL($sql);
  echo $db->affected_rows().' queues deleted.<br/><p></p>';
}
//o-------------------------------------------------------------------------------o//
function DH_deleteHost($host)
//o-------------------------------------------------------------------------------o//
{
  $db = new myJAM_db();
  $sql = 'DELETE FROM Meta_ProjectsHosts WHERE hid='.(int)$host->ID;
  $db->DoSQL($sql);
  echo $db->affected_rows().' project/hosts relations deleted.<br/><p></p>';
  $sql = 'DELETE FROM Hosts WHERE hid='.(int)$host->ID;
  $db->DoSQL($sql);
  echo 'Host deleted.<br/><p></p>';
}
//o-------------------------------------------------------------------------------o//
function DH_delete_it($host)
//o-------------------------------------------------------------------------------o//
{
  echo '<b>Host: ' . $host->Name . '</b> is going to be deleted for real now!<br/><p></p>';
  DH_transcribe_Jobs($host);
  DH_deleteQueues($host);
  DH_deleteHost($host);
}
//o-------------------------------------------------------------------------------o//
//o-------------------------------------------------------------------------------o//
//o-------------------------------------------------------------------------------o//
$admin = $ActiveUser->ADMIN;
if(!$admin)
{
  die('You are now allowed to see this page!');
}
$action = 'show';
if(isset($_GET['action']))
{
  $action = (string)$_GET['action'];
}
if(!isset($_GET['hid']))
{
  die('myJAM>> FATAL ERROR 0x5cb4');
}
$host = new myJAM_Host((int)$_GET['hid']);
if(!is_object($host) || !is_scalar($host->ID) || $host->ID < 1)
{
  die('myJAM>> FATAL ERROR 0x599f');
}
switch ($action)
{
  case 'show':
    DH_show_Pre($host);
    break;
  case 'delete':
    DH_delete_it($host);
    break;
  default:
    die('myJAM>> FATAL ERROR!!!');
}
