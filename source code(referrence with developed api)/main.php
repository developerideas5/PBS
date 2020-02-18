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

//$BENCHMARK_START_TIME = microtime(True);
@ini_set('memory_limit', '16M');
define('_FULLPATH', realpath(dirname(__FILE__)));
require_once(_FULLPATH.'/config/CFG_modules.php');
require_once(_FULLPATH.'/access.php');
require_once(_FULLPATH.'/version_control.php');
require_once(_FULLPATH.'/templateheader.php'); //loads template navigation
if (!($pagefile = $_GET['page']))
{
  $pagefile = 'welcome';
}
$sitemap = array(
  'welcome' => 'Welcome '.$ActiveUser->FullName,
  'projects' => 'Projects',
 'projectlist' => 'Project List',
  'user-settings' => 'User Settings',
  'history' => 'History',
  'queues' => 'Queue Info',
  'clusterstatus' => 'Cluster Status',
  'user-list' => 'User List',
  'admin-groups' => 'Groups',
  'admin-finances' => 'Finances',
  'admin-exit-status' => 'Exit Status Modification',
  'admin-costs' => 'Cost Models',
  'cluster-history' => 'Cluster History',
  'announcements' => 'Announcements',
  'in_voice_templates' => 'Invoice Templates',
  'invoice' => 'Invoice',
  'admin-config' => 'Configuration',
  'admin-departments' => 'Admin Departments',
  'admin-institutes' => 'Admin Institutes',
  'admin-groups-show' => 'Admin Groups -> Show',
  'admin-groups-add' => 'Admin Groups -> Add',
  'admin-groups-edit' => 'Admin Groups -> Edit',
  'admin-groups-del' => 'Admin Groups -> Delete',
  'delete_host' => 'Delete Host',
  'db_details' => 'Database Details',
  'credits' => 'Credits'
);
if (!$title = $sitemap[$pagefile])
{
  die('myJAM>> Page does not exist');
}
  include(_FULLPATH.'/title.php');
  include(_FULLPATH.'/tools.php');
switch ($pagefile)
{
  case 'admin-groups-del':
    include ('includes/in_admin-groups-del.php');
    break;
  case 'admin-groups-add':
    include ('includes/in_admin-groups-add.php');
    break;
   case 'admin-groups-edit':
    include ('includes/in_admin-groups-edit.php');
    break;
  case 'admin-groups-show':
    include ('includes/in_admin-groups-show.php');
    break;
  case 'admin-nodes-del':
    include ('includes/in_admin-nodes-del.php');
    break;
  case 'admin-nodes-add':
    include ('includes/in_admin-nodes-add.php');
    break;
   case 'admin-nodes-edit':
    include ('includes/in_admin-nodes-edit.php');
    break;
  case 'admin-nodes-show':
    include ('includes/in_admin-nodes-show.php');
    break;
  case 'welcome':
    include ('includes/in_welcome.php');
    break;
  case 'projects':
    include ('includes/in_projects.php');
    break;
  case 'projectlist':
    include ('includes/in_projectlist.php');
    break;
  case 'user-settings':
    include ('includes/in_user-settings.php');
    break;
  case 'calc':
    include ('includes/in_calc.php');
    break;
  case 'history':
    include ('includes/in_history.php');
    break;
  case 'queues':
    include ('includes/in_queues.php');
    break;
  case 'clusterstatus':
    include ('includes/in_clusterstatus.php');
    break;
  case 'user-list':
     include ('includes/in_userlist.php');
    break;
  case 'admin-finances':
    include ('includes/in_admin-finances.php');
    break;
  case 'admin-exit-status':
    include ('includes/in_admin-exit-status.php');
    break;
  case 'admin-costs':
    include ('includes/in_admin-costs.php');
    break;
  case 'cluster-history':
    include ('includes/in_cluster-history.php');
    break;
  case 'announcements':
    include ('includes/in_announcements.php');
    break;
  case 'credits':
    include ('includes/in_credits.php');
    break;
  case 'invoice':
    include ('includes/in_voice.php');
    break;
  case 'in_voice_templates':
    include ('includes/in_voice_templates.php');
    break;
  case 'admin-config':
    include ('includes/admin-config.php');
    break;
  case 'admin-departments':
    include ('includes/in_admin-departments.php');
    break;
  case 'admin-institutes':
    include ('includes/in_admin-institutes.php');
    break;
  case 'delete_host':
    include ('includes/delete_host.php');
    break;
  case 'db_details':
    include ('includes/in_db-details.php');
    break;
  default:
    die('myJAM>> FATAL ERROR 0x763a in MAIN!');
}
include(_FULLPATH.'/base.php');
