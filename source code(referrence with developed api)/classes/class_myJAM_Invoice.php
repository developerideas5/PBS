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

require_once(_FULLPATH.'/classes/class_myJAM_DB.php');
require_once(_FULLPATH.'/classes/class_myJAM_Project.php');
require_once(_FULLPATH.'/classes/class_myJAM_User.php');
require_once(_FULLPATH.'/fxn/CalcSUsByDate.php');
class myJAM_Invoice
{
  PROTECTED $db;
  PROTECTED $ID = NULL;
  PROTECTED $Date = NULL; //type int => UNIXTIMESTAMP
  PROTECTED $Period = NULL; //type int => UNIXTIMESTAMP
  PROTECTED $Maker = NULL; //type myJAM_User
  protected $pid = NULL;
  protected $ProjectObj = NULL;
  protected $TotalSUs = NULL;
  protected $OldSUs = NULL;
  protected $actualSUs = NULL;
  protected $UserListData = array();
  //o-------------------------------------------------------------------------------o
  PUBLIC function __construct($id)
  //o-------------------------------------------------------------------------------o
  {
    if (!is_object($this->db))
    {
      $this->db = new myJAM_DB();
    }
    $sql = 'SELECT UNIX_TIMESTAMP(date)'
          .',UNIX_TIMESTAMP(period)'
          .',Maker'
          .',pid'
          .' FROM Invoices'
          .' WHERE InvoiceID=\'' . (int)$id . '\'';
    $Invoice = $this->db->query($sql);
    if ($this->db->num_rows() != 1)
    {
      die("myJAM>> FATAL ERROR: Could not instantiate Invoice with ID: $id");
    }
    $this->ID = (int)$id;
    $this->Date = (int)$Invoice[0]['UNIX_TIMESTAMP(date)'];
    $this->Period = (int)$Invoice[0]['UNIX_TIMESTAMP(period)'];
    $this->pid = (int)$Invoice[0]['pid'];
    $Maker = new myJAM_User((int)$Invoice[0]['Maker']);
    if (!is_object($Maker) || !is_scalar($Maker->ID) || $Maker->ID < 1)
    {
      die('myJAM>> FATAL ERROR 0x0fd4 in class myJAM_Invoice!!!');
    }
    $this->Maker = $Maker;
    unset($Maker);
    $this->CalcUserListData();
  }
//o-------------------------------------------------------------------------------o
  PUBLIC function __get($name)
//o-------------------------------------------------------------------------------o
  {
    switch($name)
    {
      case "ID":
        return $this->ID;
      case "Date":
        return $this->Date;
      case "Period":
        return $this->Period;
      case "Maker":
        return $this->Maker;
      case "Project":
        return $this->getProject();
      case 'TotalSUs':
        return (float)$this->TotalSUs;
      case 'OldSUs':
        return (float)$this->OldSUs;
      case 'actualSUs':
        return (float)$this->actualSUs;
      case 'UserListData':
        return $this->UserListData;
      default:
        return NULL;
    }
  }
  //o-------------------------------------------------------------------------------o
  STATIC PUBLIC function CreateInvoice($Project, $period, $Maker)
  //o-------------------------------------------------------------------------------o
  {
    if (!is_object($Project) || !is_scalar($Project->ID) || (int)$Project->ID < 1)
    {
      die('myJAM>> FATAL ERROR 0x7cbe in class myJAM_Invoice');
    }
    if (!is_object($Maker) || !is_scalar($Maker->ID) || (int)$Maker->ID < 1)
    {
      die('myJAM>> FATAL ERROR 0xaa7e in class myJAM_Invoice');
    }
    $month = date('m', $period);
    $year = date('Y', $period);
    if (!checkdate($month, 01, $year))
    {
      die('myJAM>> FATAL ERROR 0x585b in class myJAM_Invoice');
    }
//just in case, the 'day'-part of $period is not '01' we rebuild this timestamp
    $period = strtotime($year . '-' . $month . '-01');
    $sql = 'INSERT INTO Invoices (pid, date, period, Maker)'
          .' VALUES (' . (int)$Project->ID . ', NOW(), FROM_UNIXTIME(' . (int)$period . '), ' . (int)$Maker->ID . ')';
    $db = new myJAM_DB();
    $db->DoSQL($sql);
//now get the new InvoiceID...
//    $sql = 'SELECT * FROM Invoices'
//          .' WHERE pid=\'' . (int)$Project->ID . '\''
//          .' AND period=FROM_UNIXTIME(' . (int)$period . ')'
//          .' AND Maker=\'' . (int)$Maker->ID . '\'';
//    $res = $db->query($sql);
//    if ($db->num_rows() != 1)
//    {
//      die('myJAM>> FATAL ERROR 0xb17b in class myJAM_Invoice');
//    }
//    $InvoiceID = $res[0]['InvoiceID'];
    $InvoiceID = $db->last_insert_id();
    $Invoice = new myJAM_Invoice($InvoiceID);
    return $Invoice;
  }
  //o-------------------------------------------------------------------------------o
  protected function getProject()
  //o-------------------------------------------------------------------------------o
  {
    if($this->ProjectObj == NULL || !is_a($this->ProjectObj, 'myJAM_Project'))
    {
      $this->ProjectObj = new myJAM_Project((int)$this->pid);
      if(!is_a($this->ProjectObj, 'myJAM_Project') ||
         !is_scalar($this->ProjectObj->ID) ||
         (int)$this->ProjectObj->ID < 1)
      {
        die('myJAM>> FATAL ERROR: could not instanciate project in myJAM_Invoice::getProject');
      }
    }
    return $this->ProjectObj;
  }
  //o-------------------------------------------------------------------------------o
  protected function calcUserListData()
  //o-------------------------------------------------------------------------------o
  {
    $this->TotalSUs = 0.0;
    $this->UserListData = array();
    $month = date('m', $this->Period);
    $year = date('Y', $this->Period);
    $ProjUserList = $this->Project->Users;
    foreach($ProjUserList as $user)
    {
      $sql = 'SELECT sum(actual_su) FROM Jobs'
            .' WHERE uid='.(int)$user->ID
            .' AND pid='.(int)$this->Project->ID
            .' AND (job_state=\'F\' OR date>0)'
            .' AND month(date)='.(int)$month
            .' AND year(date)='.(int)$year;
      $SUs = $this->db->query($sql);
      if($this->db->num_rows() != 1)
      {
        die('myJAM>> FATAL ERROR while getting usage with uid='.$user->ID.' and pid='.$Project->ID);
      }
      else
      {
        $this->TotalSUs += (float)$SUs[0]["sum(actual_su)"];
        $data = array('FullName' => $user->FullName,
                      'SUs' => (float)$SUs[0]["sum(actual_su)"]);
        $this->UserListData[$user->UserName] = $data;
      }
    }
    // Values
    $last_day_of_month_before = mktime(date('H',$this->Period), date('i',$this->Period), date('s',$this->Period), date('m',$this->Period)-1 , date('d',$this->Period), date('Y',$this->Period));
    $last_day_of_month_before = strtotime(date('t.m.Y', $last_day_of_month_before));
    $this->OldSUs = CalcSUsByDate($this->Project, $last_day_of_month_before);
    if($this->Project->Billable)
    {
      $this->actualSUs = $this->OldSUs - $this->TotalSUs;
    }
    else
    {
      $this->actualSUs = $this->OldSUs + $this->TotalSUs;
    }
  }
}
