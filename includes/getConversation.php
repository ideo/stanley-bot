<?php
//getConversation.php
require('Twilio.php');
include('config.php');
include('functions.php');
include('messages.php');


$fromNumber=$_GET['no'];
$listMessages=array();


// List of all messages by participant
foreach ($client->account->messages as $sms) {
  $html="";
  if(($fromNumber==$sms->from)||($fromNumber==$sms->to)){

    // flag read/unread messages
    if($_GET['mode']!="mark") flagReadConversation($sms->from);

    //check who is sending the message
    if($sms->from==$twilioPhoneNumber) $style="response";
    else $style="";
    if(!strpos($sms->body,"#servicemsg")){ // exclude service messages
      $html.= "\n<section class=\"sms ".$style."\">";
      $html.= "\n\t<p class=\"bold\">".formatAnonymousPhoneNumber($sms->from)."</p>";
      $html.= "\n\t<p class=\"text\">".$sms->body."</p>";
      $html.= "\n\t<p class=\"ltgrey small bold\">".convertTime($sms->date_sent,"UTC","America/New_York")."</p>\n";
      $html.= "</section>";
    }
  }
  $listMessages[]=$html;
}


$listMessages=array_reverse ($listMessages);
foreach ($listMessages as $msg) {
  echo $msg;
}


?>