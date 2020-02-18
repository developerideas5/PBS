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
require_once(_FULLPATH."/classes/class_myJAM_DB.php");
require_once(_FULLPATH.'/ColorGrd.php');
require_once(_FULLPATH.'/php-ofc-library/open_flash_chart_object.php');
require_once(_FULLPATH.'/fxn/PrintOverview.php');
require_once(_FULLPATH.'/fxn/BootStrap_DB.php');
$_CFG_AUTH_METHOD = NULL;
require(_FULLPATH.'/config/CFG_auth.php');
@session_start();
@SESSION_unset();
@SESSION_destroy();
echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"'
.'"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">'
.'<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">'
.'<head>'
.'<link rel="shortcut icon" href="images/myJAM-icon.png" type="image/png"/>'
.'<title>&lt;myJAM/&gt; - Job Accounting and Monitoring: Login</title>'
.'<link rel="stylesheet" type="text/css" href="css/style.css"/>'
.'<meta http-equiv="Content-Style-Type" content="text/css"/>'
.'<meta http-equiv="Content-Script-Type" content="text/javascript"/>'
.'</head>'
.'<body class="login">'
;
if($_CFG_AUTH_METHOD == 'HTACCESS')
{
  require(_FULLPATH.'/fxn/CheckLogIn.php');
  CheckLogIn();
  ?>
<script type="text/javascript">
window.location.href="main.php?page=welcome";
</script>
  <?php
}
?>
<script type="text/javascript" src="js/prototype.js"></script>
<script type="text/javascript" src="js/md5.js">
/*
 *  md5.js 1.0b 27/06/96
 *
 * Javascript implementation of the RSA Data Security, Inc. MD5
 * Message-Digest Algorithm.
 *
 * Copyright (c) 1996 Henri Torgemane. All Rights Reserved.
 *
 * Permission to use, copy, modify, and distribute this software
 * and its documentation for any purposes and without
 * fee is hereby granted provided that this copyright notice
 * appears in all copies.
 *
 * Of course, this soft is provided "as is" without express or implied
 * warranty of any kind.
 *
 *
 * Modified with german comments and some information about collisions.
 * (Ralf Mieke, ralf@miekenet.de, http://mieke.home.pages.de)
 */
