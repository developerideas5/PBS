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
class myJAM_InvoiceGenerator_TXT extends myJAM_InvoiceGenerator_Abstract
{
  private $width = 80;
  private $content = '';
  //o-------------------------------------------------------------------------------o
  private function line()
  //o-------------------------------------------------------------------------------o
  {
    $this->content .= str_repeat('-', $this->width)."\n";
  }
  //o-------------------------------------------------------------------------------o
  private function write_table($fields)
  //o-------------------------------------------------------------------------------o
  {
    $this->line();
    $line = false;
    foreach($fields as $row)
    {
      $this->content .= $row[0]
                       .str_repeat(' ', $this->width - strlen($row[0]) - strlen($row[1]))
                       .$row[1]
                       ."\n";
      if(!$line)
      {
        $this->line();
        $line = true;
      }
    }
    $this->line();
  }
  //o-------------------------------------------------------------------------------o
  private function write($str)
  //o-------------------------------------------------------------------------------o
  {
    $Words = explode(' ', $str);
    $pos = 0;
    foreach($Words as $word)
    {
      $nextpos = $pos + strlen($word);
      if($nextpos < $this->width)
      {
        if($pos > 0)
        {
          $this->content .= ' ';
          $nextpos++;
        }
        $this->content .= $word;
        $pos = $nextpos;
      }
      else
      {
        $this->content .= "\n" . $word;
        $pos = strlen($word);
      }
    }
  }
  //o-------------------------------------------------------------------------------o
  private function MagicWrite($str)
  //o-------------------------------------------------------------------------------o
  {
    $Lines = explode("\n", $this->ExtractTags($str));
    foreach($Lines as $line)
    {
      if($line == '%%HLINE%%')
      {
        $this->line();
      }
      else if($line == '%%USERLIST%%')
      {
        $table = array(
                        array('User', 'SUs')
                      );
        foreach($this->Invoice->UserListData as $username => $data)
        {
          $this->UserListReplace =
                             array($username,
                                   $this->Invoice->UserListData[$username]['FullName']
                                  );
          $line = str_replace($this->UserListSearch, $this->UserListReplace, $this->InvoiceTemplate['UserList_Entry']);
          $row = array($line, sprintf('%.2f',$this->Invoice->UserListData[$username]['SUs']));
          $table[] = $row;
        }
        $this->write_table($table);
      }
      else
      {
        $this->write($line."\n");
      }
    }
  }
  //o-------------------------------------------------------------------------------o
  protected function RenderInvoice($project)
  //o-------------------------------------------------------------------------------o
  {
    $this->line();
    $this->MagicWrite($this->InvoiceTexts['Address']);
    $this->MagicWrite($project->InvoiceAddress);
    $this->MagicWrite($this->InvoiceTexts['Subject']);
    $this->MagicWrite($this->InvoiceTexts['Body']);
    $this->content .= "\n\n";
    $this->MagicWrite($this->InvoiceTexts['Footer']);
    $this->line();
  }
  //o-------------------------------------------------------------------------------o
  protected function PreGenerating()
  //o-------------------------------------------------------------------------------o
  {
    $this->content = '';
  }
  //o-------------------------------------------------------------------------------o
  protected function PostGenerating()
  //o-------------------------------------------------------------------------------o
  {
    if($this->stdout)
    {
      echo $this->content;
    }
    else
    {
      $hFile = fopen($this->outputFileName.'.txt', 'w');
      fwrite($hFile, $this->content);
      fclose($hFile);
    }
  }
}
