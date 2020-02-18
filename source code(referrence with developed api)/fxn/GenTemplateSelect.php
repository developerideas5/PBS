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

//o-------------------------------------------------------------------------------o
function GenTemplateSelect()
//o-------------------------------------------------------------------------------o
{
  $out = '<select size="1"'
        .' name="TemplSelect"'
        .' id="TemplSelect"'
        .' onchange="GetInvoiceTemplate(this.options[this.selectedIndex].value);"'
        .'>'
          .'<option selected value="0">--- Choose Template ---</option>';
  $db = new myJAM_db();
  $sql = 'SELECT id, Name FROM InvoiceTemplates';
  $vTempl = $db->query($sql);
  if($db->num_rows() > 0)
  {
    foreach($vTempl as $template)
    {
      $out .= '<option value="'.(int)$template['id'].'">'
             .htmlentities($template['Name'])
             .'</option>';
    }
  }
  $out .= '</select>';
  return $out;
}