</script>
<script type="text/javascript" src="js/login.js">
</script>
<!-- 07.04.2008 DBS; cleaned up HTML code beneath -->
<?php
$db = new myJAM_db();
$conf = $db->query('SELECT * FROM Configuration');
echo
'<div class="titlebg">'
.'<div id="logo" class="logostyle1">'
.'<a class="stat" href="main.php?page=welcome">'
.'<img src="images/myjamlogo.png" width="179" height="27" alt="myJAM-Logo" />'
.'</a>'
.'</div>'
.'<div class="logostyle1" style="top:15px;left:200px;height:20px;width:50%;text-align:center;vertical-align:middle;font-size:14px;">'
.'<b>'.htmlentities($conf[0]['SiteName']).'</b>'
.'</div>'
.'</div>'
.'<table class="full" cellpadding="0" cellspacing="0">'
.'<tr>'
.'<td id="idxchlogo">'
.'<div class="index1">';
ColorGrd("#ffffff", "#E47833", "100%",35,2,-1);
echo '<div id="idxloginlogo">&lt;myJAM/&gt; Login</div>'
.'</div>'
.'</td>'
.'<td class="idx140">'
.'<div class="index1">'
.'<img src="MultiGradient.php?c1=\'ffffff\'&amp;c2=\'E47833\'&amp;c3=\'ffffff\'&amp;c4=\'3378E4\'&amp;w=140&amp;h=35&amp;f=2" alt="gradientGFX" />'
.'</div>'
.'</td>'
.'<td class="centr">'
.'<div class="index1">'
;
ColorGrd("#ffffff", "#3378E4", "100%",35,2,-1);
echo '<div id="idxclusterhistory">Cluster History</div>'
.'</div>'
.'</td>'
.'<td class="idx140">'
.'<div class="index1">'
.'<img src="MultiGradient.php?c1=\'ffffff\'&amp;c2=\'3378E4\'&amp;c3=\'ffffff\'&amp;c4=\'ffffff\'&amp;w=140&amp;h=35&amp;f=2" alt="gradientGFX" />'
.'</div>'
.'</td>'
.'</tr>'
.'</table>'
.'<table class="full" cellpadding="0" cellspacing="0">'
.'<tr>'
.'<td id="idxloginform">'
.'<div id="idxloginform2"></div>'
//LOGIN FORM
.'<form action="">'
.'<table class="full" cellpadding="0" cellspacing="0">'
.'<tr>'
.'<td colspan="2">'
.'<div id="idxlogingradient">';
ColorGrd("#ffffff", "#999999", "100%",20,1,-1);
echo '</div>'
.'</td>'
.'</tr>'
.'<tr>'
.'<td class="greybg">'
.'<div id="idxusername">Username:</div>'
.'</td>'
.'<td class="greybg"><input class="inputfont" type="text" name="real_username" id="real_username" /></td>'
.'</tr>'
.'<tr>'
.'<td class="greybg">'
.'<div id="idxpw">Password:</div>'
.'</td>'
.'<td class="greybg"><input class="inputfont" type="password" name="user_pass" id="user_pass" /></td>'
.'</tr>'
.'<tr>'
.'<td class="greybg" colspan="2" style="text-align: center">'
.'<input type="hidden" name="passcrypt" id="passcrypt" value="" size="32" />'
.'</td>'
.'</tr>'
.'<tr>'
.'<td class="greybg"></td>'
.'<td class="greybg"><input class="inputfont" type="submit" value="  LOGIN  " onclick="return LogIn();" /></td>'
.'</tr>'
.'<tr>'
.'<td colspan="2">'
.'<div class="greybg">';
ColorGrd("#999999", "#ffffff", "100%",20,1,-1);
echo '</div>'
.'</td>'
.'</tr>'
.'</table>'
.'</form>'
.'<div style="height: 165px;"></div>'
.'<p><a class="stat" href="http://validator.w3.org/check?uri=referer">'
.'<img src="http://www.w3.org/Icons/valid-xhtml10" alt="Valid XHTML 1.0 Transitional" height="31" width="88" /></a></p>'
.'<p><a class="stat" href="http://jigsaw.w3.org/css-validator/check/referer">'
.'<img style="border: 0; width: 88px; height: 31px" src="http://jigsaw.w3.org/css-validator/images/vcss" alt="CSS is valid!" /> </a></p>'
.'</td>'
//<!-- CHARTS AND STATUS -->
.'<td class="centr">';
//New output
echo BootStrap_DB_html();
echo '<table class="full" cellpadding="0" cellspacing="0">'
.'<tr>'
.'<td class="centr">';
open_flash_chart_object("90%", "300", "charts/Chart_DistArchs.php?data=1&type=1", false,"","distarchs1" );
echo '</td>'
.'<td class="centr">';
open_flash_chart_object("90%", "300", "charts/Chart_DistArchs.php?data=2&type=2", false,"","distarchs2" );
echo '</td>'
.'</tr>'
.'</table>'
.'<br />'
//<!-- HEADER FOR CLUSTER STATUS -->
.'<table class="full" cellpadding="0" cellspacing="0">'
.'<tr>'
.'<td class="idx40">'
.'<div class="index1">'
.'<img src="MultiGradient.php?c1=\'ffffff\'&amp;c2=\'ffffff\'&amp;c3=\'ffffff\'&amp;c4=\'33e478\'&amp;w=40&amp;h=35&amp;f=2" alt="Multicolor Gradient" />'
.'</div>'
.'</td>'
.'<td class="centr">'
.'<div class="index1">';
ColorGrd("#ffffff", "#33e478", "100%",35,2,-1);
echo '<div id="idxclusterstatus">Cluster Status</div>'
.'</div>'
.'</td>'
.'<td class="idx40">'
.'<div class="index1">'
.'<img src="MultiGradient.php?c1=\'ffffff\'&amp;c2=\'33e478\'&amp;c3=\'ffffff\'&amp;c4=\'ffffff\'&amp;w=40&amp;h=35&amp;f=2" alt="Multicolor Gradient" />'
.'</div>'
.'</td>'
.'</tr>'
.'</table>'
//<!-- TABLE WITH CLUSTER STATUS AND RUNNING-CHARTS -->
.'<table id="idxclustertable" cellpadding="0" cellspacing="0">'
.'<tr>'
.'<td class="leftie">';
echo PrintOverview();
echo '</td>'
.'<td class="centr">';
open_flash_chart_object("190", "300", "charts/Chart_Running.php?data=1", false,"","runningchart1" );
echo '</td>'
.'<td class="centr">';
open_flash_chart_object("190", "300", "charts/Chart_Running.php?data=2", false,"","runningchart2" );
echo '</td>'
.'<td class="centr">';
open_flash_chart_object("225", "300", "charts/Chart_Running.php?data=3", false,"","runningchart3" );
echo '</td>'
.'</tr>'
.'</table>'
.'</td>'
.'</tr>'
.'</table>'
.'<div id="footer" class="footerouter" style="font-size:8pt;text-align: center">'
.'<hr/>'
.'<b>&lt;my<i>JAM</i>/&gt;</b> '
.' CE-Edition '
;
$_MYJAM_VERSION = 'n/a';
$_MYJAM_BUILD = 'n/a';
require_once(_FULLPATH.'/version_control.php');
echo $_MYJAM_VERSION." (Build $_MYJAM_BUILD)"
.' by &quot;The myJAM-Team&quot;'
.', <a href="http://www.zim.uni-duesseldorf.de/hpc">Heinrich-Heine-University D&uuml;sseldorf</a>'
.'</div>'
.'</body></html>';
