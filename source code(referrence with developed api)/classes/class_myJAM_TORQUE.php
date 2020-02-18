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

require_once(_FULLPATH.'/classes/class_myJAM_PBSoid_Job.php');
class myJAM_Torque extends myJAM_PBSoid_Job
{
  //o-------------------------------------------------------------------------------o
  protected function getJobArgs($key, $val)
  //o-------------------------------------------------------------------------------o
  {
    switch($key)
    {
      case 'Resource_List.walltime':
        $this->ReqWallTime = $this->_GetReqWallTime($val);
        break;
      case 'queue':
        $this->Queue = $this->_GetQueue($val);
        break;
      case 'Account_Name':
        $this->Project = $this->_GetProject($val);
        break;
      case 'server':
        $this->Host = $this->_GetHost($val);
        break;
      case 'euser':
        $this->User = $this->_GetUser($val);
        break;
      case 'exec_host':
        $this->ExecHosts .= trim(str_replace('+', '', preg_replace('/\/[0-9]+/', ' ', $val)));
        $this->nbCores = count(explode(' ', $this->ExecHosts));
        break;
      case 'resources_used.walltime':
        $this->UsedWalltime = $this->_GetReqWallTime($val);
        break;
    }
  }
//o-------------------------------------------------------------------------------o
//o-------------------------------------------------------------------------------o
//o-------------------------------------------------------------------------------o
}
