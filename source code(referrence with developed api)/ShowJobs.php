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

define('_FULLPATH', realpath(dirname(__FILE__)));
require_once(_FULLPATH."/access.php");
require_once(_FULLPATH."/CloseWindow.php");
require_once(_FULLPATH."/includes/cmn_header.php");
echo '<head>'
    .'<link rel="shortcut icon" href="images/myJAM-icon.png" type="image/png"/>'
    .'<title>Job Info</title>'
    .'<link rel="stylesheet" type="text/css" href="css/style.css"/>'
    .'</head>'
    .'<body class="apopbody">'
;
?>
<script type="text/javascript" src="js/prototype.js"></script>
<script type="text/javascript" src="js/boxover.js"></script>
<script type="text/javascript" src="js/ShowJobs.js"></script>
<?php
echo '<table cellspacing="3" class="tabhead">'
    .'<tr>'
    .'<td>'
    .'<table border="0" cellspacing="0" cellpadding="0" class="tabhead2">'
    .'<tr>'
    .'<td class="gradhead1"><img src="images/multigradient_left.png" alt="gradientGFX" /></td>'
    .'<td class="gradhead4">'
    .'<div class="toolgrad3">Jobs</div>'
    .'</td>'
    .'<td class="gradhead5"><img src="images/multigradient_middle.png" alt="gradientGFX" /></td>'
    .'<td style="background-image: url(images/gradient_slice_2.png); background-repeat: repeat-x;"></td>'
    .'<td class="gradhead1"><img src="images/multigradient_right.png" alt="gradientGFX" /></td>'
    .'</tr>'
    .'</table>'
    .'</td>'
    .'</tr>'
    .'</table>'
    .'<p/>'
;
echo CloseWindow();
echo '<p/>'
.'<table id="Tab_ColSelect" class="transmaintable">'
.'<tr>'
.'<td colspan="12"><span class="transcostmodel">Select Columns</span></td>'
.'</tr>'
.'<tr>'
.'<td><input type="checkbox" id="col1" checked="checked" onclick="GetJobsRequest();" />JobID</td>'
.'<td><input type="checkbox" id="col2" checked="checked" onclick="GetJobsRequest();" />User</td>'
.'<td><input type="checkbox" id="col3" checked="checked" onclick="GetJobsRequest();" />Project</td>'
.'<td><input type="checkbox" id="col4" onclick="GetJobsRequest();" />Start Time</td>'
.'<td><input type="checkbox" id="col5" checked="checked" onclick="GetJobsRequest();" />Queueing Time</td>'
.'<td><input type="checkbox" id="col6" onclick="GetJobsRequest();" />Queue</td>'
.'<td><input type="checkbox" id="col7" checked="checked" onclick="GetJobsRequest();" />Architecture</td>'
.'<td><input type="checkbox" id="col8" checked="checked" onclick="GetJobsRequest();" />#Cores</td>' // # of Cores
.'<td><input type="checkbox" id="col9" checked="checked" onclick="GetJobsRequest();" />Service Units</td>'
.'<td><input type="checkbox" id="col12" disabled="disabled" />CPU Usage</td>'
.'</tr>'
.'</table>'
.'<div id="Div_JobTab" class="simpleborder"></div>'
;
?>
<script type="text/javascript">
GetJobsRequest();
</script>
<?php
echo '<p/>';
echo CloseWindow();
echo '</body></html>';
