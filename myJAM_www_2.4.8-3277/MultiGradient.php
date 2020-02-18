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

header("Content-Type: image/png");
$width=max(0,(min(2048,$_GET["w"])));
$height=max(0,(min(2048,$_GET["h"])));
$frequency=max(0,(min(10,$_GET["f"])));
$hImage = imagecreatetruecolor($width, $height);
for ($i = 0; $i < 3; $i++)
{
  $color1[$i] = hexdec(substr($_GET["c1"], $i*2+1, 2));
  $color2[$i] = hexdec(substr($_GET["c2"], $i*2+1, 2));
  $color3[$i] = hexdec(substr($_GET["c3"], $i*2+1, 2));
  $color4[$i] = hexdec(substr($_GET["c4"], $i*2+1, 2));
  $XGrd1[$i] = ($color3[$i] - $color1[$i]) / ($width-1);
  $XGrd2[$i] = ($color4[$i] - $color2[$i]) / ($width-1);
}
$posX = 0;
for ($XStep = 0; $XStep < $width; $XStep++)
{
  for($i = 0; $i < 3; $i++)
  {
    $ColUpper[$i] = $color1[$i] + $XStep * $XGrd1[$i];
    $ColLower[$i] = $color2[$i] + $XStep * $XGrd2[$i];
    $YGrd[$i] = ($ColLower[$i] - $ColUpper[$i]) / ($height-1);
  }
  $posY = 0;
  for ($vib = 0; $vib < $frequency; $vib++)
  {
    for ($YStep = 0; $YStep < $height; $YStep+=$frequency)
    {
      for ($i = 0; $i < 3; $i++)
      {
        $CurCol[$i] = (int)($ColUpper[$i] + $YStep * $YGrd[$i]);
      }
      $color = imagecolorallocate($hImage, $CurCol[0], $CurCol[1], $CurCol[2]);
      imagesetpixel($hImage, $posX, $posY, $color);
      $posY++;
    } //YStep
    for ($i = 0; $i < 3; $i++){$YGrd[$i] = -$YGrd[$i];};
    $tmp = $ColUpper;
    $ColUpper = $ColLower;
    $ColLower = $tmp;
  } //vib
  $posX++;
} //XStep
imagepng($hImage);
imagedestroy($hImage);
