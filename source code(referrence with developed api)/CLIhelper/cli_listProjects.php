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

require_once(_FULLPATH.'/classes/class_myJAM_Project.php');
//o-------------------------------------------------------------------------------o
function cli_listProjects()
//o-------------------------------------------------------------------------------o
{
  $ProjectList = new myJAM_Project();
  $maxlen_projname = strlen('project name');
  $maxlen_Owner = strlen('owner (username)');
  $maxlen_FullOwner = strlen('owner (fullname)');
  $maxlen_users = strlen('# of users');
  foreach($ProjectList->IDList as $pid)
  {
    $maxlen_projname = max($maxlen_projname, strlen($ProjectList->Name[$pid]));
    $maxlen_Owner = max($maxlen_Owner, strlen($ProjectList->Owner[$pid]->UserName));
    $maxlen_FullOwner = max($maxlen_FullOwner , strlen($ProjectList->Owner[$pid]->FullName));
  }
  $format = '%-'.$maxlen_projname.'s'
           .' | '
           .'%-'.$maxlen_Owner.'s'
           .' | '
           .'%-'.$maxlen_FullOwner.'s'
           .' | '
           .'%-'.$maxlen_users.'s'
           ."\n";
  $len_seperator = $maxlen_projname + $maxlen_Owner + $maxlen_FullOwner + $maxlen_users + 9;
  echo str_repeat('-', ($len_seperator))."\n";
  printf($format, 'project name', 'owner (username)', 'owner (fullname)', '# of users');
  echo str_repeat('-', ($len_seperator))."\n";
  foreach($ProjectList->IDList as $pid)
  {
    printf($format, $ProjectList->Name[$pid],
                    $ProjectList->Owner[$pid]->UserName,
                    $ProjectList->Owner[$pid]->FullName,
                    count($ProjectList->Users[$pid])
          );
  }
  echo str_repeat('-', ($len_seperator))."\n";
}
