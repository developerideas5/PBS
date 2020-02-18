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

define('_FULLPATH', realpath(dirname(__FILE__).'/../'));
require_once (_FULLPATH . '/classes/class_myJAM_DB.php');
require_once (_FULLPATH . '/classes/class_myJAM_User.php');
require_once (_FULLPATH . '/classes/class_myJAM_Project.php');
require_once (_FULLPATH . '/helper/myJAM_JSON/myJAM_JSON.php');
$ActiveUser = NULL;
require_once (_FULLPATH . '/access.php');
if(!$ActiveUser->ADMIN)
{
  die('myJAM>> FATAL ERROR 0x0eb9a in user-settings: Only admin-users can delete users!'); //error code ändern?
}
$request = new myJAM_JSON($_POST['q']);
$User = NULL;
if((int)$request->id > 0)
{
  $User = new myJAM_User((int)$request->id);
  if(!is_a($User, 'myJAM_User') || !is_scalar($User->ID) || !(int)$User->ID > 0)
  {
    die('myJAM>> FATAL ERROR 0x0c7fe in user-settings: Illegal user ID!'); //error code ändern?
  }
}
else
{
  die('myJAM>> FATAL ERROR 0xblatest!'); //error code ändern?
}
if($request->setadmin == 'true')
{
  $admin = new myJAM_User('admin');
  if(!is_a($admin, 'myJAM_User') || !is_scalar($admin->ID) || !(int)$admin->ID > 0)
  {
    die('myJAM>> FATAL ERROR 0x69d8 in user-settings: Possible database inconsistency!'); //error code ändern?
  }
  $ProjectList = new myJAM_Project();
  foreach($ProjectList->ID as $pid)
  {
    if($ProjectList->Owner[$pid]->ID == $User->ID)
    {
      $project = new myJAM_Project($pid);
      $project->Owner = $admin;
    }
  }
}
$User->SHADOW();
echo 'Everything done!';
