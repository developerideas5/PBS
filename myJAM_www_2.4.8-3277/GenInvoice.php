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
$ActiveUser = NULL;
require_once(_FULLPATH.'/access.php');
require_once(_FULLPATH.'/classes/class_myJAM_User.php');
require_once(_FULLPATH.'/InvoiceGenerator/classes/class_myJAM_ProjectList.php');
require_once(_FULLPATH.'/InvoiceGenerator/classes/class_myJAM_ProjectSelector_ByName.php');
require_once(_FULLPATH.'/InvoiceGenerator/classes/class_myJAM_InvoiceGenerator_Abstract.php');
if (! $ActiveUser->ADMIN)
{
  die('myJAM>> FATAL ERROR. ACCESS DENIED !!!');
}
//o-------------------------------------------------------------------------------o
//o-------------------------------------------------------------------------------o
//o-------------------------------------------------------------------------------o
$InvoiceGen = myJAM_InvoiceGenerator_Abstract::Factory('PDF');
$InvoiceGen->setUser($ActiveUser);
$InvoiceGen->setOutput(NULL, true);
//date
if (!isset($_GET['period']) || !is_numeric($_GET['period']) || (int)$_GET['period'] < 1)
{
  die('myJAM>> FATAL ERROR 0x3fa7 in GenInvoice!!!');
}
$period = (int)$_GET['period'];
$month = date('m', $period);
$year = date('Y', $period);
$InvoiceGen->setDate($year.'-'.$month.'-01', false);
//Get the Invoice Template
$InvoiceTemplate = NULL;
if(isset($_GET['tid']) && is_numeric($_GET['tid']) && (int)$_GET['tid'] > 0)
{
  $InvoiceGen->setTemplateByID((int)$_GET['tid']);
}
else
{
  die('myJAM>> FATAL ERROR: No Invoice Template given!');
}
$ProjectList = new myJAM_ProjectList();
//check given pid and instaciate project-object
if(isset($_GET['pid']) && is_numeric($_GET['pid']) && (int)$_GET['pid'] > 0)
{
  $Project = new myJAM_Project((int)$_GET['pid']);
  if(!is_object($Project) || !is_scalar($Project->ID))
  {
    die('myJAM>> FATAL ERROR 0x9bb0 in GenInvoice!!!');
  }
  $ProjectList->addSelector(new myJAM_ProjectSelector_ByName($Project->Name));
}
else
{
  die('myJAM>> FATAL ERROR 0x8022 in GenInvoice!!!');
}
//Generate it!
$InvoiceGen->genInvoice($ProjectList);
