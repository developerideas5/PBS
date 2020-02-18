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

require_once(_FULLPATH.'/helper/myJAM_forms/myJAM_Form.php');
class myJAM_Form_Table extends myJAM_Form
{
  protected $el_labels = array();
  //o-------------------------------------------------------------------------------o
  public function addElement($element, $label = NULL)
  //o-------------------------------------------------------------------------------o
  {
    if(!is_a($element, 'myJAM_Form_Element_Abstract') &&
       !is_a($element, 'myJAM_Form_SubForm'))
    {
      throw new Exception('Not a Form_Element or a SubForm!');
    }
    $this->form_elements[] = $element;
    if(!empty($label))
    {
      $this->el_labels[$element->getName()] = $label;
    }
    else
    {
      if(method_exists($element, 'getName'))
      {
        $this->el_labels[$element->getName()] = $element->getName();
      }
    }
    return $this;
  }
  //o-------------------------------------------------------------------------------o
  protected function _formHeader()
  //o-------------------------------------------------------------------------------o
  {
    $out = parent::_formHeader()
          .'<table class="table1" cellspacing="5" cellpadding="3">';
    return $out;
  }
  //o-------------------------------------------------------------------------------o
  protected function _beginHidden()
  //o-------------------------------------------------------------------------------o
  {
    return '</table>';
  }
  //o-------------------------------------------------------------------------------o
  protected function _renderFormElement($element)
  //o-------------------------------------------------------------------------------o
  {
    $out = '<tr>'
            .'<td class="cell3"'
               .' id="tab_'.$element->getName().'"'
            .'>'
              .'<span class="fat">'
                .$this->el_labels[$element->getName()]
              .'</span>'
            .'</td>'
            .'<td>'
              .$element->render()
            .'</td>'
          .'</tr>';
    return $out;
  }
  //o-------------------------------------------------------------------------------o
  protected function _renderSubForm($subform)
  //o-------------------------------------------------------------------------------o
  {
    $out = '<tr>'
            .'<td colspan="2" class="usercenter">'
              .$subform->renderForm()
            .'</td>'
          .'</tr>';
    return $out;
  }
  //o-------------------------------------------------------------------------------o
  protected function _renderCaption($element)
  //o-------------------------------------------------------------------------------o
  {
    $out = '<tr>'
            .'<td colspan="2">'
              .$element->render()
            .'</td>'
          .'</tr>';
    return $out;
  }
  protected function _renderHiddenElement($element)
  {
    return $element->render();
  }
}
