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

require_once(_FULLPATH."/ColorGrd.php");
require_once(_FULLPATH."/classes/class_myJAM_DB.php");
$db = new myJAM_db();
$conf = $db->query('SELECT * FROM Configuration');
echo '<head>'
.'<link rel="shortcut icon" href="images/myJAM-icon.png" type="image/png"/>'
.'<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1"/>'
.'<meta http-equiv="Content-Style-Type" content="text/css"/>'
.'<meta http-equiv="Content-Script-Type" content="text/javascript"/>'
.'<title>'
.'&lt;myJAM/&gt;:'.$title
.'</title>'
.'<link rel="stylesheet" type="text/css" href="css/style.css"/>'
.'<link rel="stylesheet" type="text/css" href="css/tabcontent.css"/>'
.'<script type="text/javascript" src="js/boxover.js"></script>'
.'<script type="text/javascript" src="js/myjam_toggle_display.js"></script>'
.'</head>'
//<body onload='document.getElementById("objoverflash").style.display="none"' style="background: #ffffff;">
.'<body>'
.'<div class="titlebg">'
.'<div id="logo" class="logostyle1">'
.'<a class="stat" href="main.php?page=welcome">'
.'<img src="images/myjamlogo.png" width="179" height="27" alt="myJAM-Logo" />'
.'</a>'
.'</div>'
.'<div class="logostyle1" style="top:15px;left:200px;height:20px;width:50%;text-align:center;vertical-align:middle;font-size:14px;">'
.'<b>'.htmlentities($conf[0]['SiteName']).'</b>'
.'</div>'
.'<div id="LogInShell" class="myjamloginshell">'
.'<img src="images/LogInShell.png" alt="LoginGFX" />'
.'<div class="logingfx">'
.'<img class="logingfx2" src="images/user-16x16.png" alt="empty" />'
.'Logged in as: <b>'
;
$user = new myJAM_User($_SESSION["uid"]);
echo $user->UserName.'</b></div>';
echo '</div><div class="logingfx3">';
if($_CFG_AUTH_METHOD == 'DB')
{
  echo '<img '
      .' src="images/delete-icon_16x16.png"'
      .' onmouseover="this.src=\'images/delete-icon_16x16_active.png\'"'
      .' onmouseout="this.src=\'images/delete-icon_16x16.png\'"'
      .' onmousedown="this.src=\'images/delete-icon_16x16_press.png\'"'
      .' onmouseup="this.src=\'images/delete-icon_16x16_active.png\'"'
      .' onclick="window.location=\'index.php?logout=true\'"'
      .' width="100%" height="100%"/>';
}
echo '</div></div>';
