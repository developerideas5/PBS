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
require_once(_FULLPATH.'/classes/class_myJAM_Message.php');
require_once(_FULLPATH.'/classes/class_myJAM_MsgDB.php');
require_once(_FULLPATH.'/classes/class_myJAM_Mail.php');
class myJAM_Announcement
{
  private static $db;
  private $nb = NULL;
  private $vIDs = array();
  private $vTitle = array();
  private $vContent = array();
  private $vDate = array();
  private $pAnnouncement = NULL;
  private $nbAnnouncements;
//o-------------------------------------------------------------------------------o
  PUBLIC function __construct($nbShow = NULL, $id = NULL)
//o-------------------------------------------------------------------------------o
  {
    if (!is_object(self::$db))
    {
      self::$db = new myJAM_DB();
    }
    if (isset($nbShow) &&
       (!is_numeric($nbShow) || !is_scalar($nbShow) || (int)$nbShow < 0))
    {
      throw new Exception('Illegal argument');
    }
    $sql = 'SELECT id, title, UNIX_TIMESTAMP(date) AS date, announcement FROM Announcements';
    if($id)
    {
      if(!is_numeric($id) || !is_scalar($id) || (int)$id < 1)
      {
        throw new Exception ('Illegal ID');
      }
      $sql .= " WHERE id='".(int)$id."'";
      $this->pAnnouncement = (int)$id;
    }
    else
    {
      $sql .= ' ORDER BY date DESC';
      if ($nbShow && (int)$nbShow > 0)
      {
        $sql .= ' LIMIT 0,' . (int)$nbShow;
      }
    }
    $vrows = self::$db->query($sql);
    if (count($vrows) > 0)
    {
      if (self::$db->num_rows() > 0)
      {
        $this->nb = 0;
        foreach ( $vrows as $row )
        {
          $id = (int)$row['id'];
          $this->vIDs[$this->nb] = $id;
          $this->vTitle[$id] = $row['title'];
          $this->vContent[$id] = $row['announcement'];
          $this->vDate[$id] = (int)$row['date'];
          $this->nb++;
        }
      }
    }
  }
//o-------------------------------------------------------------------------------o
  PUBLIC function __get($name)
//o-------------------------------------------------------------------------------o
  {
    if ($this->pAnnouncement)
    {
      switch($name)
      {
        case 'ID':
          return $this->pAnnouncement;
        case 'Title':
          return $this->vTitle[$this->pAnnouncement];
        case 'Content':
          return $this->vContent[$this->pAnnouncement];
        case 'Date':
          return $this->vDate[$this->pAnnouncement];
        default:
          die("Attribute '$name' unknown in myJAM_Announcement");
      }
    }
    switch($name)
    {
      case 'nb':
        return $this->nb;
      case 'ID':
        return $this->vIDs;
      case 'Title':
        return $this->vTitle;
      case 'Content':
        return $this->vContent;
      case 'Date':
        return $this->vDate;
      default:
        die("Attribute '$name' unknown in myJAM_Announcement");
    }
    return NULL;
  }
//o-------------------------------------------------------------------------------o
  PUBLIC function __set($name, $val)
//o-------------------------------------------------------------------------------o
  {
    if(!isset($val) || empty($val))
    {
      throw new Exception('Empty value in setter');
    }
    if(is_string($val))
    {
      $val = htmlentities($val);
    }
    if ($this->pAnnouncement)
    {
      switch($name)
      {
        case 'Title':
          $this->vTitle[$this->pAnnouncement] = $val;
          $sql_name = 'title';
          break;
        case 'Content':
          $this->vContent[$this->pAnnouncement] = $val;
          $sql_name = 'announcement';
          break;
        default:
          die("Attribute '$name' unknown in myJAM_Announcement");
      }
      $sql = 'UPDATE Announcements SET '.$sql_name.'=\''.mysql_real_escape_string($val).'\' WHERE id=\''.(int)$this->pAnnouncement.'\'';
      self::$db->query($sql);
    }
  }
//o-------------------------------------------------------------------------------o
  PUBLIC STATIC function getNbTotal()
//o------------------------------------------------------------------------------o
  {
    if (!is_object(self::$db))
    {
      self::$db = new myJAM_DB();
    }
    $sql = 'SELECT count(id) FROM Announcements';
    $res = self::$db->query($sql);
    if(count($res) != 1)
    {
      throw new Exception ('Illegal database reply');
    }
    return (int)$res[0]['count(id)'];
  }
//o-------------------------------------------------------------------------------o
  PUBLIC function delete()
//o-------------------------------------------------------------------------------o
  {
    if($this->pAnnouncement)
    {
      $sql = 'DELETE FROM Announcements WHERE id=\''.(int)$this->pAnnouncement.'\'';
      self::$db->query($sql);
    }
  }
//o-------------------------------------------------------------------------------o
  PUBLIC STATIC function create($Title = NULL, $Content = NULL, $Mail = FALSE)
//o-------------------------------------------------------------------------------o
  {
    if(!isset($Title) || empty($Title))
    {
      throw new Exception('No Title given');
    }
    if(!isset($Content) || empty($Content))
    {
      throw new Exception('No Content given');
    }
    $Message = new myJAM_Message($Title, $Content);
    myJAM_MsgDB::send($Message);
    if($Mail)
    {
      myJAM_Mail::send($Message);
    }
  }
}
