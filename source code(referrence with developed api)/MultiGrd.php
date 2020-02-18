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
require_once(_FULLPATH."/ColorGrd.php");
function MultiGrd($clr1, $clr2, $clr3, $clr4, $width, $height, $frequency)
{
  for ($i = 0; $i < 3; $i++)
  {
    $color1[$i] = hexdec(substr($clr1, $i*2+1, 2));
    $color2[$i] = hexdec(substr($clr2, $i*2+1, 2));
    $color3[$i] = hexdec(substr($clr3, $i*2+1, 2));
    $color4[$i] = hexdec(substr($clr4, $i*2+1, 2));
    $Grd1[$i] = ($color3[$i] - $color1[$i]) / ($width-1);
    $Grd2[$i] = ($color4[$i] - $color2[$i]) / ($width-1);
  }
  $posX = 0;
  for ($step = 0; $step < $width; $step++)
  {
    for($i = 0; $i < 3; $i++)
    {
      $ColUpper[$i] = $color1[$i] + $step * $Grd1[$i];
      $ColLower[$i] = $color2[$i] + $step * $Grd2[$i];
    }
    $Upper = sprintf("#%02x%02x%02x", $ColUpper[0], $ColUpper[1], $ColUpper[2]);
    $Lower = sprintf("#%02x%02x%02x", $ColLower[0], $ColLower[1], $ColLower[2]);
    ColorGrd($Upper, $Lower, "1px", $height, $frequency, $posX);
    $posX++;
  }
}
