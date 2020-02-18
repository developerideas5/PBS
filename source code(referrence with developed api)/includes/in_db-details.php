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
/*  authorization attempt, only allow ADMINs */
if(! $ActiveUser->ADMIN)
{
  die("myJAM>> You are not authorized to view these pages!");
}
require_once(_FULLPATH.'/fxn/MemOut.php');
$db = new myJAM_db();
$out = '<table style="width:100%">'
        .'<tr><td colspan="0"><img src="images/Jpceq8_24x24.png"/>'
           .'<span style="font-size:16px;font-weight:bold">&nbsp;myjam_db</span></td></tr>'
        .'<tr><td style="width:150px" align="right"><b>Database Server:</b></td>'
           .'<td><img src="images/Lajif9_16x16.png"/>&nbsp;'.$db->connection.'</td></tr>'
        .'<tr><td align="right"><b>Database Name:</b></td>'
           .'<td>'.$db->dbName.'</td></tr>'
        .'<tr><td align="right"><b>Current Size:</b></td>'
           .'<td>'.MemOut($db->size).'</td></tr>'
        .'<tr><td align="right"><b>Server Version:</b></td>'
           .'<td> MySQL '.$db->server_info.'</td></tr>'
        .'<tr><td align="right"><b>Protocol:</b></td>'
           .'<td>MySQL Protocol Version '.$db->protocol.'</td></tr>'
        .'<tr><td align="right"><b>Client Version:</b></td>'
           .'<td> MySQL '.$db->client_info.'</td></tr>'
        .'<tr><td align="right"><b>Client Encoding:</b></td>'
           .'<td>'.$db->client_encoding.'</td></tr>'
;
$out .= '</table>';
echo $out;
