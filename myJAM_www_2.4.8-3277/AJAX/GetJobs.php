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
require_once(_FULLPATH.'/access.php');
require_once(_FULLPATH.'/classes/class_myJAM_DB.php');
require_once(_FULLPATH.'/classes/class_myJAM_User.php');
require_once(_FULLPATH.'/classes/class_myJAM_Project.php');
require_once(_FULLPATH.'/classes/class_myJAM_Queue.php');
require_once(_FULLPATH.'/classes/class_myJAM_Host.php');
require_once(_FULLPATH.'/classes/class_myJAM_Architecture.php');
require_once(_FULLPATH.'/helper/myJAM_JSON/myJAM_JSON.php');
require_once(_FULLPATH.'/fxn/is_md5.php');
require_once(_FULLPATH.'/fxn/MemOut.php');
require_once(_FULLPATH.'/fxn/DurationOut.php');
$MAX_RESULTS_PER_PAGE = 100;
$MAX_PAGE_INDEX = 7;
/**
 * @desc Hilfsfunktion
 */
//o-------------------------------------------------------------------------------o//
function create_table_row($inside, $stuff='')
//o-------------------------------------------------------------------------------o//
{
  return '<td '.$stuff.' >'.$inside.'</td>';
}
//o-------------------------------------------------------------------------------o//
function GenHeader($name, $OrderBy, $SortMode)
//o-------------------------------------------------------------------------------o//
{
  // Style
  $stuff='style="background-color:#c0c0c0; cursor:pointer;';
  if ($name == $OrderBy){
    $stuff .= 'color:#ff0000;';
  }
  $stuff .= '"';
  // Javascript
  $stuff .= " onclick=\"OrderBy='$name';";
  if ($name == $OrderBy && $SortMode == "0"){
    $stuff .= 'SortMode=\'1\';';
  }else{
    $stuff .= 'SortMode=\'0\';';
  }
  $stuff .= "GetJobsRequest();\"";
  // HTML Stuff
  $inside = "<b>$name</b>";
  echo create_table_row($inside,$stuff);
}
//o-------------------------------------------------------------------------------o
function LayOutReqStr($req)
//o-------------------------------------------------------------------------------o
{
  if ($req == '')
  {
    return '<i>n/a</i>';
  }
  $req = htmlentities($req);
  $tmp = explode(';',$req);
  if (count($tmp) > 1)
  {
    $req = join("<hr style='margin-bottom:0px;margin-top:0px;'><i><u>",$tmp);
  }
  $tmp = explode(':',$req);
  if (count($tmp) > 1)
  {
    $req = join(':</u><i>',$tmp);
  }
  $tmp = explode('\+', $req);
  if (count($tmp) > 1)
  {
    $req = implode('+<br/>', $tmp);
  }
  return $req;
}
//o-------------------------------------------------------------------------------o
//o-------------------------------------------------------------------------------o
//o-------------------------------------------------------------------------------o
header('Content-Type: text/html; charset=ISO-8859-1');
$columns = "";
$db = new myJAM_DB();
$sql = 'SELECT '
     .'job_state'
     .',pbs_jobnumber'
     .',uid'
     .',firstname'
     .',lastname'
     .',ProjectName'
     .',run_stamp'
     .',FROM_UNIXTIME(run_stamp) as run_stamp_date'
     .',queue_descr'
     .',ArchName'
     .',num_procs'
     .',ROUND(actual_su,3) AS actual_su'
     .',used_vmem'
     .',ApplID'
     .',ApplName'
     .',md5_bin'
     .',RequestString'
     .',SEC_TO_TIME(run_stamp-queue_stamp) AS QueueingTime'
     .' FROM '
     .'JobDetails';
