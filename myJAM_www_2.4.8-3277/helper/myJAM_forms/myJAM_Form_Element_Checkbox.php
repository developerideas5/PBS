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
class myJAM_Form_Element_Checkbox extends myJAM_Form_Element_Abstract
{
  /**
   *
   * Enter description here ...
   * @var unknown_type
   */
  protected $checked = NULL;
  /**
   *
   * Enter description here ...
   * @var unknown_type
   */
  protected $label = NULL;
  /**
   *
   * Enter description here ...
   * @var unknown_type
   */
  protected $value = NULL;
  /**
   *
   * Enter description here ...
   * @var unknown_type
   */
  protected $enabled = NULL;
  /**
   *
   * Enter description here ...
   * @param unknown_type $name
   * @param unknown_type $checked
   * @param unknown_type $label
   * @param unknown_type $enabled
   */
  //o-------------------------------------------------------------------------------o
  public function __construct($name, $checked = NULL, $label = NULL)
  //o-------------------------------------------------------------------------------o
  {
    parent::__construct($name);
    if($checked)
    {
      $this->check();
    }
    if($label)
    {
      $this->setLabel($label);
    }
  }
  /**
   *
   * Enter description here ...
   */
  //o-------------------------------------------------------------------------------o
  public function check()
  //o-------------------------------------------------------------------------------o
  {
    $this->checked = true;
    return $this;
  }
  /**
   *
   * Enter description here ...
   */
  //o-------------------------------------------------------------------------------o
  public function uncheck()
  //o-------------------------------------------------------------------------------o
  {
    $this->checked = false;
    return $this;
  }
//  /**
//   *
//   * Enter description here ...
//   */
//  //o-------------------------------------------------------------------------------o
//  public function enable()
//  //o-------------------------------------------------------------------------------o
//  {
//    $this->enabled = true;
//
//    return $this;
//  }
//
//
//
//  /**
//   *
//   * Enter description here ...
//   */
//  //o-------------------------------------------------------------------------------o
//  public function disable()
//  //o-------------------------------------------------------------------------------o
//  {
//    $this->enabled = false;
//
//    return $this;
//  }
  /**
   *
   * Enter description here ...
   * @param unknown_type $label
   * @throws Exception
   */
  //o-------------------------------------------------------------------------------o
  public function setLabel($label)
  //o-------------------------------------------------------------------------------o
  {
    if(!is_string($label))
    {
      throw new Exception('Not a string', '0xd649');
    }
    $this->label = $label;
    return $this;
  }
  /**
   * (non-PHPdoc)
   * @see myJAM_Abstract_Form_Element::setValue()
   */
  //o-------------------------------------------------------------------------------o
  public function setValue($val = NULL)
  //o-------------------------------------------------------------------------------o
  {
    if($val == true)
    {
      $this->check();
    }
    else
    {
      $this->uncheck();
    }
    return $this;
  }
  /**
   *
   * Enter description here ...
   * @param unknown_type $val
   */
  //o-------------------------------------------------------------------------------o
  public function setSubmitValue($val = NULL)
  //o-------------------------------------------------------------------------------o
  {
    if (!empty($val))
    {
      $this->value = htmlentities($val);
    }
  }
  /**
   * (non-PHPdoc)
   * @see myJAM_Abstract_Render::render()
   */
  //o-------------------------------------------------------------------------------o
  public function render()
  //o-------------------------------------------------------------------------------o
  {
    $out = '<input type="checkbox"'
                .' name="'.$this->el_name.'"'
                .' id="'.$this->el_name.'"'
                .' value="'.$this->value.'"'
                .$this->_activityState()
                .$this->_javaHooks();
    if($this->checked)
    {
      $out .= ' checked="checked"';
    }
//    if(!$this->enabled)
//    {
//      $out .= ' disabled="disabled"';
//    }
    $out .= '/>';
    if(!empty($this->label))
    {
      $out .= $this->label;
    }
    return $out;
  }
//o-------------------------------------------------------------------------------o
//o-------------------------------------------------------------------------------o
//o-------------------------------------------------------------------------------o
}
