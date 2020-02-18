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

require_once(_FULLPATH."/classes/class_myJAM_Communicator.php");
require_once(_FULLPATH."/classes/class_myJAM_Message.php");
require_once(_FULLPATH."/classes/class_myJAM_User.php");
class myJAM_Mail extends myJAM_Communicator
{
//o-------------------------------------------------------------------------------o
  static function send($message = NULL, $recipents = NULL)
//o-------------------------------------------------------------------------------o
  {
    if (!is_a($message, 'myJAM_Message'))
    {
      throw new Exception('message MUST be an instance of myJAM_Message in myJAM_Mail::send()');
    }
    $to = '';
    if($recipents)
    {
      echo "Reading Recipents...<br>";
      foreach($recipents as $user)
      {
        if(!is_a($user, 'myJAM_User'))
        {
          throw new Exception('Only instances of myJAM_User accepted as recipent');
        }
        if(self::ValidateEmail($user->eMail))
        {
          if(empty($to))
          {
            $to .= ',';
          }
          $to .= $user->eMail;
        }
      }
    }
    else
    {
      echo "Sending to ALL...<br>";
      $Users = new myJAM_User();
      foreach($Users->ID as $id)
      {
        if(self::ValidateEmail($Users->eMail[$id]))
        {
          if(!empty($to))
          {
            $to .= ', ';
          }
          $to .= $Users->eMail[$id];
        }
      }
    }
    $_CFG_MAIL_FROM = '';
    $_CFG_MAIL_REPLY_TO = '';
    $_CFG_MAIL_ENVELOPE_SENDER = '';
    $_CFG_MAIL_SIGNATURE ='';
    require(_FULLPATH . '/config/CFG_mail.php');
    $header = 'MIME-Version: 1.0' . "\n"
            . 'Content-type: text/plain; charset=iso-8859-1'."\n"
            . 'X-Mailer: myJAM - The Sophisticated Job Accounting and Monitoring Tool'."\n"
            . "From: $_CFG_MAIL_FROM\n"
            . "Reply-To: $_CFG_MAIL_FROM\n"
            . "BCC: $to\n";
    $ret = mail('',
                $message->Title,
                html_entity_decode(str_replace("\r","",$message->Content)).$_CFG_MAIL_SIGNATURE,
                $header,
                '-f '.$_CFG_MAIL_ENVELOPE_SENDER);
    if($ret != 1)
    {
      echo "<b>Error while sending mail<b/><br/>";
    }
  }
//o-------------------------------------------------------------------------------o
  private static function ValidateEmail($email)
//o-------------------------------------------------------------------------------o
  {
    if (!preg_match('/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/', $email))
    {
      return false;
    }
    return true;
  }
}
