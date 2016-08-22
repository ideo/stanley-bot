$(".conversation").click(function() { // make the entire div clickable (on home page)
  window.location = $(this).find("a").attr("href"); 
  return false;
});

$("#message_template").change(function() { // When template is selected, paste the content in textarea
	var template=$("#message_template option:selected").val();
	console.log(template);
	$("#message_body").val(template);
  $('#message_body').keyup();
});


$( ".delete-switch" ).click(function() { // confirm delete all messages
  $( "#delete-confirm" ).toggle();
});


$( "#delete-kill" ).click(function() { // confirmed delete all
  window.location.href='deleteMessage.php?mode=kill'
});
 
$( "#conversation-tag" ).change(function() {  // when a tag is applied to a conversation (via dropdown)
  window.location.href=$( "#conversation-tag option:selected" ).val();
});


$( "#newConvoBtn" ).click(function() { // start conversation with new number
  $('#newConvo').removeClass('error');
  var phoneNumber=$('#newConvo').val();
  if(validatePhoneNumber(phoneNumber)){
  	location.href='actions/conversation.php?no='+phoneNumber;
  }
  else{
  	$('#newConvo').addClass('error');
  	return false;
  }
});

 
function getConversation(phoneNumber){  // read upcoming messages from the db
    $('#convo').load('includes/getConversation.php?no='+phoneNumber);
    console.log(phoneNumber);
}


function countCharacters() { // count characters in the response form
    var text_max = 160;
    $('#character').html(text_max + ' characters remaining');

    $('#message_body').keyup(function() {
        var text_length = $('#message_body').val().length;
        var text_remaining = text_max - text_length;

        $('#character').html(text_remaining + ' characters remaining');
    });
}


function validatePhoneNumber(txtPhone) {
    var a = txtPhone;
    var filter = /^((\+[1-9]{1,4}[ \-]*)|(\([0-9]{2,3}\)[ \-]*)|([0-9]{2,4})[ \-]*)*?[0-9]{3,4}?[ \-]*[0-9]{3,4}?$/;
    if (filter.test(a)) {
        return true;
    }
    else {
        return false;
    }
}