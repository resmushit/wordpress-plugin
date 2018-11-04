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
		return FALSE;

	if( !is_writable('../' . RESMUSHIT_LOGS_PATH) ) {
		return FALSE;
	}

	// Preserve file size under a reasonable value
	if(file_exists('../' . RESMUSHIT_LOGS_PATH)){
		if(filesize('../' . RESMUSHIT_LOGS_PATH) > RESMUSHIT_LOGS_MAX_FILESIZE) {
			$logtailed = logtail('../' . RESMUSHIT_LOGS_PATH, 20);
			$fp = fopen('../' . RESMUSHIT_LOGS_PATH, 'w');
			fwrite($fp, $logtailed);
			fclose($fp);
		}
	}
	
	$str = "[".date('d-m-Y H:i:s')."] " . $str;
	$str = print_r($str, true) . "\n";
	$fp = fopen('../' . RESMUSHIT_LOGS_PATH, 'a+');
	fwrite($fp, $str);
	fclose($fp);
}


/**
* 
* Tail function for files
*
* @param string $filepath path of the file to tail
* @param string $lines number of lines to keep
* @param string $adaptative will preserve line memory
* @return tailed file
* @author Torleif Berger, Lorenzo Stanco
* @link http://stackoverflow.com/a/15025877/995958
* @license http://creativecommons.org/licenses/by/3.0/
*/
function logtail($filepath, $lines = 1, $adaptive = true) {
	
	$f = @fopen($filepath, "rb");
	if ($f === false) return false;
	if (!$adaptive) $buffer = 4096;
	else $buffer = ($lines < 2 ? 64 : ($lines < 10 ? 512 : 4096));
	fseek($f, -1, SEEK_END);
	if (fread($f, 1) != "\n") $lines -= 1;
	
	$output = '';
	$chunk = '';

	while (ftell($f) > 0 && $lines >= 0) {
		$seek = min(ftell($f), $buffer);
		fseek($f, -$seek, SEEK_CUR);
		$output = ($chunk = fread($f, $seek)) . $output;
		fseek($f, -mb_strlen($chunk, '8bit'), SEEK_CUR);
		$lines -= substr_count($chunk, "\n");
	}

	while ($lines++ < 0) {
		$output = substr($output, strpos($output, "\n") + 1);
	}
	fclose($f);
	return trim($output);
}
