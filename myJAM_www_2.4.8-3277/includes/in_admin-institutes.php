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
/*  authorization attempt, only allow ADMINs */
if (!$ActiveUser->ADMIN)
{
  die('myJAM>> ERROR 8LJO: You are not authorized to view these pages!');
}
require_once(_FULLPATH.'/classes/class_myJAM_Department.php');
require_once(_FULLPATH.'/classes/class_myJAM_Institute.php');
/* routing via url dispatch */
if(isset($_GET['action']))
{
  if ($_GET['action'] == 'create')
  {
    create();
  }
  elseif ($_GET['action'] == 'update')
  {
    $newName = (isset($_POST['newName'])?$_POST['newName']:NULL);
    $did= (isset($_POST['did'])?$_POST['did']:NULL);
    update($_GET['id'], $newName, $did);
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
  $Institutes = new myJAM_Institute();
  if (isset($_POST['iname']) && isset($_POST['did']) && sizeof($_POST) == 2) /* write values to model */
  {
    $InstituteName = $_POST['iname'];
    $DepartmentID = (int)$_POST['did'];
    if (isset($Institutes->ID[$InstituteName]))
    {
      die("myJAM>> ERROR VTGI in module Admin Institutes: Institutes already exists!");
    }
    $Department = new myJAM_Department($DepartmentID);
    if (!is_a($Department, 'myJAM_Department') || !is_scalar($Department->ID) || (int)$Department->ID < 1)
    {
      die("myJAM>> FATAL ERROR in module admin_institutes: illegal department!");
    }
    validate($InstituteName);
    myJAM_Institute::CreateInstitute($InstituteName, $Department);
    echo '>> New institute '.htmlentities($InstituteName).' added.<br/><p/>';
    show();
  }
  else /* read values from user post */
  {
    ?>
<!-- html input form for deparment creation -->
<div class="usermaintable">
<form action="main.php?page=admin-institutes&amp;action=create"
 method="post">
<table cellpadding="2" cellspacing="2">
 <tr>
  <td class="cell3">Institute Name:</td>
  <td class="cell2"><input class="inputfont" size="30" type="text"
   name="iname" value="" /></td>
 </tr>
 <tr>
  <td class="cell3">Department Name:</td>
  <td class="cell2"><select class="inputfont" name="did">
  <?php
  $dep = new myJAM_Department();
    foreach($dep->Name as $id=>$DepName)
    {
       echo "<option value=\"$id\"> $DepName </option>";
    }
  ?>
  </select></td>
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
function show() /* R */
//o-------------------------------------------------------------------------------o
{/* generate output table */?>
<table class="usertable1">
 <tr>
  <td class="cell3">Institute Name</td>
  <td class="cell3">Department Name</td>
  <td class="cell3"></td>
  <td class="cell3"></td>
 </tr>
 <tr>
  <td class="cell2"><a
   href="main.php?page=admin-institutes&amp;action=create"> Create a new
  Institute </a></td>
 </tr>
 <tr>
  <td colspan="5">
  <hr />
  </td>
 </tr>
 <?php
 /* populate table with department names */
 $Institutes = new myJAM_Institute();
 $InstIDList = $Institutes->ID;
 foreach($InstIDList as $id)
 {
   $iname = $Institutes->Name[$id];
   $dname = $Institutes->Department[$id]->Name;
   if($iname == 'n/a')
    {
      continue;
    }
   echo "<tr>";
   echo "	<td class=\"cell2\">";
   echo '    	'.htmlentities($iname);
   echo "	</td>";
   echo "	<td class=\"cell2\">";
   echo '    	'.htmlentities($dname);
   echo "	</td>";
   echo "	<td class=\"cell2\">";
   echo "		<a href=\"main.php?page=admin-institutes&amp;action=update&amp;id=".(int)$id."\"> Edit </a>";
   echo "	</td>";
   echo "	<td>";
   echo "		<a href=\"main.php?page=admin-institutes&amp;action=delete&amp;id=".(int)$id."\"";
   echo " onclick=\"return window.confirm('Do you really want to delete this Institute?');\"";
   echo "> Delete </a>";
   echo "	</td>";
   echo "</tr>";
 }
 ?></table>
<?php // _ugly_ but avoids a warning for missing end table tag
}
//o-------------------------------------------------------------------------------o
function update($id, $newName=NULL, $newDID=NULL)
//o-------------------------------------------------------------------------------o
{
  /* sanitize and check input */
  $id = (int)$id;
  $Institute = new myJAM_Institute($id);
  if (!is_a($Institute, 'myJAM_Institute') || !is_scalar($Institute->ID) || (int)$Institute->ID < 1)
  {
    die("myJAM>> ERROR 132987 in module Admin Institutes: got no valid id!");
  }
  $oldName = $Institute->Name;
  if ($newDID)
  {
    $Department = new myJAM_Department((int)$newDID);
    if (!is_a($Department, 'myJAM_Department') || !is_scalar($Department->ID) || (int)$Department->ID < 1)
    {
      die('myJAM>> ERROR in module Admin Institutes: no valid department');
    }
  }
  else
  {
    $Department = NULL;
  }
  $updated = false;
  if ((!$newName == NULL) && $newName != $oldName)
  {
    try
    {
      $Institute->Name = $newName;
    }
    catch(Exception $e)
    {
      print_r($e);
      die("myJAM>> ERROR 214422 in module Admin Institutes: update failed miserably!");
    }
    echo ">> Institute ".htmlentities($newName)." updated.<br/><p/>";
    $updated = true;
  }
  if ($Department && ($Department->ID != $Institute->Department->ID))
  {
    try
    {
      $Institute->Department = $Department;
    }
    catch(Exception $e)
    {
      print_r($e);
      die("myJAM>> ERROR 214422 in module Admin Institutes: update failed miserably!");
    }
    echo ">> Associated Department of Institute " . htmlentities($Institute->Name) . "updated<br/><p/>";
    $updated = true;
  }
  if ($updated)
  {
    show();
  }
  else
  {
    /* no updated name posted yet, send form */
    if ($newName == $oldName)
    {
      echo "Cannont process reqeust, old name and new name are the same.";
    }
    ?>
<!-- form for new Institute name -->
<div class="usermaintable">
<form
 action="main.php?page=admin-institutes&amp;action=update&amp;id=<?php
    echo $id?>"
 method="post">
<table cellpadding="2" cellspacing="2">
 <tr>
  <td class="cell3">Institute Name:</td>
  <td class="cell2"><input class="inputfont" size="30" type="text"
   name="newName" value="<?php
    echo htmlentities($oldName);?>" /></td>
 </tr>
 <tr>
  <td class="cell3">Department Name:</td>
  <td class="cell2"><select class="inputfont" name="did">
    <?php
    $dep = new myJAM_Department();
    foreach($dep->Name as $id => $DepName)
    {
      echo '<option value="' . $id . '"';
      if ($id == $Institute->Department->ID)
      {
        echo ' selected="selected"';
      }
      echo "> $DepName</option>";
    }
    ?>
    </select></td>
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
  $inst = new myJAM_Institute((int)$id);
  if (!is_a($inst, 'myJAM_Institute') || !is_scalar($inst->ID) || (int)$inst->ID < 1)
  {
   die("myJAM>> ERROR RUGIB in module Admin Institutes: Institute does not exist!");
  }
  $iname = $inst->Name;
  $inst->Delete();
  echo "Institute ".htmlentities($iname)." deleted!<br/><p/>";
//echo '<a href="main.php?page=admin-institutes&amp;action=show">Show institutes</a>'."<br>";
  show();
}
//o-------------------------------------------------------------------------------o
function validate($name)
//o-------------------------------------------------------------------------------o
{
  /* TODO (i0) implement validation, better move into model */
}
