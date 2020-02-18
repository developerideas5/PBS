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

require_once(_FULLPATH.'/classes/class_myJAM_User.php');
//o-------------------------------------------------------------------------------o
function cli_listUsers()
//o-------------------------------------------------------------------------------o
{
  $UserList = new myJAM_User();
  $maxlen_username = strlen('username');
  $maxlen_fullname = strlen('fullname');
  $maxlen_projects = strlen('# of projects');
  //get the maximum string length of the properties we want to print
  foreach($UserList->IDList as $uid)
  {
    $maxlen_username = max($maxlen_username, strlen($UserList->UserName[$uid]));
    $maxlen_fullname = max($maxlen_fullname, strlen($UserList->FullName[$uid]));
  }
  $format = '%-'.$maxlen_username.'s'
           .' | '
           .'%-'.$maxlen_fullname.'s'
           .' | '
           .'%-'.$maxlen_projects.'s'
           ."\n";
  $len_seperator = $maxlen_username + $maxlen_fullname + $maxlen_projects + 6;
  echo str_repeat('-', ($len_seperator))."\n";
  printf($format, 'username', 'fullname', '# of projects');
  echo str_repeat('-', ($len_seperator))."\n";
  foreach($UserList->IDList as $uid)
  {
    printf($format, $UserList->UserName[$uid],
                    $UserList->FullName[$uid],
                    count($UserList->Projects[$uid])
          );
  }
  echo str_repeat('-', ($len_seperator))."\n";
}
