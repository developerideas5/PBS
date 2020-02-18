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

require_once (_FULLPATH . '/classes/class_myJAM_Abstract_Job.php');
abstract class myJAM_PBSoid_Job extends myJAM_Abstract_Job
{
  //o-------------------------------------------------------------------------------o
  public function __construct ()
  //o-------------------------------------------------------------------------------o
  {
    parent::__construct();
    $this->_checkCommands(array('qstat', 'pbsnodes'));
  }
  //o-------------------------------------------------------------------------------o
  protected abstract function getJobArgs ($key, $val);
  //o-------------------------------------------------------------------------------o
  //o-------------------------------------------------------------------------------o
  public function GetJob ()
  //o-------------------------------------------------------------------------------o
  {
    $cmd = _CFG_BATCHSYSTEM_BINDIR.DIRECTORY_SEPARATOR.'qstat -f ' . $this->JobID;
    if (_CFG_BATCHSYSTEM_SERVER != "" &&
        _CFG_BATCHSYSTEM_SERVER != "localhost" &&
        _CFG_BATCHSYSTEM_SERVER != "127.0.0.1")
    {
      $cmd = 'ssh ' . _CFG_BATCHSYSTEM_SERVER . ' "' . $cmd . '"';
    }
    $pHandle = popen($cmd, 'r');
    $key = '';
    $val = '';
    while (false !== ($line = fgets($pHandle)))
    {
      $line = trim($line);
      if (FALSE === strpos($line, ' = '))
      {
        $val = $line;
      }
      else
      {
        list ($key, $val) = explode(' = ', $line);
      }
      $this->getJobArgs($key, $val);
    }
    pclose($pHandle);
  }
  //o-------------------------------------------------------------------------------o
  protected function _DiscoverQueues ()
  //o-------------------------------------------------------------------------------o
  {
    $buffy = array();
    if (!$this->_CmdOut('qstat -q', $buffy))
    {
      echo "myJAM_DiscoverQueues>> No queues found!\n";
      return NULL;
    }
    foreach ($buffy as $line)
    {
      if (empty($line) ||
          strpos($line, 'server:') !== false ||
          strpos($line, 'Queue') !== false ||
          strpos($line, '---') !== false)
      {
        continue;
      }
      $vFrags = preg_split('/\s+/', $line);
      if (count($vFrags) != 10)
      {
        continue;
      }
      $this->_UpdateQueue($vFrags[0]);
    }
  }
  //o-------------------------------------------------------------------------------o
  protected function _DiscoverNodes ()
  //o-------------------------------------------------------------------------------o
  {
    $buffy = array();
    if (!$this->_CmdOut('pbsnodes -s '.$this->Host->Name.' | grep -v =', $buffy))
    {
      echo "myJAM_DiscoverNodes>> No nodes found!\n";
      return NULL;
    }
    foreach ($buffy as $line)
    {
      $line = trim($line);
      if(!empty($line))
      {
        $this->_AddNode($line);
      }
//      $vFrags = preg_split('/\s+/', $line);
//      if (count($vFrags) == 2)
//      {
//        $this->_AddNode($vFrags[0]);
//      }
    }
  }
//o-------------------------------------------------------------------------------o
//o-------------------------------------------------------------------------------o
//o-------------------------------------------------------------------------------o
}
