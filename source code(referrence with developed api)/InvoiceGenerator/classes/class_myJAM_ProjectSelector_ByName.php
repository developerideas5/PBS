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

require_once(_FULLPATH.'/InvoiceGenerator/classes/class_myJAM_ProjectSelector_Abstract.php');
require_once(_FULLPATH.'/classes/class_myJAM_Project.php');
class myJAM_ProjectSelector_ByName extends myJAM_ProjectSelector_Abstract
{
  protected $Projects = array();
  //o-------------------------------------------------------------------------------o
  PUBLIC function __construct($projname)
  //o-------------------------------------------------------------------------------o
  {
    if(is_scalar($projname) && is_string($projname))
    {
      $this->addProject($projname);
    }
    elseif(is_array($projname))
    {
      foreach($projname as $p)
      if(is_string($p))
      {
        $this->addProject($p);
      }
    }
    else
    {
      throw new Exception('Invalid datatype given!');
    }
    parent::__construct();
  }
  //o-------------------------------------------------------------------------------o
  private function addProject($projarg)
  //o-------------------------------------------------------------------------------o
  {
    $frags = explode(' ', $projarg);
    foreach($frags as $projname)
    {
      $Project = new myJAM_Project($projname);
      if(!is_a($Project, 'myJAM_Project') ||
         !is_scalar($Project->ID) ||
         (int)$Project->ID < 1)
      {
        throw new Exception('Invalid project name "'.$projname.'"');
      }
      $this->Projects[] = $Project;
    }
  }
  //o-------------------------------------------------------------------------------o
  PUBLIC function selectProjects()
  //o-------------------------------------------------------------------------------o
  {
    foreach($this->Projects as $project)
    {
      $this->vProjects[$project->ID] = $project;
    }
  }
}
