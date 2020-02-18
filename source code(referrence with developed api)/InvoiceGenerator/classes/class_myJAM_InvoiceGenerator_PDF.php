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

require_once(_FULLPATH.'/InvoiceGenerator/classes/class_myJAM_InvoiceGenerator_Abstract.php');
require_once(_FULLPATH.'/includes/fpdf.php');
//o-------------------------------------------------------------------------------o
//o-------------------------------------------------------------------------------o
class PDF extends FPDF
//o-------------------------------------------------------------------------------o
//o-------------------------------------------------------------------------------o
{
  private $footer = '';
  public $yFF = NULL;
  //o-------------------------------------------------------------------------------o
  function Footer()
  //o-------------------------------------------------------------------------------o
  {
    $this->SetFont('Arial','',8);
    $this->SetXY(20.0, -15);
    $this->MultiCell(170.0, 4.5, $this->footer, 0, 'C', 0.0);
  }
  //o-------------------------------------------------------------------------------o
  public function SetFooter($str)
  //o-------------------------------------------------------------------------------o
  {
    $this->footer = $str;
  }
  //o-------------------------------------------------------------------------------o
  public function my_line()
  //o-------------------------------------------------------------------------------o
  {
    $y = $this->GetY();
    $this->Line(25.0, $y+0.35, 180.0, $y+0.35);
    $this->SetY($y + 0.7);
  }
  //o-------------------------------------------------------------------------------o
  protected function FF()
  //o-------------------------------------------------------------------------------o
  {
    $this->SetY($this->GetY() + $this->yFF);
  }
}
//o-------------------------------------------------------------------------------o
//o-------------------------------------------------------------------------------o
class myJAM_InvoiceGenerator_PDF extends myJAM_InvoiceGenerator_Abstract
//o-------------------------------------------------------------------------------o
//o-------------------------------------------------------------------------------o
{
  private $pdf;
  //o-------------------------------------------------------------------------------o
  public function MagicWrite($x=0, $y=4.21, $str, $align='', $OffSetX=0)
  //o-------------------------------------------------------------------------------o
  {
    $this->pdf->yFF = $y;
    $Lines = explode("\n", $this->ExtractTags($str));
    foreach($Lines as $line)
    {
      $this->pdf->SetX($OffSetX);
      if($line == '%%HLINE%%')
      {
        $this->pdf->my_line();
        $this->pdf->FF();
      }
      else if($line == '%%USERLIST%%')
      {
        $this->pdf->my_line();
        $this->pdf->my_line();
        $this->pdf->SetX($OffSetX);
        $this->pdf->SetFont('Times','B',12);
        $this->pdf->MultiCell($x, $y, 'User', 0, 'L', 0);
        $this->pdf->SetY($this->pdf->GetY()-$y);
        $this->pdf->SetX(140.0);
        $this->pdf->cell(40.0, $y, 'SUs', 0, 'R', 0);
        $this->pdf->Ln();
        $this->pdf->SetFont('Times','',12);
        $this->pdf->my_line();
        $UserList = $this->Invoice->UserListData;
        foreach($UserList as $username => $data)
        {
          $this->UserListReplace = array($username,
                                         $this->Invoice->UserListData[$username]['FullName']);
          $line = str_replace($this->UserListSearch, $this->UserListReplace, $this->InvoiceTemplate['UserList_Entry']);
          $this->pdf->SetX($OffSetX);
          $this->pdf->MultiCell($x, $y, $this->encode($line), 0, $align, 0);
          $this->pdf->SetY($this->pdf->GetY()-$y);
          $this->pdf->SetX(140.0);
          $this->pdf->cell(40.0, $y, sprintf("%.2f",$this->Invoice->UserListData[$username]['SUs']), 0, 1, 'R');
        }
        $this->pdf->my_line();
        $this->pdf->my_line();
      }
      else
      {
        $this->pdf->MultiCell($x, $y, $this->encode($line), 0, $align, 0);
      }
    }
  }
  //o-------------------------------------------------------------------------------o
  protected function RenderInvoice($project)
  //o-------------------------------------------------------------------------------o
  {
    $this->pdf->SetFooter($this->InvoiceTexts['Footer']);
    $this->pdf->AddPage();
    $this->pdf->SetFont('Times','B',8);
    //o-------------------------------------------------------------------------------o
    $this->pdf->Line(10.0, 99.0, 13.0, 99.0);
    $this->pdf->Line(11.0, 205.0, 14.0, 205.0);
    //o-------------------------------------------------------------------------------o
    $this->pdf->Line(18.5, 42.0, 19.5, 42.0);
    $this->pdf->Line(18.5, 42.0, 18.5, 43.0);
    $this->pdf->Line(103.5, 42.0, 102.5, 42.0);
    $this->pdf->Line(103.5, 42.0, 103.5, 43.0);
    $this->pdf->Line(18.5, 85.0, 19.5, 85.0);
    $this->pdf->Line(18.5, 85.0, 18.5, 84.0);
    $this->pdf->Line(103.5, 85.0, 102.5, 85.0);
    $this->pdf->Line(103.5, 85.0, 103.5, 84.0);
    //Address / Header
    $this->pdf->SetFont('Times','B',12);
    $this->pdf->SetXY(20.0, 10.0);
    $this->MagicWrite(170.0, 4.5, $this->InvoiceTexts['Address'], 'C', 20.0);
    //Invoice Recipient
    $this->pdf->SetXY(25.0, 45.0);
    $this->pdf->SetFont('Times','',12);
    $this->MagicWrite(73.0, 5, $project->InvoiceAddress, 'L', 25.0);
    //Invoice Date
    $this->pdf->SetXY(0.0, 92.0);
    $this->pdf->SetFont('Times','B',12);
    $this->pdf->cell(0.0, 6, date('d.m.Y'), 0, 1, 'R');
    $this->pdf->SetXY(25.0, 114.0);
    $this->pdf->SetFont('Times','B',12);
    $this->MagicWrite(0, 5, $this->InvoiceTexts['Subject'], 'L', 25.0);
    $this->pdf->Ln();
    $this->pdf->Ln();
    $this->pdf->SetFont('Times','',12);
    $this->pdf->SetX(25.0);
    $this->MagicWrite(0, 4.75, $this->InvoiceTexts['Body'], '', 25.0);
  }
  //o-------------------------------------------------------------------------------o
  protected function PreGenerating()
  //o-------------------------------------------------------------------------------o
  {
    $this->pdf = new PDF('P', 'mm', 'A4');
    $this->pdf->SetCreator('Generated with <myJAM/> by The <myJAM/>-Team, Heinrich-Heine-Universität Düsseldorf');
    $this->pdf->SetSubject('<myJAM/> Invoice');
    $this->pdf->SetTitle('<myJAM/> Invoice');
    $this->pdf->SetDisplayMode('fullpage', 'single');
    $this->pdf->SetMargins(0.0, 0.0, 25.0);
  }
  //o-------------------------------------------------------------------------------o
  protected function PostGenerating()
  //o-------------------------------------------------------------------------------o
  {
    if($this->stdout)
    {
      $this->pdf->Output();
    }
    else
    {
      $this->pdf->Output($this->outputFileName.'.pdf', 'F');
    }
  }
}
