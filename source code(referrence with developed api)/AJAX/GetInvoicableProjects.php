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
require_once(_FULLPATH.'/access.php');
require_once(_FULLPATH.'/classes/class_myJAM_DB.php');
require_once(_FULLPATH.'/classes/class_myJAM_Project.php');
require_once(_FULLPATH.'/classes/class_myJAM_Invoice.php');
//o-------------------------------------------------------------------------------o
function GenMonButton($pid)
//o-------------------------------------------------------------------------------o
{
  $db = new myJAM_DB();
  $sql = "SELECT sum(actual_su), UNIX_TIMESTAMP(date) FROM Jobs WHERE pid='".(int)$pid."' GROUP BY year(date),month(date) ORDER BY date DESC";
  $vMonths = $db->query($sql);
  if($db->num_rows() > 0)
  {
    echo '<select class="inputfont" id="InvoicePID'.$pid.'" size="1">';
    foreach($vMonths as $month)
    {
      $m = date('m', $month['UNIX_TIMESTAMP(date)']);
      $y = date('Y', $month['UNIX_TIMESTAMP(date)']);
      echo '<option value="'.$month['UNIX_TIMESTAMP(date)'].'">'.$m.'-'.$y;
      printf(' (%.2f SUs)', $month['sum(actual_su)']);
      $period = strtotime($y.'-'.$m.'-01');
      $sql = "SELECT * FROM Invoices WHERE pid='".(int)$pid."' AND period=FROM_UNIXTIME(".(int)$period.")";
      $res = $db->query($sql);
      if ($db->num_rows() == 1)
      {
        echo ' (X)';
      }
      echo '</option>';
    }
    echo '</select>';
  }
  else
  {
    echo 'no jobs';
  }
}
//o-------------------------------------------------------------------------------o
//o-------------------------------------------------------------------------------o
//o-------------------------------------------------------------------------------o
if(!isset($_POST['projtype']))
{
  die('myJAM>> FATAL ERROR: no type given!');
}
$ProjType = NULL;
switch($_POST['projtype'])
{
  case 'billable':
    $ProjType = 1;
    break;
  case 'free':
    $ProjType = -1;
    break;
  case 'all':
    $ProjType = 0;
    break;
  default:
    die('myJAM>> FATAL ERROR: unknown type!');
}
echo
 '<table class="transmaintable">'
  .'<tr>'
    .'<td class="transhead">Project</td>'
    .'<td class="transhead">SUs</td>'
    .'<td class="transhead">Recalculate SUs</td>'
    .'<td class="transhead">Last Invoice</td>'
    .'<td class="transhead">Invoice Period</td>'
    .'<td class="transhead">Generate Invoice</td>'
  .'</tr>';
$ProjectList = new myJAM_Project();
$ProjIDList = $ProjectList->ID;
foreach($ProjIDList as $pid)
{
  $Project = new myJAM_Project($pid);
  if( ($ProjType == 0) ||
      ($ProjType == 1 && $Project->Billable) ||
      ($ProjType == -1 && !$Project->Billable))
  {
    echo '<tr>'
          .'<td><a href=\'main.php?page=projects&mode=info&pid='.$pid.'>' . $Project->Name . '</a></td>';
    $SUs = $Project->SUs;
    if ($SUs >= 0)
    {
//      printf("<td style=\"color:#000000;text-align:right;\">%.2f</td>", $SUs);
      $color = '#000000';
    }
    else
    {
//      printf("<td style=\"color:#ff0000;text-align:right;\">%.2f</td>", $SUs);
      $color = '#ff0000';
    }
    printf("<td style=\"color:%s;text-align:right;\">%.2f</td>", $color, $SUs);
    echo '<td style="text-align:center;">'
          .'<input class="inputfont" type="button" value=" ReCalc SUs " onclick="ReCalcSUs(' . $pid . ')" />'
        .'</td>';
    $LastInvoice = $Project->LastInvoice;
    if ($LastInvoice)
    {
      echo '<td style="text-align:right;">'
             .date('d.m.Y', $LastInvoice->Date).
           '</td>';
    }
    else
    {
      echo '<td style="text-align:center;">---</td>';
    }
    echo '<td style="text-align:center;">';
    GenMonButton($pid);
    echo '</td>';
    echo '<td style="text-align:center;">'
          .'<input class="inputfont" type="button" value=" Gen Invoice " onclick="GenInvoice(' . $pid . ');" /></td>'
        .'</tr>';
    unset($LastInvoice);
  }
}
echo '</table>';
