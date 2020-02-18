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

define('_FULLPATH', realpath(dirname(__FILE__)));
require_once(_FULLPATH."/classes/class_myJAM_DB.php");
require_once(_FULLPATH."/classes/class_myJAM_Architecture.php");
echo "<html>"
."<head></head>"
."<link rel=\"stylesheet\" type=\"text/css\" href=\"css/style.css\" />"
."<body>"
;
$db = new myJAM_db();
$ArchList = new myJAM_Architecture();
echo "<ul style=\"margin-left:0px;\">";
for ($i = 0; $i < $ArchList->nb; $i++)
{
  $ArchID = $ArchList->IDList[$i];
  echo "<li style=\"padding:4px;\"><img style=\"margin-right:3px;\" src=\"images/Aqua-Ball-8x8.png\" alt=\"AquaBall\" /> ";
  echo $ArchList->nbCores[$ArchID]." Core";
  if ($ArchList->nbCores[$ArchID] != 1){
    echo "s";
  }
  echo " ".$ArchList->Name[$ArchID]."</li>";
}
echo "</ul>"
."</body>"
."<html>"
;
