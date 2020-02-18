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
require_once(_FULLPATH."/access.php");
require_once(_FULLPATH."/classes/class_myJAM_User.php");
header('Content-Type: text/html; charset=ISO-8859-1');
if(!isset($_REQUEST["uid"])){
  die("GetUserInfo.php - REQUEST uid is not set");
}
if(!is_numeric($_REQUEST["uid"])){
    die("GetUserInfo.php - REQUEST uid is not numeric");
}
$user = new myJAM_User((int)$_REQUEST["uid"]);
if (!is_scalar($user->ID)){
  die("GetUserInfo.php - UserID from User Object is no Scalar");
}
echo "<div style=\"padding:15px; background-color:#dddddd\">"
."<div>"
."<b>User Info:</b><br/>"
.$user->FullName."<br/>"
.$user->eMail."<br/>"
."</div>"
."</div>"
;