// new JSON-based argument?
if(isset($_POST['obj']))
{
  $obj = new myJAM_JSON($_POST['obj']);
  //map object-properties to _POST-Variables. Workaround!!
  foreach($obj->getAssoc() as $key => $val)
  {
    $_POST[$key] = $val;
  }
}
//o--- JOB STATE -----------------------------------------------------------------o
if (in_array($_POST['JobState'], array('R', 'Q', 'F')))
{
  $sql .= " WHERE job_state='" . mysql_real_escape_string($_POST['JobState']) . "'";
}
if (!empty($_POST['ApplID']) &&
    is_numeric($_POST['ApplID']) &&
    (int)$_POST['ApplID'] > 0)
{
  $sql .= ' AND ApplID=\'' . (int)$_POST['ApplID'] . '\'';
}
//o--- ARCHITECTURE --------------------------------------------------------------o
if (!empty($_POST['ArchID']) &&
    is_numeric($_POST['ArchID']) &&
    (int)$_POST['ArchID'] > 0)
{
  $sql .= ' AND ArchID=\'' . (int)$_POST['ArchID'] . '\'';
}
//o--- CORES ---------------------------------------------------------------------o
if (!empty($_POST['nbCores']) &&
    is_numeric($_POST['nbCores']) &&
    (int)$_POST['nbCores'] > 0)
{
  $sql .= ' AND num_procs=\'' . (int)$_POST['nbCores'] . '\'';
}
//o--- CORES LOG2---------------------------------------------------------------------o
if (!empty($_POST['nbCoresLOG2']) &&
    is_numeric($_POST['nbCoresLOG2']) &&
    (int)$_POST['nbCoresLOG2'] > 0)
{
  $nbcores = pow(2, (int)$_POST['nbCoresLOG2'] - 1);
  $sql .= " AND num_procs>='$nbcores'";
  $nbcores *= 2;
  $sql .= " AND num_procs<'$nbcores'";
}
//o--- QUEUE ---------------------------------------------------------------------o
if (!empty($_POST['Queue'])
){
  $sql .= ' AND queue_descr=\'' . mysql_real_escape_string($_POST['Queue']) . '\'';
}
//o--- USER ----------------------------------------------------------------------o
if (!empty($_POST['UserID']) &&
    is_numeric($_POST['UserID']) &&
    (int)$_POST['UserID'] > 0)
{
  $sql .= ' AND uid=\'' . (int)$_POST['UserID'] . '\'';
}
//o--- PROJECT -------------------------------------------------------------------o
if (!empty($_POST['ProjID']) &&
    is_numeric($_POST['ProjID']) &&
    (int)$_POST['ProjID'] > 0)
{
    $sql .= ' AND pid=\'' . (int)$_POST['ProjID'] . '\'';
}
//o--- PROJECT -------------------------------------------------------------------o
if (!empty($_POST['md5']) &&
    is_md5($_POST['md5']))
{
  $sql .= ' AND md5_bin=\'' . mysql_real_escape_string($_POST['md5']) . '\'';
}
//o--- Month -------------------------------------------------------------------o
if (!empty($_POST['month']) &&
    is_numeric($_POST['month']) &&
    (int)$_POST['month'] >= 0 &&
    (int)$_POST['month'] <= 13
)
{
  $sql .= ' AND month(SortDate)=\'' . (int)$_POST['month'] . '\'';
}
//o--- Year -------------------------------------------------------------------o
if (!empty($_POST['year']) &&
    is_numeric($_POST['year']) &&
    (integer)$_POST['year'] >= 1900
)
{
  $sql .= ' AND year(SortDate)=\'' . (int)$_POST['year'] . '\'';
}
//o--- COLUMNS -------------------------------------------------------------------o
$columns = explode('c', $_POST['columns']);
//o--- ORDERING -------------------------------------------------------------------o
$orderby = array(
    "State" => "job_state"
    ,"User" => "lastname"
    ,"Project" => "ProjectName"
    ,"StartTime" => "run_stamp"
    ,"QueueingTime" => "QueueingTime"
    ,"Queue" => "queue_descr"
    ,"Architecture" => "ArchName"
    ,"nbCores" => "num_procs"
    ,"SUs" => "actual_su"
    ,"vMem" => "used_vmem"
);
$sql .= ' ORDER BY ';
if (array_key_exists($_POST['OrderBy'], $orderby))
{
  $sql .= $orderby[$_POST['OrderBy']];
}
else
{
  $sql .= 'pbs_jobnumber';
}
//o--- SORTING -------------------------------------------------------------------o
if (isset($_POST['SortMode']) && //
$_POST['SortMode'] == 1) //
{
  $sql .= ' DESC';
}
else
{
  $sql .= ' ASC';
}
//o-------------------------------------------------------------------------------o
//o-------------------------------------------------------------------------------o
//o-------------------------------------------------------------------------------o
$nbResults = $db->PreCount($sql);
if($nbResults < 1) {
  die('<b>No Jobs...</b>');
}
//HIER GEHT ES LOS!!!
echo '<p/><div class="btnclass"><b>Results: </b>'.$nbResults;
if ($nbResults > $MAX_RESULTS_PER_PAGE)
{
  $nbPages = (int)($nbResults / $MAX_RESULTS_PER_PAGE) + 1;
  $PageOffSet = 1;
  if (isset($_POST['offset']) && is_numeric($_POST['offset']) && (integer)$_POST['offset'] > 1)
  {
    $PageOffSet = (integer)$_POST['offset'];
  }
  $MaxIndex = min($nbPages, $PageOffSet + (integer)($MAX_PAGE_INDEX / 2));
  $MinIndex = max(1, $MaxIndex - $MAX_PAGE_INDEX + 1);
  $MaxIndex = min($nbPages, $MinIndex + $MAX_PAGE_INDEX - 1);
  echo '&nbsp;&nbsp;&nbsp;<span>'
      .'<a href="javascript:PageOffset=1;GetJobsRequest();">&lt;&lt;</a>';
  if ($PageOffSet > 1)
  {
    echo '&nbsp;&nbsp;<a href="javascript:PageOffset=' . max(1, $PageOffSet - 1) . ';GetJobsRequest();">&lt;</a>';
  }
  else
  {
    echo '&nbsp;&nbsp;<span style="color:#c0c0c0;"><b>&lt;</b></span>';
  }
  if ($MinIndex > 1)
  {
    echo '&nbsp;&nbsp;<a href="javascript:PageOffset=' . max(1, $PageOffSet - $MAX_PAGE_INDEX) . ';GetJobsRequest();">...</a>';
  }
  for($i = $MinIndex; $i <= $MaxIndex; $i++)
  {
    echo '&nbsp;&nbsp;';
    if ($i == $PageOffSet)
    {
      echo '<span style="background:#c0c0c0">';
    }
    echo '<a href="javascript:PageOffset=' . $i . ';GetJobsRequest();">' . $i . '</a>';
    if ($i == $PageOffSet)
    {
      echo '</span>';
    }
  }
  if ($MaxIndex < $nbPages)
  {
    echo '&nbsp;&nbsp;<a href="javascript:PageOffset=' . min($nbPages, $PageOffSet + $MAX_PAGE_INDEX) . ';GetJobsRequest();">...</a>';
  }
  if ($PageOffSet < $nbPages)
  {
    echo '&nbsp;&nbsp;<a href="javascript:PageOffset=' . min($nbPages, $PageOffSet + 1) . ';GetJobsRequest();">&gt;</a>';
  }
  else
  {
    echo '&nbsp;&nbsp;<span style="color:#c0c0c0;"><b>&gt;</b></span>';
  }
  echo '&nbsp;&nbsp;<a href="javascript:PageOffset=' . $nbPages . ';GetJobsRequest();">&gt;&gt;</a>'
      .'</span>&nbsp;&nbsp;&nbsp;<b>Pages: </b>' . $nbPages . "";
  $JobOffSet = ($PageOffSet - 1) * $MAX_RESULTS_PER_PAGE;
  $sql .= ' LIMIT ' . $JobOffSet . ", " . $MAX_RESULTS_PER_PAGE;
  $db->DropLastResults();
  $jobs = $db->query($sql);
}
else
{
  $jobs = $db->ReGetResults();
}
//o-------------------------------------------------------------------------------o
//o-------------------------------------------------------------------------------o
//o-------------------------------------------------------------------------------o
echo '</div>'
     .'<table class="full"><tr>';
