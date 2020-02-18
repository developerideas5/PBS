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

function ColorGrd($clr1, $clr2, $width, $height, $frequency, $posX, $spaces=NULL)
{
  for ($i = 0; $i < 3; $i++)
  {
    $color1[$i] = hexdec(substr($clr1, $i*2+1, 2));
    $color2[$i] = hexdec(substr($clr2, $i*2+1, 2));
    $Grd[$i] = ($color2[$i] - $color1[$i]) / ($height-1);
  }
  $posY = -$height*$posX;
  for ($vib = 0; $vib < $frequency; $vib++)
  {
    for ($step = 0; $step < $height; $step+=$frequency)
    {
      if ($posX < 0)
      {
  // Adds spaces for intendenting the (X)HTML code
//		for($j = 0; $j<$spaces; $j++) {
//			echo " ";
//		}
  echo "<div style=\"width:".$width."; background-color:rgb(";
      }
      else
      {
        echo "<div style=\"position:absolute;top:".$posY.";left:".$posX.";width:".$width."; background-color:rgb(";
      }
      for ($i = 0; $i < 3; $i++)
      {
        $col = $color1[$i] + $step * $Grd[$i];
        echo (int)($col);
        if ($i != 2) {echo ",";}
      }
      echo "); height:1px;\"></div>";
//      $posY++;
    }
    for ($i = 0; $i < 3; $i++){$Grd[$i] = -$Grd[$i];};
    $tmp = $color1;
    $color1 = $color2;
    $color2 = $tmp;
  }
}
