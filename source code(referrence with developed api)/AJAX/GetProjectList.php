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

define('_FULLPATH', realpath(dirname(__FILE__).'/../'));
$ActiveUser = NULL;
require_once (_FULLPATH . '/access.php');
if(!$ActiveUser->ADMIN)
{
  die('myJAM>> ACCESS ERROR. You are not allowed to access this view.');
}
require_once(_FULLPATH.'/helper/myJAM_Paginator/myJAM_Paginator_Abstract.php');
class ProjectList extends myJAM_Paginator_Abstract
{
  protected static $descr_cut = 100;
  //o-------------------------------------------------------------------------------o
  public function __construct($tablename)
  //o-------------------------------------------------------------------------------o
  {
    parent::__construct($tablename);
  }
  //o-------------------------------------------------------------------------------o
  protected function genRow($row)
  //o-------------------------------------------------------------------------------o
  {
    $out = '<tr style="background:#';
    if ($this->row_even)
    {
      $out .= 'ffffff';
    }
    else
    {
      $out .= 'ffe8d4';
    }
    $out .= ';">'
            . '<td>'
              . '<img src="images/Project-16x16.png"/>&nbsp;'
              . '<a class="pagtable"' .
                  ' href="main.php?page=projects&admin&mode=info&pid=' . $row['pid'] . '">'
                . $row['name']
              . '</a>'
            . '</td>'
            . '<td>';
    if (strlen($row['description']) > self::$descr_cut)
    {
      $out .= '<acronym title="' . htmlentities($row['description']) . '">'
              . htmlentities(substr($row['description'], 0, self::$descr_cut)) . "..." . '</acronym>';
    }
    else
    {
      $out .= htmlentities($row['description']);
    }
    $out .= '<td>'
            . '<img src="images/user-16x16.png"/>'
            . htmlentities($row['Owner'])
          . '</td>'
          . '<td style="text-align:right;" >'
            . sprintf('%.2f', $row['su'])
          . '</td>';
    return $out
          .'</tr>';
  }
  //o-------------------------------------------------------------------------------o
  protected function addAjaxSearch()
  //o-------------------------------------------------------------------------------o
  {
    if(!empty($this->AjaxSearchString))
    {
      $statement = '('
                  ."name LIKE '%$this->AjaxSearchString%'"
                  .' OR '
                  ."description LIKE '%$this->AjaxSearchString%'"
                  .' OR '
                  ."Owner LIKE '%$this->AjaxSearchString%'"
                  .')';
      $this->addWhere($statement);
    }
  }
}
//o-------------------------------------------------------------------------------o
//o-------------------------------------------------------------------------------o
//o-------------------------------------------------------------------------------o
$SelectedUser = NULL;
$ProjectList = new ProjectList('ProjectDetails');
if($ProjectList->request->user !== NULL)
{
  if(empty($ProjectList->request->user))
  {
    $SelectedUser = $ActiveUser;
  }
  else
  {
    $SelectedUser = new myJAM_User($ProjectList->request->user);
  }
}
if($ProjectList->request->admin !== NULL)
{
  if(!$ActiveUser->ADMIN)
  {
    die('You are not an admin...<br/>');
  }
}
else
{
  if($SelectedUser == NULL)
  {
    die('You are not allowed to see this...<br/>');
  }
}
$ProjectList->addColumn(
                      array(
                             'pid' => '',
                             'name' => 'Name',
                             'description' => 'Description',
                             'Owner' => 'Owner',
                             'su' => 'Service Units',
                           )
                    );
if($SelectedUser)
{
  $ProjectList->addWhere('proj_uid_owner='.$SelectedUser->ID);
}
$ProjectList->addOrderReplacement('Owner', 'Owner_lastname');
echo $ProjectList->genPageIndex();
echo $ProjectList->genTable(NULL, true);
