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

require_once (_FULLPATH . '/classes/class_myJAM_DB.php');
//o-------------------------------------------------------------------------------o
function BootStrap_DB ()
//o-------------------------------------------------------------------------------o
{
  $out = '';
  $db = new myJAM_db();
  $bootstrapped = false;
  //Test Configuration
  $sql = 'SELECT * FROM Configuration';
  if ($db->PreCount($sql) != 1)
  {
    $out .= "No valid configuration found. Setting defaults...\n";
    $sql = 'INSERT INTO Configuration' .
     ' (max_results_per_page, max_index_page, SiteAddress) VALUES' .
     ' (100, 7, \'\')';
    $db->DoSQL($sql);
    $bootstrapped = true;
    //Testing Users
    $sql = 'SELECT * FROM Users';
    if ($db->PreCount($sql) < 1)
    {
      $out .= "No Users found. Settings defaults...\n";
      $sql = 'INSERT INTO Users' .
       ' (real_username, email_addr, user_php_pass, admin_flag, firstname, lastname)' .
       ' VALUES' . " ('admin', 'root@localhost', '" . md5('admin') .
       "', '1', '', '')";
      $db->DoSQL($sql);
      $bootstrapped = true;
    }
    //Testing Department
    $sql = 'SELECT * FROM Departments';
    if ($db->PreCount($sql) < 1)
    {
      $out .= "No Departments found. Settings defaults...\n";
      $sql = "INSERT INTO Departments (Name) VALUES ('n/a')";
      $db->DoSQL($sql);
      //if there were no Departments most like there are alos no Institutes
      $out .= "Settings defaults for Institutes...\n";
      $sql = "INSERT INTO Institutes (Name, DepID) VALUES ('n/a', (SELECT DepID FROM Departments WHERE Name='n/a'));";
      $db->DoSQL($sql);
      $bootstrapped = true;
    }
    //Testing Architectures
    $sql = 'SELECT * FROM Architectures';
    if ($db->PreCount($sql) < 1)
    {
      $out .= "No Architectures found. Settings defaults...\n";
      $sql = 'INSERT INTO Architectures'
            .' (ArchName, CPUName, CPUClock)'
            .' VALUES'
            ." ('default','n/a','0.0')";
      $db->DoSQL($sql);
      $bootstrapped = true;
    }
    //Default CostModel
    $sql = 'SELECT * FROM Cost';
    if ($db->PreCount($sql) < 1)
    {
      $out .= "No CostModel found. Settings defaults...\n";
      $sql = 'INSERT INTO Cost'
            .' (cost_su_norm, cost_su_over, cost_descr)'
            .' VALUES'
            ." (0, 0, 'free')";
      $db->DoSQL($sql);
      $bootstrapped = true;
    }
    //BatchCommands
    $sql = 'SELECT * FROM BatchCommands';
    if ($db->PreCount($sql) < 1)
    {
      $out .= "No BatchCommands found. Settings defaults...\n";
      $sql = 'INSERT INTO BatchCommands'
            .' (id,description)'
            .' VALUES'
            ." (1,'nop')";
      $db->DoSQL($sql);
      $bootstrapped = true;
    }
  }
  return $out;
}
//html output with javascript
//o-------------------------------------------------------------------------------o
function BootStrap_DB_html()
//o-------------------------------------------------------------------------------o
{
  $out = BootStrap_DB();
  if(!empty($out))
  {
    $out = nl2br(htmlentities($out));
    $out .= '<script type="text/javascript">'."\n"
             .'window.alert("Database has been bootstraped!");'
             .'window.location.href="index.php?logout=true";'."\n"
           .'</script>'."\n";
  }
  return $out;
}
//o-------------------------------------------------------------------------------o
function ReadList ($filename, &$buffy)
//o-------------------------------------------------------------------------------o
{
  $readline = (int)0;
  $hFile = fopen($filename, 'r');
  if ($hFile)
  {
    $buffy = array();
    while (!feof($hFile))
    {
      $buffy[] = trim(fgets($hFile));
      $readline++;
    }
  }
  fclose($hFile);
  return $readline;
}
//o-------------------------------------------------------------------------------o
function ReadHash ($filename, &$buffy)
//o-------------------------------------------------------------------------------o
{
  $readline = (int)0;
  $hFile = fopen($filename, 'r');
  if ($hFile)
  {
    $buffy = array();
    while (!feof($hFile))
    {
      $buffy[] = explode(' ', trim(fgets($hFile)));
      $readline++;
    }
  }
  fclose($hFile);
  return $readline;
}
//o-------------------------------------------------------------------------------o
//o-------------------------------------------------------------------------------o
//o-------------------------------------------------------------------------------o
