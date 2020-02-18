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

require_once(_FULLPATH.'/access.php');
require_once(_FULLPATH.'/classes/class_myJAM_Project.php');
require_once(_FULLPATH.'/classes/class_myJAM_User.php');
require_once(_FULLPATH.'/classes/class_myJAM_Host.php');
require_once(_FULLPATH.'/classes/class_myJAM_CostModel.php');
require_once(_FULLPATH.'/classes/class_myJAM_Institute.php');
?>
<script type="text/javascript" src="js/prototype.js"></script>
<script type="text/javascript" src="js/ReCalcSUs.js"></script>
<script type="text/javascript" src="js/TestUserInput.js"></script>
<script type="text/javascript" src="js/showprojects.js"></script>
<?php
//o-------------------------------------------------------------------------------o
function View_ProjectDetails($Project, $ActiveUser)
//o-------------------------------------------------------------------------------o
{
  if ($ActiveUser->ADMIN || ($Project->Owner->ID == $ActiveUser->ID))
  {
    echo '<form action="main.php?page=projects';
    if (isset($_GET['user']))
    {
      echo '&amp;user';
    }
    else if (isset($_GET['admin']))
    {
      echo '&amp;admin';
    }
    echo '&amp;mode=update&amp;pid=';
    if (is_object($Project))
    {
      echo $Project->ID;
    }
    echo '" method="post">'
         .'<div><input class="inputfont" type="hidden" name="pid" value="';
    if (is_object($Project))
    {
      echo $Project->ID;
    }
    else
    {
      echo "new";
    }
    echo '" /></div>';
    DrawProjectScreen($Project, $ActiveUser);
    if ($ActiveUser->ADMIN)
    {
      echo '<div class="centr">'
      .'<input class="inputfont" type="submit" value="Add/Update" onclick="return CheckInput();" />'
      .'</div>'
      .'</form>'
      ;
      if(is_object($Project))
      {
        echo '<form action="main.php?page=projects&amp;mode=delete';
        if(isset($_GET['user']))
        {
          echo '&amp;user';
        }
        else if(isset($_GET['admin']))
        {
          echo '&amp;admin';
        }
        echo '&amp;pid='.$Project->ID.'" method="post">'
           . '<div class="centr">'
           . '<input class="inputfont" type="submit" value="Delete" onclick="return confirm(\'Are you sure you want to delete this project?\')" />'
           . '</div>'
           .'</form>'
        ;
      }
    }
  }
  else
  {
    DrawProjectScreen($Project, $ActiveUser);
  }
}
//o-------------------------------------------------------------------------------o
function Action_DeleteProject($Project, $ActiveUser)
//o-------------------------------------------------------------------------------o
{
  if (is_object($Project))
  {
    $OldName = $Project->Name;
    $Project->DELETE();
    echo "Project <b>$OldName</b> deleted!<br/>";
  }
}
//o-------------------------------------------------------------------------------o
function Action_UpdateProject($Project, $ActiveUser)
//o-------------------------------------------------------------------------------o
{
  print_r($_POST);
  //do we have a PID?
  if (!isset($_POST['pid']))
  {
    die("myJAM>> FATAL ERROR 0x7558 in Module Project!");
  }
  //is it a new Project?
  if ($_POST['pid'] == 'new')
  {
    //Test if project name already in use
    $Project = new myJAM_Project($_POST['name']);
    if(is_a($Project, 'myJAM_Project') && is_scalar($Project->ID) && (int)$Project->ID > 0)
    {
      echo 'Project name <b>'.$_POST['name'].'</b> alread in use!<br>';
      die();
    }
    $ProjOwner = new myJAM_User((int)$_POST['proj_uid_owner']);
    $Institute = new myJAM_Institute((int)$_POST['institute_id']);
    $CostModel = new myJAM_CostModel((int)$_POST['cid']);
    $ProjUpdate = myJAM_Project::CreateProject($_POST['name'],
                                               $_POST['description'],
                                               $ProjOwner,
                                               $Institute,
                                               $CostModel);
  }
  // or an already existing one? => UPDATE
  else
  {
    $ProjUpdate = new myJAM_Project((int)$_POST['pid']);
    //is the ProjUpdate-Object valid?
    if (!is_scalar($ProjUpdate->ID))
    {
      die("myJAM>> FATAL ERROR 2ULLJ IN PROJECTS");
    }
    //name changed?
    if ($_POST['name'] != '' &&
        $_POST['name'] != $ProjUpdate->Name)
    {
      $ProjUpdate->Name = $_POST['name'];
    }
    //description changed?
    if ($_POST['description'] != '' &&
        $_POST['description'] != $ProjUpdate->Description)
    {
      $ProjUpdate->Description = $_POST['description'];
    }
    //ProjectOwner changed?
    if (is_numeric($_POST['proj_uid_owner']) &&
        ($_POST['pid'] == 'new' || $ProjUpdate->Owner->ID != (integer)$_POST['proj_uid_owner']))
    {
      $NewOwner = new myJAM_User($_POST["proj_uid_owner"]);
      //NewOwner valid?
      if (!is_object($NewOwner) || !is_scalar($NewOwner->ID))
      {
        die('myJAM>> FATAL ERROR 754GI IN PROJECTS');
      }
      $ProjUpdate->Owner = $NewOwner;
      unset($NewOwner);
    }
    //Institute changed?
    if(is_numeric($_POST['institute_id']) &&
       (integer)$_POST['institute_id'] != $ProjUpdate->Institute->ID)
    {
      $NewInstitue = new myJAM_Institute((integer)$_POST['institute_id']);
      //NewInstitute valid?
      if(!is_object($NewInstitue) || !is_scalar($NewInstitue->ID))
      {
        die('myJAM>> FATAL ERROR 0xe0d5 IN PROJECTS');
      }
      $ProjUpdate->Institute = $NewInstitue;
      unset($NewInstitue);
    }
    //costmodel changed?
    if (is_numeric($_POST['cid']) &&
       ($_POST['pid'] == 'new' || $ProjUpdate->CostModel->ID != $_POST['cid']))
    {
      $NewCostModel = new myJAM_CostModel($_POST['cid']);
      //NewCostModel valid?
      if (is_array($NewCostModel->ID))
      {
        die('myJAM>> FATAL ERROR 4Y5CJ IN PROJECTS');
      }
      $ProjUpdate->CostModel = $NewCostModel;
      unset($NewCostModel);
    }
  }
  //ProjectEnabled changed?
  if(isset($_POST['proj_enable']) &&
     $_POST['proj_enable'] == 1)
  {
    if (! $ProjUpdate->Enabled)
    {
      $ProjUpdate->Enabled = 1;
    }
  }
  else
  {
    if ($ProjUpdate->Enabled)
    {
      $ProjUpdate->Enabled = 0;
    }
  }
  //ProjectBillable changed?
  if (isset($_POST['proj_billable']) &&
      $_POST['proj_billable'] == 'on')
  {
    if (! $ProjUpdate->Billable)
    {
      $ProjUpdate->Billable = 1;
    }
  }
  else
  {
    if ($ProjUpdate->Billable)
    {
      $ProjUpdate->Billable = 0;
    }
  }
  //Overrun changed?
  if (isset($_POST['allow_overrun']) &&
      $_POST['allow_overrun'] == 1)
  {
    if (! $ProjUpdate->Overrun)
    {
      $ProjUpdate->Overrun = 1;
    }
  }
  else
  {
    if ($ProjUpdate->Overrun)
    {
      $ProjUpdate->Overrun = 0;
    }
  }
  //ProjectStart changed?
  if (strtotime($_POST['project_start_date']) &&
      $ProjUpdate->StartDate != $_POST['project_start_date'])
  {
    $ProjUpdate->StartDate = $_POST['project_start_date'];
  }
  if (strtotime($_POST['project_end_date']) &&
      $ProjUpdate->EndDate != $_POST['project_end_date'])
  {
    $ProjUpdate->EndDate = $_POST['project_end_date'];
  }
  //check if Queues have changed
  if(isset($_POST['queue']))
  {
    $OldQueues = array();
    $QueueList = $ProjUpdate->Queues;
    foreach($QueueList as $queue)
    {
      $OldQueues[$queue->ID] = $queue->ID;
    }
    //check for new queues
    foreach(array_diff($_POST['queue'], $OldQueues) as $qid)
    {
      $NewQueue = new myJAM_Queue($qid);
      $ProjUpdate->AddQueue($NewQueue);
      unset($NewQueue);
    }
    //check for deleted queues
    foreach(array_diff($OldQueues, $_POST['queue']) as $qid)
    {
      $FeyQueue = new myJAM_Queue($qid);
      $ProjUpdate->DelQueue($FeyQueue);
      unset($FeyQueue);
    }
    unset($OldQueues);
  }
  //host changed?
  if(isset($_POST['host']))
  {
    $OldHosts = array();
    $HostList = $ProjUpdate->Hosts;
    foreach($HostList as $host)
    {
      $OldHosts[$host->ID] = $host->ID;
    }
    //check for new hosts
    foreach(array_diff($_POST['host'], $OldHosts) as $hid)
    {
      $NewHost = new myJAM_Host($hid);
      $ProjUpdate->AddHost($NewHost);
      unset($NewHost);
    }
    //check for deleted hosts
    foreach(array_diff($OldHosts, $_POST['host']) as $hid)
    {
      $FeyHost = new myJAM_Host($hid);
      $ProjUpdate->DelHost($FeyHost);
      unset($FeyHost);
    }
  }
  //just set the invoice adress, don't care if it has changed or not
  if(isset($_POST['projectinvoiceaddress']))
  {
    $ProjUpdate->InvoiceAddress = $_POST['projectinvoiceaddress'];
  }
  echo '<script type="text/javascript">';
  if ($_POST['pid'] == 'new')
  {
    echo 'window.location="main.php?page=projects&mode=info&pid=' . $ProjUpdate->ID . '&admin"';
  }
  else
  {
    echo 'window.location="main.php?page=projectlist&admin';
  }
  echo '</script>';
}
//o-------------------------------------------------------------------------------o
//o-------------------------------------------------------------------------------o
//o PROJECT MAIN                                                                  o
//o-------------------------------------------------------------------------------o
//o-------------------------------------------------------------------------------o
if (isset($_GET["user"]) && $ActiveUser->UserName == "admin")
{
  echo "admin is not a member of any project.<br/>";
  die();
}
//is a PID given and is it valid?
if(isset($_GET["pid"]) && (integer)$_GET["pid"] > 0)
{
  $Project = new myJAM_Project((integer)$_GET["pid"]);
  if(!is_object($Project) || !is_scalar($Project->ID))
  {
    die("myJAM>> FATAL ERROR 0x799f in class myJAM_Project");
  }
  $_SESSION['CurProjID']=$Project->ID;
}
else
{
  $Project=NULL;
  $_SESSION['CurProjID'] = NULL;
}
//is the active user a member of the project?
if(is_object($Project) &&
   is_scalar($Project->ID) &&
   !$ActiveUser->ADMIN &&
   $ActiveUser->ID != $Project->Owner->ID)
{
  $member = false;
  $UserList = $Project->Users;
  foreach($UserList as $user)
  {
    if($user->ID == $ActiveUser->ID)
      {$member = true;}
  }
  if(!$member)
  {
    echo "<b>Access forbidden</b><br>";
    die("myJAM>> ERROR 0x5502 in class myJAM_Project");
  }
}
if(isset($_GET['mode']))
  {$mode = $_GET['mode'];}
