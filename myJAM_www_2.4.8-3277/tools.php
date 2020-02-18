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
?>
<script type="text/javascript">
function onoff (zoneno)
{
  var open_value;
  if (document.getElementById)
  {
    if(document.getElementById("zone"+zoneno).style.display=="block")
    {
      document.getElementById("zone"+zoneno).style.display = "none";
      open_value=0;
      document.cookie = 'open'+zoneno+'=' + open_value;
    }
    else if (document.getElementById("zone"+zoneno).style.display=="none")
    {
      document.getElementById("zone"+zoneno).style.display = "block";
      open_value=1;
      document.cookie = 'open'+zoneno+'=' + open_value;
    }
  }
}
function pop_NewAppl()
{
  var w=window.open('appl-popup.php','name','height=330,width=500,location=no,menubar=no,status=yes,toolbar=no,scrollbars=no,resizable=no');
  if (w.blur)
  {
    w.focus();
  }
}
</script>
<?php
//o-------------------------------------------------------------------------------o
function makeLinkCSS($pagename,$parameter,$linkname)
//o-------------------------------------------------------------------------------o
{
  echo "<li class=\"cssdef\">";
  echo "<img src=\"images/arrow.png\" alt=\"arrow\" />";
  if ($pagename == "<javascript>"){
    echo "<a href=\"javascript:".$parameter."\">";
  }else{
    echo "<a href=\"main.php?page=" . $pagename ."&amp;" . $parameter . "\">";
  }
  echo "&nbsp;" . $linkname . "</a>"
  ."</li>"
  ;
}
//o-------------------------------------------------------------------------------o
function makeLinkSubMenuCSS($linkname)
//o-------------------------------------------------------------------------------o
{
  echo '<li class="cssdef">'
  .'<img src="images/arrow.png" alt="arrow" /><a href="javascript:onoff(\'' . str_replace(" ", "", $linkname) . '\')">' . "&nbsp;" . $linkname . '</a>'
  //echo '</li>'
  ;
}
//o-------------------------------------------------------------------------------o
function CreateMenuNode($name, $parameter)
//o-------------------------------------------------------------------------------o
{
  $node = array();
  $node["name"] = $name;
  $node["parameter"] = $parameter;
  $node["link"] = array();
  return $node;
}
//o-------------------------------------------------------------------------------o
function AddMenuNode(&$parent, $node)
//o-------------------------------------------------------------------------------o
{
  $parent["link"][$node["name"]] = $node;
}
//o-------------------------------------------------------------------------------o
function IterateMenuNode($parent, $level)
//o-------------------------------------------------------------------------------o
{
  $blockmenu=false;
  if($parent["name"] != "/root")
  {
    if (count($parent["link"]) > 0)
    {
      makeLinkSubMenuCSS($parent["name"]);
      $id = str_replace(" ", "", $parent["name"]); // no " " and no "_" allows in cookie name
      if (!isset($_COOKIE['open'.$id]) || $_COOKIE['open'.$id] != 1)
      {
        echo "<ul id=\"zone".$id."\" style=\"display:none\">";
        $blockmenu=true;
      }
      else
      {
        echo "<ul id=\"zone".$id."\" style=\"display:block\">"; $blockmenu=true;}
    }
    else
    {
      makeLinkCSS($parent["parameter"][0], $parent["parameter"][1], $parent["name"]);
      $blockmenu=false;
    }
  }
  if ($parent["link"])
  {
    if($blockmenu==false) {
      echo "<ul>";
    }
    foreach($parent["link"] as $node){
      IterateMenuNode($node,$level+1);
    }
    if($blockmenu==false) {
      echo "</ul>";
    }
    if ($level > 0){
      echo "</ul></li>";
    }
  }
}
//o-------------------------------------------------------------------------------o
//o      M A I N                                                                  o
//o-------------------------------------------------------------------------------o
if(!isset($_GET['why']) || $_GET['why'] != 'login')
{
  //Build the "User Tools" Menu
  $UserMenuTree['root'] = CreateMenuNode('/root', array());
  AddMenuNode($UserMenuTree['root'], CreateMenuNode('Home', array('welcome', '')));
  AddMenuNode($UserMenuTree['root'], CreateMenuNode('Projects', array('projectlist', 'user')));
  AddMenuNode($UserMenuTree['root'], CreateMenuNode('User Settings', array('user-settings', '')));
  AddMenuNode($UserMenuTree['root'], CreateMenuNode('Queue Info', array('queues', '')));
  AddMenuNode($UserMenuTree['root'], CreateMenuNode('Cluster Status', array('clusterstatus', '')));
  AddMenuNode($UserMenuTree['root'], CreateMenuNode('Cluster History', array('cluster-history', '')));
  AddMenuNode($UserMenuTree['root'], CreateMenuNode('Credits',array('credits', '')));
  //Build the "Admin Tools" Menu
  $AdminMenuTree['root'] = CreateMenuNode('/root', array());
  //The 'Admin Project' Menu
  $AdmProjMenu = CreateMenuNode('Admin Projects', array('', ''));
  AddMenuNode($AdmProjMenu, CreateMenuNode('Show Projects', array('projectlist', 'admin')));
  AddMenuNode($AdmProjMenu, CreateMenuNode('New Project', array('projects', 'mode=info&amp;proj=new')));
  AddMenuNode($AdminMenuTree['root'], $AdmProjMenu);
  //The 'Admin Users' Menu
  $AdmUsersMenu = CreateMenuNode('Admin Users', array('', ''));
  AddMenuNode($AdmUsersMenu, CreateMenuNode('Show Users', array('user-list', '')));
  AddMenuNode($AdmUsersMenu, CreateMenuNode('New User', array('user-settings', 'action=new')));
  AddMenuNode($AdminMenuTree['root'], $AdmUsersMenu);
  //The 'Admin Organisation' Menu
  $AdmOrgaMenu = CreateMenuNode('Admin Organisation', array('', ''));
  AddMenuNode($AdmOrgaMenu, CreateMenuNode('Show Departments', array('admin-departments', '')));
  AddMenuNode($AdmOrgaMenu, CreateMenuNode('Create Departments', array('admin-departments', 'action=create')));
  AddMenuNode($AdmOrgaMenu, CreateMenuNode('Show Institutes', array('admin-institutes', '')));
  AddMenuNode($AdmOrgaMenu, CreateMenuNode('Create Institutes', array('admin-institutes', 'action=create')));
  AddMenuNode($AdminMenuTree['root'], $AdmOrgaMenu);
  //The "Admin Queue" Menu
  $AdmQueueMenu = CreateMenuNode('Admin Queues', array('', ''));
  AddMenuNode($AdmQueueMenu, CreateMenuNode('Show Queues', array('queues', '')));
  AddMenuNode($AdminMenuTree['root'], $AdmQueueMenu);
  $AdmCostMenu = CreateMenuNode('Cost Models', array('', ''));
  AddMenuNode($AdmCostMenu, CreateMenuNode('Show Cost Models', array('admin-costs', '')));
  AddMenuNode($AdmCostMenu, CreateMenuNode('Add Cost Model', array('admin-costs', 'action=new')));
  AddMenuNode($AdminMenuTree['root'], $AdmCostMenu);
  //AddMenuNode($AdminMenuTree['root'], CreateMenuNode('Admin Nodes', array('admin-nodes', '')));
  AddMenuNode($AdminMenuTree['root'], CreateMenuNode('Announcements', array('announcements', '')));
  AddMenuNode($AdminMenuTree['root'], CreateMenuNode('Exit-Status Modification', array('admin-exit-status', '')));
  AddMenuNode($AdminMenuTree['root'], CreateMenuNode('Configuration', array('admin-config', '')));
  AddMenuNode($AdminMenuTree['root'], CreateMenuNode('Database', array('db_details', '')));
  $BillingMenuTree['root'] = CreateMenuNode('/root', array());
  AddMenuNode($BillingMenuTree['root'], CreateMenuNode('Transactions', array('admin-finances', '')));
  AddMenuNode($BillingMenuTree['root'], CreateMenuNode('Invoice Templates', array('in_voice_templates', '')));
  AddMenuNode($BillingMenuTree['root'], CreateMenuNode('Generate Invoice', array('invoice', '')));
  echo '<table style="width:100%">'
  .'<tr>'
  .'<td style="vertical-align:top">'
  .'<div style="width:165px;float:left;border:1px #000000 solid">'
  .'<div id="usertools" class="menuhead">User Tools</div>'
  .'<div class="menupos">';
  IterateMenuNode($UserMenuTree["root"], 0);
  if ($ActiveUser->ADMIN){
    echo '</div>'
    .'<div id="admintools" class="menuhead">Admin Tools</div>'
    .'<div class="menupos">';
    IterateMenuNode($AdminMenuTree["root"], 0);
    echo '</div>'
    .'<div id="billingtools" class="menuhead">Billing Tools</div>'
    .'<div class="menupos">';
    IterateMenuNode($BillingMenuTree["root"], 0);
    echo "</div>";
  } else {
    // User is not ADMIN
    echo "</div>";
  }
  echo '</div>';
  echo '</td>'
  .'<td style="vertical-align:top">'
  .'<div id="main" style="width:82%;float:right">'
  .'<div id="mainpart" class="toolgrad2">'
  .'<table class="tabhead" border="0" cellspacing="3" cellpadding="0">'
  .'<tr>'
  .'<td>'
  .'<table border="0" cellspacing="0" cellpadding="0" class="tabhead2">'
  .'<tr>'
  .'<td class="gradhead1"><img src="images/multigradient_left.png" alt="gradientGFX" /></td>'
  .'<td class="gradhead4">'
  .'<div class="toolgrad3">'.$title.'</div>'
  .'</td>'
  .'<td class="gradhead5"><img src="images/multigradient_middle.png" alt="gradientGFX" /></td>'
  .'<td class="gradhead6"></td>'
  .'<td class="gradhead1"><img src="images/multigradient_right.png" alt="gradientGFX" /></td>'
  .'</tr>'
  .'</table>'
  .'</td>'
  .'</tr>'
  .'</table>';
  ?>
<script type="text/javascript">
  var main=document.getElementById("main");
  main.style.width=window.innerWidth-190+"px"
  </script>
  <?php
  echo '<p></p>' ;
} // end of $_GET["why"]
