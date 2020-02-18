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

require_once(_FULLPATH.'/classes/class_myJAM_User.php');
require_once(_FULLPATH.'/config/CFG_modules.php');
//o-------------------------------------------------------------------------------o
function LogOut()
//o-------------------------------------------------------------------------------o
{
  session_unset();
  session_destroy();
  $host = htmlentities($_SERVER['HTTP_HOST']);
  $uri = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
  $extra = 'index.php?logout=true';
  $target = "Location: http://$host$uri/$extra";
  header($target);
  exit;
}
//o-------------------------------------------------------------------------------o
//o-------------------------------------------------------------------------------o
//o-------------------------------------------------------------------------------o
if(_UPGRADEPHP !== false)
{
  if(!file_exists(_UPGRADEPHP))
  {
    throw new Exception('Can not find specified upgradephp-include: '._UPGRADEPHP);
  }
  require_once(_UPGRADEPHP);
}
if (!isset($_SESSION['uid']))
{
  session_start();
}
$_CFG_AUTH_METHOD = NULL;
require (_FULLPATH . '/config/CFG_auth.php');
define('_CFG_AUTH_METHOD', $_CFG_AUTH_METHOD);
//o-------------------------------------------------------------------------------o
if (_CFG_AUTH_METHOD == 'DB')
//o-------------------------------------------------------------------------------o
{
  if (isset($_SESSION['passcrypt']) &&
      strlen($_SESSION['passcrypt']) == 32 &&
      isset($_SESSION['uid']) &&
      is_numeric($_SESSION['uid']))
  {
    $_SESSION['member_popup_pid'] = '';
    $ActiveUser = new myJAM_User($_SESSION['uid']);
    if (!is_a($ActiveUser, 'myJAM_User') || !is_scalar($ActiveUser->ID) || $ActiveUser->ID < 1)
    {
      LogOut();
    }
    if (!$ActiveUser->CheckPWDCrypt($_SESSION['passcrypt']))
    {
      LogOut();
    }
  }
  else
  {
    LogOut();
  }
}
//o-------------------------------------------------------------------------------o
else if (_CFG_AUTH_METHOD == 'HTACCESS')
//o-------------------------------------------------------------------------------o
{
  $_SESSION['member_popup_pid'] = '';
  $ActiveUser = new myJAM_User($_SESSION['uid']);
  if (!is_a($ActiveUser, 'myJAM_User') || !is_scalar($ActiveUser->ID) || $ActiveUser->ID < 1)
  {
    echo 'HTTP AUTH FAILED for user: '.$_SESSION['uid'];
    ?>
<script type="text/javascript">
window.location.href="index.php";
</script>
    <?php
  }
}
