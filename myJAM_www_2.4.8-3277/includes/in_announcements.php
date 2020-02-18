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

require_once(_FULLPATH.'/access.php');
require_once(_FULLPATH.'/classes/class_myJAM_Announcement.php');
if(isset($_GET['action']))
{
  switch($_GET['action'])
  {
    case 'edit':
      edit();
      break;
    case 'delete':
      delete();
      break;
    case 'create':
      create();
      break;
    case 'update';
      update();
      break;
    default:
      die('Illegal action in admin announcement module');
  }
}
else
{
  form('create');
}
show();
//o-------------------------------------------------------------------------------o
function form($action = NULL, $id = NULL, $title = NULL, $content=NULL)
//o-------------------------------------------------------------------------------o
{
  echo '<form action="main.php?page=announcements';
  switch($action)
  {
    case 'update':
      echo '&amp;action=update';
      $button_label = 'Update';
      break;
    case 'create':
      echo '&amp;action=create';
      $button_label = 'Add';
      break;
    default:
      die('Illegal action in admin announcement module, form-helper');
  }
  if(isset($id))
  {
    if(!is_numeric($id) || (int)$id < 1)
    {
      die('Illegal ID in admin announcement module, form-helper');
    }
    echo '&amp;id='.(int)$id;
  }
  echo '" method="post">';
?>
<table class="table1" style="width:100%;" cellpadding="2" cellspacing="2">
  <tr>
    <td class="cell3" style="width:0px;" >Subject:</td>
    <td><input class="inputfont" type="text" name="title"
      value="<?php if($title) { echo $title; } ?>"
      maxlength="35" />
    &nbsp;&nbsp;&nbsp;
    <input type="checkbox" name="enable_mail" value="enable_mail">Mail it!
    </td>
  </tr>
  <tr>
    <td class="cell3">Message:</td>
    <td><textarea class="inputfont" style="width:96%;height:150px;" name="text"><?php
if($content)
  {echo html_entity_decode($content);}?></textarea></td>
  </tr>
  <tr>
    <td></td>
    <td>
      <input class="inputfont" type="submit" name="submit"
        value="<?php echo $button_label;?>" />
      <input class="inputfont" type="button" name="cancel"
        value="Cancel" onclick="window.location.href='<?=$_SERVER['PHP_SELF']?>?page=announcements';"/>
    </td>
  </tr>
</table>
<?php
echo '</form>';
}
//o-------------------------------------------------------------------------------o
function edit()
//o-------------------------------------------------------------------------------o
{
  $id = $_GET['id'];
  if(!is_numeric($id) || (int)$id < 1)
  {
    die('Illegal ID in admin announcement module, edit-action');
  }
  $Announcement = new myJAM_Announcement(NULL, $id);
  if(!is_a($Announcement, 'myJAM_Announcement') || (int)$Announcement->ID != (int)$id)
  {
    die('Impossible Error in admin announcement module, edit-action');
  }
  form('update', $Announcement->ID, $Announcement->Title, $Announcement->Content);
}
//o-------------------------------------------------------------------------------o
function create()
//o-------------------------------------------------------------------------------o
{
  $title = htmlentities($_POST["title"]);
  $content = htmlentities($_POST["text"]);
  $mail = false;
  if($_POST['enable_mail'] == 'enable_mail')
  {
    $mail = true;
  }
  myJAM_Announcement::create($title, $content, $mail);
  form('create');
}
//o-------------------------------------------------------------------------------o
function delete()
//o-------------------------------------------------------------------------------o
{
  $id = (int)$_GET['id'];
  if($id < 1)
  {
    die('Illegal in admin announcement module, delete-action');
  }
  $Announcement = new myJAM_Announcement(NULL, $id);
  if(!is_a($Announcement, 'myJAM_Announcement') || (int)$Announcement->ID != $id)
  {
    die('Impossible Error in admin announcement module, edit-action');
  }
  $Announcement->delete();
  form('create');
}
//o-------------------------------------------------------------------------------o
function update()
//o-------------------------------------------------------------------------------o
{
  $title = $_POST["title"];
  $content = $_POST["text"];
  $id = (int)$_GET['id'];
  if($id < 1)
  {
    die('Illegal in admin announcement module, delete-action');
  }
  $Announcement = new myJAM_Announcement(NULL, $id);
  if(!is_a($Announcement, 'myJAM_Announcement') || (int)$Announcement->ID != $id)
  {
    die('Impossible Error in admin announcement module, edit-action');
  }
  if($title != $Announcement->Title)
  {
    $Announcement->Title = $title;
  }
  if($content != $Announcement->Content)
  {
    $Announcement->Content = $content;
  }
  form('create');
}
//o-------------------------------------------------------------------------------o
function show()
//o-------------------------------------------------------------------------------o
{
?>
<p></p>
<table class="tabhead" cellspacing="3" cellpadding="0">
 <tr>
  <td>
  <table cellspacing="0" cellpadding="0" class="tabhead2">
   <tr>
    <td class="gradhead1"><img src="images/multigradient_left.png"
     alt="gradientGFX" /></td>
    <td class="gradhead4"><b>Previous Announcements</b></td>
    <td class="gradhead5"><img src="images/multigradient_middle.png"
     alt="gradientGFX" /></td>
    <td class="gradhead6"></td>
    <td class="gradhead1"><img src="images/multigradient_right.png"
     alt="gradientGFX" /></td>
   </tr>
  </table>
  <hr>
  <table id="announcements">
   <tr>
    <td>
<?php
  $Announcements = new myJAM_Announcement();
  if($Announcements->nb > 0)
  {
foreach($Announcements->ID as $id)
{
    echo '<a href="main.php?page=announcements&amp;action=delete&amp;id='.$id.'" onclick="return window.confirm(\'Do you really want to delete this announcement?\')">';
    echo ' <img src="images/Trash-Empty-icon_16x16.png" title="delete" />';
    echo '</a>';
    echo ' <a href="main.php?page=announcements&amp;action=edit&amp;id='.$id.'">';
    echo '<img src="images/Edit-Document-icon_16x16.png" title="edit"/>';
    echo '</a>';
    echo ' ('.date('Y-m-d H:i',$Announcements->Date[$id]).') ';
    echo '<a href="#" class="fat" title="show" onclick="myJAM_toggle_display(\'ann_'.$id.'\');" style="text-decoration:underline;margin-right:15px;margin-top:10px;">';
    echo $Announcements->Title[$id];
    echo '</a><br>';
    echo '<p id="ann_'.$id.'" style="display:none;border:1px solid black;padding:5px;" >'.html_entity_decode(nl2br($Announcements->Content[$id]))."<p/>";
}
  }
  else
  {
    echo "<span class=\"fat\">There are currently no announcements!</span>";
  }
?>
        </td>
   </tr>
  </table>
  </td>
 </tr>
</table>
<?php
}
