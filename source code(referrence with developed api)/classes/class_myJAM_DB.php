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

//o-------------------------------------------------------------------------------o
class myJAM_db
//o-------------------------------------------------------------------------------o
{
  PROTECTED $affected_rows;
  PROTECTED $num_rows;
  PROTECTED $last_result;
  protected static $db_server = NULL;
  protected static $db_name = NULL;
  PROTECTED STATIC $db_handle;
  //o-------------------------------------------------------------------------------o
  PUBLIC function __construct()
  //o-------------------------------------------------------------------------------o
  {
    if (!is_resource(self::$db_handle))
    {
      $_CFG_DATABASE_SERVER = NULL;
      $_CFG_DATABASE_USER = NULL;
      $_CFG_DATABASE_PASSWORD = NULL;
      $_CFG_DATABASE_NAME = NULL;
      require(_FULLPATH . '/config/CFG_database.php');
      //open a new connection
      self::$db_handle = mysql_connect($_CFG_DATABASE_SERVER,
                                       $_CFG_DATABASE_USER,
                                       $_CFG_DATABASE_PASSWORD)
        or die('myJAM>> ERROR CONNECTING DATABASE SERVER: ' . mysql_error());
      mysql_select_db($_CFG_DATABASE_NAME, self::$db_handle)
        or die('myJAM>> ERROR SELECTING DATABASE: ' . mysql_error());
      self::$db_server = $_CFG_DATABASE_SERVER;
      self::$db_name = $_CFG_DATABASE_NAME;
    }
  }
  //o-------------------------------------------------------------------------------o
  public function __get($name)
  //o-------------------------------------------------------------------------------o
  {
    switch($name)
    {
      case 'server':
        return self::$db_server;
      case 'dbName':
        return self::$db_name;
      case 'connection':
        return mysql_get_host_info();
      case 'protocol':
        return mysql_get_proto_info();
      case 'server_info':
        return mysql_get_server_info();
      case 'client_encoding':
        return mysql_client_encoding();
      case 'client_info':
        return mysql_get_client_info();
      case 'size':
        return $this->getDBSize();
      default:
        die('ILLEGAL ACCESS');
    }
  }
  //o-------------------------------------------------------------------------------o
  PUBLIC function query($sql)
  //o-------------------------------------------------------------------------------o
  {
    $result = mysql_query($sql, self::$db_handle);
    $this->affected_rows = mysql_affected_rows(self::$db_handle);
    $this->num_rows = @mysql_num_rows($result);
    if (!$result)
    {
      die('SQL-ERROR: ' . mysql_error());
    }
    if ($this->num_rows)
    {
      $pRes = 0;
      while(false !== ($array[$pRes] = mysql_fetch_assoc($result)))
      {
        $pRes++;
      }
      mysql_free_result($result);
      unset($array[$pRes]);
      return $array;
    }
    return array();
  }
  //o-------------------------------------------------------------------------------o
  PUBLIC function PreCount($sql)
  //o-------------------------------------------------------------------------------o
  {
    $this->last_result = mysql_query($sql, self::$db_handle);
    if (!$this->last_result)
    {
      echo "SQL: " . $sql . "<br>";
      die("SQL-ERROR: " . mysql_error());
    }
    return @mysql_num_rows($this->last_result);
  }
  //o-------------------------------------------------------------------------------o
  PUBLIC function DropLastResults()
  //o-------------------------------------------------------------------------------o
  {
    mysql_free_result($this->last_result);
  }
  //o-------------------------------------------------------------------------------o
  PUBLIC function ReGetResults()
  //o-------------------------------------------------------------------------------o
  {
    $this->affected_rows = mysql_affected_rows(self::$db_handle);
    $this->num_rows = @mysql_num_rows($this->last_result);
    if ($this->num_rows)
    {
      $pRes = 0;
      while(false !== ($array[$pRes] = mysql_fetch_assoc($this->last_result)))
      {
        $pRes++;
      }
      mysql_free_result($this->last_result);
      unset($array[$pRes]);
      return $array;
    }
    return array();
  }
  //o-------------------------------------------------------------------------------o
  PUBLIC function DoSQL($sql)
  //o-------------------------------------------------------------------------------o
  {
    mysql_query($sql, self::$db_handle);
    $this->affected_rows = mysql_affected_rows(self::$db_handle);
    $error = mysql_error();
    if(!empty($error))
    {
      trigger_error('myJAM>> FATAL SQL ERROR: '.$error, E_USER_ERROR);
      die('SQL-Error '.$error);
    }
  }
  //o-------------------------------------------------------------------------------o
  PUBLIC function affected_rows()
  //o-------------------------------------------------------------------------------o
  {
    return $this->affected_rows;
  }
  //o-------------------------------------------------------------------------------o
  PUBLIC function num_rows()
  //o-------------------------------------------------------------------------------o
  {
    return $this->num_rows;
  }
  //o-------------------------------------------------------------------------------o
  PUBLIC function last_insert_id()
  //o-------------------------------------------------------------------------------o
  {
    $sql = 'SELECT LAST_INSERT_ID()';
    $res = $this->query($sql);
    return (int)$res[0]['LAST_INSERT_ID()'];
  }
  //o-------------------------------------------------------------------------------o
  protected function getDBSize()
  //o-------------------------------------------------------------------------------o
  {
    $sql = 'SELECT sum( data_length + index_length ) as size' //size in Bytes
          .' FROM information_schema.TABLES'
          .' WHERE table_schema=\''.self::$db_name.'\'';
    $res = $this->query($sql);
    if(count($res) == 1)
    {
      if(isset($res[0]['size']))
      {
        return (int)$res[0]['size'];
      }
    }
    return 'n/a';
  }
//o-------------------------------------------------------------------------------o
//o-------------------------------------------------------------------------------o
//o-------------------------------------------------------------------------------o
}
