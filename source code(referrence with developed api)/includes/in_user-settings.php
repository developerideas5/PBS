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
require_once(_FULLPATH.'/classes/class_myJAM_User.php');
require_once(_FULLPATH.'/classes/class_myJAM_Project.php');
require_once(_FULLPATH.'/fxn/is_md5.php');
require_once(_FULLPATH.'/helper/myJAM_forms/myJAM_Form_Table.php');
require_once(_FULLPATH.'/helper/myJAM_forms/myJAM_Form_SubForm.php');
require_once(_FULLPATH.'/helper/myJAM_forms/myJAM_Form_Element_Text.php');
require_once(_FULLPATH.'/helper/myJAM_forms/myJAM_Form_Element_Passive.php');
require_once(_FULLPATH.'/helper/myJAM_forms/myJAM_Form_Element_Password.php');
require_once(_FULLPATH.'/helper/myJAM_forms/myJAM_Form_Element_Select.php');
require_once(_FULLPATH.'/helper/myJAM_forms/myJAM_Form_Element_Button.php');
require_once(_FULLPATH.'/helper/myJAM_forms/myJAM_Form_Element_SubmitButton.php');
require_once(_FULLPATH.'/helper/myJAM_forms/myJAM_Form_Element_Caption.php');
require_once(_FULLPATH.'/helper/myJAM_forms/myJAM_Form_Element_Checkbox.php');
require_once(_FULLPATH.'/helper/myJAM_forms/myJAM_Form_Element_Hidden.php');
?>
<script type="text/javascript" src="js/TestUserInput.js"></script>
<script type="text/javascript" src="js/usersettings_controller.js"></script>
<script type="text/javascript" src="js/myJAM_JSON.js"></script>
<script type="text/javascript" src="js/prototype.js"></script>
<?php if(_CFG_AUTH_METHOD == 'DB'):?>
<script type="text/javascript" src="js/md5.js"></script>
<?php endif;?>
<?php
//o-------------------------------------------------------------------------------o
//o     M A I N                                                                   o
//o-------------------------------------------------------------------------------o
$action = 'show';
if(isset($_GET['action']))
{
  $action = $_GET['action'];
}
if(isset($_POST['action']))
{
  $action = $_POST['action'];
}
switch($action)
{
  case 'show':
    US_show();
    break;
  case 'update':
    US_update();
    break;
  case 'new':
    US_new();
    break;
  case 'create':
    US_create();
    break;
  case 'delete':
    US_delete();
    break;
  case 'shadow':
    US_shadow();
    break;
  default:
    die('myJAM>> FATAL ERROR 0x0af51 in user-settings: Illegal action!');
}
//o-------------------------------------------------------------------------------o
//o-------------------------------------------------------------------------------o
//o-------------------------------------------------------------------------------o
//o-------------------------------------------------------------------------------o
function US_show()
//o-------------------------------------------------------------------------------o
{
  global $ActiveUser;
  $User = NULL;
  //if uid is set, get an instance of this user, otherwise take the active user
  if(isset($_GET['uid']))
  {
    $User = new myJAM_User((int)$_GET['uid']);
    if(!is_a($User, 'myJAM_User') || !is_scalar($User->ID) || !(int)$User->ID > 0)
    {
      die('myJAM>> FATAL ERROR 0x0baf8 in user-settings: Illegal user ID!');
    }
  }
  elseif(isset($_GET['name']))
  {
    $User = new myJAM_User($_GET['name']);
    if(!is_a($User, 'myJAM_User') || !is_scalar($User->ID) || !(int)$User->ID > 0)
    {
      die('myJAM>> FATAL ERROR 0x09071 in user-settings: Illegal username!');
    }
  }
  else
  {
    $User = $ActiveUser;
  }
  if(strncmp($User->UserName, 'formerUser', 10) == 0)
  {
    die('myJAM>> FATAL ERROR 0x in user-settings: Just a shadow!');
  }
  //admin privileges?
  if($User->ID != $ActiveUser->ID && !$ActiveUser->ADMIN)
  {
    die('myJAM>> FATAL ERROR 0x06ed4 in user-settings: Access denied for non-Admin users!');
  }
  echo US_form($User, 'show');
}
//o-------------------------------------------------------------------------------o
function US_update()
//o-------------------------------------------------------------------------------o
{
  global $ActiveUser;
  $User = NULL;
  if(isset($_POST['uid']) && (int)$_POST['uid'] > 0)
  {
    $User = new myJAM_User((int)$_POST['uid']);
    if(!is_a($User, 'myJAM_User') || !is_scalar($User->ID) || !(int)$User->ID > 0)
    {
      die('myJAM>> FATAL ERROR 0x066fe in user-settings: Illegal user ID!');
    }
  }
  else
  {
    die('myJAM>> FATAL ERROR 0x0a08b!');
  }
  //admin privileges?
  if($User->ID != $ActiveUser->ID && !$ActiveUser->ADMIN)
  {
    die('myJAM>> FATAL ERROR 0x04a60 in user-settings: Update denied for non-Admin users!');
  }
  if($User->FirstName != $_POST['firstname'])
  {
    $User->FirstName = $_POST['firstname'];
  }
  if($User->LastName != $_POST['lastname'])
  {
    $User->LastName = $_POST['lastname'];
  }
  if($User->eMail != $_POST['email'])
  {
    $User->eMail = $_POST['email'];
  }
  if(!empty($_POST['password']) && is_md5($_POST['password']))
  {
    $User->MD5PWD = $_POST['password'];
  }
  if($_POST['admin'] == '1')
  {
    $User->ADMIN = 1;
  }
  else
  {
    $User->ADMIN = 0;
  }
  echo 'User',
       ' <b>'.$User->UserName.'</b>',
       ' updated!<br/>',
       '<p></p>';
  $_GET['uid'] = $User->ID;
  echo US_show();
}
//o-------------------------------------------------------------------------------o
function US_new()
//o-------------------------------------------------------------------------------o
{
  global $ActiveUser;
  if(!$ActiveUser->ADMIN)
  {
    die('myJAM>> FATAL ERROR 0x03611 in user-settings: Only admin-users can create new users!');
  }
  echo US_form(NULL, 'new');
}
//o-------------------------------------------------------------------------------o
function US_create()
//o-------------------------------------------------------------------------------o
{
  global $ActiveUser;
  if (!$ActiveUser->ADMIN)
  {
    die('myJAM>> FATAL ERROR 0x0e685 in user-settings: Only admin-users can create new users!');
  }
  $Users = new myJAM_User();
  $admin = 0;
  if (isset($_POST['admin']) && $_POST['admin'] == '1')
  {
    $admin = 1;
  }
  if (!isset($_POST['password']))
  {
    $_POST['password'] = '';
  }
  if (_CFG_AUTH_METHOD == 'DB' && $_POST['password'] == '')
  {
    die('myJAM>> FATAL ERROR 0x02c34 in user-settings: Password must not be empty!');
  }
  $NewUser = myJAM_User::Create($_POST['username'],
                                $_POST['firstname'],
                                $_POST['lastname'],
                                $_POST['email'],
                                $_POST['password'],
                                (int)$admin
                               );
  echo 'User',
       ' <b>'.$NewUser->UserName.'</b>'
       .' (<i>'.$NewUser->ID.'</i>)'
       .' has been created!<br/>'
       .'<p></p>';
  $_GET['uid'] = $NewUser->ID;
  echo US_show();
}
//o-------------------------------------------------------------------------------o
function US_shadow()
//o-------------------------------------------------------------------------------o
{
  global $ActiveUser;
  if(!$ActiveUser->ADMIN)
  {
    die('myJAM>> FATAL ERROR 0xbla1 in user-settings: Only admin-users can delete users!'); //TODO: Error-Code
  }
  $User = NULL;
  if(isset($_POST['uid']) && (int)$_POST['uid'] > 0)
  {
    $User = new myJAM_User((int)$_POST['uid']);
    if(!is_a($User, 'myJAM_User') || !is_scalar($User->ID) || !(int)$User->ID > 0)
    {
      die('myJAM>> FATAL ERROR 0xbla2 in user-settings: Illegal user ID!'); //TODO: Error-Code
    }
  }
  else
  {
    die('myJAM>> FATAL ERROR 0x0d8e9!'); //TODO: Error-Code
  }
  $form = '';
  $isprojectowner = $User->IsProjectOwner();
  if ($isprojectowner)
  {
    $form .= '<input id="setadmin" type="checkbox" value="setadmin" name="setadmin">'
            .'Replace the projectowner in all projects of this user with ADMIN<br>';
  }
  $form .= '<input id="shadowbutton" type="button" onclick="return US_ShadowUser(\''.$User->ID.'\',\''.$User->UserName.'\')" value="Do the shadow-magic!" name="shadowbutton">'
           .'<input id="owner" type="hidden" value="'.$isprojectowner.'" name="owner">';
  echo '<div>'.
         '<ul>'.
           '<li>User: <b>'.$User->UserName.'</b></li>'.
           '<li><p></p></li>'.
           '<li>Owner of projects:</li>'.
           US_ShowProjectOwner($User).
           '<li><p></p></li>'.
           '<li>Member of projects:</li>'.
           US_ShowProjectMember($User).
           '<li><p></p></li>'.
           US_ShowUsedPaths($User).
         '</ul>'.
         '<div id="workingdiv" style="margin-left:15px;">'.
           $form.
         '</div>'.
       '</div>';
}
//o-------------------------------------------------------------------------------o
function US_ShowUsedPaths($User)
//o-------------------------------------------------------------------------------o
{
  $used_paths = '';
  if (count($User->Paths) > 0)
  {
    $used_paths = '<li>'.count($User->Paths).' paths containing <b>/'.$User->UserName.'/</b> will be renamed to <b>/formerUser'.$User->ID.'/</b>:</li>'.
                  '<div style="border:thin solid black; height:285px; width:600px; overflow:auto"><table><tbody>';
    foreach ($User->Paths as $path)
    {
      $used_paths .= '<tr><td>'.$path.'</tr></td>';
    }
    $used_paths .= '</tbody></table></div>';
  }
  else
  {
    $used_paths = '<li>0 paths contain '.$User->UserName.'. Nothing to rename.</li>';
  }
  return $used_paths;
}
//o-------------------------------------------------------------------------------o
function US_ShowProjectOwner($User)
//o-------------------------------------------------------------------------------o
{
  $OwnedProjects = US_getProjectListByOwner($User);
  $owner_msg = 'none';
  if(count($OwnedProjects) > 0)
  {
    $owner_msg = '';
    foreach($OwnedProjects as $proj)
    {
      $owner_msg .= '<li>'.
             '<img src="images/Project-16x16.png"/>&nbsp;'.
             '<a href="main.php?page=projects&mode=info&pid='.$proj->ID.'">'.
               $proj->Name.
             '</a>'.
           '</li>';
    }
  }
  return $owner_msg;
}
//o-------------------------------------------------------------------------------o
function US_ShowProjectMember($User)
//o-------------------------------------------------------------------------------o
{
  $MemberProjects = $User->Projects;
  $member_msg = 'none';
  if(count($MemberProjects) > 0)
  {
    $member_msg = '';
    foreach($MemberProjects as $mproj)
    {
      $member_msg .= '<li>'.
             '<img src="images/Project-16x16.png"/>&nbsp;'.
             '<a href="main.php?page=projects&mode=info&pid='.$mproj->ID.'">'.
               $mproj->Name.
             '</a>'.
           '</li>';
    }
  }
  return $member_msg;
}
//o-------------------------------------------------------------------------------o
function US_delete()
//o-------------------------------------------------------------------------------o
{
  global $ActiveUser;
  if(!$ActiveUser->ADMIN)
  {
    die('myJAM>> FATAL ERROR 0x0eb9a in user-settings: Only admin-users can delete users!');
  }
  $User = NULL;
  if(isset($_POST['uid']) && (int)$_POST['uid'] > 0)
  {
    $User = new myJAM_User((int)$_POST['uid']);
    if(!is_a($User, 'myJAM_User') || !is_scalar($User->ID) || !(int)$User->ID > 0)
    {
      die('myJAM>> FATAL ERROR 0x0c7fe in user-settings: Illegal user ID!');
    }
  }
  else
  {
    die('myJAM>> FATAL ERROR 0x0d8e9!');
  }
  if ($User->IsProjectOwner() || $User->HasJobs() || count($User->Projects) > 0)
  {
    echo 'User <b>'.$User->UserName.'</b> is connected to various objects.<br>',
         'Therefore you can not <b>delete</b> this user, but you may <b>shadow</b> this user!<p></p>';
    $_GET['uid'] = $User->ID;
    echo US_show();
  }
  else
  {
    $oldname = $User->UserName;
    $User->DELETE();
    echo 'User',
         ' <b>'.$oldname.'</b>',
         ' deleted!';
  }
}
//o-------------------------------------------------------------------------------o
function US_getProjectListByOwner($user)
//o-------------------------------------------------------------------------------o
{
  $ProjectList = new myJAM_Project();
  $MemberProjects = array();
  foreach($ProjectList->ID as $pid)
  {
    if($ProjectList->Owner[$pid]->ID == $user->ID)
    {
      $MemberProjects[] = new myJAM_Project($pid);
    }
  }
  return $MemberProjects;
}
/**
 * Returns the main form. If $action is 'show', the fields are set to the corresponding values of the User object
 * @param myJAM_User $User : Instance of myJAM_User of the user to show
 * @param string $action : 'new' or 'show'
 * @return string : the HTML of the form
 */
