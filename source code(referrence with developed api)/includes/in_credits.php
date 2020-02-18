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

require_once(_FULLPATH."/access.php");
require_once(_FULLPATH."/HTMLCreator.php");
$myJAM_text ='';
$myJAM_text .=HTMLCreator::mk_span('&lt;my','class="fat"');
$myJAM_text .=HTMLCreator::mk_span('JAM/&gt;','class="fatital"');
// VAR Section -- Scroll down
$devels = array(
  HTMLCreator::mk_b('Project Coordinator, CTO: ').'Dr. rer. nat. Stephan Raub '
   .'('.HTMLCreator::mk_a('XING','href="http://www.xing.com/profile/Stephan_Raub"').')'
  ,HTMLCreator::mk_b('C++ Daemon, Web Developement: ').'B.Sc. Ingo Breuer '
 .'('.HTMLCreator::mk_a('M@il','href="mailto:ingo_breuer@t-online.de"').')'
 ,HTMLCreator::mk_b('Web Developement: ').'Michael Schlapa'
 ,HTMLCreator::mk_b('Web Developement: ').'Dennis-Bendert Schramm (retired)'
 ,HTMLCreator::mk_b('Webdesign: ').'Christoph Gierling (retired)'
 );
 $grateful = array(
  HTMLCreator::mk_a('Ajax Tabs Content Script (v 2.2)','href="http://dynamicdrive.com"')
 ,HTMLCreator::mk_a('Open Flash Chart','href="http://teethgrinder.co.uk/open-flash-chart"')
 ,HTMLCreator::mk_a('Icon Archive','href="http://iconarchive.com"')
 ,HTMLCreator::mk_a('FPDF','href="http://fpdf.org"')
 ,HTMLCreator::mk_a('Swazz BoxOver','href="http://boxover.swazz.org"')
 ,HTMLCreator::mk_a('Prototype JavaScript framework','href="http://prototypejs.org"')
 ,HTMLCreator::mk_a('MD5.js','href="http://aktuell.de.selfhtml.org/artikel/javascript/md5"')
 ,HTMLCreator::mk_a('webtoolkit','href="http://www.webtoolkit.info"')
 );
 $briskly = array(
  HTMLCreator::mk_a('JURA Coffee Machines','href="http://www.de.jura.com"')
 ,HTMLCreator::mk_a('Zend Studio 8.0.1','href="http://zend.com/de/products/studio"')
 ,HTMLCreator::mk_a('Zend Guard 5.5','href="http://zend.com/de/products/guard"')
 ,HTMLCreator::mk_a('Zend Server Community Edition','href="http://zend.com/de/products/server-ce"')
 ,HTMLCreator::mk_a('Atlassian JIRA 4.4','href="http://atlassian.com/software/jira"')
 ,HTMLCreator::mk_a('MySQL Workbench 5.1 SE','href="http://mysql.com/products/workbench"')
 ,HTMLCreator::mk_a('OTRS - Open Source Trouble Ticket System','href="http://otrs.org"')
 ,HTMLCreator::mk_a('Vim the editor (for real men)','href="http://vim.org"')
 ,HTMLCreator::mk_a('Eclipse C/C++ Development Tooling - CDT','href="http://eclipse.org/cdt"')
 ,HTMLCreator::mk_a('Apache Subversion','href="http://subversion.apache.org"')
 ,HTMLCreator::mk_a('NoMachine NX','href="http://nomachine.com"')
 ,HTMLCreator::mk_a('Closure Compiler','href="http://code.google.com/intl/de-DE/closure/compiler"')
 ,HTMLCreator::mk_a('YUI compressor','href="http://developer.yahoo.com/yui/compressor"')
 );
 ///
 ///          The Actual View Starts HERE
 ///
 echo HTMLCreator::mk_div(
HTMLCreator::mk_p(
     HTMLCreator::mk_span('Support / TroubleTicket: ','class="fat"')
    .HTMLCreator::mk_span('<a href="mailto:myjam-support@uni-duesseldorf.de">myjam-support(at)uni-duesseldorf.de</a>','style="font-weight: bold; color: #E47833;"')
 )
.HTMLCreator::mk_hr()
.HTMLCreator::mk_p(
 $myJAM_text.HTMLCreator::mk_span(' 2 ','class="fat"')
 .'has been developed within a cooperation of the <a href="http://www.zim.uni-duesseldorf.de/hpc">'
 .'Heinrich-Heine-University Duesseldorf</a> and <a href="http://www.bull.de">Bull Germany GmbH</a>'
 )
.HTMLCreator::mk_hr()
 .HTMLCreator::mk_p(
  'The '.$myJAM_text.' Team:'
  .HTMLCreator::mk_ul(HTMLCreator::arrayToList($devels,array_fill(0,count($devels),'class="credits"')))
  )
.HTMLCreator::mk_hr()
  .HTMLCreator::mk_p(
 $myJAM_text.HTMLCreator::mk_span(' 2 ','class="fat"')
 .'deeply grateful makes use of the following software projects:'
 .HTMLCreator::mk_ul(HTMLCreator::arrayToList($grateful,array_fill(0,count($grateful),'class="credits"')))
 )
.HTMLCreator::mk_hr()
 .HTMLCreator::mk_p(
 'The '.$myJAM_text.' Team briskly uses:'
 .HTMLCreator::mk_ul(HTMLCreator::arrayToList($briskly,array_fill(0,count($briskly),'class="credits"')))
 )
.HTMLCreator::mk_hr()
 .HTMLCreator::mk_h2('History','class="fat"')
 .HTMLCreator::mk_p(
'Intrinsically, we planned to make a fork of the open source project '
 .HTMLCreator::mk_a('myPBS','href="http://my-pbs.sourceforge.net"')
 .', which we found abandoned in version 0.8.4, dated from April, the 7th 2006.'
 .'But then we basically completely redeveloped it and only the look and some concepts ('
 .HTMLCreator::mk_i('e.g')
 .' the Service Units) are left.'
 )
 .HTMLCreator::mk_p(
 'The whole WebInterface has been developed in object oriented PHP using state-of-the-art design patterns and an object-relational mapping'
 .'for the database access. All generated web pages are '
 .HTMLCreator::mk_a('XHTML strict','href="http://www.w3.org/TR/xhtml1/"')
 .' conform and has '
 .'been tested against all common browsers and OSs. We excluded the Internet Exploder on purpose because of its insufficient CSS implementation.'
 )
 .HTMLCreator::mk_p(
 'Countless new features has been developed. We hope, you will find them useful.'
 )
 .HTMLCreator::mk_p(
 'We have been highly inspired by the concepts of the ancient myPBS. We even dared to name our project "myPBS-2" to show our respect.'
 .'But at the '
 .HTMLCreator::mk_a('International Supercomputing Conference 2008','href="http://www.supercomp.de/isc08/content"')
 .', while presenting our project, '
 .'we had been informed, that we are not allowed to use the substring "PBS" anymore '
 .'as it is a registered trademark of Altair Engineering, Inc.'
 )
 ,'class="simpleborder" id="credpad"')
 ;
