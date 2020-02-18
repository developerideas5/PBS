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
class myJAM_CostModel
{
  PROTECTED STATIC $db;
  PROTECTED STATIC $vCostsID;
  PROTECTED STATIC $vCostsDescription;
  PROTECTED STATIC $vCostsNormal;
  PROTECTED STATIC $vCostsOver;
  PROTECTED $pCostID;
//o-------------------------------------------------------------------------------o
  PUBLIC function __construct($costID=NULL)
//o-------------------------------------------------------------------------------o
  {
    if (!is_object(self::$db))
    {
      self::$db = new myJAM_DB();
      self::$vCostsID = array();
      self::$vCostsDescription = array();
      self::$vCostsNormal = array();
      self::$vCostsOver = array();
      $query = 'SELECT cid, cost_descr, cost_su_norm, cost_su_over FROM Cost';
      $vCosts = self::$db->query($query);
      $nbCosts = self::$db->num_rows();
      for($i = 0; $i < $nbCosts; $i++)
      {
        $cid = $vCosts[$i]['cid'];
        self::$vCostsID[$i] = $cid;
        self::$vCostsDescription[$cid] = $vCosts[$i]['cost_descr'];
        self::$vCostsNormal[$cid] = (float)$vCosts[$i]['cost_su_norm'];
        self::$vCostsOver[$cid] = (float)$vCosts[$i]['cost_su_over'];
      }
    }
    if (isset($costID))
    {
      if (isset(self::$vCostsDescription[(int)$costID]))
      {
        $this->pCostID = (int)$costID;
      }
      else if (($this->pCostID = array_search($costID, self::$vCostsDescription)) === false)
      {
        $this->pCostID = NULL;
      }
    }
  }
//o-------------------------------------------------------------------------------o
  PUBLIC function __get($name)
//o-------------------------------------------------------------------------------o
  {
    if ($this->pCostID)
    {
      switch($name)
      {
        case 'ID':
          return $this->pCostID;
        case 'Description':
          return self::$vCostsDescription[$this->pCostID];
        case 'Norm':
          return self::$vCostsNormal[$this->pCostID];
        case 'Over':
          return self::$vCostsOver[$this->pCostID];
        case 'NbProjects':
          return self::GetNbProjects($this->pCostID);
        default:
          return NULL;
      }
    }
    switch($name)
    {
      case 'ID':
        return self::$vCostsID;
      case 'Description':
        return self::$vCostsDescription;
      case 'Norm':
        return self::$vCostsNormal;
      case 'Over':
        return self::$vCostsOver;
      case 'NbProjects':
        $tmp = array();
        foreach(self::$vCostsID as $cid)
        {
          $tmp[(int)$cid] = self::GetNbProjects($cid);
        }
        return $tmp;
      default:
        return NULL;
    }
  }
//o-------------------------------------------------------------------------------o
  PUBLIC function __set($name, $val)
//o-------------------------------------------------------------------------------o
  {
    if (!isset($val) || $val == "")
    {
      die("myJAM>> FATAL ERROR 0x61b6 in class myJAM_CostModel!");
    }
    if(is_string($val))
    {
      $val = htmlentities($val);
    }
    if ($this->pCostID)
    {
      switch($name)
      {
        case 'Description':
          self::$vCostsDescription[$this->pCostID] = $val;
          $sql_name = 'cost_descr';
          break;
        case 'Norm':
          if (!is_numeric($val))
          {
            return NULL;
          }
          $val = sprintf('%4.3f', $val);
          self::$vCostsNormal[$this->pCostID] = $val;
          $sql_name = 'cost_su_norm';
          break;
        case 'Over':
          if (!is_numeric($val))
          {
            return NULL;
          }
          $val = sprintf('%4.3f', $val);
          self::$vCostsOver[$this->pCostID] = $val;
          $sql_name = 'cost_su_over';
          break;
        default:
          return NULL;
      }
      $sql = "UPDATE Cost SET " . $sql_name . "='" . mysql_real_escape_string($val) . "' WHERE cid=" . (int)$this->pCostID;
      self::$db->query($sql);
    }
    return NULL;
  }
//o-------------------------------------------------------------------------------o
  PROTECTED function GetNbProjects($cid)
//o-------------------------------------------------------------------------------o
  {
    if (!is_numeric($cid))
    {
      die("myJAM>> FATAL ERROR 0x4fd2 in class myJAM_CostModel!");
    }
    $sql = 'SELECT pid FROM Projects WHERE cid=\''.(int)$cid.'\'';
    self::$db->query($sql);
    return self::$db->num_rows();
  }
//o-------------------------------------------------------------------------------o
  PUBLIC static function Create($name, $norm, $over)
//o-------------------------------------------------------------------------------o
  {
    $db = new myJAM_db();
    if (!isset($name) || $name == "")
    {
      die("myJAM>> FATAL ERROR 0xca99 in class myJAM_CostModel!");
    }
    $name = htmlentities($name);
    if (!isset($norm) || $norm == "" | !is_numeric($norm))
    {
      die("myJAM>> FATAL ERROR 0xb2a8 in class myJAM_CostModel!");
    }
    $norm = (float)$norm;
    if (!isset($over) || $over == "" || !is_numeric($over))
    {
      die("myJAM>> FATAL ERROR 0xb2a8 in class myJAM_CostModel!");
    }
    $over = (float)$over;
    $norm = sprintf("%4.3f", $norm);
    $over = sprintf("%4.3f", $over);
    $sql = 'INSERT INTO Cost (cost_descr, cost_su_norm, cost_su_over)'
          .' VALUES('
          .'\''.mysql_real_escape_string($name).'\''
          .',\''.mysql_real_escape_string($norm).'\''
          .',\''.mysql_real_escape_string($over).'\''
          .')';
    $db->DoSQL($sql);
    $cid = $db->last_insert_id();
    self::DestroyCache();
    return new myJAM_CostModel($cid);
  }
//o-------------------------------------------------------------------------------o
  PUBLIC function DELETE()
//o-------------------------------------------------------------------------------o
  {
    if ($this->pCostID)
    {
      if( ($pointer = array_search($this->pCostID, self::$vCostsID)) === false)
      {
        die("myJAM>> FATAL INTERNAL ERROR 9K7V in class CostModel");
      }
      unset(self::$vCostsID[(int)$pointer]);
      unset(self::$vCostsDescription[$this->pCostID]);
      unset(self::$vCostsNormal[$this->pCostID]);
      unset(self::$vCostsOver[$this->pCostID]);
      $sql = 'DELETE FROM Cost where cid=\'' . (int)$this->pCostID. '\'';
      self::$db->query($sql);
      return (count(self::$vCostsID));
    }
    return NULL;
  }
//o-------------------------------------------------------------------------------o
  public static function DestroyCache()
//o-------------------------------------------------------------------------------o
  {
    self::$db = NULL;
    self::$vCostsID = array();
    self::$vCostsDescription = array();
    self::$vCostsNormal = array();
    self::$vCostsOver = array();
  }
}
