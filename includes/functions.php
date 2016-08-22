<?php
//functions.php


function convertTime($time, $currentTimezone, $timezoneRequired) {
    $dayLightFlag = false;
    $dayLgtSecCurrent = $dayLgtSecReq = 0;
    $system_timezone = date_default_timezone_get();
    $local_timezone = $currentTimezone;
    date_default_timezone_set($local_timezone);
    $local = date("Y-m-d H:i:s");

    date_default_timezone_set("GMT");
    $gmt = date("Y-m-d H:i:s");

    $require_timezone = $timezoneRequired;
    date_default_timezone_set($require_timezone);
    $required = date("Y-m-d H:i:s ");


    date_default_timezone_set($system_timezone);
    $diff1 = (strtotime($gmt) - strtotime($local));
    $diff2 = (strtotime($required) - strtotime($gmt));

    $date = new DateTime($time);

    $date->modify("+$diff1 seconds");
    $date->modify("+$diff2 seconds");

    if ($dayLightFlag) {
        $final_diff = $dayLgtSecCurrent + $dayLgtSecReq;
        $date->modify("$final_diff seconds");
    }

    $timestamp = $date->format("D, F jS, Y * h:i A");
    $timestamp=str_replace("*"," at ",$timestamp);

    return $timestamp;
}


function formatPhoneNumber($phoneNumber) {
    global $contactsList;
    $setNumber=$phoneNumber;
    $phoneNumber = preg_replace('/[^0-9]/','',$phoneNumber);

    if(strlen($phoneNumber) > 10) {
        $countryCode = substr($phoneNumber, 0, strlen($phoneNumber)-10);
        $areaCode = substr($phoneNumber, -10, 3);
        $nextThree = substr($phoneNumber, -7, 3);
        $lastFour = substr($phoneNumber, -4, 4);

        $phoneNumber = ' ('.$areaCode.') '.$nextThree.'-'.$lastFour;
    }
    else if(strlen($phoneNumber) == 10) {
        $areaCode = substr($phoneNumber, 0, 3);
        $nextThree = substr($phoneNumber, 3, 3);
        $lastFour = substr($phoneNumber, 6, 4);

        $phoneNumber = '('.$areaCode.') '.$nextThree.'-'.$lastFour;
    }
    else if(strlen($phoneNumber) == 7) {
        $nextThree = substr($phoneNumber, 0, 3);
        $lastFour = substr($phoneNumber, 3, 4);

        $phoneNumber = $nextThree.'-'.$lastFour;
    }

    if($contactsList[$setNumber]) $personName=$contactsList[$setNumber];
    else $personName="Unknown";
    $personName.=" &mdash; ".$phoneNumber."";

    return $personName;
}

function formatAnonymousPhoneNumber($phoneNumber) {
    global $contactsList;
    $setNumber=$phoneNumber;
    $phoneNumber = preg_replace('/[^0-9]/','',$phoneNumber);

    if(strlen($phoneNumber) > 10) {
        $countryCode = substr($phoneNumber, 0, strlen($phoneNumber)-10);
        $areaCode = substr($phoneNumber, -10, 3);
        $nextThree = substr($phoneNumber, -7, 3);
        $lastFour = substr($phoneNumber, -4, 4);

        $phoneNumber = ' ('.$areaCode.') ***-'.$lastFour;
    }
    else if(strlen($phoneNumber) == 10) {
        $areaCode = substr($phoneNumber, 0, 3);
        $nextThree = substr($phoneNumber, 3, 3);
        $lastFour = substr($phoneNumber, 6, 4);

        $phoneNumber = '('.$areaCode.') ***-'.$lastFour;
    }
    else if(strlen($phoneNumber) == 7) {
        $nextThree = substr($phoneNumber, 0, 3);
        $lastFour = substr($phoneNumber, 3, 4);

        $phoneNumber = '***-'.$lastFour;
    }

    if($contactsList[$setNumber]) $personName=$contactsList[$setNumber];
    else $personName="Unknown";
    $personName.=" &mdash; ".$phoneNumber."";

    return $personName;
}