//o-------------------------------------------------------------------------------o
function US_form($User = NULL, $action)
//o-------------------------------------------------------------------------------o
{
  global $ActiveUser;
  $nextaction = 'update';
  $button_text = 'Update';
  if($action == 'new')
  {
    $nextaction = 'create';
    $button_text = 'Create User';
  }
  $next_uid = 'new';
  $link = 'main.php?page=user-settings&amp;action='.$nextaction;
  if(isset($_GET['list']))
  {
    $link .= '&amp;list';
  }
  //Auth Method: Database or .htaccess?
  $pw_active = true;
  if(_CFG_AUTH_METHOD != 'DB')
  {
    $pw_active = false;
  }
  $firstname = NULL;
  $lastname = NULL;
  $email = NULL;
  $form = new myJAM_Form_Table($link, 'usersettings');
//Action?
  if($action == 'show')
  {
    $next_uid = $User->ID;
    $button_alert = 'Are you sure you want to update the user \\\''.$User->UserName.'\\\'?';
    $firstname = $User->FirstName;
    $lastname = $User->LastName;
    $email = $User->eMail;
    $form->addElement(new myJAM_Form_Element_Passive('username', $User->UserName), 'Username: ');
  }
  //Create a new User
  else
  {
    //Set alert on button
    $button_alert = 'Are you sure you want to create this user?';
    //Change field for username to a text-input, so that you can enter a username
    $form->addElement(new myJAM_Form_Element_Text('username'), 'Username: ');
  }
  $form->addElement(new myJAM_Form_Element_Text('firstname', $firstname), 'Firstname: ', 30);
  $form->addElement(new myJAM_Form_Element_Text('lastname', $lastname), 'Lastname: ', 30);
  $form->addElement(new myJAM_Form_Element_Text('email', $email, 30, 255), 'eMail: ');
  //If Auth Method is DB, that you need fields to enter a password
  if($pw_active)
  {
    $form->addElement(new myJAM_Form_Element_Caption(' '));
    $form->addElement(new myJAM_Form_Element_Password('password', NULL, 32), 'Password: ');
    $form->addElement(new myJAM_Form_Element_Password('confirm_password', NULL, 32), 'Re-type Password: ');
  }
  if ($ActiveUser->ADMIN)
  {
    $form->addElement(new myJAM_Form_Element_Caption(' '));
    $element = new myJAM_Form_Element_Select('admin');
    $element->addOptions(array('0' => 'Regular User'));
    $element->addOptions(array('1' => 'Administrator'));
    $element->setSize(1);
    $element->unsetMultiple();
    if ($action == 'show')
    {
      if ($User->ADMIN)
      {
        $element->setPreselected('1');
      }
      else
      {
        $element->setPreselected('0');
      }
    }
    $form->addElement($element, 'Administrator Level: ');
  }
  $cancel_location = 'main.php?page=welcome';
  if(isset($_GET['list']))
  {
    $cancel_location = 'main.php?page=user-list';
  }
  $form->addElement(new myJAM_Form_Element_Hidden('uid', $next_uid))
       ->addElement(new myJAM_Form_Element_Hidden('action', $nextaction));
  $subform = new myJAM_Form_SubForm();
  $button = new myJAM_Form_Element_SubmitButton('submitbutton', ' '.$button_text.' ');
  $button->onclick('return US_CheckInput(\''.$button_alert.'\');');
  $subform->addElement($button);
  $subform->addElement(new myJAM_Form_Element_Passive(NULL, '&nbsp;&nbsp;'));
  $button = new myJAM_Form_Element_Button('cancel', ' CANCEL ');
  $button->onclick('window.location=\''.$cancel_location.'\'');
  $subform->addElement($button);
  if($action != 'new' && $ActiveUser->ADMIN)
  {
    $subform->addElement(new myJAM_Form_Element_Passive(NULL, '&nbsp;&nbsp;'));
    $button = new myJAM_Form_Element_SubmitButton('deletebutton', ' Delete ');
    $button->onclick('return US_DeleteUser(\''.$User->ID.'\',\''.$User->UserName.'\')"');
    $subform->addElement($button);
    $subform->addElement(new myJAM_Form_Element_Passive(NULL, '&nbsp;&nbsp;'));
    $button = new myJAM_Form_Element_SubmitButton('shadowbutton', ' SHADOW! ');
    $button->onclick('document.getElementById(\'action\').value = \'shadow\'; return true;');
    $subform->addElement($button);
  }
  $form->addElement(new myJAM_Form_Element_Caption(' '));
  $form->addElement($subform);
  return $form->renderForm();
}
