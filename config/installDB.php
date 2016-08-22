<?php
// installDB.php

include('config/config.php');


// Create DB table if it doesn't exist
if(mysql_num_rows(mysqli_query($dbConnection, "SHOW TABLES LIKE '".$dbTable."'"))==0){
    $createDbTable="DROP TABLE IF EXISTS `".$dbTable."`;
    CREATE TABLE `".$dbTable."` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `phonenumber` varchar(50) DEFAULT NULL,
  `smsid` varchar(255) NOT NULL DEFAULT '',
  `flagread` varchar(11) DEFAULT '',
  `label` varchar(255) DEFAULT '',
  `active` varchar(11) DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
    mysqli_query($dbConnection, $createDbTable);
}

?>