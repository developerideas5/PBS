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
require_once(_FULLPATH.'/classes/class_myJAM_Host.php');
class myJAM_Queue
{
  PROTECTED STATIC $db;
  PROTECTED STATIC $nbQueues;
  PROTECTED STATIC $vQueueID;
  PROTECTED STATIC $vQueueIDHash;
  PROTECTED STATIC $vQueueIDHashList;
  PROTECTED STATIC $vQueueName;
  PROTECTED STATIC $vQueueNameHash;
  PROTECTED STATIC $vQueueAdjust;
  PROTECTED STATIC $vQueueAdjustHash;
  PROTECTED STATIC $vQueueHost;
  PROTECTED STATIC $vQueueRunning;
  PROTECTED STATIC $vQueueOpen;
  PROTECTED $pQueueID;
//o-------------------------------------------------------------------------------o
  PUBLIC function __construct($queue=NULL)
//o-------------------------------------------------------------------------------o
  {
    if (!is_object(self::$db))
    {
      self::$db = new myJAM_DB();
      self::$nbQueues = 0;
      self::$vQueueID = array();
      self::$vQueueIDHash = array();
      self::$vQueueName = array();
      self::$vQueueNameHash = array();
      self::$vQueueAdjust = array();
      self::$vQueueAdjustHash = array();
      self::$vQueueHost = array();
      $query = 'SELECT qid, queue_descr, queue_adjust, hid, running, open FROM Queues WHERE queue_descr!=\'<deleted queue>\'';
      $vQueues = self::$db->query($query);
      self::$nbQueues = self::$db->num_rows();
      if (self::$nbQueues < 1)
      {
        die("myJAM>> ERROR! NO QUEUES FOUND IN DATABASE! Try a DISCOVERY run...");
      }
      for($i = 0; $i < self::$nbQueues; $i++)
      {
        $qid = $vQueues[$i]["qid"];
        $qName = $vQueues[$i]["queue_descr"];
        self::$vQueueID[$i] = $qid;
        self::$vQueueIDHash[$qName] = $qid;
        self::$vQueueIDHashList[$qid] = $i;
        self::$vQueueName[$i] = $qName;
        self::$vQueueNameHash[$qid] = $qName;
        self::$vQueueAdjust[$i] = $vQueues[$i]["queue_adjust"];
        self::$vQueueAdjustHash[$qid] = $vQueues[$i]["queue_adjust"];
        self::$vQueueHost[$qid] = new myJAM_Host($vQueues[$i]["hid"]);
        self::$vQueueOpen[$qid] = $vQueues[$i]["open"];
        self::$vQueueRunning[$qid] = $vQueues[$i]["running"];
      }
    }
    if ($queue)
    {
//test if $queue is a QueueID
      if (!is_numeric($queue) && !@self::$vQueueNameHash[(int)$queue])
      {
//well, not an ID. perhaps a name?
        if (!@$this->pQueueID = self::$vQueueIDHash[$queue])
        {
//nope... nothing left to do.
          return NULL;
        }
      }
      else
      {
        $this->pQueueID = $queue;
      }
    }
    else
    {
//$queue not given => LIST Object
      $this->pQueueID = 0;
    }
  }
//o-------------------------------------------------------------------------------o
  PUBLIC function __get($name)
//o-------------------------------------------------------------------------------o
  {
    if ($this->pQueueID)
    {
      switch ($name)
      {
        case "ID" :
          return $this->pQueueID;
        case "Name" :
          return self::$vQueueNameHash[$this->pQueueID];
        case "Adjust" :
          return self::$vQueueAdjustHash[$this->pQueueID];
        case "Host" :
          return self::$vQueueHost[$this->pQueueID];
        case "nbRunning" :
          return $this->GetNbRunning($this->pQueueID);
        case "nbQueued" :
          return $this->GetNbQueued($this->pQueueID);
        case "open" :
          return self::$vQueueOpen[$this->pQueueID];
        case "running" :
          return self::$vQueueRunning[$this->pQueueID];
        default :
          return NULL;
      }
    }
    switch ($name)
    {
      case "ID" :
        return self::$vQueueIDHash;
      case "IDList" :
        return self::$vQueueID;
      case "Name" :
        return self::$vQueueNameHash;
      case "NameList" :
        return self::$vQueueName;
      case "Adjust" :
        return self::$vQueueAdjustHash;
      case "AdjustList" :
        return self::$vQueueAdjust;
      case "Host" :
        return self::$vQueueHost;
      case "open" :
        return self::$vQueueOpen;
      case "running" :
        return self::$vQueueRunning;
      case "nb" :
        return self::$nbQueues;
      case "nbRunning" :
        $vtmp = array();
        foreach ( self::$vQueueID as $qid )
        {
          $vtmp[$qid] = $this->GetNbRunning($qid);
        }
        return $vtmp;
      case "nbQueued" :
        $vtmp = array();
        foreach ( self::$vQueueID as $qid )
        {
          $vtmp[$qid] = $this->GetNbQueued($qid);
        }
        return $vtmp;
    }
    return NULL;
  }
//o-------------------------------------------------------------------------------o
  PUBLIC function __set($name, $val)
//o-------------------------------------------------------------------------------o
  {
    if (!isset($val))
    {
      die("myJAM>> FATAL ERROR 0x81bd in class myJAM_User!");
    }
    if (is_string($val))
    {
      $val = htmlentities($val);
    }
    if ($this->pQueueID)
    {
      switch ($name)
      {
        case 'Name' :
          unset(self::$vQueueIDHash[self::$vQueueNameHash[$this->pQueueID]]);
          self::$vQueueNameHash[$this->pQueueID] = $val;
          self::$vQueueName[self::$vQueueIDHashList[$this->pQueueID]] = $val;
          self::$vQueueIDHash[$val] = $this->pQueueID;
          $sql_name = 'queue_descr';
          break;
        case 'Adjust' :
          if (!is_numeric($val))
          {
            die("myJAM>> FATAL ERROR 0x3241 in class myJAM_User!");
          }
          $val = (float)$val;
          self::$vQueueAdjustHash[$this->pQueueID] = $val;
          self::$vQueueAdjust[self::$vQueueIDHashList[$this->pQueueID]] = $val;
          $sql_name = 'queue_adjust';
          break;
        case 'Host' :
          if(!is_a($val, 'myJAM_Host'))
          {
            die('myJAM>> FATAL ERROR!');
          }
          self::$vQueueHost[$this->pQueueID] = $val;
          $val = (int)$val->ID;
          $sql_name = 'hid';
          break;
        default :
          return NULL;
      }
      $sql = "UPDATE Queues SET " . $sql_name . "='" .mysql_real_escape_string($val) . "' WHERE qid='" . (int)$this->pQueueID. "'";
      self::$db->DoSQL($sql);
      return $val;
    }
    return NULL;
  }
  //o-------------------------------------------------------------------------------o
  PUBLIC static function Create($qName, $qAdjust, $qhid)
  //o-------------------------------------------------------------------------------o
  {
    $db = new myJAM_db();
    if (!isset($qName) || !isset($qAdjust) || !isset($qhid))
    {
      die("myJAM>> FATAL ERROR 0x64fb in class myJAM_Queue!");
    }
    if (is_string($qName))
    {
      $qName = htmlentities($qName);
    }
    else
    {
      die("myJAM>> FATAL ERROR 0x6902 in class myJAM_Queue!");
    }
    if (!is_numeric($qAdjust) || !is_numeric($qhid))
    {
      die("myJAM>> FATAL ERROR 0x1bc8 in class myJAM_Queue!");
    }
    $qAdjust = (float)$qAdjust;
    $sql = "INSERT INTO Queues (queue_descr, queue_adjust, hid, open, running)";
    $sql .= " VALUES('"
           . mysql_real_escape_string($qName) . "', '"
           . (float)$qAdjust . "', '"
           . (int)$qhid
           ."', '0', '0')";
    $db->DoSQL($sql);
    $qid = $db->last_insert_id();
    if($qid < 0)
    {
      die('myJAM>> FATAL ERROR 0x0ed7e in class myJAM_Queue');
    }
    self::DestroyCache();
    return new myJAM_Queue($qid);
  }
  //o-------------------------------------------------------------------------------o
  PROTECTED function GetNbRunning($qid)
  //o-------------------------------------------------------------------------------o
  {
    $sql = "SELECT count(pbs_jobnumber) FROM Jobs WHERE job_state='R' and qid='" . (int)$qid. "'";
    $result = self::$db->query($sql);
    return (int)$result[0]["count(pbs_jobnumber)"];
  }
  //o-------------------------------------------------------------------------------o
  PROTECTED function GetNbQueued($qid)
  //o-------------------------------------------------------------------------------o
  {
    $sql = "SELECT count(pbs_jobnumber) FROM Jobs WHERE job_state='Q' and qid='" . (int)$qid . "'";
    $result = self::$db->query($sql);
    return (int)$result[0]["count(pbs_jobnumber)"];
  }
//o-------------------------------------------------------------------------------o
  public static function DestroyCache()
//o-------------------------------------------------------------------------------o
  {
    self::$db = NULL;
    self::$nbQueues = NULL;
    self::$vQueueID = array();
    self::$vQueueIDHash = array();
    self::$vQueueIDHashList = array();
    self::$vQueueName = array();
    self::$vQueueNameHash = array();
    self::$vQueueAdjust = array();
    self::$vQueueAdjustHash = array();
    self::$vQueueHost = array();
    self::$vQueueRunning = array();
    self::$vQueueOpen = array();
  }
}
