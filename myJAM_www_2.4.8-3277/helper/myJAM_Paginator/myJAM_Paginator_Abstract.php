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

require_once (_FULLPATH . '/classes/class_myJAM_DB.php');
require_once (_FULLPATH . '/helper/myJAM_JSON/myJAM_JSON.php');
abstract class myJAM_Paginator_Abstract
{
  protected $db = NULL;
  protected $tablename = '';
  protected $columns = array();
  protected $widths = array();
  protected $where = array();
  protected $groupby = array();
  protected $orderby = array();
  protected $sortfield = NULL;
  protected $sortmode = NULL;
  protected $initialized = false;
  protected $sql = '';
  protected $results = NULL;
  protected $num_results = NULL;
  protected $num_pages = NULL;
  protected $max_results_per_page = 100;
  protected $max_page_index = 7;
  protected $row_even = NULL;
  protected $request = NULL;
  protected $AjaxSearchString = NULL;
  protected $OrderReplacement = array();
  //o-------------------------------------------------------------------------------o
  public function __construct($tablename)
  //o-------------------------------------------------------------------------------o
  {
    $this->db = new myJAM_db();
    if(empty($tablename) || !is_string($tablename))
    {
      throw new Exception('No table specified!');
    }
    $this->tablename = $tablename;
    if(!isset($_POST['q']))
    {
      throw new Exception('Illegal POST');
    }
    $this->request = new myJAM_JSON($_POST['q']);
    $select = new myJAM_JSON($this->request->select);
    foreach($select->getAssoc() as $field=>$value)
    {
      $this->addWhere($field.'=\''.mysql_real_escape_string($value).'\'');
    }
  }
  //o-------------------------------------------------------------------------------o
  public function __get($name)
   //o-------------------------------------------------------------------------------o
  {
    switch ($name)
    {
      case "request":
        return $this->request;
        break;
    }
    return NULL;
  }
  //o-------------------------------------------------------------------------------o
  public function addFixedWidth($widths)
  //o-------------------------------------------------------------------------------o
  {
    if(!is_array($widths))
    {
      throw new Exception('Argument must by an array!');
    }
    foreach($widths as $col_name => $width)
    {
      if(array_key_exists($col_name, $this->columns))
      {
        $this->widths[$col_name] = $width;
      }
      else
      {
        throw new Exception("Column '$col_name' not found");
      }
    }
    return $this;
  }
  //o-------------------------------------------------------------------------------o
  public function addColumn($cols)
  //o-------------------------------------------------------------------------------o
  {
    if(!is_array($cols))
    {
      throw new Exception('Argument must by an array!');
    }
    $this->initialized = false;
    foreach($cols as $col_name => $header_name)
    {
      $this->columns[$col_name] = $header_name;
    }
    return $this;
  }
  //o-------------------------------------------------------------------------------o
  public function addOrderReplacement($origColumn, $orderColumn)
  //o-------------------------------------------------------------------------------o
  {
    if(!key_exists($origColumn, $this->columns))
    {
      throw new Exception('Column "'.$origColumn.'" not found!');
    }
    $this->OrderReplacement[$origColumn] = $orderColumn;
    return $this;
  }
  //o-------------------------------------------------------------------------------o
  public function addWhere($where)
  //o-------------------------------------------------------------------------------o
  {
    $this->initialized = false;
    if(is_scalar($where))
    {
      $where = array($where);
    }
    foreach($where as $statement)
    {
      if(is_scalar($statement) && is_string($statement))
      {
        $this->where[] = "($statement)";
      }
    }
    return $this;
  }
  //o-------------------------------------------------------------------------------o
  public function addGroupBy($groupby)
  //o-------------------------------------------------------------------------------o
  {
    $this->initialized = false;
    if(is_scalar($groupby))
    {
      $groupby = array($groupby);
    }
    foreach($groupby as $statement)
    {
      if(is_scalar($statement) && is_string($statement))
      {
        $this->groupby[] = $statement;
      }
    }
    return $this;
  }
  //o-------------------------------------------------------------------------------o
  public function addOrderBy($orderby)
  //o-------------------------------------------------------------------------------o
  {
    $this->initialized = false;
    if(is_scalar($orderby))
    {
      $orderby = array($orderby);
    }
    foreach($orderby as $statement)
    {
      if(is_scalar($statement) && is_string($statement))
      {
        if(isset($this->OrderReplacement[$statement]))
        {
          $statement = $this->OrderReplacement[$statement];
        }
        $this->orderby[] = $statement;
      }
    }
    return $this;
  }
  //o-------------------------------------------------------------------------------o
  public function genPageIndex($PageNo = NULL)
  //o-------------------------------------------------------------------------------o
  {
    $this->init();
    if($PageNo == NULL)
    {
      $PageNo = (int)$this->request->pageno;
    }
    $PageNo = max(1, $PageNo);
    $PageNo = min($PageNo, $this->num_pages);
    if($PageNo > $this->num_pages || $PageNo < 1)
    {
      throw new Exception('Illegal page number!');
    }
    $out = '';
    if($this->num_results > 0)
    {
      $out .= '<p><div class="usercenter">'
             .'<b>Results: </b>'.(int)$this->num_results;
      if($this->num_results > $this->max_results_per_page)
      {
        $nbPages = (int)($this->num_results / $this->max_results_per_page) + 1;
        $MaxIndex = min($nbPages, $PageNo + (int)($this->max_page_index/2));
        $MinIndex = max(1, $MaxIndex - $this->max_page_index+1);
        $MaxIndex = min($nbPages, $MinIndex + $this->max_page_index-1);
        $out .= '&nbsp;&nbsp;&nbsp;'
               .'<span>'
               .'<a href="javascript:PAG_PageNo=1;PAG_GetTable();">&lt;&lt;</a>'
               .'&nbsp;&nbsp;';
        if($PageNo > 1)
        {
          $out .= '<a href="javascript:PAG_PageNo='.max(1, $PageNo-1).';PAG_GetTable();">&lt;</a>';
        }
        else
        {
          $out .= '<span style="color:#c0c0c0;"><b>&lt;</b></span>';
        }
        if($MinIndex > 1)
        {
          $out .= '&nbsp;&nbsp;'
                 .'<a href="javascript:PAG_PageNo='.max(1, $PageNo-$this->max_page_index).';PAG_GetTable();">...</a>';
        }
        for($i = $MinIndex; $i <= $MaxIndex; $i++)
        {
          $out .= '&nbsp;&nbsp;';
          if ($i == $PageNo)
          {
            $out .= '<span style="background:#c0c0c0">';
          }
          $out .= '<a href="javascript:PAG_PageNo='.$i.';PAG_GetTable();">'.$i.'</a>';
          if ($i == $PageNo)
          {
            $out .= '</span>';
          }
        }
        if($MaxIndex < $nbPages)
        {
          $out .= '&nbsp;&nbsp;'
                 .'<a href="javascript:PAG_PageNo='.min($nbPages, $PageNo+$this->max_page_index).';PAG_GetTable();">...</a>';
        }
        $out .= '&nbsp;&nbsp;';
        if ($PageNo < $nbPages)
        {
          $out .= '<a href="javascript:PAG_PageNo='.min($nbPages, $PageNo+1).';PAG_GetTable();">&gt;</a>';
        }
        else
        {
          $out .= '<span style="color:#c0c0c0;"><b>&gt;</b></span>';
        }
        $out .= '&nbsp;&nbsp;'
               .'<a href="javascript:PAG_PageNo='.$nbPages.';PAG_GetTable();">&gt;&gt;</a>'
               .'</span>'
               .'&nbsp;&nbsp;&nbsp;'
               .'<b>Pages: </b>'.$nbPages;
      }
    }
    else
    {
      $out .= '<b>No results...</b>';
    }
    $out .= '</div>';
    return $out;
  }
  //o-------------------------------------------------------------------------------o
  public function genTable($PageNo = NULL, $border = false)
  //o-------------------------------------------------------------------------------o
  {
    $this->init();
    if($PageNo == NULL)
    {
      $PageNo = (int)$this->request->pageno;
    }
    $PageNo = max(1, $PageNo);
    $PageNo = min($PageNo, $this->num_pages);
    $this->getResultsByPage($PageNo);
    $out = '<table class="full"';
    if($border)
    {
      $out .= ' style="border-style:solid;border-width:1px"';
    }
    $out .= '>'
           .$this->genHeader();
    $this->row_even = false;
    foreach($this->results as $row)
    {
      $out .= $this->genRow($row);
      $this->row_even = !$this->row_even;
    }
    $out .= '</table>';
    return $out;
  }
  //o-------------------------------------------------------------------------------o
  private function getSortField()
  //o-------------------------------------------------------------------------------o
  {
    if($this->request->sortfield !== NULL)
    {
      $this->sortfield = mysql_real_escape_string($this->request->sortfield);
    }
    else
    {
      $this->sortfield = NULL;
    }
    if($this->request->sortmode !== NULL && (int)$this->request->sortmode > 0)
    {
      $this->sortmode = true;
    }
    else
    {
      $this->sortmode = false;
    }
  }
  //o-------------------------------------------------------------------------------o
  private function genHeader()
  //o-------------------------------------------------------------------------------o
  {
    $out = '<tr>';
    foreach($this->columns as $col_name => $header_name)
    {
      if(!empty($header_name))
      {
        $sortflag = false;
        $arrow = '<span style="width:1em;display:block;float:left">';
        $out .= '<td style="';
        if(isset($this->widths[$col_name]))
        {
          $out .= 'width:'.$this->widths[$col_name].';';
        }
        $out .= 'background:#c0c0c0;';
        if($this->sortfield === $col_name)
        {
          $out .= 'color:#ff0000;';
          $sortflag = !$this->sortmode;
          if($this->sortmode)
          {
            $arrow .= '<b>&uarr;</b>';
          }
          else
          {
            $arrow .= '<b>&darr;</b>';
          }
        }
        else
        {
          $arrow .= '&nbsp;';
        }
        $arrow .= '</span>';
        $out .= 'cursor:pointer"'
             .' onclick="PAG_SortField=\''.$col_name.'\';'
                       .'PAG_SortMode=\''.(int)$sortflag.'\';'
                       .'PAG_GetTable();">'
                       .$arrow
                       .'<b>'.$header_name.'</b></td>';
      }
    }
    $out .= '</tr>';
    return $out;
  }
  //o-------------------------------------------------------------------------------o
  protected function genRow($row)
  //o-------------------------------------------------------------------------------o
  {
    $out = '<tr style="background:#';
    if($this->row_even)
    {
      $out .= 'ffffff';
    }
    else
    {
      $out .= 'ffd0b2';
    }
    $out .= '">';
    foreach($row as $col_name => $value)
    {
      if(!empty($this->columns[$col_name]))
      {
        $out .= '<td>'
               .$value
               .'</td>';
      }
    }
    $out .= '</tr>';
    return $out;
  }
  //o-------------------------------------------------------------------------------o
  protected function addAjaxSearch()
  //o-------------------------------------------------------------------------------o
  {
  }
  //o-------------------------------------------------------------------------------o
  private function init()
  //o-------------------------------------------------------------------------------o
  {
    if(!$this->initialized)
    {
      $this->getSortField();
      if(!empty($this->sortfield))
      {
        $this->addOrderBy($this->sortfield);
      }
      if($this->request->searchstring !== NULL)
      {
        $this->AjaxSearchString = mysql_real_escape_string($this->request->searchstring);
        $this->addAjaxSearch();
      }
      $this->genSQLStatement();
      $this->num_results = (int)$this->db->PreCount($this->sql);
      $this->num_pages = (int)($this->num_results / $this->max_results_per_page) + 1;
    }
    $this->initialized = true;
  }
  //o-------------------------------------------------------------------------------o
  private function getResultsByPage($PageNo)
  //o-------------------------------------------------------------------------------o
  {
    $this->init();
    if($PageNo > $this->num_pages || $PageNo < 1)
    {
      throw new Exception('Illegal page number!');
    }
    if($this->num_results > $this->max_results_per_page)
    {
      $this->db->DropLastResults();
      $JobOffSet = ($PageNo - 1) * $this->max_results_per_page;
      $this->results = $this->db->query($this->sql.' LIMIT '.(int)$JobOffSet.','.(int)$this->max_results_per_page);
    }
    else
    {
      $this->results = $this->db->ReGetResults();
    }
  }
  //o-------------------------------------------------------------------------------o
  private function genSQLStatement()
  //o-------------------------------------------------------------------------------o
  {
    $this->sql = 'SELECT';
    if(empty($this->columns))
    {
      $this->sql .= ' *';
    }
    else
    {
      $this->sql .= ' '
                   .implode(',', array_keys($this->columns));
    }
    $this->sql .= ' FROM '.$this->tablename.'';
    if(!empty($this->where))
    {
      $this->sql .= ' WHERE '
                   .implode(' AND ', $this->where);
    }
    if(!empty($this->groupby))
    {
      $this->sql .= ' GROUP BY '
                   .implode(',',$this->groupby);
    }
    if(!empty($this->orderby))
    {
      $this->sql .= ' ORDER BY '
                   .implode(',',$this->orderby);
      if($this->sortmode)
      {
        $this->sql .= ' DESC';
      }
      else
      {
        $this->sql .= ' ASC';
      }
    }
  }
  //o-------------------------------------------------------------------------------o
  public static function genAjaxSearchField($label)
  //o-------------------------------------------------------------------------------o
  {
    $out = '<span class="fat">'
             .$label
          .'</span>'
          .'<input class="inputfont"'
            .' type="text"'
            .' id="PAG_SearchString"'
            .' name="PAG_SearchString"'
            .' size="40"'
            .' maxlength="40"'
            .' onkeyup="PAG_OnChangeDelay();"'
          .' />';
    return $out;
  }
//o-------------------------------------------------------------------------------o
//o-------------------------------------------------------------------------------o
//o-------------------------------------------------------------------------------o
}
