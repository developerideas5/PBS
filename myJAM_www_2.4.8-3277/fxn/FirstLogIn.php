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

require_once (_FULLPATH . '/classes/class_myJAM_User.php');
require_once (_FULLPATH . '/classes/class_myJAM_Project.php');
//o-------------------------------------------------------------------------------o
function FirstLogIn($username)
//o-------------------------------------------------------------------------------o
{
  if(_CFG_AUTH_METHOD != 'HTACCESS')
  {
    die("myJAM>> FATAL ERROR. Possible security break!");
  }
  if(isset($_GET['action']))
  {
    if($_GET['action'] == 'register')
    {
      FirstLogInCreate();
    }
  }
  else
  {
    $User = new myJAM_User($_SERVER['PHP_AUTH_USER']);
    if (!is_array($User->ID))
    {
      print('myJAM>> ERROR: User is already registered!');
    }
    else
    {
      FirstLogInForm($username);
      die();
    }
  }
  print'<p></p>'
      .'<a href="index.php">Go to &lt;myJAM/&gt; main page...</a>'
      ;
}
//o-------------------------------------------------------------------------------o
function FirstLogInCreate()
//o-------------------------------------------------------------------------------o
{
  if(_CFG_AUTH_METHOD != 'HTACCESS')
  {
    die('myJAM>> FATAL ERROR. Possible security break!');
  }
  if(!isset($_POST['username']) || !isset($_SERVER['PHP_AUTH_USER']) || $_POST['username'] != $_SERVER['PHP_AUTH_USER'])
  {
    die('myJAM>> FATAL ERROR. POST and SERVER differ');
  }
  $User = new myJAM_User($_POST['username']);
  if (!is_array($User->ID))
  {
    die("myJAM>> ERROR VTGI: User already exists!");
  }
  $NewUser = myJAM_User::Create($_POST['firstname'],
                                $_POST['lastname'],
                                $_POST['email_addr'],
                                $_SERVER['PHP_AUTH_PW'],
                                0);
  echo 'User <b>'.$_POST['username'].'</b> sucessfully registered.<br/><p></p>';
  if(_CFG_AUTH_DEFAULT_PROJECT != '')
  {
    $Project = new myJAM_Project(_CFG_AUTH_DEFAULT_PROJECT);
    if(!is_a($Project, 'myJAM_Project') || !is_scalar($Project->ID) || !(int)$Project->ID > 0)
    {
      die('myJAM>> FATAL ERROR. Illegal Default Project. Check your configuration!');
    }
    $Project->AddUser($NewUser);
    echo 'You have been assigned to the default project <b>'.$Project->Name.'</b><br><p></p>';
  }
  else
  {
    echo '<b>For submitting Jobs an administrator needs to associate you to a project.</b><br><p></p>';
  }
}
//o-------------------------------------------------------------------------------o
function FirstLogInForm($username)
//o-------------------------------------------------------------------------------o
{
?>
<form action="RegisterUser.php?action=register" method="post">
  <table class="table1" cellspacing="5" cellpadding="3">
    <tr>
      <td class="cell3" id="tab_username">
        <span class="fat">Username:</span>
      </td>
      <td class="cell2">
        <?php print($username);?>
      </td>
    </tr>
    <tr>
      <td class="cell3" id="tab_firstname">
        <span class="fat">Firstname:</span>
      </td>
      <td class="cell2">
        <input class="usersettings" type="text" name="firstname" id="firstname" value=""/>
      </td>
    </tr>
    <tr>
      <td class="cell3" id="tab_lastname">
        <span class="fat">Lastname:</span>
      </td>
      <td class="cell2">
        <input class="usersettings" type="text" name="lastname" id="lastname" value=""/>
      </td>
    </tr>
    <tr>
      <td class="cell3" id="tab_email">
        <span class="fat">Email Address:</span>
      </td>
      <td class="cell2">
        <input class="usersettings" type="text" name="email_addr" id="email_addr" value=""/>
      </td>
    </tr>
    <tr>
      <td class="cell2" colspan="2" style="text-align:center;">
        <input class="inputfont" type="submit" value="Register" onclick="return CheckInput();"/>
      </td>
    </tr>
  </table>
  <input type="hidden" name="username" id = "username" value="<?php print($username);?>">
</form>
<?php
}
