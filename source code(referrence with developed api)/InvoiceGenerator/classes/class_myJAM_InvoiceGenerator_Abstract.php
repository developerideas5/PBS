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

require_once(_FULLPATH.'/classes/class_myJAM_DB.php');
require_once(_FULLPATH.'/classes/class_myJAM_User.php');
require_once(_FULLPATH.'/classes/class_myJAM_Invoice.php');
abstract class myJAM_InvoiceGenerator_Abstract
{
  protected $db = NULL;
  private $period = NULL;
  protected $Invoice = NULL;
  private $ActiveUser = NULL;
  protected $InvoiceTemplate = NULL;
  protected $InvoiceTexts = NULL;
  private $MetricsSearch = array();
  private $MetricsReplace = array();
  private $NamesSearch = array();
  private $NamesReplace = array();
  protected $UserListSearch = array();
  protected $UserListReplace = array();
  protected $outputFileName = '';
  protected $stdout = false;
  protected $encoding = '';
  //o-------------------------------------------------------------------------------o
  PUBLIC function __construct()
  //o-------------------------------------------------------------------------------o
  {
    $this->db = new myJAM_db();
    //Set the search-strings for the macro-replacements
    $this->MetricsSearch =
                 array('%%INVOICENO%%',
                       '%%NOW%%',
                       '%%STARTDATE%%',
                       '%%ENDDATE%%',
                       '%%USEDSUS%%',
                       '%%OLDSUS%%',
                       '%%NEWSUS%%',
                       '%%COSTNORMAL%%',
                       '%%COSTOVER%%',
                       '%%AMOUNT%%'
                      );
    $this->NamesSearch = array('%%PROJECTNAME%%',
                               '%%PROJECTOWNER%%');
    $this->UserListSearch = array('%%UL_USERNAME%%',
                                  '%%UL_FULLNAME%%');
  }
  //o-------------------------------------------------------------------------------o
  public function setTemplateByName($TemplName)
  //o-------------------------------------------------------------------------------o
  {
    if(empty($TemplName) || !is_string($TemplName))
    {
      throw new Exception('Invalid TemplateName given!');
    }
    $sql = 'SELECT * FROM InvoiceTemplates where Name=\''
           .mysql_real_escape_string($TemplName)
           .'\'';
    $vRes = $this->db->query($sql);
    if($this->db->num_rows() != 1)
    {
      throw new Exception('Error reading InvoiceTemplate from database');
    }
    $this->InvoiceTemplate = $vRes[0];
  }
  //o-------------------------------------------------------------------------------o
  public function setTemplateByID($TemplID)
  //o-------------------------------------------------------------------------------o
  {
    if(empty($TemplID) || !is_integer($TemplID))
    {
      throw new Exception('Invalid TemplateID given!');
    }
    $sql = 'SELECT * FROM InvoiceTemplates where id=\''
           .(int)$TemplID
           .'\'';
    $vRes = $this->db->query($sql);
    if($this->db->num_rows() != 1)
    {
      throw new Exception('Error reading InvoiceTemplate from database');
    }
    $this->InvoiceTemplate = $vRes[0];
  }
  //o-------------------------------------------------------------------------------o
  public function setDate($date, $last_month)
  //o-------------------------------------------------------------------------------o
  {
    //get epoche
    if($date == NULL || !isset($date) || empty($date))
    {
      $epoch = time();
    }
    else
    {
      $epoch = $this->parseDate($date);
    }
    //normalize date to the first of the month
    $date = '01.'.date('m.Y',$epoch);
    $epoch = strtotime($date);
    //last month?
    if($last_month)
    {
      $epoch = mktime(date('H',$epoch), date('i',$epoch), date('s',$epoch), date('m',$epoch)-1 , date('d',$epoch), date('Y',$epoch));
    }
    $this->period = $epoch;
  }
  //o-------------------------------------------------------------------------------o
  public function setUser($user)
  //o-------------------------------------------------------------------------------o
  {
    if(!is_a($user, 'myJAM_User') || !is_scalar($user->ID) || (int)$user->ID < 1)
    {
      throw new Exception('Invalid user!');
    }
    $this->ActiveUser = $user;
  }
  //o-------------------------------------------------------------------------------o
  public function setOutput($filename = '', $stdout = true)
  //o-------------------------------------------------------------------------------o
  {
    if($stdout)
    {
      $this->stdout = true;
      $this->outputFileName = NULL;
    }
    else
    {
      $this->stdout = false;
      if(!empty($filename) && is_string($filename))
      {
        $this->outputFileName = $filename;
      }
      else
      {
        $this->outputFileName = 'myjam_invoice_'.date('Y-m-d_').dechex((int)date('U'));
      }
    }
  }
  //o-------------------------------------------------------------------------------o
  public function setEncoding($encoding)
  //o-------------------------------------------------------------------------------o
  {
    if(!empty($encoding) && is_string($encoding))
    {
      $this->encoding = $encoding;
    }
  }
  //o-------------------------------------------------------------------------------o
  protected function encode($str)
  //o-------------------------------------------------------------------------------o
  {
    switch($this->encoding)
    {
      case 'utf8dec':
        return utf8_decode($str);
      case 'utf8enc':
        return utf8_encode($str);
    }
    return $str;
  }
  //o-------------------------------------------------------------------------------o
  protected function parseDate($date)
  //o-------------------------------------------------------------------------------o
  {
    //test if we can convert the given date...
    $d = strtotime($date);
    if($d > 0)
    {
      return $d;
    }
    // okay, perhaps it was something like '03.2010' for March 2010. Try adding and '01.'...
    $d = strtotime('01.'.$date);
    if($d > 0)
    {
      return $d;
    }
    //Hmm... Perhaps an american notation, e.g. '2010-03'...
    $d = strtotime($date.'-01');
    if($d > 0)
    {
      return $d;
    }
    throw new Exception('Could not interpretate given date: '.$date);
  }
  //o-------------------------------------------------------------------------------o
  protected function getInvoiceObj($Project)
  //o-------------------------------------------------------------------------------o
  {
    //check, if an invoice for this period has already been created once
    $sql = 'SELECT InvoiceID, UNIX_TIMESTAMP(date) FROM Invoices'
          ." WHERE pid='".(int)$Project->ID."'"
          ." AND period=FROM_UNIXTIME(".(int)$this->period.")";
    $res = $this->db->query($sql);
    switch($this->db->num_rows())
    {
      case 0:
        $copy = false;
        break;
      case 1:
        $copy = true;
        $invoicedate = $res[0]['UNIX_TIMESTAMP(date)'];
        $InvoiceID = (int)$res[0]["InvoiceID"];
        break;
      default:
        throw new Exception('Strange result while looking for Invoices in the database!');
    }
    if($copy)
    {
      $this->Invoice = new myJAM_Invoice($InvoiceID);
    }
    else
    {
      $this->Invoice = myJAM_Invoice::CreateInvoice($Project, $this->period, $this->ActiveUser);
    }
  }
  //o-------------------------------------------------------------------------------o
  protected function genMacros($Project)
  //o-------------------------------------------------------------------------------o
  {
    $this->MetricsReplace =
                  array($this->Invoice->ID,
                        date("d.m.Y"),
                        date("d.m.Y",$this->period),
                        date("t.m.Y",$this->period),
                        sprintf("%.2f",$this->Invoice->TotalSUs),
                        sprintf("%.2f",$this->Invoice->OldSUs),
                        sprintf("%.2f",$this->Invoice->actualSUs),
                        sprintf("%.2f",$Project->CostModel->Norm),
                        sprintf("%.2f",$Project->CostModel->Over),
                        'Amount of Money'
                       );
    $this->InvoiceTexts = str_replace($this->MetricsSearch, $this->MetricsReplace, $this->InvoiceTemplate);
    $this->NamesReplace = array($Project->Name,
                                $Project->Owner->FullName);
    $this->InvoiceTexts = str_replace($this->NamesSearch, $this->NamesReplace, $this->InvoiceTexts);
    $this->InvoiceTexts = str_replace('%%USERLIST%%', "%%USERLIST%%\n", $this->InvoiceTexts);
    $this->InvoiceTexts = str_replace('%%HLINE%%', "%%HLINE%%\n", $this->InvoiceTexts);
  }
  //o-------------------------------------------------------------------------------o
  protected function ExtractTags($str)
  //o-------------------------------------------------------------------------------o
  {
    $out = '';
    $Lines = explode("\n", $str);
    foreach($Lines as $line)
    {
      if($line != '%%HLINE%%' &&
         $line != '%%USERLIST%%')
      {
        $line = str_replace('%%HLINE%%', "\n%%HLINE%%", $line);
        $line = str_replace('%%USERLIST%%', "\n%%USERLIST%%", $line);
      }
      $out .= $line."\n";
    }
    return $out;
  }
  //o-------------------------------------------------------------------------------o
  abstract protected function RenderInvoice($project);
  //o-------------------------------------------------------------------------------o
  //o-------------------------------------------------------------------------------o
  abstract protected function PreGenerating();
  //o-------------------------------------------------------------------------------o
  //o-------------------------------------------------------------------------------o
  abstract protected function PostGenerating();
  //o-------------------------------------------------------------------------------o
  //o-------------------------------------------------------------------------------o
  PUBLIC function genInvoice($ProjectList)
  //o-------------------------------------------------------------------------------o
  {
    if(!is_a($ProjectList, 'myJAM_ProjectList'))
    {
      throw new Exception('Invalid project list opject');
    }
    $this->PreGenerating();
    foreach($ProjectList->getProjectList() as $project)
    {
      $this->getInvoiceObj($project);
      $this->genMacros($project);
      $this->RenderInvoice($project);
    }
    $this->PostGenerating();
  }
  //o-------------------------------------------------------------------------------o
  public function getDate()
  //o-------------------------------------------------------------------------------o
  {
    return $this->period;
  }
  //o-------------------------------------------------------------------------------o
  static function Factory($format)
  //o-------------------------------------------------------------------------------o
  {
    switch($format)
    {
      case 'txt':
      case 'TXT':
        require_once(_FULLPATH.'/InvoiceGenerator/classes/class_myJAM_InvoiceGenerator_TXT.php');
        return new myJAM_InvoiceGenerator_TXT();
      case 'pdf':
      case 'PDF':
        require_once(_FULLPATH.'/InvoiceGenerator/classes/class_myJAM_InvoiceGenerator_PDF.php');
        return new myJAM_InvoiceGenerator_PDF();
      default:
        throw new Exception('Unknown object type');
    }
  }
}
