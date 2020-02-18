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

class myJAM_Message
{
  PROTECTED $_title;
  PROTECTED $_content;
//o-------------------------------------------------------------------------------o
  PUBLIC function __construct($title = NULL, $content = NULL)
//o-------------------------------------------------------------------------------o
  {
    if(!$title)
    {
      throw new Exception('No title given for myJAM_Message');
    }
    $this->_title = $title;
    if(!$content)
    {
      throw new Exception('No content given for myJAM_Message');
    }
    $this->_content = $content;
  }
//o-------------------------------------------------------------------------------o
  PUBLIC function __get($name)
//o-------------------------------------------------------------------------------o
  {
    switch($name)
    {
      case 'title':
      case 'Title':
        return $this->_title;
      case 'content':
      case "Content":
        return $this->_content;
      default:
        die("Attribute '$name' not known in myJAM_Message");
    }
    return NULL;
  }
}
