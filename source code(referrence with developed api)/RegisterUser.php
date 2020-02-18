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

define('_FULLPATH', realpath(dirname(__FILE__)));
require_once(_FULLPATH.'/version_control.php');
require_once(_FULLPATH.'/templateheader.php');
$_CFG_AUTH_METHOD = NULL;
$_CFG_AUTH_DEFAULT_PROJECT = NULL;
require (_FULLPATH . '/config/CFG_auth.php');
define('_CFG_AUTH_METHOD', $_CFG_AUTH_METHOD);
if (isset($_CFG_AUTH_DEFAULT_PROJECT) && !empty($_CFG_AUTH_DEFAULT_PROJECT)){
  define('_CFG_AUTH_DEFAULT_PROJECT', $_CFG_AUTH_DEFAULT_PROJECT);
}else{
  define('_CFG_AUTH_DEFAULT_PROJECT', '');
}
if(_CFG_AUTH_METHOD != 'HTACCESS'){
  die('myJAM>> FATAL ERROR! Possible security break!');
}
echo '<head>'
.'<link rel="shortcut icon" href="images/myJAM-icon.png" type="image/png" />'
.'<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />'
.'<title>&lt;myJAM/&gt;: User Registration</title>'
.'<link rel="stylesheet" type="text/css" href="css/style.css" />'
.'<link rel="stylesheet" type="text/css" href="css/tabcontent.css" />'
.'</head>'
.'<body>'
;
?>
<script type="text/javascript">
//o-------------------------------------------------------------------------------o
function CheckInput()
//o-------------------------------------------------------------------------------o
{
  var state = true;
  //Check eMail
  if(!(/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/.test(document.getElementById("email_addr").value)))
  {
    window.alert("eMail address seems to have a non-valid form.");
    document.getElementById("tab_email").style.color="#FF0000";
    state = false;
  }
  //Check FirstName
  if(!(/^[a-zA-ZäöüÄÖÜß\-']+$/.test(document.getElementById("firstname").value)))
  {
    window.alert("Illegal character in firstname! Only [a-zA-Z\\-'] allowed.");
    document.getElementById("tab_firstname").style.color="#FF0000";
    state = false;
  }
  //Check LastName
  if(!(/^[a-zA-ZäöüÄÖÜß\-']+$/.test(document.getElementById("lastname").value)))
  {
    window.alert("Illegal character in lastname! Only [a-zA-Z\\-'] allowed.");
    document.getElementById("tab_lastname").style.color="#FF0000";
    state = false;
  }
  return state;
}
</script>
<?php
echo '<div class="titlebg">'
.'<div id="logo" class="logostyle1"><a class="stat" href="">'
.'<img src="images/myjamlogo.png" width="263" height="55" alt="myJAM-Logo" /></a>'
.'</div>'
.'<div class="logostyle2">'
.'Sophisticated Job Accounting and Monitoring (Version '.$GLOBALS["_MYJAM_VERSION"].'-'.$GLOBALS["_MYJAM_BUILD"].')'
.'</div>'
.'<div id="LogInShell" class="myjamloginshell">'
.'<img src="images/LogInShell.png" alt="LoginGFX" />'
.'<div class="logingfx">'
.'<img class="logingfx2" src="images/user-16x16.png"	alt="empty" />'
.'Logged in as: <b>'. $_SERVER['PHP_AUTH_USER'].'</b>'
.'</div>'
.'</div>'
.'</div>'
.'<div style="position: absolute; left: 10px; top: 75px; width: 450px;">'
.'<table cellspacing="3" cellpadding="0" class="tabhead">'
.'<tr>'
.'<td>'
.'<table cellspacing="0" cellpadding="0" class="tabhead2">'
.'<tr>'
.'<td class="gradhead1"><img src="images/multigradient_left.png" alt="gradientGFX" /></td>'
.'<td class="gradhead4">'
.'<div class="toolgrad3">User Registration</div>'
.'</td>'
.'<td class="gradhead5"><img src="images/multigradient_middle.png" alt="gradientGFX" /></td>'
.'<td class="gradhead6"></td>'
.'<td class="gradhead1"><img src="images/multigradient_right.png" alt="gradientGFX" /></td>'
.'</tr>'
.'</table>'
.'</td>'
.'</tr>'
.'</table>'
.'<p />'
.'<div class="simpleborder" style="padding: 5px;"><br />'
.'Welcome to &lt;myJAM/&gt; - the sophisticated Job Accounting and Monitoring Tool.<br />'
.'<p></p>'
.'This is the first time you visit the &lt;myJAM/&gt; webapplication so '
.'&lt;myJAM/&gt; does not know anything about you except for your login'
.'username.<br /><p></p>'
.'Please fill in the registration form below.<br />'
.'<p></p>'
;
require_once(_FULLPATH.'/fxn/FirstLogIn.php');
FirstLogIn($_SERVER['PHP_AUTH_USER']);
echo '</div>'
.'</div>'
.'<div id="footer" class="footerouter">'
.'<div class="footerinner">'
.'<a class="class1" href="http://www.zim.uni-duesseldorf.de/hpc">'
.'<span class="fat">&lt;my</span>'
.'<span class="fatital">JAM/&gt;</span>'
.'<span class="fat"> 2</span> by: The myJAM-Team, University Duesseldorf, Germany</a><br />'
.'</div>'
.'<div class="footerbg"></div>'
.'</div>'
.'</body>'
.'</html>'
;
