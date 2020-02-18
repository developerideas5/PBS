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
require_once(_FULLPATH.'/classes/class_myJAM_Queue.php');
class myJAM_Host
{
  PROTECTED STATIC $db;
  PROTECTED STATIC $vHostsID;
  PROTECTED STATIC $vHostsIDHash;
  PROTECTED STATIC $vHostsName;
  PROTECTED STATIC $vHostsNameHash;
  PROTECTED STATIC $vHostsQueueIDs;
  PROTECTED STATIC $vHostsQueueObjs;
  PROTECTED STATIC $vBatchSystemHash;
  PROTECTED $pHostID;
  PROTECTED $LastLifeSign;
  //o-------------------------------------------------------------------------------o#
  PUBLIC function __construct($host=NULL)
  //o-------------------------------------------------------------------------------o#
  {
    if (!is_object(self::$db))
    {
      self::$db = new myJAM_DB();
      self::$vHostsID = array();
      self::$vHostsIDHash = array();
      self::$vHostsName = array();
      self::$vHostsNameHash = array();
      self::$vHostsQueueIDs = array();
      self::$vHostsQueueObjs = array();
      $query = 'SELECT * FROM Hosts'
               .' WHERE hostname!="<deleted host>"';
      $vHosts = self::$db->query($query);
      $nbHosts = self::$db->num_rows();
      if ($nbHosts < 1)
      {
        die("FATAL ERROR 0x0041 IN HOST OBJECT: No host found in database. Try to run a DISCOVER job...");
      }
      for($i = 0; $i < $nbHosts; $i++)
      {
        $hid = $vHosts[$i]["hid"];
        $hostname = $vHosts[$i]["hostname"];
        self::$vHostsID[$i] = $hid;
        self::$vHostsIDHash[$hostname] = $hid;
        self::$vHostsName[$i] = $hostname;
        self::$vHostsNameHash[$hid] = $hostname;
        self::$vBatchSystemHash[$hid] = $vHosts[$i]["BatchSystem"];
        self::$vHostsQueueIDs[$hid] = self::GetQueueIDs($hid);
      }
    }
    if ($host)
    {
      if (!@self::$vHostsNameHash[$host])
      {
        if (!$this->pHostID = self::$vHostsIDHash[$host])
        {
          return NULL;
        }
      }
      else
      {
        $this->pHostID = $host;
      }
    }
    else
    {
      $this->phostID = 0;
    }
  }
  //o-------------------------------------------------------------------------------o#
  PUBLIC function __get($name)
  //o-------------------------------------------------------------------------------o#
  {
    if ($this->pHostID)
    {
      switch($name)
      {
        case "Name":
          return self::$vHostsNameHash[$this->pHostID];
        case "ID":
          return $this->pHostID;
        case "BatchSystem":
          return self::$vBatchSystemHash[$this->pHostID];
        case "Queues":
          return self::GetQueueObjs($this->pHostID);
        case "DaemonAlive":
          return self::CheckDaemon();
        case "LastLifeSign":
          return $this->LastLifeSign;
        default:
          return NULL;
      }
    }
    switch($name)
    {
      case "Name":
        return self::$vHostsNameHash;
      case "NameList":
        return self::$vHostsName;
      case "ID":
        return self::$vHostsIDHash;
      case "IDList":
        return self::$vHostsID;
      case "BatchSystem":
        return self::$vBatchSystemHash;
      case "Queues":
        return self::GetQueueObjs();
      default:
        return NULL;
    }
  }
  //o-------------------------------------------------------------------------------o#
  PROTECTED function GetQueueIDs($hid)
  //o-------------------------------------------------------------------------------o#
  {
    if (!isset($hid) || $hid == "" || !is_numeric($hid))
    {
      die("myJAM>> FATAL ERROR 0xc667 in class myJAM_Host!");
    }
    $query = "SELECT qid FROM Queues WHERE hid='" . (int)$hid. "'";
    $vQ = self::$db->query($query);
    $QIDs = array();
    foreach($vQ as $q)
    {
      $QIDs[] = $q["qid"];
    }
    return $QIDs;
  }
//o-------------------------------------------------------------------------------o#
  PROTECTED function GetQueueObjs($host=NULL)
//o-------------------------------------------------------------------------------o#
  {
    if (count(self::$vHostsQueueObjs) != count(self::$vHostsQueueIDs))
    {
      foreach(self::$vHostsID as $hid)
      {
        $QObjs = array();
        foreach(self::$vHostsQueueIDs[$hid] as $qid)
        {
          $QObjs[] = new myJAM_Queue($qid);
        }
        self::$vHostsQueueObjs[$hid] = $QObjs;
        unset($QObjs);
      }
    }
    if ($host)
    {
      return self::$vHostsQueueObjs[$host];
    }
    else
    {
      return self::$vHostsQueueObjs;
    }
  }
  //o-------------------------------------------------------------------------------o#
  PROTECTED function CheckDaemon()
  //o-------------------------------------------------------------------------------o#
  {
    $res = self::$db->query("SELECT UNIX_TIMESTAMP(LifeSign) from Hosts WHERE hid='" . (int)$this->pHostID . "'");
    if (isset($res[0]["UNIX_TIMESTAMP(LifeSign)"]))
    {
      $this->LastLifeSign = (int)$res[0]["UNIX_TIMESTAMP(LifeSign)"];
      $DeltaT = time() - $this->LastLifeSign;
      if ($DeltaT <= 120)
      {
        return true;
      }
    }
    $sql = 'INSERT INTO Events '
          .' (object,message,sender,level,value,first,last)'
          .' VALUES (\'myjamd\',\'myjamd seems to be dead\',\'myjam_www\',4,\'\',UNIX_TIMESTAMP(),UNIX_TIMESTAMP())'
          .' ON DUPLICATE KEY UPDATE last=VALUES(last)';
    self::$db->DoSQL($sql);
    return false;
  }
  //o-------------------------------------------------------------------------------o#
  public static function DestroyCache()
  //o-------------------------------------------------------------------------------o#
  {
    self::$db = NULL;
    self::$vHostsID = array();
    self::$vHostsIDHash = array();
    self::$vHostsName = array();
    self::$vHostsNameHash = array();
    self::$vHostsQueueIDs = array();
    self::$vHostsQueueObjs = array();
    self::$vBatchSystemHash = array();
  }
//o-------------------------------------------------------------------------------o#
//o-------------------------------------------------------------------------------o#
//o-------------------------------------------------------------------------------o#
}
