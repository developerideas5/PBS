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
require_once(_FULLPATH."/access.php");
require_once(_FULLPATH."/classes/class_myJAM_User.php");
require_once(_FULLPATH."/classes/class_myJAM_Project.php");
require_once(_FULLPATH."/CloseWindow.php");
require_once(_FULLPATH."/fxn/StatusBar.php");
require_once(_FULLPATH."/includes/cmn_header.php");
echo '<head>'
.'<link rel="shortcut icon" href="images/myJAM-icon.png" type="image/png"/>'
.'<title>Project Info</title>'
.'<link rel="stylesheet" type="text/css" href="css/style.css">'
.'</head>'
.'<body style="margin:5px;">'
;
?>
<script
 type="text/javascript" src="js/prototype.js"></script>
<script type="text/javascript">
//o-------------------------------------------------------------------------------o
function ShowUserInfo(uid)
//o-------------------------------------------------------------------------------o
{
 var ajax_updater = new Ajax.Updater("DivUserInfo","AJAX/GetUserInfo.php", { method: "post", parameters: "uid="+uid } );
};
</script>
<?php
echo '<table cellspacing="3" border="0" cellpadding="0" class="tabhead">'
.'<tr>'
.'<td>'
.'<table border="0" cellspacing="0" cellpadding="0" class="tabhead2">'
.'<tr>'
.'<td class="gradhead1">'
.'<img src="images/multigradient_left.png" alt="gradientGFX" />'
.'</td>'
.'<td style="width:150px; height:20px; background-image:url(images/gradient_slice_1.png);">'
.'<b>Project Info</b>'
.'</td>'
.'<td class="gradhead5">'
.'<img src="images/multigradient_middle.png" alt="gradientGFX" />'
.'</td>'
.'<td style="background-image:url(images/gradient_slice_2.png); background-repeat:repeat-x;"></td>'
.'<td class="gradhead1">'
.'<img src="images/multigradient_right.png" alt="gradientGFX" />'
.'</td>'
.'</tr>'
.'</table>'
.'</td>'
.'</table>'
.'<p/>'
.'<div style="border-style:solid;border-width:1px;">'
;
if(!$ActiveUser->ADMIN){
  die("myJAM>> ACCESS VIOLATION in ProjectInfo");
}
if(isset($_GET["projname"])){
  $Project = new myJAM_Project($_GET["projname"]);
}
if(!is_object($Project) || !is_scalar($Project->ID) || (integer)$Project->ID < 1){
  die("myJAM>> FATAL ERROR 0x3197 in ProjectInfo");
}
$RunningOnly = NULL;
if (isset($_GET["runningonly"]) && $_GET["runningonly"] == "1"){
  $RunningOnly = true;
}
echo '<table style="width: 100%;">'
.'<tr>'
.'<td class="right"><b>Project Name:&nbsp;</b></td>'
.'<td style="text-align: left;"><i>'.$Project->Name.'</i>'
.'&nbsp;&nbsp;&nbsp;<span style=\"color:'
;
if($Project->Enabled){
  echo '#009000;\">(enabled';
}else{
  echo '#ff0000;\">(disabled';
}
echo ')</span>'
.'</td>'
.'</tr>'
.'<tr>'
.'<td class="right"><b>Project Description:&nbsp;</b></td>'
.'<td style="width: 70%; text-align: left;">'. $Project->Description.'</td>'
.'</tr>'
.'<tr>'
.'<td class="right"><b>Project Owner:&nbsp;</b></td>'
.'<td style="text-align: left;">'.$Project->Owner->FullName.'</td>'
.'</tr>'
.'<tr>'
.'<td class="right"><b>Cost Model:&nbsp;</b></td>'
.'<td style="text-align: left;">'
;
if(! $Project->Billable) {
  echo "<i><span style=\"color:#009000;\">free</span></i>";
} else {
  echo $Project->CostModel->Description." (Norm:".$Project->CostModel->Norm."&euro;, Over:	".$Project->CostModel->Over."&euro;)";
  if ($Project->Overrun) {
    echo "&nbsp;&nbsp;&nbsp;<span style=\"color:#ff0000\">OVERUN allowed</span>";
  }
}
echo '</td>'
.'</tr>'
.'</table>'
.'<hr>';
$vUser = StatusBar(NULL, $Project->ID, NULL, NULL, $RunningOnly);
echo '<div class="full">'
.'<table border="0" align="center" style="width: 500px;">'
.'<tr>'
.'<td width="50%"><span class="fat">Users</span><br>'
.'<select class="inputfont" size="10">'
;
$UserList = new myJAM_User();
foreach ($vUser as $uid=>$count)
{
  echo '<option class="inputfont" onclick="ShowUserInfo(\''.(int)$uid.'\')">'.$UserList->FullName[$uid].' ('.$count.')</option>';
}
echo '</select>'
.'</td>'
.'<td width="50%">'
.'<div id="DivUserInfo"></div>'
.'</td>'
.'</tr>'
.'</table>'
.'</div>'
.'</div>';
echo CloseWindow();
echo '</body></html>';
