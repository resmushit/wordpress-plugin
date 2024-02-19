<?php

require('resmushit.settings.php');
require('classes/resmushit.class.php');
require('classes/resmushitUI.class.php');
if ( defined( 'WP_CLI' ) && WP_CLI ) {
	require('classes/resmushitWPCLI.class.php');
}
//require('resmushit.admin.php');


/**
*
* Calculates time ago
*
* @param string $datetime time input
* @param boolean $full number of lines to keep
* @param string $adaptative will preserve line memory
* @return string
* @author Glavić
* @link https://stackoverflow.com/questions/1416697/converting-timestamp-to-time-ago-in-php-e-g-1-day-ago-2-days-ago
*/
function time_elapsed_string($duration, $full = false) {
		$datetime = "@" . (time() - $duration);

		$now = new DateTime;
		$then = new DateTime( $datetime );
		$diff = (array) $now->diff( $then );

		$diff['w']  = floor( $diff['d'] / 7 );
		$diff['d'] -= $diff['w'] * 7;

		$string = array(
				'y' => 'year',
				'm' => 'month',
				'w' => 'week',
				'd' => 'day',
				'h' => 'hour',
				'i' => 'minute',
				's' => 'second',
		);

		foreach( $string as $k => & $v )
		{
				if ( $diff[$k] )
				{
						$v = $diff[$k] . ' ' . $v .( $diff[$k] > 1 ? 's' : '' );
				}
				else
				{
						unset( $string[$k] );
				}
		}

    if (!$full) $string = array_slice($string, 0, 1);
    return $string ? implode(', ', $string) : __('just now', 'resmushit-image-optimizer');
}
