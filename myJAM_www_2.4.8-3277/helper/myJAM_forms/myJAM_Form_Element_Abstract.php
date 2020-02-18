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

abstract class myJAM_Form_Element_Abstract
{
  protected $el_name = '';
  protected $java_hooks = array();
  protected $disabled = false;
  //o-------------------------------------------------------------------------------o
  public function __construct($name)
  //o-------------------------------------------------------------------------------o
  {
    $this->el_name = $name;
  }
  //o-------------------------------------------------------------------------------o
  public function getName()
  //o-------------------------------------------------------------------------------o
  {
    return $this->el_name;
  }
  //o-------------------------------------------------------------------------------o
  public function onkeyup($java_cmd)
  //o-------------------------------------------------------------------------------o
  {
    $this->java_hooks['onkeyup'] = $java_cmd;
  }
  //o-------------------------------------------------------------------------------o
  public function onblur($java_cmd)
  //o-------------------------------------------------------------------------------o
  {
    $this->java_hooks['onblur'] = $java_cmd;
  }
  //o-------------------------------------------------------------------------------o
  public function onchange($java_cmd)
  //o-------------------------------------------------------------------------------o
  {
    $this->java_hooks['onchange'] = $java_cmd;
  }
  //o-------------------------------------------------------------------------------o
  public function onfocus($java_cmd)
  //o-------------------------------------------------------------------------------o
  {
    $this->java_hooks['onfocus'] = $java_cmd;
  }
  //o-------------------------------------------------------------------------------o
  public function onselect($java_cmd)
  //o-------------------------------------------------------------------------------o
  {
    $this->java_hooks['onselect'] = $java_cmd;
  }
  //o-------------------------------------------------------------------------------o
  public function onclick($java_cmd)
  //o-------------------------------------------------------------------------------o
  {
    $this->java_hooks['onclick'] = $java_cmd;
  }
  //o-------------------------------------------------------------------------------o
  public function ondblclick($java_cmd)
  //o-------------------------------------------------------------------------------o
  {
    $this->java_hooks['ondblclick'] = $java_cmd;
  }
  //o-------------------------------------------------------------------------------o
  public function onmousedown($java_cmd)
  //o-------------------------------------------------------------------------------o
  {
    $this->java_hooks['onmousedown'] = $java_cmd;
  }
  //o-------------------------------------------------------------------------------o
  public function onmouseup($java_cmd)
  //o-------------------------------------------------------------------------------o
  {
    $this->java_hooks['onmouseup'] = $java_cmd;
  }
  //o-------------------------------------------------------------------------------o
  public function onmouseover($java_cmd)
  //o-------------------------------------------------------------------------------o
  {
    $this->java_hooks['onmouseover'] = $java_cmd;
  }
  //o-------------------------------------------------------------------------------o
  public function onmousemove($java_cmd)
  //o-------------------------------------------------------------------------------o
  {
    $this->java_hooks['onmousemove'] = $java_cmd;
  }
  //o-------------------------------------------------------------------------------o
  public function onmouseout($java_cmd)
  //o-------------------------------------------------------------------------------o
  {
    $this->java_hooks['onmouseout'] = $java_cmd;
  }
  //o-------------------------------------------------------------------------------o
  public function onkeypress($java_cmd)
  //o-------------------------------------------------------------------------------o
  {
    $this->java_hooks['onkeypress'] = $java_cmd;
  }
  //o-------------------------------------------------------------------------------o
  public function onkeydown($java_cmd)
  //o-------------------------------------------------------------------------------o
  {
    $this->java_hooks['onkeydown'] = $java_cmd;
  }
  //o-------------------------------------------------------------------------------o
  protected function _javaHooks()
  //o-------------------------------------------------------------------------------o
  {
    $out = '';
    if(!empty($this->java_hooks))
    {
      foreach($this->java_hooks as $hook => $java_cmd)
      {
        $out .= ' '.$hook.'="'.$java_cmd.'"';
      }
    }
    return $out;
  }
  //o-------------------------------------------------------------------------------o
  public function setDisabled()
  //o-------------------------------------------------------------------------------o
  {
    $this->disabled = true;
  }
  //o-------------------------------------------------------------------------------o
  public function setEnabled()
  //o-------------------------------------------------------------------------------o
  {
    $this->disabled = false;
  }
  //o-------------------------------------------------------------------------------o
  protected function _activityState()
  //o-------------------------------------------------------------------------------o
  {
    if($this->disabled)
    {
      return ' disabled="disabled"';
    }
  }
  //o-------------------------------------------------------------------------------o
  abstract public function render();
  //o-------------------------------------------------------------------------------o
}
