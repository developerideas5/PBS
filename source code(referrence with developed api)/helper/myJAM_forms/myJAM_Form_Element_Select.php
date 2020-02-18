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

require_once(_FULLPATH.'/helper/myJAM_forms/myJAM_Form_Element_Abstract.php');
class myJAM_Form_Element_Select extends myJAM_Form_Element_Abstract
{
  private $options = array();
  private $preselect = NULL;
  private $size = NULL;
  private $multiple = NULL;
  //o-------------------------------------------------------------------------------o
  public function __construct($name, $options = NULL, $preselect = NULL, $size = NULL, $multiple = NULL)
  //o-------------------------------------------------------------------------------o
  {
    parent::__construct($name);
    if(!empty($options))
    {
      $this->addOptions($options);
    }
    if($preselect)
    {
      $this->setPreselected($preselect);
    }
    if($size)
    {
      $this->setSize($size);
    }
    if($multiple)
    {
      $this->setMultiple();
    }
  }
  //o-------------------------------------------------------------------------------o
  public function addOptions($options = NULL)
  //o-------------------------------------------------------------------------------o
  {
    if(!is_array($options))
    {
      throw new Exception('Associative array expected!');
    }
    foreach($options as $val => $opt)
    {
      if(isset($val) && is_scalar($val) &&
         !empty($opt) && is_string($opt))
      {
        $this->options[$val] = $opt;
      }
      else
      {
        throw new Exception("Illegal option '$val' => '$opt'");
      }
    }
    return $this;
  }
  //o-------------------------------------------------------------------------------o
  public function setPreselected($preselect = NULL)
  //o-------------------------------------------------------------------------------o
  {
    if(is_scalar($preselect))
    {
      $this->preselect = $preselect;
    }
    else
    {
      throw new Exception('Illegale value: '.$preselect);
    }
    return $this;
  }
  //o-------------------------------------------------------------------------------o
  public function setSize($size = NULL)
  //o-------------------------------------------------------------------------------o
  {
    if(!empty($size) && is_int($size) && (int)$size > 0)
    {
      $this->size = (int)$size;
    }
    else
    {
      throw new Exception('Illegale value: '.$size);
    }
    return $this;
  }
  //o-------------------------------------------------------------------------------o
  public function setMultiple()
  //o-------------------------------------------------------------------------------o
  {
    $this->multiple = true;
    return $this;
  }
  //o-------------------------------------------------------------------------------o
  public function unsetMultiple()
  //o-------------------------------------------------------------------------------o
  {
    $this->multiple = NULL;
    return $this;
  }
  //o-------------------------------------------------------------------------------o
  public function render()
  //o-------------------------------------------------------------------------------o
  {
    $out = '<select class="usersettings"'
                 .' name="'.$this->el_name.'"'
                 .' id="'.$this->el_name.'"';
    if(!empty($this->size))
    {
      $out .= ' size="'.(int)$this->size.'"';
    }
    if($this->multiple)
    {
      $out .= ' multiple="multiple"';
    }
    $out .= $this->_javaHooks()
           .$this->_activityState()
           .'>';
    foreach($this->options as $val => $opt)
    {
      $out .= '<option'
             .' value="'.$val.'"';
      if($this->preselect)
      {
        if($this->preselect == $val || $this->preselect == $opt)
        {
          $out .= ' selected="selected"';
        }
      }
      $out .= '>'
               .$opt
             .'</option>';
    }
    $out .= '</select>';
    return $out;
  }
}
