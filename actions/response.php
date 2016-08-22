<?php
// response.php

require('../includes/Twilio.php');
include('../config/config.php');
include('../includes/functions.php');
include('../config/messages.php');

$counter = $_SESSION['counter'];
if(!strlen($counter)) $counter = 0;

$fromNumber=$_REQUEST['From'];
$messageBody=$_REQUEST['Body'];
if(($fromNumber=="")||($fromNumber==$twilioPhoneNumber)) die("Phone number not valid");


// AUTOMATED RESPONSE (WHEN THE PROTOTYPING SESSION IS OVER)
if($automatedResponseOver=="y"){
	$sms = $client->account->messages->sendMessage(
 	    $twilioPhoneNumber,
 	    $fromNumber,
 	    $messageTeamClosed
 	);
 	die();
 }


// AUTOMATED RESPONSE IN TIME RANGES (WHEN THE TEAM IS SLEEPING)
if($automatedResponseTeamSleeping=="y"){
	$currentTime = time();
	$closeTime = strtotime($automatedResponseTimeStart);
	$openTime = strtotime($automatedResponseTimeEnd);

	if ($currentTime > $closeTime && $currentTime < $openTime) {
		$sms = $client->account->messages->sendMessage(
		    $twilioPhoneNumber,
		    $fromNumber,
		    $messageTeamOff
		);
		die();
	}
}


// Formatting elements for the notifications
$conversationURL=$siteURL."/actions/conversation.php?no=".urlencode($fromNumber);
$slackMessage=' <!channel>: "'.$messageBody.'" - <'.$conversationURL.'|View conversation>';


$data = "payload=" . json_encode(array(
        "channel"       =>  "#{$slackRoom}",
        "text"          =>  $slackMessage,
        "username"      =>  $slackBotName,
        "icon_emoji"    =>  $slackBotIcon,
        "unfurl_links"  =>  "true"
    ));


$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $slackWebHookURL);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$result = curl_exec($ch);
echo var_dump($result);
if($result === false)
{
    echo 'Curl error: ' . curl_error($ch);
}

curl_close($ch);



// AUTO-RESPONSE
$counter=0;

foreach ($client->account->messages as $sms) {
	if($fromNumber==$sms->from) $counter++;
}

if($counter==0) {
	$sms = $client->account->messages->sendMessage(
	    $twilioPhoneNumber,
        $fromNumber,
        $responseDefault
	);
}

?>
