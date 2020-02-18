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
require_once(_FULLPATH.'/access.php');
require_once(_FULLPATH.'/classes/class_myJAM_User.php');
require_once(_FULLPATH.'/fxn/StatusBar.php');
require_once(_FULLPATH.'/includes/cmn_header.php');
require_once(_FULLPATH.'/CloseWindow.php');
require_once(_FULLPATH."/HTMLCreator.php");
if(isset($_GET["username"]))
{
  $User = new myJAM_User($_GET["username"]);
}
if(!is_object($User) || !is_scalar($User->ID) || (integer)$User->ID < 1)
{
  die("myJAM>> FATAL ERROR 0xa498 in UserInfo");
}
$head = HTMLCreator::mk_head(
     HTMLCreator::mk_title('User Info')
    .HTMLCreator::mk_link('','rel="shortcut icon" href="images/myJAM-icon.png" type="image/png"')
    .HTMLCreator::mk_link('','rel="stylesheet" type="text/css" href="css/style.css"')
    );
echo $head;
//$body = HTMLCreator::mk_body(
echo '<body class="apopbody">'.
HTMLCreator::mk_table(
HTMLCreator::mk_tr(
HTMLCreator::mk_td(
HTMLCreator::mk_table(
HTMLCreator::mk_tr(
HTMLCreator::mk_td(
HTMLCreator::mk_img('','src="images/multigradient_left.png" alt="gradientGFX"')
,'class="gradhead1"'
)
.HTMLCreator::mk_td(
HTMLCreator::mk_div('User Info','class="toolgrad3"')
,'class="gradhead4"'
)
.HTMLCreator::mk_td(
HTMLCreator::mk_img('','src="images/multigradient_middle.png" alt="gradientGFX"')
,'class="gradhead5"'
)
.HTMLCreator::mk_td(''
,'class="gradhead6"')
.HTMLCreator::mk_td(
HTMLCreator::mk_img('','src="images/multigradient_right.png" alt="gradientGFX"')
,'class="gradhead1"'
)
)
,'cellspacing="0" cellpadding="0" class="tabhead2"')
)
)
,'cellspacing="3" cellpadding="0" class="tabhead"')
.
HTMLCreator::mk_div(
HTMLCreator::mk_table(
HTMLCreator::mk_tr(
HTMLCreator::mk_td(HTMLCreator::mk_span('Username:','class="fat"'),'class="right"')
.HTMLCreator::mk_td($User->UserName,'class="leftie"')
).
HTMLCreator::mk_tr(
HTMLCreator::mk_td(HTMLCreator::mk_span('Real name:','class="fat"'),'class="right"')
.HTMLCreator::mk_td($User->FullName,'class="leftie"')
).
HTMLCreator::mk_tr(
HTMLCreator::mk_td(HTMLCreator::mk_span('eMail:','class="fat"'),'class="right"')
.HTMLCreator::mk_td($User->eMail,'class="leftie"')
)
,'class="full"'
)
.HTMLCreator::mk_hr('')
,'class="simpleborder"')
.
HTMLCreator::mk_div('','class="apopbody"')
;
//,'class="apopbody"'
//);
StatusBar($User->ID);
echo CloseWindow()
.'<body/>'
.'</html>';
