<?php
// conversation.php

require('../includes/Twilio.php');
include('../config/config.php');
include('../includes/functions.php');
include('../config/messages.php');

//check that a number is provided in querystring
if($_GET['no']=="") die("No access");
$fromNumber=$_GET['no'];



//if form is submitted, send sms
if($_GET['mode']=="sms"){

  $messageBody=$_POST['message_body']; 
  $messageNumber=$_POST['message_number'];

  if(($messageBody!="")&&($messageNumber!="")){

      if((strstr($messageBody, 'http://'))||(strstr($messageBody, 'https://'))){
         $sms = $client->account->messages->sendMessage(
              $twilioPhoneNumber, 
              $messageNumber,
              "",
              $messageBody
          );
      }
      else{
         $sms = $client->account->messages->sendMessage(
              $twilioPhoneNumber, 
              $messageNumber,
              $messageBody
          );
      }
  }

  // prevent form to be submitted again when refreshing page
  unset($_POST);
  header('location:conversation.php?no='.urlencode($fromNumber));

}

if($_GET['mode']=="filter"){ // assign tag to conversation
  assignTagConversation($fromNumber, $_GET['f']);
}


?>
<!DOCTYPE html>
<html lang="en">
<head>
<?php include('../config/meta.php');?>
</head>

<body>

<header class="header">
  <div class="left bold">
    <a href="../index.php" class="btnBorder headerElements">Back</a>
  </div>
  <div>
    <p class="bold">Project <?php echo $projectName; ?> / <?php echo $projectPhoneNumber; ?></p>
  </div>
</header>

<section class="wrapper">
  <div class="filterBox">
  <span>Conversation with<br><strong><?php echo formatAnonymousPhoneNumber($fromNumber)?></strong></span>
  </div>

    <div class="addfilter">
      <div class="left">
        <label>
          <select id="conversation-tag">
          <?php setConversationFilters($fromNumber); ?>  
          </select>
        </label>
      </div>
      <div class="right unread">
        <a href="../index.php?mode=mark&no=<?php echo urlencode($fromNumber);?>">Mark as unread?</a>
      </div>
    </div>

  <section id="convo" class="main altwrapper">
  <?php

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
        $html.= "\n\t<p class=\"text\">".formatImages($sms->body)."</p>";

        if($sms->num_media>0){
            foreach ($client->account->messages->get($sms->sid)->media as $media) {
                 $html.= "<img class=\"conversationImage\" src=\"https://api.twilio.com".$media->uri."\"/>\n";
            }    
        }

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
  </section>

  <form action="?mode=sms&no=<?php echo urlencode($fromNumber); ?>" method="POST">
    <section class="respond">

      <?php
      // Print a confirmation message when sms is successfully sent
      if($_GET['mode']=="sms") echo "<div class=\"confirm-response\"><p class=\"bold\">Message successfully sent to ".formatPhoneNumber($messageNumber).":</p><p class=\"text\">".$messageBody."</p></div>";
      ?>

      <div>
        <p class="bold">Send a message:</p>
      </div>  
      <div class="field">
          <select name="message_template" id="message_template" class="small">
            <option value="" disabled selected>Select a response template</option>
            <?php 
            $c=0;

            foreach($messagesTypes as $type){
                echo "<optgroup label=\"$type\">";
                foreach($messagesContent[$c] as $msg){
                    echo "<option value=\"$msg\">".shortenText($msg,100)."</option>";
                 }
                echo "</optgroup>";
                $c++;
            }
            ?>
          </select><br/><br/>


          <textarea id="message_body" name="message_body" maxlength="160"></textarea>
          <input type="hidden" id="message_number" name="message_number" value="<?php echo $fromNumber?>"/>
          <div id="character" class="small ltgrey bold"></div>
      </div>
      <div>
        <button class="bold small">Send</button>
      </div>
    </section>
  </form>

</section>



<!--Scripts-->
<script src="../assets/js/jquery-2.1.3.js"></script>
<script src="../assets/js/main.js"></script>
<script>

var fromNumber="<?php echo $fromNumber?>";

jQuery().ready(function(){
    intId = setInterval(getConversation, 30000, fromNumber);
    countCharacters();
});
</script>
</body>
</html>