else
  {$mode = "";}
//Set Default for sorting
if (!isset($_GET['sort']) && !isset($_GET['mod']))
{
  $_GET['sort']='project';
  $_GET['mod']=1;
}
//o-------------------------------------------------------------------------------o
if ($mode == "info")
//o-------------------------------------------------------------------------------o
{
  View_ProjectDetails($Project, $ActiveUser);
}
//o-------------------------------------------------------------------------------o
elseif ($mode=="delete" && $ActiveUser->ADMIN)
//o-------------------------------------------------------------------------------o
{
  Action_DeleteProject($Project, $ActiveUser);
}
//o-------------------------------------------------------------------------------o
elseif ($mode=="update" && ($ActiveUser->ADMIN || $Project->Owner->ID==$ActiveUser->ID))
//o-------------------------------------------------------------------------------o
{
  Action_UpdateProject($Project, $ActiveUser);
}
//o-------------------------------------------------------------------------------o
else
//o-------------------------------------------------------------------------------o
{
  print("myJAM>>Fatal Error!!!");
  throw new Exception('Illegal Project Action');
}
//o-------------------------------------------------------------------------------o
function DrawProjectScreen($Project, $User)
//o-------------------------------------------------------------------------------o
{
  // 05.05.2008 DBS; This function draws the screen Project Admin -> (Click on one of the listed projects)
  echo "<table class=\"maintable\">"
  ."<tr>"
  // Erste Spalte
  ."<td class=\"projectstable\">"
  ."<div class=\"simpleborder\">"
  ."<table class=\"innertable\">"
  ."<tr>"
  // Project Name
  ."<td id=\"tab_name\" class=\"projectnametd\">"
  ."<span class=\"fat\">Project Name:</span>"
  ."</td>"
  ."<td class=\"projectnametd2\">"
  ;
  if($User->ADMIN)
  {
    // STYLESHEET!!!
    echo "<input class=\"inputfont\" type=\"text\" style=\"width:100%;\" maxlength=\"16\" name=\"name\" id=\"name\" value=\"";
    if(is_object($Project))
      {echo $Project->Name;}
    else
      {echo "NewProject";}
    echo "\" />";
  }
  echo "</td>"
  ."</tr>"
  ."<tr>"
  ."<td colspan=\"2\" class=\"bottomborder\"></td>"
  ."</tr>"
  // Project Description
  ."<tr>"
  ."<td id=\"tab_description\" class=\"projdesc\">"
  ."<span class=\"fat\">Project Description:</span>"
  ."</td>"
  ."<td class=\"textdesc\">"
  ;
  if(($User->ADMIN || (is_object($Project) && $Project->Owner->ID==$User->ID)))
  {
    echo "<textarea class=\"inputfont\" name=\"description\" id=\"description\" cols=\"50\" rows=\"5\">";
    if(is_object($Project))
    {
      echo $Project->Description;
    }
    else
    {
      echo "&lt;&lt;Short Description&gt;&gt;";
    }
    echo "</textarea>";
  }
  else
  {
    echo $Project->Description;
  }
  echo "</td>"
  ."</tr>"
  ."<tr>"
  ."<td colspan=\"2\" class=\"bottomborder\"></td>"
  ."</tr>"
  // Project Owner
  ."<tr>"
  ."<td class=\"projowner\" id=\"tab_owner\">"
  ."<span class=\"fat\">Project Owner:</span>"
  ."</td>"
  ."<td class=\"cell2\">"
  ;
  GenerateProjectOwnerPHP($Project,$User);
  echo "</td>"
  ."</tr>"
  ."<tr>"
  ."<td colspan=\"2\" class=\"bottomborder\"></td>"
  ."</tr>"
  // Project's Institute
  ."<tr>"
  ."<td class=\"projowner\">"
  ."<span class=\"fat\">Institute:</span>"
  ."</td>"
  ."<td class=\"cell2\">"
  ;
  GenerateInstitutes($Project, $User);
  echo "</td>"
  ."</tr>"
  ."<tr>"
  ."<td colspan=\"2\" class=\"bottomborder\"></td>"
  ."</tr>"
  // Display Queue Information
  ."<tr>"
  ."<td class=\"projowner\">"
  ."<span class=\"fat\">Accessible Hosts/Queues:</span>"
  ."</td>"
  ."<td class=\"queuedisp\">"
  ;
  DisplayQueues($Project,$User);
  echo "</td>"
  ."</tr>"
  ."</table>"
  ."</div>"
  ."<p/>"
  // Cost Group and Service Units
  ."<div class=\"simpleborder\">"
  ."<table class=\"projtab2\">"
  ."<tr>"
  ."<td class=\"projactivity\">"
  ."<span class=\"fat\">Cost Group:</span>"
  ."</td>"
  ."<td class=\"projactivityinput\">"
  ;
  DisplayCostInfo($Project,$User);
  echo "</td>";
  if(is_object($Project))
  {
    echo "<td>"
    ."<span class=\"projbill\">Service Units:</span>"
    ."</td>"
    ."<td class=\"cell2\">"
    ;
    if(is_object($Project))
    {
      echo $Project->SUs;
    }
    echo "</td>";
  }
  echo "</tr>"
  ."</table>"
  // Project Activity, Project Billable and Allow Overrun
  ."<table class=\"projtab2\">"
  ."<tr>"
  ."<td class=\"projactivity\">"
  ."<span class=\"fat\">Project active?</span>"
  ."</td>"
  ."<td class=\"projactivityinput\">"
  ;
  if ($User->ADMIN)
  {
    echo "<input class=\"inputfont\" type=\"checkbox\" name=\"proj_enable\" value=\"1\" ";
    if (is_object ( $Project ) && $Project->Enabled)
    {
      echo "checked=\"checked\"";
    }
    echo "/>";
  }
  else
  {
    if (is_object ( $Project ) && $Project->Enabled)
    {
      echo "Yes";
    }
    else
    {
      echo "<span class=\"red\">disabled</span>";
    }
  }
  echo "</td>"
  ."<td>"
  ."<span class=\"projbill\">Project billable?</span>"
  ."</td>"
  ."<td class=\"projactivityinput\">";
  DisplayBillablePHP ( $Project, $User );
  echo "</td>"
  ."<td>"
  ."<span class=\"projbill\">Allow Overrun?</span>"
  ."</td>"
  ."<td class=\"cell2\">"
  ;
  GenerateOverrunPHP ( $Project, $User );
  echo "</td>"
  ."</tr>"
  ."</table>"
  // Start Date and End Date
  ."<table class=\"full\">"
  ."<tr>"
  ."<td class=\"projactivity\">"
  ."<span class=\"fat\">Project Start Date:</span>"
  ."<div class=\"projstart\">"
  ;
  if ($User->ADMIN)
  {
    if (is_object ( $Project ))
    {
      if (! $Project->StartDate)
      {
        $Project->StartDate = date ( "Y-m-d" );
      }
      echo "<input class=\"inputfont\" type=\"text\" name=\"project_start_date\" value=\"" . $Project->StartDate . "\" />";
    }
    else
    {
      echo "<input class=\"inputfont\" type=\"text\" name=\"project_start_date\" value=\"" . date ( "Y-m-d" ) . "\" />";
    }
    echo "<br/><span class=\"fat\">(YYYY-MM-DD)</span></div>";
  }
  else
  {
    echo $Project->StartDate . "</div>";
  }
  echo "</td>"
  ."<td class=\"projend\">"
  ."<span class=\"fat\">Project End Date:</span>"
  ."<div class=\"projstart\">"
  ;
  if ($User->ADMIN)
  {
    if (is_object ( $Project ))
    {
      echo "<input class=\"inputfont\" type=\"text\" name=\"project_end_date\" value=\"" . $Project->EndDate . "\" />";
    }
    else
    {
      echo "<input class=\"inputfont\" type=\"text\" name=\"project_end_date\" value=\"0000-00-00\" />";
    }
    echo "<br/><span class=\"fat\">(YYYY-MM-DD)</span></div>";
  }
  else
  {
    echo $Project->EndDate . "</div>";
  }
  echo "</td>"
  ."</tr>"
  ."</table>"
  ."</div>"
  ."</td>"
  // Zweite Spalte
  ."<td class=\"secondcol\">"
  ."<div class=\"simpleborder\">"
  ."<div class=\"apopbody\">"
  ."<span class=\"fat\">Invoice Address:</span>"
  ."<div class=\"centr\"><p/>"
  ."<textarea "
  ;
  if (! $User->ADMIN || ! $Project || ! $User->ID == $Project->Owner->ID)
  {
    // Disable text area
    echo "disabled=\"disabled\" ";
  }
  echo "class=\"inputfont\" id=\"projectinvoiceaddress\" name=\"projectinvoiceaddress\" rows=\"10\" cols=\"60\">";
  if (is_object ( $Project ))
  {
    echo $Project->InvoiceAddress;
  }
  echo "</textarea>"
  ."</div>"
  ."</div>"
  ."</div>"
  ."<p/>"
  ;
  if (is_object ( $Project ))
  {
    echo "<div class=\"simpleborder\">"
    ."<table>"
    ."<tr>"
    ."<td class=\"projactivity\">"
    ."<span class=\"fat\">Service Units:</span>"
    ."</td>"
    ."<td>"
    .$Project->SUs
    ."</td>"
    ;
    if ($User->ADMIN)
    {
      echo "<td>"
      ."<input class=\"inputfont\" type=\"button\" value=\"ReCalc\" onclick=\"ReCalcSUs('" . $Project->ID . "')\" />"
      ."</td>"
      ;
    }
    echo "</tr>"
    ."</table>"
    ."</div>"
    ."<p/>";
  }
  if (is_object ( $Project ))
  {
    echo "<div class=\"jobsdiv\">"
    ."<span class=\"fat\">Jobs:</span>"
    ."<div class=\"jobsdiv2\">"
    ."<table class=\"table1\" style=\"width:100%;\">"
    ."<tr>"
    ."<td class=\"cell3\">Running</td>"
    ."<td class=\"cell3\">Finished</td>"
    ."</tr>"
    ."<tr>"
    ;
    if ($User->ADMIN || $User->ID == $Project->Owner->ID)
    {
      echo "<td class=\"centr\"><a href=\"#\" onclick=\"pop_ShowJobs('running=1','" . $Project->ID . "')\">" . $Project->Running . "</a></td>"
      ."<td class=\"centr\"><a href=\"#\" onclick=\"pop_ShowJobs('finished=1','" . $Project->ID . "')\">" . $Project->Finished . "</a></td>"
      ;
    }
    else
    {
      echo "<td class=\"centr\"><span class=\"fat\">" . $Project->Running . "</span></td>"
      ."<td class=\"centr\"><b>" . $Project->Finished . "</b></td>"
      ;
    }
    echo "</tr>"
    ."</table>"
    ."</div>"
    ."</div>"
    ."<p/>"
    ."<div class=\"simpleborder\">"
    ."<table class=\"full\" border=\"0\">"
    ."<tr>"
    ."<td class=\"projmem\" id=\"tab_member\">"
    ."<span class=\"fat\">Project Members:</span><p/>"
    ."<div class=\"btnclass\">"
    ;
    if (is_object ( $Project ))
    {
      GenerateMemberPHP ( $Project, $User );
    }
    echo "</div>"
    ."</td>"
    ."<td class=\"userinf\">"
    ."<div id=\"DivUserInfo\">"
    ."</div>"
    ."</td>"
    ."</tr>"
    ."</table>"
    ."</div>"
    ;
  }
  echo "</td>"
  ."</tr>"
  ."</table>"
  ."<p/>"
  ;
  if (! $User->ADMIN && (is_object ( $Project ) && ! $Project->Enabled))
  {
    echo "<span class=\"red\">PROJECT IS DISABLED</span>";
  }
}
//o-------------------------------------------------------------------------------o
function DisplayBillablePHP($Project, $User)
//o-------------------------------------------------------------------------------o
{
  if ($User->ADMIN)
  {
    echo '<input class="inputfont" type="checkbox" name="proj_billable" id="proj_billable';
    if (is_object($Project) && $Project->Billable)
    {
      echo ' checked="checked"';
    }
    echo ' value="1"'
        .' onchange="ChangeBillable(this);"'
        .'/>';
  }
  else
  {
    if (is_object($Project) && $Project->Billable)
    {
      echo 'Yes';
    }
    else
    {
      echo 'No';
    }
  }
}
//o-------------------------------------------------------------------------------o
function GenerateOverrunPHP($Project, $User)
//o-------------------------------------------------------------------------------o
{
  if ($User->ADMIN)
  {
    echo '<input class="inputfont" type="checkbox" name="allow_overrun" id="allow_overrun" ';
    if(!is_object($Project) || !$Project->Billable)
    {
      echo 'disabled="disabled" ';
    }
    else if (is_object($Project) && $Project->Overrun)
    {
      echo 'checked="checked" ';
    }
    echo 'value="1" />';
  }
  else
  {
    if (is_object($Project) && $Project->Overrun)
    {
      echo 'Yes';
    }
    else
    {
      echo 'No';
    }
  }
}
//o-------------------------------------------------------------------------------o
function DisplayCostInfo($Project, $User)
//o-------------------------------------------------------------------------------o
{
  if ($User->ADMIN)
  {
    echo "<select class=\"inputfont\" name=\"cid\">";
    $CostModels = new myJAM_CostModel();
    $CostIDList = $CostModels->ID;
    foreach($CostIDList as $cid)
    {
      echo "<option class=\"inputfont\" value=\"".$cid."\" ";
      if (is_object($Project) && $Project->CostModel->ID == $cid)
        {echo "selected=\"selected\"";}
      echo ">".$CostModels->Description[$cid].", Norm: ".$CostModels->Norm[$cid].", Over: ".$CostModels->Over[$cid]."</option>";
    }
        echo "</select>";
  }
  else
  {
    echo $Project->CostModel->Description.", Norm: ".$Project->CostModel->Norm.", Over: ".$Project->CostModel->Over;
  }
}
//o-------------------------------------------------------------------------------o
function DisplayQueues($Project, $User)
//o-------------------------------------------------------------------------------o
{
  echo "<table class=\"full\">";
  $HOSTCOLOR = "#999999";
  $HostList = new myJAM_Host();
  $HostIDList = $HostList->ID;
  foreach($HostIDList as $hid)
  {
    echo "<tr>"
    ."<td align=\"center\" style=\"background-color:".$HOSTCOLOR.";\">"
    ."<input class=\"inputfont\" type=\"checkbox\" name=\"host[".$hid."]\" value=\"".$hid."\" "
    ;
    if(is_object($Project))
      {echo checkbox($hid, $Project->Hosts);}
    if (! $User->ADMIN)
      {echo "disabled=\"disabled\" ";}
    echo " />"
    ."</td>"
    ."<td colspan=\"2\" style=\"background-color:".$HOSTCOLOR.";\">"
    ."<span class=\"fat\">".$HostList->Name[$hid]."</span>"
    ."</td>"
    ."</tr>"
    ;
    $toggle = 0;
    $QueueList = $HostList->Queues[$hid];
    foreach($QueueList as $queue)
    {
      if ($toggle==0)
      {
        $color = "#e47833";
        $toggle = 1;
      }
      else
      {
        $color = "#e4c7bf";
        $toggle = 0;
      }
      echo "<tr>"
      ."<td style=\"background-color:".$color."\">"
      ."</td>"
      ."<td align=\"center\" style=\"background-color:".$color."\">"
      ."<input class=\"inputfont\" type=\"checkbox\" name=\"queue[".$queue->ID."]\" value=\"".$queue->ID."\""
      ;
      if(is_object($Project))
        {echo checkbox($queue->ID, $Project->Queues);}
      if (!$User->ADMIN)
      {
        echo " disabled=\"disabled\" ";
      }
      echo " />"
      ."</td>"
      ."<td style=\"background-color:".$color."\">"
      .$queue->Name
      ."</td>"
      ."</tr>"
      ;
    }
  }
  echo "</table>";
}//end of function
//o-------------------------------------------------------------------------------o
function checkbox($query, $ObjList)
//o-------------------------------------------------------------------------------o
{
  if(isset($ObjList) && isset($query))
  {
    foreach($ObjList as $obj)
    {
      if ($obj->ID == $query)
        {return " checked=\"checked\"";}
    }
  }
  return "";
}
//o-------------------------------------------------------------------------------o
function GenerateMemberPHP($Project, $User)
//o-------------------------------------------------------------------------------o
{
  if(!isset($_GET["proj"]) || $_GET["proj"] != "new")
  {
    echo "<select class=\"inputfont\" size=\"10\"";
    if(!$User->ADMIN && $User->ID != $Project->Owner->ID)
      {print" disabled=\"disabled\"";}
    echo ">";
    $UserList = $Project->Users;
    foreach($UserList as $user)
    {
      echo "<option ";
      if ($User->ADMIN || $User->ID == $Project->Owner->ID)
        {echo "onclick=\"ShowUserInfo('".$user->ID."')\"";}
      echo ">".$user->UserName." (".$user->FullName.")</option>";
    }
    echo "</select>";
  }
  if ($User->ADMIN || $Project->Owner->ID == $User->ID)
  {
    if (!isset($_GET["proj"]) || $_GET["proj"] !="new")
      {echo "<p><a href=\"javascript:pop();\">Update Members</a></p>";}
    elseif (isset($_GET["proj"]) && $_GET["proj"] =="new")
      {echo "Add once project<br> is started";}
  }
}
//o-------------------------------------------------------------------------------o
function GenerateProjectOwnerPHP($Project, $User)
//o-------------------------------------------------------------------------------o
{
  if ($User->ADMIN)
  {
    echo "<select class=\"inputfont\" name=\"proj_uid_owner\" id=\"proj_uid_owner\">"
    ."<option class=\"inputfont\"></option>";
    $UserList = new myJAM_User();
    $UserIDList = $UserList->ID;
    foreach($UserIDList as $uid)
    {
      //MYJAM-490: Keinen formerUser anzeigen
      if (strncmp($UserList->UserName[$uid], 'formerUser', 10) != 0)
      {
        echo "<option class=\"inputfont\" value=\"".$uid."\" ";
        if (is_object($Project) && $uid == $Project->Owner->ID)
        {
          echo "selected=\"selected\"";
        }
        echo ">".$UserList->UserName[$uid];
        if ($UserList->FullName[$uid] != " ")
        {
          echo "(".$UserList->FullName[$uid].")";
        }
        echo "</option>";
      }
    }
    echo "</select>";
  }
  else
  {
    echo $Project->Owner->UserName;
    if ($Project->Owner->FullName != "")
    {
      echo "(".$Project->Owner->FullName.")";
    }
  }
}
//o-------------------------------------------------------------------------------o
function GenerateInstitutes($Project, $User)
//o-------------------------------------------------------------------------------o
{
  if ($User->ADMIN)
  {
    echo "<select class=\"inputfont\" name=\"institute_id\" id=\"institute_id\">";
    echo "<option class=\"inputfont\"></option>";
    $InstList = new myJAM_Institute();
    $InstIDList = $InstList->ID;
    foreach($InstIDList as $InstID)
    {
      echo "<option class=\"inputfont\" value=\"".$InstID."\" ";
      if (is_object($Project) && is_object($Project->Institute) &&
          $Project->Institute->ID == $InstID)
      {
        echo "selected=\"selected\"";
      }
      echo ">".$InstList->Name[$InstID];
    }
    echo "</select>";
  }
}
