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
class myJAM_User
{
  PROTECTED STATIC $db;
  PROTECTED STATIC $nbUsers;
  PROTECTED STATIC $vUserName;
  PROTECTED STATIC $vUserNameHash;
  PROTECTED STATIC $vUserID;
  PROTECTED STATIC $vUserIDHash;
  PROTECTED STATIC $vUserIDListHash;
  PROTECTED STATIC $vUserFirstName;
  PROTECTED STATIC $vUserFirstNameHash;
  PROTECTED STATIC $vUserLastName;
  PROTECTED STATIC $vUserLastNameHash;
  PROTECTED STATIC $vUserEMail;
  PROTECTED STATIC $vUserEMailHash;
  PROTECTED STATIC $vUserProjects;
  PROTECTED STATIC $vUserProjectObjs;
  PROTECTED $pUserID;
  //o-------------------------------------------------------------------------------
  PUBLIC function __construct($user=NULL)
  //o-------------------------------------------------------------------------------
  {
    if (!is_object(self::$db))
    {
      self::$db = new myJAM_DB();
      self::$nbUsers = 0; /* nb:=number */
      self::$vUserName = array();
      self::$vUserNameHash = array();
      self::$vUserID = array();
      self::$vUserIDHash = array();
      self::$vUserIDListHash = array();
      self::$vUserFirstName = array();
      self::$vUserFirstNameHash = array();
      self::$vUserLastName = array();
      self::$vUserLastNameHash = array();
      self::$vUserEMail = array();
      self::$vUserEMailHash = array();
      self::$vUserProjects = array();
      self::$vUserProjectObjs = array();
      $query = 'SELECT uid, real_username, email_addr, firstname, lastname'
             .' FROM Users'
             .' WHERE real_username NOT LIKE "formerUser%"'
             .' ORDER BY lastname ASC';
      $vUsers = self::$db->query($query);
      self::$nbUsers = self::$db->num_rows();
      if (self::$nbUsers < 1)
      {
        die("myJAM>> ERROR! NO USER FOUND IN DATABASE!!!");
      }
      for($i = 0; $i < self::$nbUsers; $i++)
      {
        $uid = (int)$vUsers[$i]['uid'];
        $UserName = $vUsers[$i]['real_username'];
        self::$vUserID[$i] = $uid;
        self::$vUserIDHash[$UserName] = $uid;
        self::$vUserIDListHash[$uid] = $i;
        self::$vUserName[$i] = $UserName;
        self::$vUserNameHash[$uid] = $UserName;
        self::$vUserFirstName[$i] = $vUsers[$i]['firstname'];
        self::$vUserFirstNameHash[$uid] = $vUsers[$i]['firstname'];
        self::$vUserLastName[$i] = $vUsers[$i]['lastname'];
        self::$vUserLastNameHash[$uid] = $vUsers[$i]['lastname'];
        self::$vUserEMail[$i] = $vUsers[$i]['email_addr'];
        self::$vUserEMailHash[$uid] = $vUsers[$i]['email_addr'];
        self::$vUserProjects[$uid] = self::GetProjectIDs($uid);
      }
    }
    if (isset($user))
    {
      //is $user a $userid?
      if (!is_numeric($user) || !@self::$vUserNameHash[(int)$user])
      {
        //well, it is not... so perhaps it is a username?
        if (!@$this->pUserID = self::$vUserIDHash[$user])
        {
          //well, i give up... just return NULL
          $this->pUserID = 0;
          return NULL;
        }
      }
      else
      {
        //it IS a user ID.
        $this->pUserID = (int)$user;
      }
    }
    else
    {
      //$user not given. so i assume this is meant to be a LIST object
      $this->pUserID = 0;
    }
  }
  //o-------------------------------------------------------------------------------
  PUBLIC function __get($name)
  //o-------------------------------------------------------------------------------
  {
    if ($this->pUserID > 0)
    {
      switch($name)
      {
        case 'UserName':
          return self::$vUserNameHash[$this->pUserID];
        case 'ID':
          return $this->pUserID;
        case 'FirstName':
          return self::$vUserFirstNameHash[$this->pUserID];
        case 'LastName':
          return self::$vUserLastNameHash[$this->pUserID];
        case 'FullName':
          return self::$vUserFirstNameHash[$this->pUserID] . ' ' . self::$vUserLastNameHash[$this->pUserID];
        case 'eMail':
          return self::$vUserEMailHash[$this->pUserID];
        case 'ADMIN':
          return $this->GetAdminFlag();
        case 'Projects':
          return self::GetProjects($this->pUserID);
        case 'Paths':
          return self::GetPaths();
        default:
          return NULL;
      }
    }
    switch($name)
    {
      case 'nb':
        return self::$nbUsers;
      case 'UserName':
        return self::$vUserNameHash;
      case 'UserNameList':
        return self::$vUserName;
      case 'ID':
        return self::$vUserIDHash;
      case 'IDList':
        return self::$vUserID;
      case 'FirstName':
        return self::$vUserFirstNameHash;
      case 'FirstNameList':
        return self::$vUserFirstName;
      case 'LastName':
        return self::$vUserLastNameHash;
      case 'LastNameList':
        return self::$vUserLastName;
      case 'FullName':
        $vFullName = array();
        for($i = 0; $i < self::$nbUsers; $i++)
        {
          $pid = self::$vUserID[$i];
          $vFullName[$pid] = self::$vUserFirstName[$i] . ' ' . self::$vUserLastName[$i];
        }
        return $vFullName;
      case 'FullNameList':
        $vFullNameList = array();
        for($i = 0; $i < self::$nbUsers; $i++)
        {
          $vFullNameList[$i] = self::$vUserFirstName[$i] . ' ' . self::$vUserLastName[$i];
        }
        return $vFullNameList;
      case 'eMail':
        return self::$vUserEMailHash;
      case 'eMailList':
        return self::$vUserEMail;
      case 'Projects':
        return self::GetProjects();
      default:
        return NULL;
    }
  }
  //o-------------------------------------------------------------------------------
  PUBLIC function __set($name, $val)
  //o-------------------------------------------------------------------------------
  {
    if (!isset($val))
    {
      die("myJAM>> FATAL ERROR 0x0d9c3 in class myJAM_User!");
    }
    if ($this->pUserID > 0)
    {
      switch($name)
      {
        case 'UserName':
          unset(self::$vUserIDHash[self::$vUserNameHash[$this->pUserID]]);
          self::$vUserNameHash[$this->pUserID] = $val;
          self::$vUserName[self::$vUserIDListHash[$this->pUserID]] = $val;
          self::$vUserIDHash[$val] = $this->pUserID;
          $sql_name = 'real_username';
          break;
        case 'FirstName':
          self::$vUserFirstNameHash[$this->pUserID] = $val;
          self::$vUserFirstName[self::$vUserIDListHash[$this->pUserID]] = $val;
          $sql_name = 'firstname';
          break;
        case 'LastName':
          self::$vUserLastNameHash[$this->pUserID] = $val;
          self::$vUserLastName[self::$vUserIDListHash[$this->pUserID]] = $val;
          $sql_name = 'lastname';
          break;
        case 'eMail':
          self::$vUserEMailHash[$this->pUserID] = $val;
          self::$vUserEMail[self::$vUserIDListHash[$this->pUserID]] = $val;
          $sql_name = 'email_addr';
          break;
        case 'PWD':
          $val = md5($val);
          $sql_name = 'user_php_pass';
          break;
        case 'MD5PWD':
          if (!is_string($val) || strlen($val) != 32 || !preg_match('/^[0-9a-fA-F]+$/', $val))
          {
            die('myJAM>> FATAL ERROR 0x21ed in class myJAM_User!');
          }
          $sql_name = 'user_php_pass';
          break;
        case 'ADMIN':
          if ($val != 1)
          {
            $val = 0;
          }
          $sql_name = 'admin_flag';
          break;
        default:
          return NULL;
      }
      $sql = 'UPDATE Users SET '
             .$sql_name.'=\''.mysql_real_escape_string($val).'\' WHERE uid=\''.(int)$this->pUserID.'\'';
      self::$db->query($sql);
      return $val;
    }
    return NULL;
  }
  //o-------------------------------------------------------------------------------
  PUBLIC function CheckPWD($pwd)
  //o-------------------------------------------------------------------------------
  {
    if ($this->pUserID > 0)
    {
      $pwd = md5($pwd);
      $sql = "SELECT user_php_pass FROM Users WHERE uid=" . (int)$this->pUserID;
      $result = self::$db->query($sql);
      if ($result[0]["user_php_pass"] == $pwd)
      {
        return TRUE;
      }
    }
    return FALSE;
  }
  //o-------------------------------------------------------------------------------
  PUBLIC function CheckPWDMD5($pwd)
  //o-------------------------------------------------------------------------------
  {
    if ($this->pUserID > 0)
    {
      $sql = 'SELECT user_php_pass FROM Users WHERE uid=' . (int)$this->pUserID;
      $result = self::$db->query($sql);
      if ($result[0]["user_php_pass"] == $pwd)
      {
        return TRUE;
      }
    }
    return FALSE;
  }
  //o-------------------------------------------------------------------------------
  PUBLIC function CheckPWDCrypt($pwd)
  //o-------------------------------------------------------------------------------
  {
    if ($this->pUserID > 0)
    {
      $sql = 'SELECT user_php_pass FROM Users WHERE uid=' . (int)$this->pUserID;
      $result = self::$db->query($sql);
      $passcrypt = md5(self::$vUserNameHash[$this->pUserID] . '::' . $result[0]['user_php_pass']);
      if ($passcrypt == $pwd)
      {
        return TRUE;
      }
    }
    return FALSE;
  }
  //o-------------------------------------------------------------------------------
  PUBLIC static function Create($UserName,
                                $FirstName,
                                $LastName,
                                $eMail,
                                $pwd,
                                $admin)
  //o-------------------------------------------------------------------------------
  {
      $pwd = md5($pwd);
      if (!isset($UserName) || empty($UserName) || !is_string($UserName))
      {
        die("myJAM>> FATAL ERROR 0x00545 in class myJAM_User!");
      }
      if (isset($eMail) && !empty($eMail) && is_string($eMail))
      {
        if (!preg_match('/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/', $eMail))
        {
          die("myJAM>> FATAL ERROR 0x02512 in class myJAM_User!");
        }
      }
      if (!isset($pwd) || empty($pwd) || !is_string($pwd))
      {
        die("myJAM>> FATAL ERROR 0x0151a in class myJAM_User!");
      }
      if (!isset($admin) || ($admin != 1 && $admin != 0))
      {
        die("myJAM>> FATAL ERROR 0x0303d in class myJAM_User!");
      }
      $db = new myJAM_db();
      $sql = "INSERT INTO Users (real_username, firstname, lastname, email_addr, user_php_pass, admin_flag)"
            ." VALUES('"
            . mysql_real_escape_string($UserName) . "', '"
            . mysql_real_escape_string($FirstName) . "', '"
            . mysql_real_escape_string($LastName) . "', '"
            . mysql_real_escape_string($eMail) . "', '"
            . mysql_real_escape_string($pwd) . "', '"
            . (int)$admin . "')";
      $db->query($sql);
      $uid = $db->last_insert_id();
      if ($uid < 1)
      {
        die("myJAM>> FATAL ERROR 0x0921a in class myJAM_User!");
      }
      self::DestroyCache();
      return new myJAM_User($uid);
  }
  //o-------------------------------------------------------------------------------
  PUBLIC function SHADOW()
  //o-------------------------------------------------------------------------------
  {
    if ($this->pUserID > 0)
    {
      $old_username = self::$vUserNameHash[$this->pUserID];
      $new_username = 'formerUser' . $this->pUserID;
      $sql = "UPDATE Jobs SET application=REPLACE(application, '/$old_username/', '/$new_username/')"
            ." WHERE application LIKE '%/$old_username/%'";
      self::$db->DoSQL($sql);
      $sql = "UPDATE Users SET real_username='".$new_username."',
                           email_addr='',
                           user_php_pass='',
                           admin_flag=0,
                           firstname='',
                           lastname='',
                           last_logon=NULL";
      $sql .= "WHERE uid=".(int)$this->pUserID;
      self::$db->DoSQL($sql);
      self::$nbUsers--;
      $this->pUserID = NULL;
      self::DestroyCache();
      return self::$nbUsers;
    }
    return NULL;
  }
  //o-------------------------------------------------------------------------------
  PUBLIC function DELETE()
  //o-------------------------------------------------------------------------------
  {
    if ($this->pUserID > 0)
    {
      unset(self::$vUserName[self::$vUserIDListHash[$this->pUserID]]);
      unset(self::$vUserID[self::$vUserIDListHash[$this->pUserID]]);
      unset(self::$vUserFirstName[self::$vUserIDListHash[$this->pUserID]]);
      unset(self::$vUserLastName[self::$vUserIDListHash[$this->pUserID]]);
      unset(self::$vUserEMail[self::$vUserIDListHash[$this->pUserID]]);
      unset(self::$vUserIDHash[self::$vUserNameHash[$this->pUserID]]);
      unset(self::$vUserIDListHash[$this->pUserID]);
      unset(self::$vUserNameHash[$this->pUserID]);
      unset(self::$vUserFirstNameHash[$this->pUserID]);
      unset(self::$vUserLastNameHash[$this->pUserID]);
      unset(self::$vUserEMailHash[$this->pUserID]);
      $sql = 'DELETE FROM Users WHERE uid=\''.(int)$this->pUserID.'\'';
      self::$db->query($sql);
      self::$nbUsers--;
      $this->pUserID = NULL;
      return self::$nbUsers;
    }
    return NULL;
  }
  //o-------------------------------------------------------------------------------
  PROTECTED function GetProjectIDs($uid)
  //o-------------------------------------------------------------------------------
  {
    $sql = "SELECT * FROM Meta_ProjectsUsers WHERE uid='" . (int)$uid . "'";
    $res = self::$db->query($sql);
    $vProj = array();
    if (self::$db->num_rows() > 0)
    {
      foreach($res as $row)
      {
        $vProj[] = $row["pid"];
      }
    }
    return $vProj;
  }
  //o-------------------------------------------------------------------------------
  PROTECTED function GetProjects($user=NULL)
  //o-------------------------------------------------------------------------------
  {
    if (count(self::$vUserProjectObjs) != count(self::$vUserProjects))
    {
      foreach(self::$vUserID as $uid)
      {
        $ProjObjs = array();
        foreach(self::$vUserProjects[$uid] as $pid)
        {
          $ProjObjs[] = new myJAM_Project($pid);
        }
        self::$vUserProjectObjs[$uid] = $ProjObjs;
        unset($ProjObjs);
      }
    }
    if ($user)
    {
      return self::$vUserProjectObjs[$user];
    }
    else
    {
      return self::$vUserProjectObjs;
    }
  }
  //o-------------------------------------------------------------------------------
  PROTECTED function GetPaths()
  //o-------------------------------------------------------------------------------
  {
    if ($this->pUserID > 0)
    {
      $sql = "SELECT Application FROM Jobs WHERE Application LIKE '%/$this->UserName/%' GROUP BY Application";
      $res = self::$db->query($sql);
      $paths = array();
      foreach ($res as $key => $value)
      {
        foreach ($value as $path)
        {
          $paths[] = $path;
        }
      }
      return $paths;
    }
    else
    {
      die('myJAM>> Not possible for list-Objects'); //TODO: Error-Code
    }
  }
  //o-------------------------------------------------------------------------------o
  PUBLIC function IsProjectOwner()
  //o-------------------------------------------------------------------------------o
  {
    if ($this->pUserID > 0)
    {
      $ProjectList = new myJAM_Project();
      foreach($ProjectList->ID as $pid)
      {
        if($ProjectList->Owner[$pid]->ID == $this->pUserID)
        {
          return true;
        }
      }
      return false;
    }
    else
    {
      die('myJAM>> Not possible for list-Objects'); //TODO: Error-Code
    }
  }
  //o-------------------------------------------------------------------------------o
  PUBLIC function HasJobs()
  //o-------------------------------------------------------------------------------o
  {
  if ($this->pUserID > 0)
    {
      $sql = "SELECT uid FROM Jobs WHERE uid = $this->pUserID LIMIT 1";
      $res = self::$db->query($sql);
      if (count($res) == 1) {
        return true;
      }
      elseif (count($res) == 0)
      {
        return false;
      }
      else
      {
        die("myJAM>> DB-QUERY ERROR"); //TODO: Error-Code
      }
    }
  }
  //o-------------------------------------------------------------------------------
  PROTECTED function GetAdminFlag()
  //o-------------------------------------------------------------------------------
  {
    if ($this->pUserID > 0)
    {
      $sql = 'SELECT admin_flag FROM Users WHERE uid=\'' . (int)$this->pUserID.'\'';
      $res = self::$db->query($sql);
      if ($res[0]["admin_flag"] == "1")
      {
        return TRUE;
      }
    }
    return FALSE;
  }
  //o-------------------------------------------------------------------------------
  public static function DestroyCache()
  //o-------------------------------------------------------------------------------
  {
    self::$db = NULL;
    self::$nbUsers = NULL;
    self::$vUserName = array();
    self::$vUserNameHash = array();
    self::$vUserID = array();
    self::$vUserIDHash = array();
    self::$vUserIDListHash = array();
    self::$vUserFirstName = array();
    self::$vUserFirstNameHash = array();
    self::$vUserLastName = array();
    self::$vUserLastNameHash = array();
    self::$vUserEMail = array();
    self::$vUserEMailHash = array();
    self::$vUserProjects = array();
    self::$vUserProjectObjs = array();
  }
}
//o-------------------------------------------------------------------------------
//o-------------------------------------------------------------------------------
//o-------------------------------------------------------------------------------
