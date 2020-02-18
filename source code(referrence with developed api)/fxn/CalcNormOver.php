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

//o-------------------------------------------------------------------------------o
function CalcNormOver($oldSUs, $payment, $cm_norm, $cm_over)
//o-------------------------------------------------------------------------------o
{
  $result = array();
  if($oldSUs < 0)
  {
    $overrun_costs = -$oldSUs * $cm_over;
    if($overrun_costs <= $payment)
    {
      $result["overrun_sus"] = $oldSUs;
      $result["overrun_costs"] = $overrun_costs;
      $oldSUs = 0.0;
      $payment -= $overrun_costs;
    }
    else
    {
      $afford = $payment / $cm_over;
      $result["overrun_sus"] = $afford;
      $result["overrun_costs"] = $payment;
      $oldSUs += $afford;
      $payment = 0.0;
    }
  }
  else
  {
    $result["overrun_sus"] = 0.0;
    $result["overrun_costs"] = 0.0;
  }
  if($payment > 0)
  {
    $afford = $payment / $cm_norm;
    $result["norm_sus"] = $afford;
    $result["norm_costs"] = $payment;
    $oldSUs += $afford;
  }
  else
  {
    $result["norm_sus"] = 0.0;
    $result["norm_costs"] = 0.0;
  }
  return $result;
}
