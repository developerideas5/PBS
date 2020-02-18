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
require_once(_FULLPATH.'/classes/class_myJAM_Architecture.php');
class myJAM_Jobs
{
 PROTECTED STATIC $db;
 PROTECTED $args;
 PROTECTED $ret_array;
 PROTECTED $vPos;
 //o-------------------------------------------------------------------------------o
 PUBLIC function __construct($args=NULL)
 //o-------------------------------------------------------------------------------o
 {
  if(! is_object(self::$db))
  {
      self::$db = new myJAM_DB();
  }
  $this->args = $args;
  $this->ret_array = array();
  $this->vPos = array();
 }
 //o-------------------------------------------------------------------------------o
  PUBLIC function __get($name)
//o-------------------------------------------------------------------------------o
  {
    if(isset($this->ret_array[$name]))
    {
      return $this->ret_array[$name];
    }
    switch($name)
    {
      case "Data":
        return $this->ret_array;
        break;
      case "IsMonthly":
        return $this->IsMonthly();
        break;
      case "IsCumulative":
        return $this->IsCumulative();
        break;
      case "SUsReq":
        return $this->SUsReq();
        break;
      case "JobsReq":
        return $this->JobsReq();
        break;
    }
    return NULL;
  }
//o-------------------------------------------------------------------------------o
  PUBLIC function getDistArchs()
//o-------------------------------------------------------------------------------o
  {
    $this->ret_array = array();
    $this->ret_array["data"] = array();
    $this->ret_array["nbMonths"] = 0;
    $this->ret_array["MonthNames"] = array();
    $this->ret_array["MaxData"] = 0.0;
    $sql = ", ArchID, month(date), year(date) FROM Jobs WHERE job_state='F' ";
    $sql .= $this->Args2SQL();
    if(!$this->IsCumulative())
      {$sql .= " GROUP BY year(date),month(date),ArchID";}
    $this->GenPosArray("ArchIDs");
    if($this->SUsReq())
      {$sql = "SELECT sum(actual_su)".$sql;}
    elseif($this->JobsReq())
      {$sql = "SELECT count(pbs_jobnumber)".$sql;}
    else
      {die("myJAM>> FATAL ERROR. UNKOWN DATA TYPE.");}
    $vJobs = self::$db->query($sql);
    foreach($vJobs as $job)
    {
      $pos = $this->vPos[sprintf("%02u",$job["month(date)"])."-".$job["year(date)"]];
      if(!isset($pos))
        {die("");}
      if((int)$job["ArchID"]>0)
      {
        if($this->SUsReq())
          {$data = $job["sum(actual_su)"];}
        elseif($this->JobsReq())
          {$data = $job["count(pbs_jobnumber)"];}
        $this->ret_array["data"][(int)$job["ArchID"]][$pos] = $data;
        $this->ret_array["MaxData"] = max($this->ret_array["MaxData"], $data);
      }
    }
  }
//o-------------------------------------------------------------------------------o
 PUBLIC function GetProjectMonths($args)
 //o-------------------------------------------------------------------------------o
 {
    $ret_array = array();
    $ret_array['data'] = array();
    $ret_array['nbDataLines'] = 0;
    $ret_array['nbMonths'] = 0;
    $ret_array['MonthNames'] = array();
    $ret_array['MaxData'] = 0.0;
    $ret_array['ProjNames'] = array();
    $ret_array['pMonth'] = array();
    $ret_array['pYear'] = array();
    $sql = ", pid, month(date), year(date) FROM Jobs WHERE job_state='F' AND pid!=''";
    if (isset($args["ArchIDs"]) && !empty($args["ArchIDs"]))
      {$sql .= " AND ".self::GenSQLStatement("ArchID", $args["ArchIDs"]);}
    if (isset($args["UserIDs"]) && !empty($args["UserIDs"]))
      {$sql .= " AND ".self::GenSQLStatement("uid", $args["UserIDs"]);}
    if (isset($args["ProjIDs"]) && !empty($args["ProjIDs"]))
      {$sql .= " AND ".self::GenSQLStatement("pid", $args["ProjIDs"]);}
    if (isset($args["StartTime"]))
      {$sql .= " AND UNIX_TIMESTAMP(date)>='".$args["StartTime"]."'";}
    if (isset($args["EndTime"]))
      {$sql .= " AND UNIX_TIMESTAMP(date)<='".$args["EndTime"]."'";}
    $sql .= " GROUP BY year(date),month(date),pid";
//initialize data arrays
    $vPos = array();
    $cur_time = $args["StartTime"];
    while($cur_time <= $args["EndTime"])
    {
      $ret_array["MonthNames"][$ret_array["nbMonths"]] = date("M-y", $cur_time);
      $vPos[date("m-Y", $cur_time)] = $ret_array["nbMonths"];
      foreach($args["ProjIDs"] as $pid)
      {
        $ret_array["data"][$pid][$ret_array["nbMonths"]] = 0.0;
      }
      $ret_array["nbMonths"]++;
      $cur_time = strtotime("+1 month", $cur_time);
    }
    switch($args["data"])
    {
      case "SUs":
        $sql = "SELECT sum(actual_su)".$sql;
        $select = "sum(actual_su)";
        break;
      case "JOBS":
        $sql = "SELECT count(pbs_jobnumber)".$sql;
        $select = "count(pbs_jobnumber)";
        break;
      default:
        die("myJAM>> FATAL ERROR. UNKOWN DATA TYPE.");
    } // end of switch
    $vJobs = self::$db->query($sql);
    foreach($vJobs as $job)
    {
      $pos = $vPos[sprintf("%02u",$job["month(date)"])."-".$job["year(date)"]];
      if(!isset($pos))
        {die("");}
      if((int)$job["pid"]>0)
      {
        $data = $job[$select];
        $ret_array["data"][(int)$job["pid"]][$pos] = $data;
        $ret_array["pMonth"][(int)$job["pid"]][$pos] = $job["month(date)"];
        $ret_array["pYear"][(int)$job["pid"]][$pos] = $job["year(date)"];
        $ret_array["MaxData"] = max($ret_array["MaxData"], $data);
      }
    }
    return $ret_array;
 }
 //o-------------------------------------------------------------------------------o
  PUBLIC function GetUserMonths($args)
//o-------------------------------------------------------------------------------o
  {
    $ret_array = array();
    $ret_array["data"] = array();
    $ret_array["nbDataLines"] = 0;
    $ret_array["nbMonths"] = 0;
    $ret_array["MonthNames"] = array();
    $ret_array["MaxData"] = 0.0;
    $ret_array["UserNames"] = array();
    $ret_array["pMonth"] = array();
    $ret_array["pYear"] = array();
    $sql = ", uid, month(date), year(date) FROM Jobs WHERE job_state='F' AND uid!=''";
    if (isset($args["ArchIDs"]) && !empty($args["ArchIDs"]))
      {$sql .= " AND ".self::GenSQLStatement("ArchID", $args["ArchIDs"]);}
    if (isset($args["UserIDs"]) && !empty($args["UserIDs"]))
      {$sql .= " AND ".self::GenSQLStatement("uid", $args["UserIDs"]);}
    if (isset($args["ProjIDs"]) && !empty($args["ProjIDs"]))
      {$sql .= " AND ".self::GenSQLStatement("pid", $args["ProjIDs"]);}
    if (isset($args["StartTime"]))
      {$sql .= " AND UNIX_TIMESTAMP(date)>='".$args["StartTime"]."'";}
    if (isset($args["EndTime"]))
      {$sql .= " AND UNIX_TIMESTAMP(date)<='".$args["EndTime"]."'";}
    $sql .= " GROUP BY year(date),month(date),uid";
//initialize data arrays
    $vPos = array();
    $cur_time = $args["StartTime"];
    while($cur_time <= $args["EndTime"])
    {
      $ret_array["MonthNames"][$ret_array["nbMonths"]] = date("M-y", $cur_time);
      $vPos[date("m-Y", $cur_time)] = $ret_array["nbMonths"];
      foreach($args["UserIDs"] as $uid)
        {$ret_array["data"][$uid][$ret_array["nbMonths"]] = 0.0;}
      $ret_array["nbMonths"]++;
      $cur_time = strtotime("+1 month", $cur_time);
    }
    switch($args["data"])
    {
      case "SUs":
        $sql = "SELECT sum(actual_su)".$sql;
        $select = "sum(actual_su)";
        break;
      case "JOBS":
        $sql = "SELECT count(pbs_jobnumber)".$sql;
        $select = "count(pbs_jobnumber)";
        break;
      default:
        die("myJAM>> FATAL ERROR. UNKOWN DATA TYPE.");
    } // end of switch
    $vJobs = self::$db->query($sql);
    foreach($vJobs as $job)
    {
      $pos = $vPos[sprintf("%02u",$job["month(date)"])."-".$job["year(date)"]];
      if(!isset($pos))
        {die("");}
      if((int)$job["uid"]>0)
      {
        $data = $job[$select];
        $ret_array["data"][(int)$job["uid"]][$pos] = $data;
        $ret_array["pMonth"][(int)$job["uid"]][$pos] = $job["month(date)"];
        $ret_array["pYear"][(int)$job["uid"]][$pos] = $job["year(date)"];
        $ret_array["MaxData"] = max($ret_array["MaxData"], $data);
      }
    }
    return $ret_array;
  }
 //o-------------------------------------------------------------------------------o
  PUBLIC function GetDistDeps($args)
//o-------------------------------------------------------------------------------o
  {
    $sql = " from Jobs, Projects, Institutes, Departments".
           " WHERE Jobs.pid!=''".
           " AND Jobs.ArchID!=''".
           " AND Jobs.uid!=''".
           " AND Jobs.pid=Projects.pid".
           " AND Projects.InstID=Institutes.InstID".
           " AND Institutes.DepID=Departments.DepID";
    if (!empty($args["JobState"]))
    {
      $sql .= " AND Jobs.job_state='" . $args["JobState"] . "'";
    }
    if (isset($args["ArchIDs"]) && !empty($args["ArchIDs"]))
    {
      $sql .= " AND " . self::GenSQLStatement("Jobs.ArchID", $args["ArchIDs"]);
    }
    if (isset($args["StartTime"]) && (int)$args["StartTime"] > 0)
    {
      $sql .= " AND UNIX_TIMESTAMP(Jobs.date)>='" . $args["StartTime"] . "'";
    }
    if (isset($args["EndTime"]) && (int)$args["EndTime"] > 0)
    {
      $sql .= " AND UNIX_TIMESTAMP(Jobs.date)<='" . $args["EndTime"] . "'";
    }
    $sql .= " GROUP BY DepName ORDER BY Jobs DESC";
    $this->ret_array = array();
    $this->ret_array["data"] = array();
    $this->ret_array["DepNames"] = array();
    $this->ret_array["nbData"] = 0;
    $this->ret_array["MaxData"] = 0;
    switch($args["data"])
    {
      case "JOBS":
        $sql = "SELECT round(count(Jobs.pbs_jobnumber),2) AS Jobs, Departments.Name AS DepName".$sql;
        break;
      case "SUs":
        $sql = "SELECT round(sum(Jobs.actual_su),2) AS Jobs, Departments.Name AS DepName".$sql;
        break;
      default:
        die("ILLEGAL ARGUMENT");
    }
    $vDeps = self::$db->query($sql);
    foreach($vDeps as $dep)
    {
      $this->ret_array["data"][$this->ret_array["nbData"]] = $dep['Jobs'];
      $this->ret_array["DepNames"][$this->ret_array["nbData"]] = $dep['DepName'];
      $this->ret_array["nbData"]++;
      $this->ret_array["MaxData"] = max($this->ret_array["MaxData"], $dep['Jobs']);
    }
    return(true);
  }
//o-------------------------------------------------------------------------------o
  PUBLIC function GetDistInst($args)
//o-------------------------------------------------------------------------------o
  {
    $sql = " from Jobs, Projects, Institutes".
           " WHERE Jobs.pid!=''".
           " AND Jobs.ArchID!=''".
           " AND Jobs.uid!=''".
           " AND Jobs.pid=Projects.pid".
           " AND Projects.InstID=Institutes.InstID";
    if (!empty($args["JobState"]))
      {$sql .= " AND Jobs.job_state='".$args["JobState"]."'";}
    if (isset($args["ArchIDs"]) && !empty($args["ArchIDs"]))
    {
      $sql .= " AND ".self::GenSQLStatement("Jobs.ArchID", $args["ArchIDs"]);
    }
    if (isset($args["StartTime"]) && (int)$args["StartTime"] > 0)
      {$sql .= " AND UNIX_TIMESTAMP(Jobs.date)>='".$args["StartTime"]."'";}
    if (isset($args["EndTime"]) && (int)$args["EndTime"] > 0)
      {$sql .= " AND UNIX_TIMESTAMP(Jobs.date)<='".$args["EndTime"]."'";}
    $sql .= " GROUP BY InstName ORDER BY Jobs DESC";
    $this->ret_array = array();
    $this->ret_array['data'] = array();
    $this->ret_array['InstNames'] = array();
    $this->ret_array['nbData'] = 0;
    $this->ret_array['MaxData'] = 0;
    switch($args['data'])
    {
      case 'JOBS':
        $sql = 'SELECT round(count(Jobs.pbs_jobnumber),2) AS Jobs, Institutes.Name AS InstName'.$sql;
        break;
      case 'SUs':
        $sql = 'SELECT round(sum(Jobs.actual_su),2) AS Jobs, Institutes.Name AS InstName'.$sql;
        break;
      default:
        die('ILLEGAL ARGUMENT');
    }
    $vInst = self::$db->query($sql);
    foreach($vInst as $inst)
    {
      $this->ret_array['data'][$this->ret_array['nbData']] = $inst['Jobs'];
      $this->ret_array['InstNames'][$this->ret_array['nbData']] = html_entity_decode($inst['InstName']);
      $this->ret_array['nbData']++;
      $this->ret_array['MaxData'] = max($this->ret_array['MaxData'], $inst['Jobs']);
    }
    return(true);
  }
 //o-------------------------------------------------------------------------------o
  PUBLIC function GetDistUser($args)
//o-------------------------------------------------------------------------------o
  {
    $UserList = new myJAM_User();
    $sql = " FROM Jobs WHERE uid!=''";
    if (!empty($args["JobState"]))
      {$sql .= " AND job_state='".$args["JobState"]."'";}
    if (isset($args["ArchIDs"]) && !empty($args["ArchIDs"]))
    {
      $sql .= " AND ".self::GenSQLStatement("ArchID", $args["ArchIDs"]);
    }
    if (isset($args["StartTime"]))
      {$sql .= " AND UNIX_TIMESTAMP(date)>='".$args["StartTime"]."'";}
    if (isset($args["EndTime"]))
      {$sql .= " AND UNIX_TIMESTAMP(date)<='".$args["EndTime"]."'";}
    $sql .= " GROUP BY uid";
    $ret_array = array();
    $ret_array["data"] = array();
    $ret_array["UserNames"] = array();
    $ret_array["nbData"] = 0;
    $ret_array["MaxData"] = 0;
    $vCounts = array();
    switch($args["data"])
    {
      case "JOBS":
        $sql = "SELECT uid, count(pbs_jobnumber) ".$sql;
        $vJobs = self::$db->query($sql);
        foreach($vJobs as $job)
          {$vCounts[$UserList->UserName[$job["uid"]]] = $job["count(pbs_jobnumber)"];}
        break;
      case "SUs":
        $sql = "SELECT sum(actual_su), uid ".$sql;
        $vJobs = self::$db->query($sql);
        foreach($vJobs as $job)
          {$vCounts[$UserList->UserName[$job["uid"]]] = $job["sum(actual_su)"];}
        break;
      default:
        die("ILLEGAL ARGUMENT");
    }
    arsort($vCounts);
    foreach($vCounts as $user=>$count)
    {
      $ret_array["data"][$ret_array["nbData"]] = $count;
      $ret_array["UserNames"][$ret_array["nbData"]] = $user;
      $ret_array["nbData"]++;
      $ret_array["MaxData"] = max($ret_array["MaxData"], $count);
    }
    return($ret_array);
  }
//o-------------------------------------------------------------------------------o
  PUBLIC function GetDistProjects($args)
//o-------------------------------------------------------------------------------o
  {
    $ProjList = new myJAM_Project();
    $sql = " FROM Jobs WHERE pid!=''";
    if (!empty($args["JobState"]))
      {$sql .= " AND job_state='".$args["JobState"]."'";}
    if (isset($args["ArchIDs"]) && !empty($args["ArchIDs"]))
    {
      $sql .= " AND ".self::GenSQLStatement("ArchID", $args["ArchIDs"]);
    }
    if (isset($args["StartTime"]))
      {$sql .= " AND UNIX_TIMESTAMP(date)>='".$args["StartTime"]."'";}
    if (isset($args["EndTime"]))
      {$sql .= " AND UNIX_TIMESTAMP(date)<='".$args["EndTime"]."'";}
    $sql .= " GROUP BY pid";
    $ret_array = array();
    $ret_array["data"] = array();
    $ret_array["UserNames"] = array();
    $ret_array["nbData"] = 0;
    $ret_array["MaxData"] = 0;
    $vCounts = array();
    switch($args["data"])
    {
      case "JOBS":
        $sql = "SELECT pid, count(pbs_jobnumber) ".$sql;
        $vJobs = self::$db->query($sql);
        foreach($vJobs as $job)
          {$vCounts[$ProjList->Name[$job["pid"]]] = $job["count(pbs_jobnumber)"];}
        break;
      case "SUs":
        $sql = "SELECT sum(actual_su), pid ".$sql;
        $vJobs = self::$db->query($sql);
        foreach($vJobs as $job)
          {$vCounts[$ProjList->Name[$job["pid"]]] = $job["sum(actual_su)"];}
        break;
      default:
        die("ILLEGAL ARGUMENT");
    }
    arsort($vCounts);
    foreach($vCounts as $proj=>$count)
    {
      $ret_array["data"][$ret_array["nbData"]] = $count;
      $ret_array["ProjNames"][$ret_array["nbData"]] = $proj;
      $ret_array["nbData"]++;
      $ret_array["MaxData"] = max($ret_array["MaxData"], $count);
    }
    return($ret_array);
  }
//o-------------------------------------------------------------------------------o
  PUBLIC function GetDistCores($args)
//o-------------------------------------------------------------------------------o
  {
    $sql = " FROM Jobs WHERE num_procs!=''";
    if (!empty($args["JobState"]))
      {$sql .= " AND job_state='".$args["JobState"]."'";}
    if (isset($args["ArchIDs"]) && !empty($args["ArchIDs"]))
    {
      $sql .= " AND ".self::GenSQLStatement("ArchID", $args["ArchIDs"]);
    }
    if (isset($args["StartTime"]))
      {$sql .= " AND UNIX_TIMESTAMP(date)>='".$args["StartTime"]."'";}
    if (isset($args["EndTime"]))
      {$sql .= " AND UNIX_TIMESTAMP(date)<='".$args["EndTime"]."'";}
    $sql .= " GROUP BY num_procs";
    $ret_array = array();
    $ret_array["data"] = array();
    $ret_array["nbData"] = 0;
    $ret_array["MaxData"] = 0;
    $ret_array["MaxCores"] = 0;
    switch($args["data"])
    {
      case "JOBS":
        $sql = "SELECT num_procs, count(pbs_jobnumber) ".$sql;
        $vJobs = self::$db->query($sql);
        break;
      case "SUs":
        $sql = "SELECT num_procs, sum(actual_su) ".$sql;
        $vJobs = self::$db->query($sql);
        break;
      default:
        die("ILLEGAL ARGUMENT");
    }
    foreach($vJobs as $job)
    {
      switch($args["data"])
      {
        case "JOBS":
          $ret_array["data"][$job["num_procs"]] = $job["count(pbs_jobnumber)"];
          break;
        case "SUs":
          $ret_array["data"][$job["num_procs"]] = $job["sum(actual_su)"];
          break;
      }
      $ret_array["MaxData"] = max($ret_array["MaxData"], $ret_array["data"][$job["num_procs"]]);
      $ret_array["MaxCores"] = max($ret_array["MaxCores"], $job["num_procs"]);
      $ret_array["nbData"]++;
    }
    return($ret_array);
  }
//o-------------------------------------------------------------------------------o
  PUBLIC function GetDistCoresLOG2($args)
//o-------------------------------------------------------------------------------o
  {
    $sql = " FROM Jobs WHERE num_procs!=''";
    if (!empty($args['JobState']))
      {$sql .= " AND job_state='".$args['JobState']."'";}
    if (isset($args['ArchIDs']) && !empty($args['ArchIDs']))
    {
      $sql .= ' AND '.self::GenSQLStatement('ArchID', $args['ArchIDs']);
    }
    if (isset($args['StartTime']))
      {$sql .= " AND UNIX_TIMESTAMP(date)>='".$args['StartTime']."'";}
    if (isset($args['EndTime']))
      {$sql .= " AND UNIX_TIMESTAMP(date)<='".$args['EndTime']."'";}
    $sql .= ' GROUP BY log2_procs';
    $ret_array = array();
    $ret_array['data'] = array();
    $ret_array['nbData'] = 0;
    $ret_array['MaxData'] = 0;
    $ret_array['MaxCoresLOG2'] = 0;
    switch($args['data'])
    {
      case 'JOBS':
        $sql = 'SELECT FLOOR(LOG2(num_procs)) AS log2_procs, count(pbs_jobnumber) '.$sql;
        $vJobs = self::$db->query($sql);
        break;
      case 'SUs':
        $sql = 'SELECT FLOOR(LOG2(num_procs)) AS log2_procs, sum(actual_su) as norm_su '.$sql;
        $vJobs = self::$db->query($sql);
        break;
      default:
        die('ILLEGAL ARGUMENT');
    }
    foreach($vJobs as $job)
    {
      switch($args['data'])
      {
        case 'JOBS':
          $ret_array['data'][$job['log2_procs']] = $job['count(pbs_jobnumber)'];
          break;
        case 'SUs':
          $ret_array['data'][$job['log2_procs']] = $job['norm_su'];
          break;
      }
      $ret_array['MaxData'] = max($ret_array['MaxData'], $ret_array['data'][$job['log2_procs']]);
      $ret_array['MaxCoresLOG2'] = max($ret_array['MaxCoresLOG2'], $job['log2_procs']);
      $ret_array['nbData']++;
    }
    return($ret_array);
  }
 //o-------------------------------------------------------------------------------o
 PUBLIC function GetRunning($Mode)
 //o-------------------------------------------------------------------------------o
 {
   $this->ret_array = array();
    $this->ret_array["MaxData"] = 0.0;
    $this->ret_array["nbData"] = 0;
    $this->ret_array["data"] = array();
    $this->ret_array["ArchNames"] = array();
//get Current date
    $CurMonth = date("m");
    $CurYear = date("Y");
//get Architectures
    $Archs = new myJAM_Architecture();
    $vPos = array();
    $TotalCores = 0;
    $ArchIDList = $Archs->IDList;
    foreach($ArchIDList as $ArchID)
    {
      $this->ret_array["data"][$this->ret_array["nbData"]] = 0.0;
      $this->ret_array["ArchNames"][$this->ret_array["nbData"]] = $Archs->Name[$ArchID];
      $TotalCores += $Archs->nbCores[$ArchID];
      $vPos[$ArchID] = $this->ret_array["nbData"];
      $this->ret_array["nbData"]++;
    }
    $sql = ", ArchID FROM Jobs WHERE ArchID!='' AND job_state='R' GROUP BY ArchID";
    switch($Mode)
    {
      case "JOBS":
        $select = "count(pbs_jobnumber)";
        break;
      case "CORES":
      case "LOAD":
        $select = "sum(num_procs)";
        break;
    }
    $sql = "SELECT ".$select.$sql;
    $vResJobs = self::$db->query($sql);
    $RunningCores = 0;
    foreach($vResJobs as $job)
    {
      if(isset($vPos[$job["ArchID"]]))
      {
        $pos = $vPos[$job["ArchID"]];
        if($Mode=="LOAD")
          {$this->ret_array["data"][$pos] = $job[$select] / $Archs->nbCores[$job["ArchID"]] * 100.0;}
        else
          {$this->ret_array["data"][$pos] = $job[$select];}
        $this->ret_array["MaxData"] = max ($this->ret_array["MaxData"], $job[$select]);
        $RunningCores += $job[$select];
      }
    }
    if ($Mode == "LOAD")
    {
      $result = ($RunningCores / $TotalCores) * 100.0;
      $this->ret_array["nbData"]++;
      $this->ret_array["data"][$this->ret_array["nbData"]] = $result;
      $this->ret_array["MaxData"] = max ($this->ret_array["MaxData"], $result);
      $this->ret_array["ArchNames"][$this->ret_array["nbData"]] = "Total";
    }
    return(true);
 }
 //o-------------------------------------------------------------------------------o
  PROTECTED function GenPosArray($attribute)
//o-------------------------------------------------------------------------------o
  {
    $this->vPos = array();
    $cur_time = $this->args["StartTime"];
    while($cur_time <= $this->args["EndTime"])
    {
      $this->ret_array["MonthNames"][$this->ret_array["nbMonths"]] = date("M-y", $cur_time);
      $this->vPos[date("m-Y", $cur_time)] = $this->ret_array["nbMonths"];
      foreach($this->args[$attribute] as $id)
        {$this->ret_array["data"][$id][$this->ret_array["nbMonths"]] = 0.0;}
      $this->ret_array["nbMonths"]++;
      $cur_time = strtotime("+1 month", $cur_time);
    }
  }
//o-------------------------------------------------------------------------------o
  PROTECTED function Args2SQL()
//o-------------------------------------------------------------------------------o
  {
    $sql = "";
    if (isset($this->args["ArchIDs"]) && !empty($this->args["ArchIDs"]))
      {$sql .= " AND ".$this->GenSQLStatement("ArchID", $this->args["ArchIDs"]);}
    if (isset($this->args["UserIDs"]) && !empty($this->args["UserIDs"]))
      {$sql .= " AND ".$this->GenSQLStatement("uid", $this->args["UserIDs"]);}
    if (isset($this->args["ProjIDs"]) && !empty($this->args["ProjIDs"]))
      {$sql .= " AND ".$this->GenSQLStatement("pid", $this->args["ProjIDs"]);}
    if (isset($this->args["StartTime"]))
      {$sql .= " AND UNIX_TIMESTAMP(date)>='".$this->args["StartTime"]."'";}
    if (isset($this->args["EndTime"]))
      {$sql .= " AND UNIX_TIMESTAMP(date)<='".$this->args["EndTime"]."'";}
    return $sql;
  }
//o-------------------------------------------------------------------------------o
 PROTECTED function GenSQLStatement($attribute, $list)
 //o-------------------------------------------------------------------------------o
 {
   $sql = "(";
   foreach($list as $id)
   {
     if($sql != "(")
     {
       $sql .= " OR";
     }
     $sql .= " ".$attribute."='".(int)$id."'";
   }
   $sql .= ")";
   return $sql;
 }
 //o-------------------------------------------------------------------------------o
  PROTECTED function IsMonthly()
//o-------------------------------------------------------------------------------o
  {
    if(strpos($this->args["data"], "MONTHLY") !== false)
      {return true;}
    else
      {return false;}
  }
//o-------------------------------------------------------------------------------o
  PROTECTED function IsCumulative()
//o-------------------------------------------------------------------------------o
  {
    if(strpos($this->args["data"], "CUMULATIVE") !== false)
      {return true;}
    else
      {return false;}
  }
//o-------------------------------------------------------------------------------o
  PROTECTED function JobsReq()
//o-------------------------------------------------------------------------------o
  {
    if(strpos($this->args["data"], "JOBS") !== false)
      {return true;}
    else
      {return false;}
  }
//o-------------------------------------------------------------------------------o
  PROTECTED function SUsReq()
//o-------------------------------------------------------------------------------o
  {
    if(strpos($this->args["data"], "SUs") !== false)
      {return true;}
    else
      {return false;}
  }
}
