<?php
// index.php

require('includes/Twilio.php');
include('config/config.php');
include('includes/functions.php');
include('config/messages.php');

// get all contacts and...
$smsMessages=array();
$twilioMessages=$client->account->messages;


foreach($twilioMessages as $sms) {
  if($twilioPhoneNumber!=$sms->from) {
  	array_push($smsMessages,array("from"=>$sms->from,"to"=>$sms->to,"body"=>$sms->body,"date_sent"=>$sms->date_sent));   
  }
}

// ...eliminate duplicates
$smsMessages=array_multi_unique($smsMessages,"from");

// Mark Conversation as unread
if($_GET['mode']=="mark"){
  markConversationUnread($_GET['no']);
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
<?php include('config/meta.php');?>
</head>

<body>

<header class="header">
	<p class="bold">Project <?php echo $projectName; ?> / <?php echo $projectPhoneNumber; ?></p>
</header>

<section class="wrapper">
	
	<div class="filterBox">
	<span>Filter messages by...</span>
		<div>
			<ul>
				<?php 
            	foreach($conversationFilters as $f){
					if($f!=$conversationFilters[0]) echo "<li><a href=\"?mode=filter&f=".$f."\">#".$f."</a></li>";
				}
				if($_GET['mode']=="filter"){
					echo "<br><br><a href=\"index.php\">( Show All )</a>";
				}				
				?>			
			</ul>
		</div>
	</div>
	
	<section class="main">

		<div class="newConvoBox">
			<span>Start a conversation with
			<input type="tel" name="newConvo" id="newConvo" class="newConvo" placeholder="enter phone number" />
			<input type="button" value="go" id="newConvoBtn" class="newConvoBtn bold small" />
			</span>
		</div>

        <?php
        // List of all the conversation participants

       foreach ($smsMessages as $sms) {
          $html="";
          $flag=checkReadConversation($sms['from']); // check conversation is read / unread
          $thisFilter=getConversationFilter($sms['from']);
          $html.="\n<section class=\"conversation\">";
          $html.= "\n\t<p class=\"bold\"><a href=\"actions/conversation.php?no=".urlencode($sms['from'])."\">".formatAnonymousPhoneNumber($sms['from'])."</a></p>";

          if(!strpos($sms['body'],"#servicemsg")){
        		if($flag=="unread") $html.="\n\t<p class=\"text bold\">".shortenText($sms['body'],120)."</p>";
        		else $html.="\n\t<p class=\"text\">".shortenText($sms['body'],120)."</p>";

        		if(($thisFilter!=$conversationFilters[0])&&($thisFilter!=""))  $html.="\n\t<p class=\"ltgrey small bold\">#".$thisFilter."</p>\n";

      			$html.="\n\t<p class=\"ltgrey small bold\">".convertTime($sms['date_sent'],"UTC","America/New_York")."</p>\n";
      	  }

		   $html.="</section>";

		   // if filter mode is on, show only related conversations
		   if($_GET['mode']=="filter"){
		   	if($thisFilter==$_GET['f']) echo $html;
		   }
		   else{
		   	echo $html;
		   }

        }
	
	    mysqli_close($dbConnection);
    

        ?>

	</section>
</section>


<!--Scripts-->
<script src="assets/js/jquery-2.1.3.js"></script>
<script src="assets/js/main.js"></script>

</body>
</html>