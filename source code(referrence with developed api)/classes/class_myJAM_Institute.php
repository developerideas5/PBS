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

/*
 * Prototype Institute Class for <myJAM/>
 *
 */
// #ifdef DEVELMODE
// require_once(_FULLPATH.'/classes/class_myJAM_DevelopmentMode.php');
// #endif
require_once (_FULLPATH . '/classes/class_myJAM_DB.php');
require_once (_FULLPATH . '/classes/class_myJAM_Department.php');
class myJAM_Institute
{
  protected static $db; /* db object for database access */
  protected static $nbInst; /* total number of institutes */
  /* Institute object sql cache _table_ */
  protected static $vInstIDs; /* vector of institues unique ids */
  protected static $vInstIDsHash; /* maps institute ids to vector positions  */
  protected static $vInstIDsListHash; /* maps  */
  protected static $vInstituteNames; /* vector of institute names */
  protected static $vInstituteNameHash; /* vector of institute names */
  protected static $vDeps; /* vector of department ids */
//   protected static $debug;
  protected $pInstID; /* points via id to actual used instance within the cache table */
//o-------------------------------------------------------------------------------oo
  PUBLIC function __construct($institute = Null)
//o-------------------------------------------------------------------------------o
  {
//     if (!is_object(self::$debug))
//     {
//       self::$debug = class_myJAM_DevelopmentMode::getInstance();
//     }
    if (!is_object(self::$db))
    { /* init cache table */
      self::$db = new myJAM_DB();
      self::$nbInst = 0;
      self::$vInstIDs = array();
      self::$vInstituteNames = array();
      self::$vDeps = array();
      self::$vInstIDsHash = array();
      self::$vInstIDsListHash = array();
      self::$vInstituteNameHash = array();
      /* db querry */
      $query = 'SELECT InstID, Name, DepID'
             . ' FROM Institutes'
             . ' ORDER BY Name ASC';
      $vInstitutes = self::$db->query($query);
      self::$nbInst = self::$db->num_rows();
      /* init sql cache table for GUI use with existing values from DB */
      for($i = 0; $i < self::$nbInst; $i++)
      {
        /* get temp values from Institute table row */
        $InstituteID = (int)$vInstitutes[$i]['InstID'];
        $InstituteName = $vInstitutes[$i]['Name'];
        $DepID = (int)$vInstitutes[$i]['DepID'];
        /* map tmp values to cache table */
        self::$vInstIDs[$i] = $InstituteID;
        self::$vInstituteNames[$i] = $InstituteName;
        self::$vDeps[$InstituteID] = new myJAM_Department($DepID);
        /* hashing */
        self::$vInstIDsHash[$InstituteName] = $InstituteID;
        self::$vInstIDsListHash[$InstituteID] = $i;
        self::$vInstituteNameHash[$InstituteID] = $InstituteName;
      }
    }
    if (isset($institute))
    {
      if (!is_numeric($institute) || !isset(self::$vInstituteNameHash[(integer)$institute]))
      {
        if (!$this->pInstID = self::$vInstIDsHash[$institute])
        {
          $this->pInstID = 0;
          return NULL;
        }
      }
      else
      {
        /* ERROR ASSUMPTION WORNG pInstID != sql solved id !!! */
        $this->pInstID = (integer)$institute;
      }
    }
    else /* $institute not given so I assume this meant to be a LIST object [sic]*/
    {
      $this->pInstID = 0;
    }
  }
//o-------------------------------------------------------------------------------o
  PUBLIC function __get($name)
//o-------------------------------------------------------------------------------o
  {
    if ($this->pInstID > 0)
    {
      switch($name)
      {
        case 'ID':
          return $this->pInstID;
        case 'Name':
          return self::$vInstituteNameHash[$this->pInstID];
        case 'Department':
          return self::$vDeps[$this->pInstID];
        default:
          $trace = debug_backtrace();
          trigger_error('Undefined property via __get(): ' . $name . ' in ' . $trace[0]['file'] . ' on line ' . $trace[0]['line'], E_USER_NOTICE);
          return NULL;
      }
    }
    switch($name)
    {
      case 'nb':
        return self::$nbInst;
      case 'Name':
        return self::$vInstituteNameHash;
      case 'NameList':
        return self::$vInstituteNames;
      case 'ID':
        return self::$vInstIDsHash;
      case 'IDList':
        return self::$vInstIDs;
      case 'Department':
        return self::$vDeps;
      default:
        $trace = debug_backtrace();
        trigger_error('Undefined property via __get(): ' . $name . ' in ' . $trace[0]['file'] . ' on line ' . $trace[0]['line'], E_USER_NOTICE);
        return NULL;
    }
  }
//o-------------------------------------------------------------------------------o
  PUBLIC function __set($name, $val)
//o-------------------------------------------------------------------------------o
  {
    if ($this->pInstID > 0)
    {
      switch($name)
      {
        case 'Name':
          if (!self::ValidateInstituteName($val))
          {
            throw new Exception('Illegale name for Institute');
          }
          $sql_name = 'Name';
          break;
        case 'Department':
          if (!is_a($val, 'myJAM_Department') || !is_scalar($val->ID) || (int)$val->ID < 0)
          {
            throw new Exception('Can not set illegal myJAM_Department');
          }
          $val = (int)$val->ID;
          $sql_name = 'DepID';
          break;
        default:
          throw new Exception('Can not set attribute ' . $name);
          break;
      }
      $sql = 'UPDATE Institutes SET '.$sql_name.'=\''.mysql_real_escape_string($val).'\''
            .' WHERE InstID=\'' . (int)$this->pInstID . '\'';
      try
      {
        self::$db->DoSQL($sql);
      }
      catch(Exception $e)
      {
        print_r($e);
        die("::error in class_myJAM_Institutes.php->__set => could not update DB row");
      }
      self::DestroyCache();
      self::__construct($this->pInstID);
      return $val;
    }
    /* else no pInstID set */
    return NULL;
  }
//o-------------------------------------------------------------------------------o
  PUBLIC static function CreateInstitute($name, $Department = NULL)
//o-------------------------------------------------------------------------------o
  {
    $db = new myJAM_db();
    if (!isset($name) || empty($name) || !is_string($name))
    {
      die("myJAM>> FATAL ERROR 'MISSING ERROR CODE 1 FOR ILLEGAL ARGUMENT EXCEPTION::_name_(iname) is not valid' in class myJAM_Institue!");
    }
    $InstituteName = $name;
    if (!self::ValidateInstituteName($InstituteName))
    {
      die('myJAM>> FATAL ERROR IN CreateInstitute: Illegal Name');
    }
    if (!is_a($Department, 'myJAM_Department') || !is_scalar($Department->ID) || (int)$Department->ID < 1)
    {
      die("myJAM>> FATAL ERROR 'MISSING ERROR CODE 1 FOR ILLEGAL ARGUMENT EXCEPTION::Department _id_(did) is not valid' in class myJAM_Institue!");
    }
    $sql = "INSERT INTO Institutes (Name, DepID)"
          ." VALUES('" . mysql_real_escape_string($InstituteName) . "', '" . (int)$Department->ID . "')";
    try
    {
      $db->DoSQL($sql);
    }
    catch(Exception $e)
    {
      print_r($e);
      die("::error in class_myJAM_Institutes->__set => could not create new Institute");
    }
    $InstID = $db->last_insert_id();
    if ($InstID < 1)
    {
      die("myJAM>> FATAL ERROR 'ANOTHER MISSING ERROR CODE FOR AN MALFORMED INSTITUTE_ID' in class myJAM_Institute!");
    }
    self::DestroyCache();
    $NewInstitute = new myJAM_Institute($InstID);
    return $NewInstitute;
  }
//o-------------------------------------------------------------------------------o
  PUBLIC function Delete()
//o-------------------------------------------------------------------------------o
  {
    if ($this->pInstID > 0)
    {
      $sql = "DELETE FROM Institutes WHERE InstID=" . (int)$this->pInstID;
      self::$db->query($sql);
      self::$nbInst--;
      $this->pInstID = NULL;
      self::DestroyCache();
      self::__construct();
      return self::$nbInst;
    }
    else
    {
      die("myJAM>> FATAL ERROR 'KLOPS' in class myJAM_Institute! Cannot delete non existent institute!");
    }
  }
//o-------------------------------------------------------------------------------o
  PRIVATE static function ValidateInstituteName($name)
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
    self::$nbInst = NULL;
    self::$vInstIDs = array();
    self::$vInstIDsHash = array();
    self::$vInstIDsListHash = array();
    self::$vInstituteNameHash = array();
    self::$vDeps = array();
  }
}