function shortenText($t,$l){ // text, length
    if(strlen($t)>$l){
        $t=substr($t,0,$l)."...";
    }
    return $t;
}


function flagReadConversation($phoneNumber){ // Flag message as read
    global $dbConnection, $dbTable;

    $qSelect = "SELECT * FROM ".$dbTable." WHERE phonenumber='$phoneNumber';";
    $r = mysqli_query($dbConnection, $qSelect); 

    if($r->num_rows==0){
        $qInsert = "INSERT INTO ".$dbTable." (id,phonenumber,flagread,label,active) VALUES ('','$phoneNumber','r','No tag assigned','');";
        $r = mysqli_query($dbConnection, $qInsert); 
    }
    else {
        $qUpdate = "UPDATE ".$dbTable." SET flagread='r' WHERE phonenumber='$phoneNumber';";
        $r = mysqli_query($dbConnection, $qUpdate); 
    }

}



function markConversationUnread($phoneNumber) { // mark a conversation as unread
    global $dbConnection, $dbTable;
    $qUpdate = "UPDATE ".$dbTable." SET flagread='u' WHERE phonenumber='$phoneNumber';";
    //echo $qUpdate;
    $r = mysqli_query($dbConnection, $qUpdate); 

}

function assignTagConversation($phoneNumber, $filter){ // assigns a tag to a conversation
    global $dbConnection, $dbTable;
    $qUpdate = "UPDATE ".$dbTable." SET label='$filter' WHERE phonenumber='$phoneNumber';";
    //echo $qUpdate;
    $r = mysqli_query($dbConnection, $qUpdate); 
}


function setConversationFilters($phoneNumber){
    global $dbConnection, $dbTable, $conversationFilters;

    foreach($conversationFilters as $f){
        $qSelect = "SELECT * FROM ".$dbTable." WHERE phonenumber='$phoneNumber' AND label='$f';";
        $r = mysqli_query($dbConnection, $qSelect); 
        if($r->num_rows>0) echo "<option selected value=\"?mode=filter&no=".urlencode($phoneNumber)."&f=".$f."\">#".$f."</option>";
        else echo "<option value=\"?mode=filter&no=".urlencode($phoneNumber)."&f=".$f."\">#".$f."</option>";
    }   
}


function checkReadConversation($phoneNumber){ // check read / unread conversations
    global $dbConnection, $dbTable;
    $flagRead="unread";

    $qSelect = "SELECT * FROM ".$dbTable." WHERE phonenumber='$phoneNumber' AND flagread='r';";
    $r = mysqli_query($dbConnection, $qSelect); 
    //echo "<hr/>".$qSelect."<br/>COUNT ".$r->num_rows;

    if($r->num_rows>0){
        $flagRead="read";
    }
    else {
        $flagRead="unread";
    }
    return $flagRead;
}


function getConversationFilter($phoneNumber){ // check read / unread conversations
    global $dbConnection, $dbTable;

    $qSelect = "SELECT label FROM ".$dbTable." WHERE phonenumber='$phoneNumber';";
    $r = mysqli_query($dbConnection, $qSelect);
    if ($r) {
      $obj = $r->fetch_object();
    }
    return $obj->label;

}


function array_multi_unique($array, $key){ // array_unique for multidimensional arrays
    $temp_array = array();
    $i = 0;
    $key_array = array();
    
    foreach($array as $val){
        if(!in_array($val[$key],$key_array)){
            $key_array[$i] = $val[$key];
            $temp_array[$i] = $val;
        }
        $i++;
    }
    return $temp_array;
}


function formatImages($text) {
    $regex = '#\bhttps?://[^\s()<>]+(?:\([\w\d]+\)|([^[:punct:]\s]|/))#';
    return preg_replace_callback($regex, function ($matches) {
        $img=$matches[0];
        return '<img src=".$img."/>';
    }, $text);
}

/*
function debugThis($obj){
    echo "<pre>";
    var_dump($obj);
    echo "</pre>";
}
*/


?>