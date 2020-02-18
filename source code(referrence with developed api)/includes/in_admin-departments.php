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
  die("myJAM>> ERROR 8LJO: You are not authorized to view these pages!");
}
require_once(_FULLPATH."/classes/class_myJAM_DB.php");
require_once(_FULLPATH."/classes/class_myJAM_Department.php");
/* route by action */
if (isset($_GET['action']))
{
  if ($_GET['action'] == "create")
  {
    create();
  }
  elseif ($_GET['action'] == 'update' && isset($_GET['id']))
  {
    $newName = (isset($_POST['newName'])?$_POST['newName']:NULL);
    update($_GET['id'], $newName);
  }
  elseif ($_GET['action'] == 'delete')
  {
    delete($_GET['id']);
  }
  else
  {
    show();
  }
}
else
{
  show();
}
//o-------------------------------------------------------------------------------o
function create()
//o-------------------------------------------------------------------------------o
{
  $dep = new myJAM_Department();
  if (isset($_POST['dname']) && sizeof($_POST) === 1) /* Write Values to DB */
  {
    $newName = $_POST['dname'];
    if (isset($dep->ID[$newName]))
    {
      die("myJAM>> ERROR VTGI in module Admin Departments: Deparment already exists!");
    }
    try
    {
      $NewDepartment = myJAM_Department::CreateDepartment($newName);
    }
    catch(Exception $e)
    {
      print_r($e);
      die();
    }
    echo "New department " . htmlentities($NewDepartment->Name) . " added.<br>";
    show();
  }
  else /* Read values from client post */
  {
?>
<!-- html input form for deparment creation -->
<div class="usermaintable">
<form action="main.php?page=admin-departments&amp;action=create"
 method="post">
<table cellpadding="2" cellspacing="2">
 <tr>
  <td class="cell3">Department Name:</td>
  <td class="cell2"><input class="inputfont" size="30" type="text"
   name="dname" value="" /></td>
 </tr>
</table>
<div class="usercenter"><input class="inputfont" type="submit"
 value="Create" /></div>
</form>
</div>
<!-- end of html -->
<?php
  }
}
//o-------------------------------------------------------------------------------o
function show($message=NULL) /* R */
//o-------------------------------------------------------------------------------o
{
  $deps = new myJAM_Department();
  echo "$message";
  ?>
<!-- html table header-->
<table class="usertable1">
 <tr>
  <td class="cell3">Department Name</td>
  <td class="cell3"></td>
  <td class="cell3"></td>
 </tr>
 <tr>
  <td class="cell2"><a
   href="main.php?page=admin-departments&amp;action=create"> Create new
  Department </a></td>
 </tr>
 <tr>
  <td colspan="5">
  <hr />
  </td>
 </tr>
 <!--html end-->
 <?php
  /* populate table with department names */
 $DepIDList = $deps->ID;
  foreach($DepIDList as $id)
  {
    $dname = $deps->Name[$id];
    echo "<tr>";
    echo "	<td class=\"cell2\">";
    echo "    	".htmlentities($dname);
    echo "	</td>";
    echo "	<td class=\"cell2\">";
    echo "		<a href=\"main.php?page=admin-departments&amp;action=update&amp;id=".(int)$id."\"> Edit </a>";
    echo "	</td>";
    echo "	<td>";
    echo "		<a href=\"main.php?page=admin-departments&amp;action=delete&amp;id=".(int)$id."\"";
    echo " onclick=\"return window.confirm('Do you really want to delete this Department?');\"";
    echo "> Delete </a>";
    echo "	</td>";
    echo "</tr>";
  }
  ?>
 </table>
<?php //closing the table
}
//o-------------------------------------------------------------------------------o
function update($id, $newName=NULL)
//o-------------------------------------------------------------------------------o
{
    /* sanitize and check input */
  $id = (int)$_GET['id'];
  if (!is_numeric($id) || $id < 1)
  {
    die("myJAM>> ERROR 132987 in module Admin Departments: got no valid id!");
  }
  /* get Department object by id */
  $dep = new myJAM_Department($id);
  $oldName = NULL;
  /* check if Department exists and get old name*/
  if (is_a($dep, 'myJAM_Department') && is_scalar($dep->ID) && (int)$dep->ID > 0)
  {
    $oldName = $dep->Name;
  }
  else
  {
    die("myJAM>> ERROR 98764 in module Admin Departments: no valid id!");
  }
  /* get new Name and update entry in DB */
  if ((!$newName == NULL) && $newName != $oldName)
  {
    try
    {
      $dep->Name = $newName;
    }
    catch(Exception $e)
    {
      print_r($e);
      die("myJAM>> ERROR 214422 in module Admin Departments: update failed miserably!");
    }
    echo "Department ".htmlentities($oldName)." updated to ".htmlentities($newName).".<br/>";
    show();
  }
  /* no updated name posted yet, send form */
  else
  {
    if ($newName == $oldName)
    {
      echo "Cannont process reqeust, old name and new name are the same.";
    }
    ?>
<!-- form for new Department name -->
<div class="usermaintable">
<form
 action="main.php?page=admin-departments&amp;action=update&amp;id=<?php
    echo $id?>"
 method="post">
<table cellpadding="2" cellspacing="2">
 <tr>
  <td class="cell3">Department Name:</td>
  <td class="cell2"><input class="inputfont" size="30" type="text"
   name="newName" value="<?php
    echo $oldName?>" /></td>
 </tr>
</table>
<div class="usercenter"><input class="inputfont" type="submit"
 value="Update" /></div>
</form>
</div>
<!-- end of html form -->
<?php
  }
}
//o-------------------------------------------------------------------------------o
function delete($id)
//o-------------------------------------------------------------------------------o
{
  $id = (int)$id;
  $dep = new myJAM_Department($id);
  if (!is_a($dep, 'myJAM_Department') || !is_scalar($dep->ID) || (int)$dep->ID < 1)
  {
    die('myJAM>> FATAL ERROR in admin-departments::delete');
  }
  $dname = $dep->Name; // remember name and say goodbye
  if (!$dep->isClean())
  {
    echo "Department ".htmlentities($dname)." cannot be deleted, because Institutes refer to it!<br/>";
    echo '<a href="main.php?page=admin-departments&amp;action=show">Back</a>' . "<br>";
    die();
  }
//   try
//  {
    $dep->Delete();
//  }
//   catch(Exception $e)
//   {
//     print_r($e);
//     die(/* TODO add better exception handling */);
//   }
  echo "Department $dname deleted.<br/>";
  show();
}
