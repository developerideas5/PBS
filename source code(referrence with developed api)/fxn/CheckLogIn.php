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
//o-------------------------------------------------------------------------------o
function CheckLogIn()
//o-------------------------------------------------------------------------------o
{
  $_CFG_AUTH_METHOD = NULL;
  require (_FULLPATH . '/config/CFG_auth.php');
  //o-------------------------------------------------------------------------------o
  if ($_CFG_AUTH_METHOD == 'DB')
  //o-------------------------------------------------------------------------------o
  {
    $user = new myJAM_User($_REQUEST['username']);
    if (!is_a($user, 'myJAM_User') || !is_scalar($user->ID) || $user->ID < 1)
    {
      die('REJECTED');
    }
    if (strlen($_REQUEST['passcrypt']) === 32 && $user->CheckPWDCrypt($_REQUEST['passcrypt']))
    {
      @SESSION_unset();
      @SESSION_destroy();
      session_start();
      $_SESSION['uid'] = $user->ID;
      $_SESSION['browser'] = $_SERVER['HTTP_USER_AGENT'];
      $_SESSION['access'] = 'allow';
      $_SESSION['passcrypt'] = $_REQUEST['passcrypt'];
      die('GRANTED');
    }
    die('DENIED');
  }
  //o-------------------------------------------------------------------------------o
  else if ($_CFG_AUTH_METHOD == 'HTACCESS')
  //o-------------------------------------------------------------------------------o
  {
    echo "HTACCESS Auth Method requested for user: " . $_SERVER['PHP_AUTH_USER'] . "<br/>";
    $user = new myJAM_User($_SERVER['PHP_AUTH_USER']);
    if (!is_a($user, 'myJAM_User') || !is_scalar($user->ID) || $user->ID < 1)
    {
      ?>
        <script type="text/javascript">
        window.location.href="RegisterUser.php";
        </script>
      <?php
      die();
    }
    @SESSION_unset();
    @SESSION_destroy();
    session_start();
    $_SESSION['uid'] = $user->ID;
    $_SESSION['browser'] = $_SERVER['HTTP_USER_AGENT'];
    $_SESSION['access'] = 'allow';
  }
}
