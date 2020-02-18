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

require_once(_FULLPATH."/classes/class_myJAM_DB.php");
class myJAM_Field
{
  PROTECTED STATIC $db;
  PROTECTED STATIC $vFieldID;
  PROTECTED STATIC $vFieldDepHash;
  PROTECTED STATIC $vFieldNameHash;
  PROTECTED STATIC $vFieldIDHash;
  PROTECTED $pFieldID;
  //o-------------------------------------------------------------------------------o
  PUBLIC function __construct($field=NULL)
  //o-------------------------------------------------------------------------------o
  {
    if (!is_object(self::$db))
    {
      self::$db = new myJAM_DB();
      self::$vFieldID = array();
      self::$vFieldDepHash = array();
      self::$vFieldNameHash = array();
      self::$vFieldIDHash = array();
      $query = "SELECT * FROM Appl_Fields ORDER BY Department, Field";
      $vFields = self::$db->query($query);
      foreach($vFields as $FieldItem)
      {
        self::$vFieldID[] = $FieldItem["fid"];
        self::$vFieldDepHash[$FieldItem["fid"]] = $FieldItem["Department"];
        self::$vFieldNameHash[$FieldItem["fid"]] = $FieldItem["Field"];
        self::$vFieldIDHash[$FieldItem["Field"]] = (int)$FieldItem["fid"];
      }
    }
    if ($field)
    {
      if (!@self::$vFieldNameHash[(int)$field])
      {
        if (!@$this->pFieldID = self::$vFieldIDHash[$field])
        {
          return NULL;
        }
      }
      else
      {
        $this->pFieldID = (int)$field;
      }
    }
    else
    {
      $this->pFieldID = 0;
    }
  }
  //o-------------------------------------------------------------------------------o
  PUBLIC function __get($name)
  //o-------------------------------------------------------------------------------o
  {
    if ($this->pFieldID)
    {
      switch($name)
      {
        case "ID":
          return $this->pFieldID;
        case "Department":
          return self::$vFieldDepHash[$this->pFieldID];
        case "Field":
          return self::$vFieldNameHash[$this->pFieldID];
        default:
          return NULL;
      }
    }
    switch($name)
    {
      case "IDList":
        return self::$vFieldID;
      case "Department":
        return self::$vFieldDepHash;
      case "Field":
        return self::$vFieldNameHash;
      default:
        return NULL;
    }
    return NULL;
  }
  //o-------------------------------------------------------------------------------o
  public function searchID($FieldName)
  //o-------------------------------------------------------------------------------o
  {
    $fid = array_search($FieldName, self::$vFieldNameHash);
    if(is_numeric($fid) && (int)$fid > 0)
    {
      return (int)$fid;
    }
    return NULL;
  }
  //o-------------------------------------------------------------------------------o
  public static function DestroyCache()
//o-------------------------------------------------------------------------------o
  {
    self::$db = NULL;
    self::$vFieldID = array();
    self::$vFieldDepHash = array();
    self::$vFieldNameHash = array();
  }
}
