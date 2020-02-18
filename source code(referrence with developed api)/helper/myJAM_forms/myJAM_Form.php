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

class myJAM_Form
{
  protected $action_link = NULL;
  protected $submit_method = 'POST';
  protected $form_elements = array();
  protected $form_name = NULL;
  //o-------------------------------------------------------------------------------o
  public function __construct($link, $name = NULL)
  //o-------------------------------------------------------------------------------o
  {
    $this->action_link = $link;
    if(!empty($name))
    {
      $this->form_name = $name;
    }
  }
  //o-------------------------------------------------------------------------------o
  public function addElement($element)
  //o-------------------------------------------------------------------------------o
  {
    if(is_a($element, 'myJAM_Form_Element_Abstract') ||
       is_a($element, 'myJAM_Form_SubForm'))
    {
      $this->form_elements[] = $element;
      return $this;
    }
    throw new Exception('Not a Form_Element!');
  }
  //o-------------------------------------------------------------------------------o
  public function getElement($el_name)
  //o-------------------------------------------------------------------------------o
  {
    foreach($this->form_elements as $element)
    {
      if($element->getName() == $el_name)
      {
        return $element;
      }
    }
    return NULL;
  }
  //o-------------------------------------------------------------------------------o
  public function setSubmitMethod($method)
  //o-------------------------------------------------------------------------------o
  {
    $method = strtoupper($method);
    if($method != 'GET' && $method != 'POST')
    {
      throw new Exception('Illegal submit method: '.$method);
    }
    $this->submit_method = $method;
    return $this;
  }
  //o-------------------------------------------------------------------------------o
  public function renderForm()
  //o-------------------------------------------------------------------------------o
  {
    $out = $this->_formHeader();
    foreach($this->form_elements as $element)
    {
      switch(get_class($element))
      {
        case 'myJAM_Form_SubForm':
          $out .= $this->_renderSubForm($element);
          break;
        case 'myJAM_Form_Element_Caption':
          $out .= $this->_renderCaption($element);
          break;
        case 'myJAM_Form_Element_Hidden':
          break;
        default:
          $out .= $this->_renderFormElement($element);
      }
    }
    $out .= $this->_beginHidden();
    foreach($this->form_elements as $element)
    {
      if(is_a($element, 'myJAM_Form_Element_Hidden'))
      {
        $out .= $this->_renderHiddenElement($element);
      }
    }
    $out .= $this->_endHidden();
    $out .= $this->_formFooter();
    return $out;
  }
  //o-------------------------------------------------------------------------------o
  protected function _formHeader()
  //o-------------------------------------------------------------------------------o
  {
    $out = '<form'
          .' action="'.$this->action_link.'"'
          .' method="'.strtolower($this->submit_method).'"';
    if(!empty($this->form_name))
    {
      $out .= ' id="'.$this->form_name.'"';
    }
    $out .= '>';
    return $out;
  }
  //o-------------------------------------------------------------------------------o
  protected function _formFooter()
  //o-------------------------------------------------------------------------------o
  {
    return '</form>';
  }
  //o-------------------------------------------------------------------------------o
  protected function _renderFormElement($element)
  //o-------------------------------------------------------------------------------o
  {
    return $element->render();
  }
  //o-------------------------------------------------------------------------------o
  protected function _renderSubForm($subform)
  //o-------------------------------------------------------------------------------o
  {
    return $subform->renderForm();
  }
  //o-------------------------------------------------------------------------------o
  protected function _beginHidden()
  //o-------------------------------------------------------------------------------o
  {
  }
  //o-------------------------------------------------------------------------------o
  protected function _endHidden()
  //o-------------------------------------------------------------------------------o
  {
  }
  //o-------------------------------------------------------------------------------o
  protected function _renderHiddenElement($element)
  //o-------------------------------------------------------------------------------o
  {
    return $this->_renderFormElement($element);
  }
  //o-------------------------------------------------------------------------------o
  protected function _renderCaption($element)
  //o-------------------------------------------------------------------------------o
  {
    return $this->_renderFormElement($element);
  }
}
