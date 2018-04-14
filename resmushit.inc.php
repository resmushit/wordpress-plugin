<?php

require('resmushit.settings.php');
require('classes/resmushit.class.php');
require('classes/resmushitUI.class.php');
require('resmushit.admin.php');



/**
* 
* Embedded file log function
*
* @param string $str text to log in file
* @return none
*/
function rlog($str) {
	if(get_option('resmushit_logs') == 0)
		return false;
	$str = "[".date('d-m-Y H:i:s')."] " . $str;
	$str = print_r($str, true) . "\n";
	$fp = fopen('../' . RESMUSHIT_LOGS_PATH, 'a+');
	fwrite($fp, $str);
	fclose($fp);
}