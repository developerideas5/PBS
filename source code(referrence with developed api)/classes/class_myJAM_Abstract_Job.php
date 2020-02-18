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

require_once(_FULLPATH.'/classes/class_myJAM_User.php');
require_once(_FULLPATH.'/classes/class_myJAM_Project.php');
require_once(_FULLPATH.'/classes/class_myJAM_Architecture.php');
require_once(_FULLPATH.'/classes/class_myJAM_Host.php');
require_once(_FULLPATH.'/classes/class_myJAM_Queue.php');
require_once(_FULLPATH.'/classes/class_myJAM_TORQUE.php');
abstract class myJAM_Abstract_Job
{
  public $JobID = NULL;
  public $Project = NULL;
  public $User = NULL;
  public $Queue = NULL;
  public $Host = NULL;
  public $ReqWallTime = 0.0;
  public $UsedWalltime = 0.0;
  public $nbCores = 0;
  public $ExitID = NULL;
  public $state = NULL;
  public $ExecHosts = '';
  protected $db;
  //o-------------------------------------------------------------------------------o
  public function __construct()
  //o-------------------------------------------------------------------------------o
  {
    require(_FULLPATH.'/config/CFG_database.php');
    $this->db = new myJAM_db();
  }
  //o-------------------------------------------------------------------------------o
  public abstract function GetJob();
  //o-------------------------------------------------------------------------------o
  //o-------------------------------------------------------------------------------o
  protected abstract function _DiscoverQueues();
  //o-------------------------------------------------------------------------------o
  //o-------------------------------------------------------------------------------o
  protected abstract function _DiscoverNodes();
  //o-------------------------------------------------------------------------------o
  //o-------------------------------------------------------------------------------o
  public function GetFromDB()
  //o-------------------------------------------------------------------------------o
  {
    if(!empty($this->JobID))
    {
      $sql = 'SELECT num_procs, job_state, req_su, hid, pid, uid, qid, actual_su'
            .',(UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(run_stamp))/3600.0 AS used_su'
            .' FROM Jobs'
            ." WHERE pbs_jobnumber='".mysql_real_escape_string($this->JobID)."'";
      $vres = $this->db->query($sql);
      if($this->db->num_rows() != 1)
      {
        die('myJAM>> FATAL INTERNAL ERROR IN CLASS Abstract_Jobs!');
      }
      $this->nbCores = (int)$vres[0]['num_procs'];
      $this->state = $vres[0]['job_state'];
      $this->ReqWallTime = (float)$vres[0]['req_su'];
      $this->Host = new myJAM_Host($vres[0]['hid']);
      $this->Project = new myJAM_Project($vres[0]['pid']);
      $this->User = new myJAM_User($vres[0]['uid']);
      $this->Queue = new myJAM_Queue($vres[0]['qid']);
      if($this->state == 'F')
      {
        $this->UsedWalltime = (float)$vres[0]['actual_su'] / (float)$this->nbCores;
      }
      else
      {
        $this->UsedWalltime = (float)$vres[0]['used_su'];
      }
    }
  }
  //o-------------------------------------------------------------------------------o
  public function Discover()
  //o-------------------------------------------------------------------------------o
  {
    $buffy = array();
    if(!$this->_CmdOut('hostname -f', $buffy))
    {
      die('myJAM_Discover>> FATAL ERROR. Can not determine hostname.');
    }
    echo "myJAM_Discover>> Hostname: $buffy[0]\n";
    if($this->_GetNbHosts() < 1)
    {
      echo "  >> No Host in database.\n";
      $this->_AddHost($buffy[0]);
    }
    else
    {
      if(!($this->Host = $this->_GetHost($buffy[0])))
      {
        echo "  >> Hostname not in <myJAM/> database.\n";
        $this->_AddHost($buffy[0]);
      }
    }
    echo "\nmyJAM_Discover>> Discovering Queues...\n";
    $this->_DiscoverQueues();
    echo "\nmyJAM_Discover>> Discovering Nodes...\n";
    $this->_DiscoverNodes();
    echo "myJAM_Discover>> DONE.\n";
  }
  //o-------------------------------------------------------------------------------o
  public function GetPrologueArgs($argv)
  //o-------------------------------------------------------------------------------o
  {
    if(!isset($argv[1]))
    {
      die("myJAM_Prologue>> JobID not given!!!");
    }
    $this->JobID = $argv[1];
  }
  //o-------------------------------------------------------------------------------o
  public function GetEpilogueArgs($argv)
  //o-------------------------------------------------------------------------------o
  {
    if(!isset($argv[1]))
    {
      die("myJAM_Prologue>> JobID not given!!!");
    }
    $this->JobID = $argv[1];
    if(!isset($argv[2]))
    {
      die("myJAM_Prologue>> ExitID not given!!!");
    }
    $this->ExitID = $argv[2];
  }
  //o-------------------------------------------------------------------------------o
  public function Insert()
  //o-------------------------------------------------------------------------------o
  {
    $statements = array('pbs_jobnumber=\''.mysql_real_escape_string($this->JobID).'\'',
                        'num_procs='.(int)$this->nbCores,
                        'job_state=\'R\'',
                        'req_su='.(float)($this->nbCores * $this->ReqWallTime),
                        'hid='.(int)$this->Host->ID,
                        'pid='.(int)$this->Project->ID,
                        'uid='.(int)$this->User->ID,
                        'qid='.(int)$this->Queue->ID,
                        'ArchID=(SELECT ArchID FROM Nodes WHERE Name=\''.mysql_real_escape_string(array_shift(explode(' ', $this->ExecHosts))).'\')'
                       );
    $sql = 'INSERT INTO Jobs SET'
          .' run_stamp=NOW(),'.implode(',', $statements)
          .' ON DUPLICATE KEY UPDATE '.implode(',', $statements);
    $this->db->DoSQL($sql);
    if($this->Project->Billable)
    {
      $sql = 'UPDATE Projects SET'
             ." su=su-".(float)($this->nbCores * $this->ReqWallTime)
             ." WHERE pid=".(int)$this->Project->ID."";
      print "SQL: $sql\n";
      $this->db->DoSQL($sql);
    }
    $sql = 'SELECT NodeID FROM Meta_JobsNodes WHERE pbs_jobnumber=\''.mysql_real_escape_string($this->JobID).'\'';
    $res = $this->db->query($sql);
    if(count($res) < 1)
    {
      $sql = 'INSERT INTO Meta_JobsNodes'
      ." (pbs_jobnumber, NodeID)"
      ." SELECT Jobs.pbs_jobnumber AS pbs_jobnumber,"
             ." Nodes.NodeID AS NodeID"
      ." FROM Jobs,Nodes"
      ." WHERE Nodes.Name IN ('".str_replace(' ', '\',\'', $this->ExecHosts)."')"
      ." AND Jobs.pbs_jobnumber='".$this->JobID."'"
      ;
      $this->db->DoSQL($sql);
    }
  }
  //o-------------------------------------------------------------------------------o
  public function Finish()
  //o-------------------------------------------------------------------------------o
  {
    $this->GetJob();
    $sql = 'UPDATE Jobs SET'
          .' date=NOW()'
          .",comment='Finished'"
          .",job_state='F'"
          .",eid='".(int)$this->ExitID."'"
          .',actual_su=(UNIX_TIMESTAMP(date)-UNIX_TIMESTAMP(run_stamp))/3600*num_procs'
//          .',actual_su='.(float)($this->UsedWalltime*$this->nbCores)
          ." WHERE pbs_jobnumber='".mysql_real_escape_string($this->JobID)."'";
    print "SQL: $sql\n";
    $this->db->DoSQL($sql);
    $this->GetFromDB();
    if($this->Project->Billable)
    {
      $sql = 'UPDATE Projects SET'
             ." su=su+".(float)(($this->ReqWallTime - $this->UsedWalltime)*$this->nbCores)
             ." WHERE pid=".(int)$this->Project->ID;
    }
    else
    {
      $sql = 'UPDATE Projects SET'
             ." su=su+".(float)($this->UsedWalltime*$this->nbCores)
             ." WHERE pid=".(int)$this->Project->ID;
    }
    $this->db->DoSQL($sql);
  }
  //o-------------------------------------------------------------------------------o
  protected function objOut($obj, $propname)
  //o-------------------------------------------------------------------------------o
  {
    if($obj)
    {
      if(is_object($obj))
      {
        $val = $obj->$propname;
        if($val)
        {
          return $val;
        }
      }
    }
    else
    {
      return '<UNKNOWN>';
    }
  }
  //o-------------------------------------------------------------------------------o
  public function dump()
  //o-------------------------------------------------------------------------------o
  {
    print "\n\n";
    print 'JobID: '.$this->JobID."\n";
    print '  |--- Job Owner: '.$this->objOut($this->User, 'UserName')."\n";
    print '  |--- Project  : '.$this->objOut($this->Project, 'Name')."\n";
    print '  |--- Queue    : '.$this->objOut($this->Queue, 'Name')."\n";
    print '  |--- Host     : '.$this->objOut($this->Host, 'Name')."\n";
    printf("  |--- Req. Wall: %.2f\n",$this->ReqWallTime);
    printf("  |--- Req. SUs : %.2f\n",$this->ReqWallTime*$this->nbCores);
    print '  `--- nbCores  : '.$this->nbCores."\n";
    print '         `--- Exec Hosts: '.$this->ExecHosts."\n";
    print "\n";
  }
  //o-------------------------------------------------------------------------------o
  protected function _GetReqWallTime($time_str)
  //o-------------------------------------------------------------------------------o
  {
    $WTFrags = explode(':', $time_str);
    $WallSeconds = 0;
    //seconds
    if((int)$val = array_pop($WTFrags))
    {
      $WallSeconds += $val;
    }
    //minutes
    if((int)$val = array_pop($WTFrags))
    {
      $WallSeconds += ($val*60);
    }
    //hours
    if((int)$val = array_pop($WTFrags))
    {
      $WallSeconds += ($val*3600);
    }
    //days
    if((int)$val = array_pop($WTFrags))
    {
      $WallSeconds += ($val*86400);
    }
    return (float)$WallSeconds/3600.0;
  }
  //o-------------------------------------------------------------------------------o
  protected function _GetUser($UserName)
  //o-------------------------------------------------------------------------------o
  {
    $User = new myJAM_User($UserName);
    if(is_object($User) &&
       is_scalar($User->ID) &&
       (int)$User->ID > 0)
    {
      return $User;
    }
    else
    {
      print "myJAM>> USER '$UserName' NOT FOUND\n";
      return NULL;
    }
  }
  //o-------------------------------------------------------------------------------o
  protected function _GetQueue($QueueName)
  //o-------------------------------------------------------------------------------o
  {
    $queue = new myJAM_Queue($QueueName);
    if(is_object($queue) &&
    is_scalar($queue->ID) &&
    (int)$queue->ID > 0)
    {
      return $queue;
    }
    else
    {
      return NULL;
    }
  }
  //o-------------------------------------------------------------------------------o
  protected function _GetNbQueues()
  //o-------------------------------------------------------------------------------o
  {
    $sql = 'SELECT qid from Queues';
    return (int)$this->db->PreCount($sql);
  }
  //o-------------------------------------------------------------------------------o
  protected function _AddQueue($queuename)
  //o-------------------------------------------------------------------------------o
  {
    $queuename = htmlentities($queuename);
    echo "  >> Adding queue \"$queuename\"\n";
    $sql = 'INSERT INTO Queues'
          .' (queue_descr, queue_adjust, hid, open, running)'
          .' VALUES'
          ." ('".mysql_real_escape_string($queuename)."', 1.0, '".(int)$this->Host->ID."', 1, 1)";
    $this->db->DoSQL($sql);
    myJAM_Queue::DestroyCache();
    if(!$this->_GetQueue($queuename))
    {
      die('myJAM>> FATAL ERROR. Can not add queue to database');
    }
  }
  //o-------------------------------------------------------------------------------o
  protected function _UpdateQueue($queuename)
  //o-------------------------------------------------------------------------------o
  {
    $queuename = htmlentities($queuename);
    echo "myJAM_Discover>> Queue: $queuename\n";
    if($this->_GetNbQueues() < 1)
    {
      echo "  >> no queues in database.\n";
      $this->_AddQueue($queuename);
    }
    else
    {
      if(!$this->_GetQueue($queuename))
      {
        echo "  >> queue not in database.\n";
        $this->_AddQueue($queuename);
      }
    }
  }
  //o-------------------------------------------------------------------------------o
  protected function _AddNode($hostname)
  //o-------------------------------------------------------------------------------o
  {
    $Architecture = new myJAM_Architecture();
    $ArchID = $Architecture->ID['default'];
    if(!$ArchID)
    {
      die('Default architecture not found!');
    }
    $buffy = array();
    $statements = array();
    print ' >> '.$hostname;
    $statements[] = 'Name=\''.mysql_real_escape_string($hostname).'\'';
    $statements[] = 'hid='.(int)$this->Host->ID;
    $statements[] = 'ArchID='.(int)$ArchID;
    if($this->_CheckNode($hostname))
    {
      print ' [online]';
      $cmd = 'ssh '.$hostname.' \''
            .'cat /proc/version;' // version
            .'uname -m;' // arch
            .'grep ^processor /proc/cpuinfo | sort -u | wc -l;' // #cores
            .'grep ^physical /proc/cpuinfo | sort -u | wc -l;' // #sockets
            .'grep MemTotal /proc/meminfo;' // Memory
            .'\'';
      print "Cmd: $cmd\n";
      if($this->_CmdOut($cmd, $buffy))
      {
        if(count($buffy) == 6)
        {
          print "\n";
          if(!empty($buffy[0]))
          {
            $statements[] = 'OS=\''.mysql_real_escape_string($buffy[0]).'\'';
            print '              OS: '.$buffy[0]."\n";
          }
          if(!empty($buffy[1]))
          {
            print '    Architecture: '.$buffy[1]."\n";
          }
          if(!empty($buffy[2]) && is_numeric($buffy[2]))
          {
            $nbCores = (int)$buffy[2];
            if(!empty($buffy[3]) && is_numeric($buffy[3]))
            {
              $nbSockets = (int)$buffy[3];
              $CoresPerCPU = $nbCores / $nbSockets;
            }
            else
            {
              $nbSockets = $nbCores;
              $CoresPerCPU = 1;
            }
            $statements[] = 'nbCPUs='.$nbSockets;
            print '          # CPUs: '.(int)$nbSockets."\n";
            $statements[] = 'nbCPUCores='.(int)$CoresPerCPU;
            print '   Cores Per CPU: '.$CoresPerCPU."\n";
          }
          if(!empty($buffy[4]))
          {
            $frags = preg_split('/\s+/', $buffy[4]);
            if(count($frags) == 3)
            {
              if(!empty($frags[1]) && is_numeric($frags[1]))
              {
                $mem = (int)$frags[1]; // in bytes
                $statements[] = 'Memory='.(int)$mem;
                print '          Memory: '.$mem." Bytes\n";
              }
            }
          }
        }
        else
        {
          print ' [NO DATA]';
        }
      }
    }
    else
    {
      print ' [offline]';
    }
    $sql = 'INSERT INTO Nodes SET '.implode(',', $statements).' ON DUPLICATE KEY UPDATE '.implode(',', $statements);
    $this->db->DoSQL($sql);
    print "\n";
  }
  //o-------------------------------------------------------------------------------o
  protected function _CheckNode($hostname)
  //o-------------------------------------------------------------------------------o
  {
    $buffy = array();
    if($this->_CmdOut('ping -c 1 '.$hostname, $buffy))
    {
      if(count($buffy) > 0)
      {
        foreach($buffy as $line)
        {
          if(strpos($line, 'icmp_seq=') !== false &&
             strpos($line, 'ttl=') !== false &&
             strpos($line, 'time=') !== false)
          {
            return true;
          }
        }
      }
    }
    return false;
  }
  //o-------------------------------------------------------------------------------o
  protected function _GetHost($HostName)
  //o-------------------------------------------------------------------------------o
  {
    $HostName = htmlentities($HostName);
    $host = new myJAM_Host($HostName);
    if(is_object($host) &&
       is_scalar($host->ID) &&
       (int)$host->ID > 0)
    {
      return $host;
    }
    else
    {
      return NULL;
    }
  }
  //o-------------------------------------------------------------------------------o
  protected function _GetNbHosts()
  //o-------------------------------------------------------------------------------o
  {
    $sql = 'SELECT hid from Hosts';
    return (int)$this->db->PreCount($sql);
  }
  //o-------------------------------------------------------------------------------o
  protected function _AddHost($hostname)
  //o-------------------------------------------------------------------------------o
  {
    $hostname = htmlentities($hostname);
    echo "  >> Adding host \"$hostname\"\n";
    $sql = "INSERT INTO Hosts (hostname) VALUES ('".mysql_real_escape_string($hostname)."')";
    $this->db->DoSQL($sql);
    if(!($this->Host = $this->_GetHost($hostname)))
    {
      die('myJAM>> FATAL ERROR. Can not add host to database');
    }
  }
  //o-------------------------------------------------------------------------------o
  protected function _GetProject($ProjectName)
  //o-------------------------------------------------------------------------------o
  {
    $ProjectName = htmlentities($ProjectName);
    $project = new myJAM_Project($ProjectName);
    if(is_a($project, 'myJAM_Project') &&
       is_scalar($project->ID) &&
       (int)$project->ID > 0)
    {
      return $project;
    }
    else
    {
      print "myJAM>> PROJECT '$ProjectName' NOT FOUND!\n";
      return NULL;
    }
  }
  //o-------------------------------------------------------------------------------o
  protected function _CmdOut($cmd, &$buffy)
  //o-------------------------------------------------------------------------------o
  {
    $buffy = array();
    if(_CFG_BATCHSYSTEM_SERVER != 'localhost' &&
       _CFG_BATCHSYSTEM_SERVER != '127.0.0.1')
    {
      $pHandle = popen('ssh '._CFG_BATCHSYSTEM_SERVER.' "'.$cmd . ' 2>&1"', 'r');
    }
    else
    {
      $pHandle = popen($cmd . ' 2>&1', 'r');
    }
    if ($pHandle)
    {
      while (!feof($pHandle))
      {
        $buffy[] = trim(fgets($pHandle));
      }
      fclose($pHandle);
      return count($buffy);
    }
    return NULL;
  }
  //o-------------------------------------------------------------------------------o
  protected function _commandExists($cmd)
  //o-------------------------------------------------------------------------------o
  {
    if(_CFG_BATCHSYSTEM_BINDIR != '')
    {
      $cmd = _CFG_BATCHSYSTEM_BINDIR.DIRECTORY_SEPARATOR.$cmd;
    }
    $tmp = "if [ -f ".$cmd." ];then echo 1;fi";
    $buffy = array();
    $this->_CmdOut($tmp, $buffy);
    if(count($buffy > 0) && $buffy[0] == "1")
    {
      return true;
    }
    return false;
  }
  //o-------------------------------------------------------------------------------o
  protected function _checkCommands($Commands)
  //o-------------------------------------------------------------------------------o
  {
    if(!is_array($Commands) || count($Commands) < 1)
    {
      die('myJAM>> FATAL ERROR! Invalid Argument');
    }
    $error = false;
    foreach($Commands as $cmd)
    {
      if(!$this->_commandExists($cmd))
      {
        print " >> COULD NOT FIND COMMAND '$cmd' in '"._CFG_BATCHSYSTEM_BINDIR."'\n";
        $error = true;
      }
    }
    if($error)
    {
      die();
    }
  }
  //o-------------------------------------------------------------------------------o
  public static function Factory($BatchType)
  //o-------------------------------------------------------------------------------o
  {
    $construct_cmd = '$Job = new myJAM_' . strtoupper($BatchType) . '();';
    $Job = NULL;
    try
    {
      eval($construct_cmd);
    }
    catch (Exception $e)
    {
      die("myJAM>> FATAL EXCEPTION in myJAM_Abstract_Job::Factory " . $e . "");
    }
    return $Job;
  }
}
