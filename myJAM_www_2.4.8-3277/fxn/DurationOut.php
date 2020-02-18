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

require_once(_FULLPATH."/access.php");
//o-------------------------------------------------------------------------------o
function DurationOut($seconds)
//o-------------------------------------------------------------------------------o
{
  $out='';
  if ($seconds >= 86400)
  {
    $days = (int)($seconds / 86400);
    $seconds -= $days * 86400;
    $out .= $days . " day".(($out===1)?" ":"s ");
  }
  if ($seconds >= 3600)
  {
    $hours = (int)($seconds / 3600);
    $seconds -= $hours * 3600;
    $out .= $hours. " hour".(($out===1)?" ":"s ");
  }
  if ($seconds >= 60)
  {
    $minutes = (int)($seconds / 60);
    $seconds -= $minutes * 60;
    $out .= $minutes . " min".(($out===1)?" ":"s ");
  }
  $out .= $seconds;
  return $out." sec".(($out===1)?"":"s");
 ;
}
