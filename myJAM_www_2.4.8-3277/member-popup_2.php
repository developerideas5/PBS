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
require_once(_FULLPATH."/ColorGrd.php");
$CurPID = $_SESSION["CurProjID"];
$CurUID = $_SESSION["uid"];
if (! $CurPID)
{exit;}
$UserList=new myJAM_User();
$user = new myJAM_User($CurUID);
$project=new myJAM_Project($CurPID);
if(! $user->ADMIN && $project->Owner->ID != $user->ID)
{exit;}
if(!isset($_GET["action"]))
{
  header('Content-Type: text/html; charset=ISO-8859-1');
  echo '<?xml version="1.0" encoding="ISO-8859-1"?>'
  .'<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">'
  .'<html xmlns="http://www.w3.org/1999/xhtml">'
  .'<head>'
  .'<link rel="shortcut icon" href="images/myJAM-icon.png" type="image/png"/>'
  .'<title>Project membership for '.$project->Name.'</title>'
  .'<link rel="stylesheet" type="text/css" href="css/style.css" />'
  ;
  ?>
<script type="text/javascript" src="js/memberpopup.js"> </script>
  <?php
  echo '</head>'
  .'<body>'
  .'<div class="mempmargin">'
  .'<table class="memptable" cellspacing="3" cellpadding="0">'
  .'<tr>'
  .'<td>'
  .'<table cellspacing="0" cellpadding="0" class="tabhead2">'
  .'<tr>'
  .'<td class="gradhead1">'
  .'<img src="images/multigradient_left.png" alt="gradientGFX" />'
  .'</td>'
  .'<td class="gradhead4">'
  .'</td>'
  .'<td class="gradhead5">'
  .'<img src="images/multigradient_middle.png" alt="gradientGFX" />'
  .'</td>'
  .'<td class="gradhead6">'
  .'</td>'
  .'<td class="gradhead1">'
  .'<img src="images/multigradient_right.png" alt="gradientGFX" />'
  .'</td>'
  .'</tr>'
  .'</table>'
  .'</td>'
  .'</tr>'
  .'</table>'
  .'</div>'
  .'<div class="mempproject">'
  .'Project membership for '.$project->Name
  .'</div>'
  .'<p/>'
  .'<div class="memplistpos">'
  .'<form id="userform" action="member-popup_2.php" method="post">'
  .'<table class="full">'
  .'<tr>'
  .'<td class="mempallusers">'
  .'All Users Available:'
  .'</td>'
  .'<td rowspan="2" style="text-align:center;">'
  .'<input class="inputfont" type="button" name="adduser2project" value="Add User to Project" onclick="javascript:Add();" />'
  .'<br/>'
  .'<input class="inputfont" type="button" name="removeuserproject" value="Remove User from Project" onclick="javascript:Remove()" />'
  .'</td>'
  .'<td class="mempallusers">'
  .'Members of Project'.$project->Name
  .'</td>'
  .'</tr>'
  .'<tr>'
  .'<td>'
  .'<select class="mempif" name="allusers" size="15">'
  ;
  foreach ($UserList->FullName as $uid=>$name)
  {
    if ($UserList->UserName[$uid] == "admin"){
      echo "<option>&lt;==THE ADMIN==&gt;</option>";
    }else{
      echo "<option id=\"u".$uid."\">".$name."</option>";
    }
  }
  echo '</select>'
  .'</td>'
  .'<td>'
  .'<select class="mempif" name="memusers" multiple="multiple" size="15">'
  ;
  for($i = 0; $i < $project->nbUsers; $i++)
  {
    if ($project->Users[$i]->ID == $project->Owner->ID){
      //echo "<option name=\"owner\" id=\"o".$project->Users[$i]->ID."\">";
      echo "<option id=\"owner\">";
    }else{
      echo "<option id=\"p".$project->Users[$i]->ID."\">";
    }
    echo $project->Users[$i]->FullName."</option>";
  }
  echo '</select>'
  .'</td>'
  .'</tr>'
  .'</table>'
  .'</form>'
  .'<div>'
  .'<form id="useridlist" action="member-popup_2.php?action=update" method="post">'
  .'<table class="full">'
  .'<tr>'
  .'<td colspan="2">'
  .'<textarea class="memptxt" name="hiduserlist" cols="10" rows="5">'
  ;
  // hier schon den User mit der Bezeichnung "owner" entfernen
  for($i = 0; $i < $project->nbUsers; $i++){
    if($project->Users[$i]->ID!=$project->Owner->ID) {
      // evtl. noch ein u oder p vor den Namen!
      echo "p".$project->Users[$i]->ID.";";
    }
  }
  echo '</textarea>'
  .'</td>'
  .'</tr>'
  .'<tr>'
  .'<td class="mempupdatebtn">'
  .'<input class="inputfont" type="submit" value="Update" />'
  .'</td>'
  .'<td class="mempcancelbtn">'
  .'<input class="inputfont" type="button" value="Cancel" onclick="javascript:CloseWin()" />'
  .'</td>'
  .'</tr>'
  .'</table>'
  .'</form>'
  .'</div>'
  .'</div>'
  .'</body>'
  .'</html>'
  ;
  //if we get this far, we should be OK.
}
elseif(isset($_GET["action"]) && $_GET["action"] == "update")
{
  $vHiddenUserList = explode(";",$_POST["hiduserlist"]);
  //ereg_replace(" ist", " war", $string);
  // Replace 'u' with nothing
  for($i=0;$i<count($vHiddenUserList);$i++) {
    $vHiddenUserList[$i]=ereg_replace("[pu]","",$vHiddenUserList[$i]);
  }
  $vProjUserIDs = array();
  foreach($project->Users as $user)
  {
    //if $user is not in $vHiddenUserlist and is NOT owner of the project
    //(this is necessary, because the ID of the owner is replaced by "owner" in $vHiddenList)
    if (! in_array($user->ID, $vHiddenUserList) && $user->ID != $project->Owner->ID)
    {$project->DelUser($user);}
    else
    {$vProjUserIDs[] = $user->ID;}
  }
  foreach($vHiddenUserList as $user)
  {
    if (! in_array($user, $vProjUserIDs) and $user > 0)
    {
      $NewUser = new myJAM_User($user);
      $project->AddUser($NewUser);
    }
  }
  ?>
<script type="text/javascript">
  opener.location.reload(true);
  this.close();
</script>
  <?php
}
