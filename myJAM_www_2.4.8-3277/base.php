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
echo '</div>'
//<!-- Here div main is closed -->
.'</div>'
.'</td></tr></table>'
//<!-- This has only an effect on the left divs -->
//.'<div style="width:100%" id="leftmain_end">BLA BLA LEFT</div>'
.'<div id="footer" class="footerouter" style="font-size:8pt;text-align:center">'
.'<hr/>'
.'<b>&lt;my<i>JAM</i>/&gt;</b> '
.' CE-Edition '
;
$_MYJAM_BUILD = 'n/a';
$_MYJAM_VERSION = 'n/a';
require(_FULLPATH."/version_control.php");
echo $_MYJAM_VERSION." (Build $_MYJAM_BUILD)"
.' by &quot;The myJAM-Team&quot;'
.', <a href="http://www.zim.uni-duesseldorf.de/hpc">Heinrich-Heine-University D&uuml;sseldorf</a>'
//.'</div>'
.'</div>';
echo '</body></html>';
