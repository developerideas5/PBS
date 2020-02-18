#!/usr/bin/php
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

define('_FULLPATH', realpath(dirname(__FILE__).'/../'));
require_once(_FULLPATH.'/classes/class_myJAM_User.php');
require_once(_FULLPATH.'/InvoiceGenerator/classes/class_myJAM_ProjectList.php');
require_once(_FULLPATH.'/InvoiceGenerator/classes/class_myJAM_InvoiceGenerator_Abstract.php');
//o-------------------------------------------------------------------------------o
//o-------------------------------------------------------------------------------o
//o-------------------------------------------------------------------------------o
//Define short command line options
$shortopt = 'h' # help
           .'d:' # date of the invoice(s)
           .'l' # generate for last month
           .'t:' # name of the Invoice Template
           .'m:' # username of the "maker" of the invoice
           .'a' # select all projects for generation
           .'f' # select free projects only for generation
           .'b' # select billable projects only for generation
           .'p:' # select project by name
           .'u:' # select project(s) where user "u" is a member
           .'o:' # select project(s) where user "o" is owner
;
//Define long command line options
$longopt = array(
                  'help', # help
                  'date:', # date of the invoice(s)
                  'last_month', # generate for last month
                  'template:', # name of the Invoice Template
                  'maker:', # username of the "maker" of the invoice
                  'list_templates', # list all available templates
                  'all_projects', # select all projects for generation
                  'free_projects', # select free projects only for generation
                  'bill_projects', # select billable projects only for generation
                  'projects:', # space separted list of project names
                  'list_projects', # list all projects
                  'list_users', # list all users
                  'user:', # select project(s) where user "u" is a member
                  'owner:', # select project(s) where user "o" is owner
                  'txt', # Output to stdout. (Default)
                  'pdf', # PDF output
                  'stdout', # Output to stdout
                  'file:', # name out the output file
                  'utf8dec', # do a utf8_decode on the text
                  'utf8enc' # do a utf8_encode on the text
                );
$CLOptions = getopt($shortopt, $longopt);
$ProjectList = new myJAM_ProjectList();
$TemplateName = '';
$Date = '';
$MakeName = 'admin';
$last_month = false;
$run = true;
$outputformat = 'txt';
$outFileName = '';
$stdout = false;
$encoding = '';
// Now we go through all options and see what we have to do...
foreach($CLOptions as $key => $val)
{
  switch($key)
  {
    case 'a':
    case 'all_projects':
      require_once(_FULLPATH.'/InvoiceGenerator/classes/class_myJAM_ProjectSelector_All.php');
      $ProjectList->addSelector(new myJAM_ProjectSelector_All());
      break;
    case 'f':
    case 'free_projects':
      require_once(_FULLPATH.'/InvoiceGenerator/classes/class_myJAM_ProjectSelector_Free.php');
      $ProjectList->addSelector(new myJAM_ProjectSelector_Free());
      break;
    case 'b':
    case 'bill_projects':
      require_once(_FULLPATH.'/InvoiceGenerator/classes/class_myJAM_ProjectSelector_Billable.php');
      $ProjectList->addSelector(new myJAM_ProjectSelector_Billable());
      break;
    case 'p':
    case 'projects':
      require_once(_FULLPATH.'/InvoiceGenerator/classes/class_myJAM_ProjectSelector_ByName.php');
      $ProjectList->addSelector(new myJAM_ProjectSelector_ByName($val));
      break;
    case 'o':
    case 'owner':
      require_once(_FULLPATH.'/InvoiceGenerator/classes/class_myJAM_ProjectSelector_ByOwner.php');
      $ProjectList->addSelector(new myJAM_ProjectSelector_ByOwner($val));
      break;
    case 'u':
    case 'user':
      require_once(_FULLPATH.'/InvoiceGenerator/classes/class_myJAM_ProjectSelector_ByUser.php');
      $ProjectList->addSelector(new myJAM_ProjectSelector_ByUser($val));
      break;
    case 't':
    case 'template':
      $TemplateName = $val;
      break;
    case 'd':
    case 'date':
      $Date = $val;
      break;
    case 'l':
    case 'last_month':
      $last_month = true;
      break;
    case 'm':
    case 'maker':
      $MakeName = $val;
      break;
    case 'txt':
    case 'pdf':
      $outputformat = $key;
      break;
    case 'file':
      $outFileName = $val;
      $stdout = false;
      break;
    case 'stdout':
      $outFileName = NULL;
      $stdout = true;
      break;
    case 'utf8enc':
    case 'utf8dec':
      $encoding = $key;
      break;
    case 'list_users':
      require_once(_FULLPATH.'/CLIhelper/cli_listUsers.php');
      cli_listUsers();
      $run = false;
      break;
    case 'list_projects':
      require_once(_FULLPATH.'/CLIhelper/cli_listProjects.php');
      cli_listProjects();
      $run = false;
      break;
    case 'list_templates':
      require_once(_FULLPATH.'/CLIhelper/cli_listInvoiceTemplates.php');
      cli_listInvoiceTemplates();
      $run = false;
      break;
    case 'h':
    case 'help':
      require_once(_FULLPATH.'/InvoiceGenerator/InvoiceGenerator_Usage.php');
      die();
    default:
      die("Unknown keyword \"$key\". Try -h or --help for help");
  }
}
if(!$run)
{
  die("Nothing else to do...\n");
}
//Template given?
if(!isset($TemplateName) || empty($TemplateName))
{
  die("No template name given!\n");
}
//Maker?
$Maker = new myJAM_User($MakeName);
if(!is_a($Maker, 'myJAM_User') || !is_scalar($Maker->ID) || (int)$Maker->ID < 1)
{
  die('Maker is an unkown username!');
}
if(!$Maker->ADMIN)
{
  die('Maker is not an admin!');
}
$InvoiceGen = myJAM_InvoiceGenerator_Abstract::Factory($outputformat);
$InvoiceGen->setTemplateByName($TemplateName);
$InvoiceGen->setDate($Date, $last_month);
$InvoiceGen->setUser($Maker);
$InvoiceGen->setOutput($outFileName, $stdout);
$InvoiceGen->setEncoding($encoding);
$InvoiceGen->genInvoice($ProjectList);
