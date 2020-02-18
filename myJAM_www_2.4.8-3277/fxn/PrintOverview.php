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

require_once(_FULLPATH.'/classes/class_myJAM_DB.php');
//o-------------------------------------------------------------------------------o
function PrintOverview()
//o-------------------------------------------------------------------------------o
{
  $db = new myJAM_db();
  $query = 'SELECT count(pbs_jobnumber), job_state, pid, uid FROM Jobs WHERE job_state IN(\'R\',\'Q\') GROUP BY job_state,pid,uid;';
  $vRes = $db->query($query);
  $nbRunning = 0;
  $nbQueued = 0;
  $nbUsers = array();
  $nbProjects = array();
  foreach($vRes as $row)
  {
    $nbJobs = (int)$row['count(pbs_jobnumber)'];
    if($row['job_state'] == 'Q')
    {
      $nbQueued += $nbJobs;
    }
    else
    {
      $nbRunning += $nbJobs;
    }
    $nbUsers[$row['uid']] = '';
    $nbProjects[$row['pid']] = '';
  }
  $nbUsers = count($nbUsers);
  $nbProjects = count($nbProjects);
  $out = '';
  //chip icon and iframe
  $out .= '<ul>'
          .'<li style="padding:4px">'
            .'<img style="margin-right:3px" src="images/Hardware-Chip-Cut.png" alt="HardwareChip"/>'
            .'<b>Architectures</b><br/>'
            .'<object data="cpuiframe.php" type="text/html" width="200" height="93"></object>'
          .'</li>'
  //running Jobs
          .'<li style="padding:4px">'
            .'<img style="margin-right:3px" src="images/Utilities-Cut.png" alt="Utilities"/>'
            .'<b>'.$nbRunning.' job'
            .($nbRunning>1 ? 's' : '')
            .' running</b>'
          .'</li>'
  //active Users
          .'<li style="padding:4px">'
            .'<img style="margin-right:3px;" src="images/user-32x32.png" alt="User"/>'
            .'<b>'.$nbUsers.' active user'
            .($nbUsers>1 ? 's' : '')
            .'</b>'
          .'</li>'
  //active Projects
          .'<li style="padding:4px">'
            .'<img style="margin-right:3px;" src="images/Project-32x32.png" alt="Project"/>'
            .'<b>'.$nbProjects.' active project'
            .($nbProjects>1 ? 's' : '')
            .'</b>'
          .'</li>'
        .'</ul>';
  return $out;
}
