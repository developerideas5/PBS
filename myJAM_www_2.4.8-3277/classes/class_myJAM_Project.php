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
require_once(_FULLPATH.'/classes/class_myJAM_User.php');
require_once(_FULLPATH.'/classes/class_myJAM_Host.php');
require_once(_FULLPATH.'/classes/class_myJAM_Queue.php');
require_once(_FULLPATH.'/classes/class_myJAM_CostModel.php');
require_once(_FULLPATH.'/classes/class_myJAM_Invoice.php');
require_once(_FULLPATH.'/classes/class_myJAM_Institute.php');
class myJAM_Project
{
  PROTECTED STATIC $db;
  PROTECTED STATIC $nbProjects;
  PROTECTED STATIC $vProjectID;
  PROTECTED STATIC $vProjectIDHash;
  PROTECTED STATIC $vProjectIDListHash;
  PROTECTED STATIC $vProjectName;
  PROTECTED STATIC $vProjectNameHash;
  PROTECTED STATIC $vProjectUsers;
  PROTECTED STATIC $vProjectQueues;
  PROTECTED STATIC $vProjectOwner;
  PROTECTED STATIC $vProjectDescription;
  PROTECTED STATIC $vProjectStartDate;
  PROTECTED STATIC $vProjectEndDate;
  PROTECTED STATIC $vProjectEnabled;
  PROTECTED STATIC $vProjectHosts;
  PROTECTED STATIC $vProjectCostModel;
  PROTECTED STATIC $vProjectBillable;
  PROTECTED STATIC $vProjectOverrun;
  PROTECTED STATIC $vInstIDs;
  PROTECTED $pProjectID;
  //o-------------------------------------------------------------------------------o
  PUBLIC function __construct($project=NULL)
  //o-------------------------------------------------------------------------------o
  {
    if (! is_object(self::$db))
    {
      self::$db = new myJAM_DB();
      self::$nbProjects = 0;
      self::$vProjectID = array();
      self::$vProjectIDHash = array();
      self::$vProjectName = array();
      self::$vProjectNameHash = array();
      self::$vProjectUsers = array();
      self::$vProjectQueues = array();
      self::$vProjectOwner = array();
      self::$vProjectDescription = array();
      self::$vProjectStartDate = array();
      self::$vProjectEndDate = array();
      self::$vProjectEnabled = array();
      self::$vProjectHosts = array();
      self::$vProjectCostModel = array();
      self::$vProjectBillable = array();
      self::$vProjectOverrun = array();
      self::$vInstIDs = array();
      $query = 'SELECT pid, name, description, proj_uid_owner, project_start_date, project_end_date'
              .', proj_enable, cid, proj_billable, allow_overrun, InstID'
              .' FROM Projects'
              .' ORDER BY name';
      $vProjects = self::$db->query($query);
      self::$nbProjects = self::$db->num_rows();
      for ($i = 0; $i < self::$nbProjects; $i++)
      {
        $pid = $vProjects[(int)$i]['pid'];
        $pName = $vProjects[(int)$i]['name'];
        self::$vProjectID[(int)$i] = $pid;
        self::$vProjectIDHash[$pName] = $pid;
        self::$vProjectIDListHash[$pid] = (int)$i;
        self::$vProjectName[(int)$i] = $pName;
        self::$vProjectNameHash[$pid] = $pName;
        self::$vProjectDescription[$pid] = $vProjects[(int)$i]['description'];
        self::$vProjectStartDate[$pid] = $vProjects[(int)$i]['project_start_date'];
        self::$vProjectEndDate[$pid] = $vProjects[(int)$i]['project_end_date'];
        self::$vProjectEnabled[$pid] = $vProjects[(int)$i]['proj_enable'];
        self::$vProjectOwner[$pid] = new myJAM_User($vProjects[(int)$i]['proj_uid_owner']);
        self::$vProjectQueues[$pid] = self::GetQueues($pid);
        self::$vProjectUsers[$pid] = self::GetUsers($pid);
        self::$vProjectHosts[$pid] = self::GetHosts($pid);
        self::$vProjectCostModel[$pid] = new myJAM_CostModel($vProjects[(int)$i]['cid']);
        self::$vProjectBillable[$pid] = $vProjects[(int)$i]['proj_billable'];
        self::$vProjectOverrun[$pid] = $vProjects[(int)$i]['allow_overrun'];
        self::$vInstIDs[$pid] = $vProjects[(int)$i]['InstID'];
      }
    }
    if ($project)
    {
      if (! @self::$vProjectNameHash[$project])
      {
        if (! @$this->pProjectID = self::$vProjectIDHash[$project])
        {
          return NULL;
        }
      }
      else
      {$this->pProjectID = $project;}
    }
    else
    {$this->pProjectID = 0;}
  }
  //o-------------------------------------------------------------------------------o
  PUBLIC function __get($name)
  //o-------------------------------------------------------------------------------o
  {
    if ($this->pProjectID)
    {
      switch($name)
      {
        case "Name":
          return self::$vProjectNameHash[$this->pProjectID];
        case "ID":
          return $this->pProjectID;
        case "nbUsers":
          return count(self::$vProjectUsers[$this->pProjectID]);
        case "Users":
          return self::$vProjectUsers[$this->pProjectID];
        case "Queues":
          return self::$vProjectQueues[$this->pProjectID];
        case "Hosts":
          return self::$vProjectHosts[$this->pProjectID];
        case "Owner":
          return self::$vProjectOwner[$this->pProjectID];
        case "StartDate":
          return self::$vProjectStartDate[$this->pProjectID];
        case "EndDate":
          return self::$vProjectEndDate[$this->pProjectID];
        case "Enabled":
          return self::$vProjectEnabled[$this->pProjectID];
        case "Description":
          return self::$vProjectDescription[$this->pProjectID];
        case "SUs":
          return $this->GetSUs();
        case "Running":
          return $this->GetNbRunning();
        case "Queued":
          return $this->GetNbQueued();
        case "Finished":
          return $this->GetNbFinished();
        case "CostModel":
          return self::$vProjectCostModel[$this->pProjectID];
        case "Billable":
          return self::$vProjectBillable[$this->pProjectID];
        case "Overrun":
          return self::$vProjectOverrun[$this->pProjectID];
        case "InvoiceAddress":
          return $this->GetInvoiceAddress();
        case "LastInvoice":
          return $this->GetLastInvoice();
        case "Institute":
          return $this->GetInstitute();
        default:
          return NULL;
      }
    }
    switch($name)
    {
      case "Name":
        return self::$vProjectNameHash;
      case "NameList":
        return self::$vProjectName;
      case "ID":
        return self::$vProjectIDHash;
      case "IDList":
        return self::$vProjectID;
      case "nbUsers":
        $vNbUsers = array();
        for ($i = 0; $i < self::$nbProjects; $i++)
        {
          $pid = self::$vProjectID[$i];
          $vNbUsers[$pid] = count(self::$vProjectUsers[$pid]);
        }
        return $vNbUsers;
      case "Users":
        return self::$vProjectUsers;
      case "Queues":
        return self::$vProjectQueues;
      case "Hosts":
        return self::$vProjectHosts;
      case "Owner":
        return self::$vProjectOwner;
      case "StartDate":
        return self::$vProjectStartDate;
      case "EndDate":
        return self::$vProjectEndDate;
      case "Enabled":
        return self::$vProjectEnabled;
      case "Description":
        return self::$vProjectDescription;
      case "SUs":
        return self::GetSUs();
      case "nb":
        return self::$nbProjects;
      case "Running":
        return self::GetNbRunning();
      case "Queued":
        return self::GetNbQueued();
      case "Finished":
        return self::GetNbFinished();
      case "CostModel":
        return self::$vProjectCostModel;
      case "Billable":
        return self::$vProjectBillable;
      case "Overrun":
        return self::$vProjectOverrun;
      case "InstID":
        return self::$vInstIDs;
    }
    return NULL;
  }
  //o-------------------------------------------------------------------------------o
  PUBLIC function __set($name, $val)
  //o-------------------------------------------------------------------------------o
  {
    if (!isset($val))
    {
      die("myJAM>> FATAL ERROR 0x4dda in class myJAM_Project!");
    }
    if ($this->pProjectID)
    {
      switch($name)
      {
        case "Name":
          $pointer = (int)self::$vProjectIDListHash[$this->pProjectID];
          $oldname = self::$vProjectNameHash[$this->pProjectID];
          unset(self::$vProjectIDHash[$oldname]);
          self::$vProjectIDHash[$val] = $this->pProjectID;
          self::$vProjectIDListHash[$this->pProjectID] = $pointer;
          self::$vProjectName[$pointer] = $val;
          self::$vProjectNameHash[$this->pProjectID] = $val;
          $sql_name = "name";
          break;
        case "Description":
          self::$vProjectDescription[$this->pProjectID] = $val;
          $sql_name = "description";
          break;
        case "StartDate":
          self::$vProjectStartDate[$this->pProjectID] = $val;
          $sql_name = "project_start_date";
          break;
        case "EndDate":
          self::$vProjectEndDate[$this->pProjectID] = $val;
          $sql_name = "project_end_date";
          break;
        case "Owner":
          if (!is_a($val, 'myJAM_User') || !is_scalar($val->ID) || !(int)$val->ID > 0)
          {
            return NULL;
          }
          self::$vProjectOwner[$this->pProjectID] = $val;
          $val = (int)self::$vProjectOwner[$this->pProjectID]->ID;
          $sql_name = "proj_uid_owner";
          break;
        case "CostModel":
          if (! $val->ID)
          {
            return NULL;
          }
          self::$vProjectCostModel[$this->pProjectID] = $val;
          $val = (int)self::$vProjectCostModel[$this->pProjectID]->ID;
          $sql_name = "cid";
          break;
        case "Enabled":
          if ($val == 1)
          {
            self::$vProjectEnabled[$this->pProjectID] = 1;
          }
          elseif ($val == 0)
          {
            self::$vProjectEnabled[$this->pProjectID] = 0;
          }
          else
          {
            return NULL;
          }
          $sql_name = "proj_enable";
          break;
        case "Billable":
          if ($val == 1)
          {
            self::$vProjectBillable[$this->pProjectID] = 1;
          }
          elseif ($val == 0)
          {
            self::$vProjectBillable[$this->pProjectID] = 0;
          }
          else
          {
            return NULL;
          }
          $sql_name = "proj_billable";
          break;
        case "Overrun":
          if ($val == 1)
          {
            self::$vProjectOverrun[$this->pProjectID] = 1;
          }
          elseif ($val == 0)
          {
            self::$vProjectOverrun[$this->pProjectID] = 0;
          }
          else
          {
            return NULL;
          }
          $sql_name = "allow_overrun";
          break;
        case "InvoiceAddress":
          $this->SetInvoiceAddress($val);
          return $val;
          break;
        case "Institute":
          if (!is_a($val, 'myJAM_Institute') || !is_scalar($val->ID) || (int)$val->ID < 0)
          {
            throw new Exception('Values must be an instance of myJAM_Institute');
          }
          $val = (int)$val->ID;
          $sql_name = "InstID";
          self::$vInstIDs[$this->pProjectID] = $val;
          break;
        default:
          return NULL;
      }
      $sql = 'UPDATE Projects SET '
            .$sql_name.'=\''.mysql_real_escape_string($val).'\''
            .' WHERE pid=\''.$this->pProjectID.'\'';
      self::$db->DoSQL($sql);
      return $val;
    }
    return NULL;
  }
  //o-------------------------------------------------------------------------------o
  PUBLIC function AddUser($user)
  //o-------------------------------------------------------------------------------o
  {
    if(!is_a($user, 'myJAM_User') || is_array($user->ID))
    {
      die("myJAM>> FATAL ERROR 0xdba5 in class myJAM_Project!");
    }
    if ($this->pProjectID)
    {
      self::$vProjectUsers[$this->pProjectID][] = $user;
      if ($this->UpdateMembersSQL())
      {
        return count(self::$vProjectUsers[$this->pProjectID]);
      }
      else
      {
        return NULL;
      }
    }
    return NULL;
  }
  //o-------------------------------------------------------------------------------o
  PUBLIC function DelUser($user)
  //o-------------------------------------------------------------------------------o
  {
    if (!is_a($user, 'myJAM_User') || !is_scalar($user->ID) || (int)$user->ID < 1)
    {
      die("myJAM>> FATAL ERROR 0x3b09 in class myJAM_Project!");
    }
    if ($this->pProjectID)
    {
      //search for the given user in self::$vProjectUsers
      foreach(self::$vProjectUsers[$this->pProjectID] as $pUser=>$OldUser)
      {
        if ($OldUser->ID == $user->ID)
        {
          unset(self::$vProjectUsers[$this->pProjectID][$pUser]);
          if ($this->UpdateMembersSQL())
          {
            return count(self::$vProjectUsers[$this->pProjectID]);
          }
        }
      }
    }
    return NULL;
  }
  //o-------------------------------------------------------------------------------o
  PRIVATE function GetUsers($pid)
  //o-------------------------------------------------------------------------------o
  {
    $sql = "SELECT uid"
         ." FROM Meta_ProjectsUsers"
         ." WHERE pid='" . (int)$pid . "'"
         ." AND uid NOT IN (SELECT uid FROM Users WHERE real_username LIKE 'formerUser%')";
    $users = self::$db->query($sql);
    $lUserObj = array();
    if (self::$db->affected_rows() > 0)
    {
      foreach($users as $user)
      {
        $lUserObj[] = new myJAM_User($user["uid"]);
      }
    }
    unset($users);
    return $lUserObj;
  }
  //o-------------------------------------------------------------------------------o
  PROTECTED function UpdateMembersSQL()
  //o-------------------------------------------------------------------------------o
  {
    if ($this->pProjectID)
    {
      //delete old entries from meta table
      self::$db->query("DELETE FROM Meta_ProjectsUsers WHERE pid='" . (int)$this->pProjectID . "'");
      if (count(self::$vProjectUsers[$this->pProjectID]) > 0)
      {
        //gen insert statement
        $sql = '';
        foreach(self::$vProjectUsers[$this->pProjectID] as $user)
        {
          if ($sql)
          {
            $sql .= ',';
          }
          $sql .= "('" . (int)$this->pProjectID . "', '" . (int)$user->ID . "')";
        }
        $sql = 'INSERT INTO Meta_ProjectsUsers (pid, uid) VALUES ' . $sql;
        self::$db->query($sql);
      }
      return 1;
    }
    return NULL;
  }
  //o-------------------------------------------------------------------------------o
  PUBLIC function AddQueue($queue)
  //o-------------------------------------------------------------------------------o
  {
    if (!is_a($queue, 'myJAM_Queue') || !is_scalar($queue->ID) || (int)$queue->ID < 1)
    {
      die("myJAM>> FATAL ERROR 0x06ec in class myJAM_Project!");
    }
    if ($this->pProjectID)
    {
      self::$vProjectQueues[$this->pProjectID][] = $queue;
      if ($this->UpdateQueuesSQL())
      {
        return count(self::$vProjectQueues[$this->pProjectID]);
      }
    }
    return NULL;
  }
  //o-------------------------------------------------------------------------------o
  PUBLIC function DelQueue($queue)
  //o-------------------------------------------------------------------------------o
  {
    if (!is_a($queue, 'myJAM_Queue') || !is_scalar($queue->ID) || (int)$queue->ID < 1)
    {
      die("myJAM>> FATAL ERROR 0x6e54 in class myJAM_Project!");
    }
    if ($this->pProjectID)
    {
      //search for the given queue in self::$vProjectQueues
      foreach(self::$vProjectQueues[$this->pProjectID] as $pQueue => $OldQueue)
      {
        if ($OldQueue->ID == $queue->ID)
        {
          unset(self::$vProjectQueues[$this->pProjectID][$pQueue]);
          if ($this->UpdateQueuesSQL())
          {
            return count(self::$vProjectQueues[$this->pProjectID]);
          }
        }
      }
    }
    return NULL;
  }
  //o-------------------------------------------------------------------------------o
  PROTECTED function UpdateQueuesSQL()
  //o-------------------------------------------------------------------------------o
  {
    if ($this->pProjectID)
    {
      //delete old entries from meta table
      self::$db->query("DELETE FROM Meta_ProjectsQueues WHERE pid='" . (int)$this->pProjectID . "'");
      if (count(self::$vProjectQueues[$this->pProjectID]) > 0)
      {
        //gen insert statement
        $sql = "";
        foreach(self::$vProjectQueues[$this->pProjectID] as $queue)
        {
          if ($sql)
          {
            $sql .= ",";
          }
          $sql .= "('" . (int)$this->pProjectID . "', '" . (int)$queue->ID . "')";
        }
        $sql = "INSERT INTO Meta_ProjectsQueues (pid, qid) VALUES " . $sql;
        self::$db->query($sql);
      }
      return 1;
    }
    return NULL;
  }
  //o-------------------------------------------------------------------------------o
  PRIVATE function GetQueues($pid)
  //o-------------------------------------------------------------------------------o
  {
    $sql = "SELECT qid FROM Meta_ProjectsQueues WHERE pid='" . (int)$pid . "'";
    $queues = self::$db->query($sql);
    $lQObj = array();
    if (self::$db->affected_rows() > 0)
    {
      foreach($queues as $queue)
      {
        $lQObj[] = new myJAM_Queue($queue['qid']);
      }
    }
    unset($queues);
    return $lQObj;
  }
  //o-------------------------------------------------------------------------------o
  PUBLIC function AddHost($host)
  //o-------------------------------------------------------------------------------o
  {
    if (!is_a($host, 'myJAM_Host') || !is_scalar($host->ID) || !(int)$host->ID > 0)
    {
      die("myJAM>> FATAL ERROR 0xe057 in class myJAM_Project!");
    }
    if ($this->pProjectID)
    {
      self::$vProjectHosts[$this->pProjectID][] = $host;
      if ($this->UpdateHostsSQL())
      {
        return count(self::$vProjectHosts[$this->pProjectID]);
      }
    }
    return NULL;
  }
  //o-------------------------------------------------------------------------------o
  PUBLIC function DelHost($host)
  //o-------------------------------------------------------------------------------o
  {
    if (!is_a($host, 'myJAM_Host') || !is_scalar($host->ID) || !(int)$host->ID > 0)
    {
      die("myJAM>> FATAL ERROR 0x62f3 in class myJAM_Project!");
    }
    if($this->pProjectID)
    {
      foreach(self::$vProjectHosts[$this->pProjectID] as $pHost=>$OldHost)
      {
        if ($OldHost->ID == $host->ID)
        {
          unset(self::$vProjectHosts[$this->pProjectID][$pHost]);
          if ($this->UpdateHostsSQL())
          {
            return count(self::$vProjectHosts[$this->pProjectID]);
          }
        }
      }
    }
    return NULL;
  }
  //o-------------------------------------------------------------------------------o
  PROTECTED function UpdateHostsSQL()
  //o-------------------------------------------------------------------------------o
  {
    if ($this->pProjectID)
    {
      //delete old entries from meta table
      self::$db->query("DELETE FROM Meta_ProjectsHosts WHERE pid='" . (int)$this->pProjectID . "'");
      if (count(self::$vProjectHosts[$this->pProjectID]) > 0)
      {
        //gen insert statement
        $sql = '';
        foreach(self::$vProjectHosts[$this->pProjectID] as $host)
        {
          if ($sql)
          {
            $sql .= ',';
          }
          $sql .= "('" . (int)$this->pProjectID . "', '" . (int)$host->ID . "')";
        }
        $sql = 'INSERT INTO Meta_ProjectsHosts (pid, hid) VALUES ' . $sql;
        self::$db->query($sql);
      }
      return 1;
    }
    return NULL;
  }
  //o-------------------------------------------------------------------------------o
  PRIVATE function GetHosts($pid)
  //o-------------------------------------------------------------------------------o
  {
    $sql = "SELECT hid FROM Meta_ProjectsHosts WHERE pid='" . (int)$pid . "'";
    $hosts = self::$db->query($sql);
    $lHostObj = array();
    if (self::$db->affected_rows() > 0)
    {
      foreach($hosts as $host)
      {
        $lHostObj[] = new myJAM_Host($host["hid"]);
      }
    }
    unset($hosts);
    return $lHostObj;
  }
  //o-------------------------------------------------------------------------------o
  PROTECTED function GetSUs()
  //o-------------------------------------------------------------------------------o
  {
    if ($this->pProjectID)
    {
      $query = "SELECT su FROM Projects WHERE pid='".(int)$this->pProjectID."'";
      $result = self::$db->query($query);
      if (self::$db->num_rows() != 1)
      {
        die("myJAM>> FATAL ERROR 002 IN CLASS PROJECT");
      }
      return sprintf("%.2f", $result[0]["su"]);
    }
    else
    {
      $query = "SELECT pid, su FROM Projects";
      $vResults = self::$db->query($query);
      if (self::$db->num_rows() != self::$nbProjects)
      {
        die("myJAM>> FATAL ERROR 003 IN CLASS PROJECT");
      }
      $SUs = array();
      foreach($vResults as $res)
      {
        $SUs[$res["pid"]] = sprintf("%.2f", $res["su"]);
      }
      return $SUs;
    }
  }
  //o-------------------------------------------------------------------------------o
  PROTECTED function GetNbRunning()
  //o-------------------------------------------------------------------------------o
  {
    if ($this->pProjectID)
    {
      $query = "SELECT count(pid) FROM Jobs WHERE pid='".(int)$this->pProjectID."' AND job_state='R'";
      $result = self::$db->query($query);
      return $result[0]["count(pid)"];
    }
    else
    {
      $vNbRun = array();
      $result = self::$db->query("select pid, count(pid) from Jobs where job_state='R' GROUP BY pid;");
      foreach($result as $job)
      {
        $vNbRun[$job["pid"]] = $job["count(pid)"];
      }
      return $vNbRun;
    }
  }
  //o-------------------------------------------------------------------------------o
  PROTECTED function GetNbQueued()
  //o-------------------------------------------------------------------------------o
  {
    if ($this->pProjectID)
    {
      $query = "SELECT count(pid) FROM Jobs WHERE pid='".(int)$this->pProjectID."' AND job_state='Q'";
      $result = self::$db->query($query);
      return $result[0]["count(pid)"];
    }
    else
    {
      $vNbQed = array();
      $result = self::$db->query("select pid, count(pid) from Jobs where job_state='Q' GROUP BY pid;");
      foreach($result as $job)
      {
        $vNbQed[$job["pid"]] = $job["count(pid)"];
      }
      return $vNbQed;
    }
  }
  //o-------------------------------------------------------------------------------o
  PROTECTED function GetNbFinished()
  //o-------------------------------------------------------------------------------o
  {
    if ($this->pProjectID)
    {
      $query = "SELECT count(pid) FROM Jobs WHERE pid='".(int)$this->pProjectID."' AND job_state='F'";
      $result = self::$db->query($query);
      return $result[0]["count(pid)"];
    }
    else
    {
      $vNbFinished = array();
      $result = self::$db->query("select pid, count(pid) from Jobs where job_state='F' GROUP BY pid;");
      foreach($result as $job)
      {
        $vNbFinished[$job["pid"]] = $job["count(pid)"];
      }
      return $vNbFinished;
    }
  }
  //o-------------------------------------------------------------------------------o
  PROTECTED function GetInvoiceAddress()
  //o-------------------------------------------------------------------------------o
  {
    if ($this->pProjectID)
    {
      $query = "SELECT invoice_address FROM Projects WHERE pid='".(int)$this->pProjectID."'";
      $result = self::$db->query($query);
      return $result[0]["invoice_address"];
    }
  }
  //o-------------------------------------------------------------------------------o
  PROTECTED function SetInvoiceAddress($addr)
  //o-------------------------------------------------------------------------------o
  {
    if ($this->pProjectID)
    {
      $addr = htmlentities($addr);
      $query = "UPDATE Projects SET invoice_address='"
               .mysql_real_escape_string($addr)."' WHERE pid='"
               .(int)$this->pProjectID."'";
      self::$db->DoSQL($query);
    }
  }
  //o-------------------------------------------------------------------------------o
  PROTECTED function GetLastInvoice()
  //o-------------------------------------------------------------------------------o
  {
    if($this->pProjectID)
    {
      $sql = "SELECT max(UNIX_TIMESTAMP(period)) FROM Invoices WHERE pid='".(int)$this->pProjectID."'";
      $maxdate = self::$db->query($sql);
      if(self::$db->num_rows() < 1 || (int)$maxdate[0]["max(UNIX_TIMESTAMP(period))"] < 1)
      {
        return NULL;
      }
      $sql = "SELECT InvoiceID from Invoices WHERE UNIX_TIMESTAMP(period)='".$maxdate[0]["max(UNIX_TIMESTAMP(period))"]."'";
      $invoice = self::$db->query($sql);
      return new myJAM_Invoice((int)$invoice[0]["InvoiceID"]);
    }
  }
  //o-------------------------------------------------------------------------------o
  PROTECTED function GetInstitute()
  //o-------------------------------------------------------------------------------o
  {
    if($this->pProjectID)
    {
      if(self::$vInstIDs[$this->pProjectID])
      {
        return new myJAM_Institute(self::$vInstIDs[$this->pProjectID]);
      }
      else
      {
        return NULL;
      }
    }
  }
  //o-------------------------------------------------------------------------------o
  PUBLIC static function CreateProject($Name,
                                       $Description,
                                       $ProjOwner,
                                       $Institute,
                                       $CostModel)
  //o-------------------------------------------------------------------------------o
  {
    if (! is_object(self::$db))
    {
      self::$db = new myJAM_DB();
    }
    if (!isset($Name) || empty($Name))
    {
      die("myJAM>> FATAL ERROR 0xe4c9 in class myJAM_Project: Illegal character in project name!");
    }
    if (!isset($Description) || empty($Description))
    {
      die("myJAM>> FATAL ERROR 0xa54e in class myJAM_Project: Illegal character in description!");
    }
    if(!is_a($ProjOwner, 'myJAM_User') || !is_scalar($ProjOwner->ID) || !((int)$ProjOwner->ID > 0))
    {
      die("myJAM>> FATAL ERROR 0x0508 in class myJAM_Project: Illegal project owner!");
    }
    if(!is_a($Institute, 'myJAM_Institute') || !is_scalar($Institute->ID) || !((int)$Institute->ID > 0))
    {
      die("myJAM>> FATAL ERROR 0x394c in class myJAM_Project: Illegal institue!");
    }
    if(!is_a($CostModel, 'myJAM_CostModel') || !is_scalar($CostModel->ID) || !((int)$CostModel->ID > 0))
    {
      die("myJAM>> FATAL ERROR 0x8be5 in class myJAM_Project: Illegal cost model!");
    }
    $sql = 'INSERT INTO Projects'
          .' (name,description,proj_uid_owner,InstID,cid,su,history_su,billable_su,PenaltyCommand)'
          .' VALUES'
          ." ('".mysql_real_escape_string($Name)."'"
          .",'".mysql_real_escape_string($Description)."'"
          .",'".(int)$ProjOwner->ID."'"
          .",'".(int)$Institute->ID."'"
          .",'".(int)$CostModel->ID."'"
          .",0.0,0.0,0.0"
          .",(SELECT id FROM BatchCommands LIMIT 0,1)"
          .")";
    self::$db->DoSQL($sql);
    $NewProjID = self::$db->last_insert_id();
    if($NewProjID < 1)
    {
      die('myJAM>> FATAL ERROR 0x0b620 in class myJAM_Project');
    }
    self::DestroyCache();
    return new myJAM_Project($NewProjID);
  }
  //o-------------------------------------------------------------------------------o
  PUBLIC function DELETE()
  //o-------------------------------------------------------------------------------o
  {
    if($this->pProjectID)
    {
      $i = self::$vProjectIDListHash[$this->pProjectID];
      $name = self::$vProjectNameHash[$this->pProjectID];
      unset(self::$vProjectID[$i]);
      unset(self::$vProjectIDHash[$name]);
      unset(self::$vProjectIDListHash[$this->pProjectID]);
      unset(self::$vProjectName[$i]);
      unset(self::$vProjectNameHash[$this->pProjectID]);
      unset(self::$vProjectUsers[$this->pProjectID]);
      unset(self::$vProjectQueues[$this->pProjectID]);
      unset(self::$vProjectOwner[$this->pProjectID]);
      unset(self::$vProjectDescription[$this->pProjectID]);
      unset(self::$vProjectStartDate[$this->pProjectID]);
      unset(self::$vProjectEndDate[$this->pProjectID]);
      unset(self::$vProjectEnabled[$this->pProjectID]);
      unset(self::$vProjectHosts[$this->pProjectID]);
      unset(self::$vProjectCostModel[$this->pProjectID]);
      unset(self::$vProjectBillable[$this->pProjectID]);
      unset(self::$vProjectOverrun[$this->pProjectID]);
      $sql = 'DELETE FROM Meta_ProjectsHosts where pid=\''.(int)$this->pProjectID.'\'';
      self::$db->DoSQL($sql);
      $sql = 'DELETE FROM Meta_ProjectsQueues where pid=\''.(int)$this->pProjectID.'\'';
      self::$db->DoSQL($sql);
      $sql = 'DELETE FROM Meta_ProjectsUsers where pid=\''.(int)$this->pProjectID.'\'';
      self::$db->DoSQL($sql);
      $sql = 'DELETE FROM Projects WHERE pid=\''.(int)$this->pProjectID.'\'';
      self::$db->DoSQL($sql);
      self::$nbProjects--;
      $this->pProjectID = NULL;
      return self::$nbProjects;
    }
    return NULL;
  }
  //o-------------------------------------------------------------------------------o
  public static function DestroyCache()
  //o-------------------------------------------------------------------------------o
  {
    self::$db = NULL;
    self::$nbProjects = NULL;
    self::$vProjectID = array();
    self::$vProjectIDHash = array();
    self::$vProjectIDListHash = array();
    self::$vProjectName = array();
    self::$vProjectNameHash = array();
    self::$vProjectUsers = array();
    self::$vProjectQueues = array();
    self::$vProjectOwner = array();
    self::$vProjectDescription = array();
    self::$vProjectStartDate = array();
    self::$vProjectEndDate = array();
    self::$vProjectEnabled = array();
    self::$vProjectHosts = array();
    self::$vProjectCostModel = array();
    self::$vProjectBillable = array();
    self::$vProjectOverrun = array();
    self::$vInstIDs = array();
  }
}
//o-------------------------------------------------------------------------------
//o-------------------------------------------------------------------------------
//o-------------------------------------------------------------------------------
