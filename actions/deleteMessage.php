<?php
//deleteMessage.php

require('../includes/Twilio.php');
include('../config/config.php');
include('../includes/functions.php');

$smsId=$_GET['smsid'];
$mode=$_GET['mode'];

// TARGETED DELETE
// $smsId="SM811131bfd099432ea1578cee9118bf0e";
// $client->account->messages->delete($smsId);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<?php include('../config/meta.php');?>
</head>

<body>
<header class="header">
  <div class="bold" style="display:inline-block;">
    <span><a href="../index.php">Project <?php echo $projectName; ?></a></span>
  </div>
  <?php
  // delete all buttons
  if($mode==""){
  ?>
  <div class="right bold">
    <a class="delete-switch btnBorder headerElements">Delete</a>
    <div class="clear"></div>
  </div>
  <div class="clear"></div>
  <?php
  }
  else{
  ?>
  <div style="right headerElements">
    <strong><a href="deleteMessage.php" class="btnBorder headerElements">Back</a></strong>
  </div>
  <?php
  }
  ?>
</header>
<p id="delete-confirm" style="text-align: center; background-color: red; margin: 0; padding: 0 0 10px 0; color: #fff; font-weight: bold"><br/>Are you sure? <a id="delete-kill" class="cnfrm">Yes</a> / <a class="delete-switch cnfrm">No</a></p>

<section class="wrapper" style="padding: 50px">

<?php
  // delete all process
  if($mode=="kill"){
  	// DELETE ALL MESSAGES
  	foreach ($client->account->messages as $sms) {
  		echo "<br/> Message '".$sms->sid."' deleted<hr/>";
  		if ($sms->sid) $client->account->messages->delete($sms->sid);
  	}
  }
  // delete selected message
  elseif(($mode=="del")&&($smsId!="")){
  	if($client->account->messages->get($smsId)){
  		$client->account->messages->delete($smsId);
  		echo "<h3>Message '".$smsId."' successfully deleted</h3><hr/>";
  	}
  }

  // List all messages
  foreach ($client->account->messages as $sms) {
      echo "<div>";
      echo "<h1>".$sms->body."</h1><br/><a href=\"?mode=del&amp;smsid=".$sms->sid."\" class=\"del\">❌ Delete this message</a><br/><br/><hr/><br/>";
      echo "</div>";
  }

  ?>
</section>

<script src="../assets/js/jquery-2.1.3.js"></script>
<script src="../assets/js/main.js"></script>

</body>
</html>