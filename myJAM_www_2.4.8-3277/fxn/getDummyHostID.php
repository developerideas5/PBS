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

require_once (_FULLPATH . '/access.php');
require_once (_FULLPATH . '/classes/class_myJAM_DB.php');
//o-------------------------------------------------------------------------------o//
function getDummyHostID ()
//o-------------------------------------------------------------------------------o//
{
  $db = new myJAM_db();
  //test if we already have a dummy host:
  $sql = 'SELECT hid from Hosts WHERE hostname="<deleted host>"';
  $res = $db->query($sql);
  if (count($res) == 0)
  {
    $sql = 'INSERT INTO Hosts (hostname, Description, IP_LogIn, BatchSystem)'
         . ' VALUES'
         . ' ("<deleted host>", "this host does not exists anymore", 0, "")';
    $db->DoSQL($sql);
    $hid = $db->last_insert_id();
  }
  elseif (count($res) == 1)
  {
    $hid = $res[0]['hid'];
  }
  else
  {
    die('myJAM>> FATAL DATABSE ERROR!');
  }
  return (int)$hid;
}