$headerstuff = array(
    '1' => 'JobID'
    ,'2' => 'User'
    ,'3' => 'Project'
    ,'4' => 'StartTime'
    ,'5' => 'QueueingTime'
    ,'6' => 'Queue'
    ,'7' => 'Architecture'
    ,'8' => 'nbCores'
    ,'9' => 'SUs'
    ,'10' => 'vMem'
);
GenHeader('State', $_POST['OrderBy'], $_POST['SortMode']);
foreach ($headerstuff as $key => $val)
{
  if (in_array($key, $columns))
  {
    GenHeader($val, $_POST['OrderBy'], $_POST['SortMode']);
  }
}
echo '</tr>';
//o-------------------------------------------------------------------------------o
//o-------------------------------------------------------------------------------o
//o-------------------------------------------------------------------------------o
$admin = $ActiveUser->ADMIN;
$active_uid = $ActiveUser->ID;
$row = 0;
$line = 0;
foreach($jobs as $job)
{
  $line++;
  if ($row == 0)
  {
    $row = 1;
    echo '<tr>';
  }
  else
  {
    $row = 0;
    echo '<tr style="background-color:#ffd0b2;">';
  }
  echo '<td id="state_'.$line.'">';
  switch($job['job_state'])
  {
    case 'F':
      echo '<span style="color:#008000;float:right;"><b>F</b></span>';
      break;
    case 'Q':
      echo '<span style="color:#808000;float:right;"><b>Q</b></span>';
      break;
    case 'R':
      echo '<span style="float:left;">'
          .'</span>'
          .'<span style="color:#ff0000;float:right;"><b>R</b></span>'
          ;
      break;
    default:
      echo '---';
  }
  echo '</td>';
  $stuffs = array(
     '1' => ' title="header=[' . $job['pbs_jobnumber'] . ']'
         .' body=['
         .'Requested:'. LayOutReqStr($job['RequestString'])
         .']'
         .' windowlock=[on]'
         .' cssheader=[toolupper]'
         .' cssbody=[toollower]"'
  );
  $rowstr = '';
  if (in_array('11', $columns))
  {
    $rowstr = '';
    $stuffs['11'] = 'id="appl_'.$line.'"';
  }
  $insides=array(
       '1' => $job['pbs_jobnumber']
      ,'2' => htmlentities($job['lastname']).', '.htmlentities($job['firstname'])
      ,'3' => htmlentities($job['ProjectName'])
      ,'4' => $job['run_stamp_date']
      ,'5' => $job['QueueingTime']
      ,'6' => htmlentities($job['queue_descr'])
      ,'7' => htmlentities($job['ArchName'])
      ,'8' => $job['num_procs']
      ,'9' => $job['actual_su']
      ,'10' => MemOut($job['used_vmem'] * 1024)
      ,'11' => $rowstr
  );
  foreach($insides as $key => $val)
  {
    if (in_array($key, $columns))
    {
      if (!array_key_exists($key, $stuffs))
      {
        echo create_table_row($val);
      }
      else
      {
        echo create_table_row($val, $stuffs[$key]);
      }
    }
  }
  echo '</tr>';
}
