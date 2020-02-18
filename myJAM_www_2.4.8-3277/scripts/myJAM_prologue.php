#!/usr/bin/php
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
require_once(_FULLPATH.'/classes/class_myJAM_Abstract_Job.php');
$_CFG_BATCHSYSTEM_TYPE=NULL;
$_CFG_BATCHSYSTEM_SERVER=NULL;
$_CFG_BATCHSYSTEM_BINDIR=NULL;
require(_FULLPATH.'/config/CFG_batchsystem.php');
define('_CFG_BATCHSYSTEM_TYPE', $_CFG_BATCHSYSTEM_TYPE);
define('_CFG_BATCHSYSTEM_SERVER', $_CFG_BATCHSYSTEM_SERVER);
define('_CFG_BATCHSYSTEM_BINDIR', $_CFG_BATCHSYSTEM_BINDIR);
$Job = myJAM_Abstract_Job::Factory(_CFG_BATCHSYSTEM_TYPE);
$Job->GetPrologueArgs($argv);
$Job->GetJob();
$Job->dump();
//Check Project
if(!is_object($Job->Project))
{
  if($Job->Project == NULL)
  {
    echo "myJAM>> Accounting String invalid. Corresponding Project unknown!\n";
    die(1);
  }
  else if ($Job->Project == "n/a")
  {
    echo "myJAM>> No Accounting String given! Job cannot be started!\n";
    die(2);
  }
  else
  {
    echo "myJAM>> FATAL ERROR 0x7296 in myJAM_Prologue!!!\n";
    die(3);
  }
}
echo "myJAM_Prologue>> Project '".$Job->Project->Name."' is valid.\n";
//Check is project is enabled
if(!$Job->Project->Enabled)
{
  echo "myJAM>> Project '".$Job->Project->Name."' is not enabled!!\n";
  die(8);
}
echo "myJAM_Prologue>> Project is enabled.\n";
//Check start and end date of project
$Now = (int)date('U');
$Start = (int)strtotime($Job->Project->StartDate);
if($Start > $Now)
{
  echo "myJAM>> Project hasn't started yet. Start date is ".$Job->Project->StartDate."\n";
  die(9);
}
if($Job->Project->EndDate != '0000-00-00')
{
  $End = (int)strtotime($Job->Project->EndDate);
  if($End < $Now)
  {
    echo "myJAM_Prologue>> Project has ended on ".$Job->Project->EndDate."\n";
    die(10);
  }
}
//Check User
if(!is_object($Job->User))
{
  if($Job->User == NULL)
  {
    echo "myJAM_Prologue>> User unknown\n";
    die(4);
  }
  else if ($Job->User == "n/a")
  {
    echo "myJAM_Prologue>> No User given! Job cannot be started!\n";
    die(5);
  }
  else
  {
    echo "myJAM>> FATAL ERROR 0x865b in myJAM_Prologue!!!\n";
    die(6);
  }
}
echo "myJAM_Prologue>> User '".$Job->User->UserName."' is valid.\n";
//Check if user is member of the project
$isMember = false;
foreach($Job->User->Projects as $proj)
{
  if($proj->ID == $Job->Project->ID)
  {
    $isMember = true;
    break;
  }
}
if (!$isMember)
{
  echo "myJAM_Prologue>> User '".$Job->User->UserName."' is not a member of the project '".$Job->Project->Name."'!!\n";
  die(7);
}
echo "myJAM_Prologue>> User '".$Job->User->UserName."' is member of the project '".$Job->Project->Name."'.\n";
//Check, if project is allowed on this host
$HostAllowed = false;
foreach($Job->Project->Hosts as $host)
{
  if($host->ID == $Job->Host->ID)
  {
    $HostAllowed = true;
    break;
  }
}
if(!$HostAllowed)
{
  echo "myJAM_Prologue>> Project '".$Job->Project->Name."' is not allowed to use host '".$Job->Host->Name."'!!\n";
  die(11);
}
echo "myJAM_Prologue>> Project '".$Job->Project->Name."' is allowed to use host '".$Job->Host->Name."'.\n";
//Check, if project is allowed to use queue
$QueueAllowed = false;
foreach($Job->Project->Queues as $queue)
{
  if($queue->ID == $Job->Queue->ID)
  {
    $QueueAllowed = true;
    break;
  }
}
if(!$QueueAllowed)
{
  echo "myJAM_Prologue>> Project '".$Job->Project->Name."' is not allowed to use queue '".$Job->Queue->Name."'!!\n";
  die(12);
}
echo "myJAM_Prologue>> Project '".$Job->Project->Name."' is allowed to use queue '".$Job->Queue->Name."'.\n";
//Check Accounting!
if($Job->Project->Billable)
{
  echo "myJAM_Prologue>> Project is billable.\n";
  $ReqSUs = $Job->nbCores * $Job->ReqWallTime;
  echo "myJAM_Prologues>> Project requests ".$ReqSUs." SUs.\n";
  if($Job->Project->Overrun)
  {
    //overrun allowed. Just Start the Job!
    echo "myJAM_Prologue>> Overrun allowed for project '".$Job->Project->Name."'.\n";
  }
  else
  {
    //check SUs
    $Account = $Job->Project->SUs;
    if($Account < $ReqSUs)
    {
      echo "myJAM_Prologue>> Project '".$Job->Project->Name."' has not enough SUs to start this job.\n"
      . "     >> Requested: ".$ReqSUs."\n"
      . "     >> Account  : ".$Account."\n";
      die(13);
    }
  }
}
else
{
  echo "myJAM_Prologue>> Project '".$Job->Project->Name."' is free!\n";
}
$Job->Insert();
print "myJAM_Prologue>> Job ".$Job->JobID." has been started.\n";
print "SUCCESS!\n";
return 0;
