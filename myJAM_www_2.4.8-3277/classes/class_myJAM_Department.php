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

// require_once(_FULLPATH."/classes/class_myJAM_DevelopmentMode.php");
require_once (_FULLPATH . "/classes/class_myJAM_DB.php");
/**
 * @desc Prototype Department Class for <myJAM/>
 */
class myJAM_Department
{
  protected static $db; /* db object for database access */
  protected static $nbDeps; /* total number of departments */
  /* Department object sql cache table */
  protected static $vDepIDs; /* vector of deparment's unique id number */
  protected static $vDepIDsHash; /* maps dep. names to their ids */
  protected static $vDepIDsListHash; /* maps ids to vector positions */
  protected static $vDepNames; /* vector of department names */
  protected static $vDepNameHash; /* maps ids to their names */
  //protected static $debug;
  protected $pDepID; /* points to actual used instance within the cache table */
//o-------------------------------------------------------------------------------o
  PUBLIC function __construct($dep = NULL)
//o-------------------------------------------------------------------------------o
  {
//     if (!is_object(self::$debug))
//     {
//       self::$debug = class_myJAM_DevelopmentMode::getInstance();
//     }
    if (!is_object(self::$db))
    {
      self::$db = new myJAM_DB();
      self::$nbDeps = 0;
      self::$vDepIDs = array();
      self::$vDepNames = array();
      self::$vDepIDsHash = array();
      self::$vDepIDsListHash = array();
      self::$vDepNameHash = array();
      $query = 'SELECT DepID, Name FROM Departments' . ' WHERE Name!=\'n/a\' ORDER BY Name ASC';
      $vDepartments = self::$db->query($query);
      self::$nbDeps = self::$db->num_rows();
      /* init sql cache table for GUI use with existing values from DB */
      for($i = 0; $i < self::$nbDeps; $i++)
      {
        /* get temp values from Department table row */
        $DepID = $vDepartments[$i]['DepID'];
        $DepName = $vDepartments[$i]['Name'];
        /* map temp values to cache tabel */
        self::$vDepIDs[$i] = $DepID;
        self::$vDepNames[$i] = $DepName;
        /* setup hash (index) access parameters */
        self::$vDepIDsHash[$DepName] = $DepID;
        self::$vDepIDsListHash[$DepID] = $i;
        self::$vDepNameHash[$DepID] = $DepName;
      }
    }
    if ($dep)
    {
      if (!is_numeric($dep) || !isset(self::$vDepNameHash[(int)$dep]))
      {
        if (!@$this->pDepID = self::$vDepIDsHash[$dep])
        {
          $this->pDepID = 0;
          return NULL;
        }
      }
      else
      {
        $this->pDepID = (int)$dep;
      }
    }
    else /* $dep not given so I assume this meant to be a LIST object [sic]*/
    {
      $this->pDepID = 0;
    }
  }
//o-------------------------------------------------------------------------------o
  PUBLIC function __get($name)
//o-------------------------------------------------------------------------------o
  {
    if ($this->pDepID > 0)
    {
      switch($name)
      {
        case 'ID':
          return $this->pDepID;
        case 'Name':
          return self::$vDepNameHash[$this->pDepID];
        default:
          throw new Exception('Undefined property via __get() '.$name);
      }
    }
    switch($name)
    {
      case 'nb':
        return self::$nbDeps;
      case 'Name':
        return self::$vDepNameHash;
      case 'NameList':
        return self::$vDepNames;
      case 'ID':
        return self::$vDepIDsHash;
      case 'IDList':
        return self::$vDepIDs;
      default:
        //$trace = debug_backtrace();
        //trigger_error('Undefined property via __get(): ' . $name . ' in ' . $trace[0]['file'] . ' on line ' . $trace[0]['line'], E_USER_NOTICE);
        throw Exception('Undefined property via __get() '.$name);
        //return NULL;
    }
  }
//o-------------------------------------------------------------------------------o
  PUBLIC function __set($name, $val)
//o-------------------------------------------------------------------------------o
  {
    if (!isset($val))
    {
      die("myJAM>> FATAL ERROR 0x421f20f in class myJAM_Department!");
    }
    if ($this->pDepID > 0)
    {
      switch($name)
      {
        case 'Name':
          $sql_name = 'Name';
          break;
        default:
          throw new Exception("Attribute $name can not be set.");
          break;
      }
      $sql = 'UPDATE Departments SET '.$sql_name.'=\''.mysql_escape_string($val).'\''
            .' WHERE DepID=\'' . (int)$this->pDepID . '\'';
      try
      {
        self::$db->query($sql);
      }
      catch(Exception $e)
      {
        print_r($e);
        die("::error in class_myJAM_Department.php->__set => could not update DB row");
      }
      self::DestroyCache();
      self::__construct($this->pDepID);
      return $val;
    }
    return NULL;
  }
//o-------------------------------------------------------------------------------o
  PUBLIC static function CreateDepartment($name)
//o-------------------------------------------------------------------------------o
  {
    $db = new myJAM_db();
    /* sanitize and check input */
    if (!isset($name) || empty($name) || !is_string($name) || !self::ValidateDepartmentName($name))
    {
      die("myJAM>> FATAL ERROR 'MISSING ERROR CODE FOR ILLEGAL ARGUMENT EXCEPTION' in class myJAM_Department!");
    }
    $pointer = self::$nbDeps;
    /* incerement deparment count */
    self::$nbDeps++;
    /* create and execute sql query */
    $sql = "INSERT INTO Departments (Name)"
          ." VALUES('" . mysql_real_escape_string($name) . "')";
    $db->DoSQL($sql);
    $DepID = $db->last_insert_id();
    if ($DepID < 1)
    {
      die("myJAM>> FATAL ERROR 908164: MALFORMED DEPARTMENT_ID in class myJAM_Department.CreateDepartment!");
    }
    self::DestroyCache();
    $NewDep = new myJAM_Department($DepID);
    return $NewDep;
  }
//o-------------------------------------------------------------------------------o
  PUBLIC function Delete()
//o-------------------------------------------------------------------------------o
  {
//     if (self::$debug->Department)
//     {
//       echo ":: deleting department with id=" . $this->pDepID;
//       echo " << class_myJAM_Depatrment<br>";
//     }
    /* check institute dependencies */
    if (!$this->isClean())
    {
      $DepName = self::$vDepNameHash[$this->pDepID];
      throw new Exception("Department($DepName," . $this->pDepID . ") is refrenced by existing institutes, cannot delete.");
    }
    /* try to delete from db */
    try
    {
      $sql = "DELETE FROM Departments WHERE DepID='" . (int)$this->pDepID . "'";
      self::$db->query($sql);
    }
    catch(Exception $e)
    {
      throw $e;
    }
    $pointer = self::$vDepIDsListHash[$this->pDepID];
    $DepName = self::$vDepNameHash[$this->pDepID];
    unset(self::$vDepIDs[$pointer]);
    unset(self::$vDepNames[$pointer]);
    unset(self::$vDepIDsHash[$DepName]);
    unset(self::$vDepIDsListHash[$this->pDepID]);
    unset(self::$vDepNameHash[$this->pDepID]);
    self::$nbDeps--;
    return self::$nbDeps;
  }
//o-------------------------------------------------------------------------------o
  PUBLIC function isClean()
//o-------------------------------------------------------------------------------o
  {
//     if (self::$debug->Department)
//     {
//       echo "::checking department dependencies";
//       echo " << class_myJAM_Depatrment->isClean()<br>";
//     }
//     try
//     {
      $sql = "SELECT count(InstID) AS nbInst FROM Institutes WHERE DepID='" . (int)$this->pDepID . "'";
      $result_set = self::$db->query($sql);
//     }
//     catch(Exception $e)
//     {
//       //impl. excep. handling
//     }
    if ((int)$result_set[0]['nbInst'] == 0)
    {
//       if (self::$debug->Department)
//       {
//         echo ":: no institutes depend on this department, is clean to delete";
//         echo " <<class_myJAM_Depatrment->isClean()<br>";
//       }
      return true; // no institutes depend on this department, is clean to delete
    }
    else
    {
//       if (self::$debug->Department)
//       {
//         echo ":: institutes _depend_ on this department, is _not_ clean to delete";
//         echo " <<class_myJAM_Depatrment->isClean()<br>";
//       }
      return false; // institute(s) depend on this department, is _not_ clean to delete
    }
  }
//o-------------------------------------------------------------------------------o
  private static function ValidateDepartmentName($name)
//o-------------------------------------------------------------------------------o
  {
    $pattern = '/[\wäöüß]{3,255}/';
    if ($name != NULL && preg_match($pattern, $name))
    {
      return true;
    }
    else
    {
      return false;
    }
  }
//o-------------------------------------------------------------------------------o
  public static function DestroyCache()
//o-------------------------------------------------------------------------------o
  {
    self::$db = NULL;
    self::$nbDeps = NULL;
    self::$vDepIDs = array();
    self::$vDepIDsHash = array();
    self::$vDepIDsListHash = array();
    self::$vDepNames = array();
    self::$vDepNameHash = array();
  }
} /* EOClass */
