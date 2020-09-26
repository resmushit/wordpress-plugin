<?php

 /**
   * ReSmushit Admin UI class
   * 
   * 
   * @package    Resmush.it
   * @subpackage UI
   * @author     Charles Bourgeaux <hello@resmush.it>
   */
Class reSmushitHelpers {

	/**
	 *
	 * Helper to format size in bytes
	 *
	 * @param  int $bytes filesize in bytes
	 * @return string rendered filesize
	 */
	public static function formatSize($bytes) {
	    if ($bytes > 0)
	    {
	        $unit = intval(log($bytes, 1024));
	        $units = array('B', 'KB', 'MB', 'GB');

	        if (array_key_exists($unit, $units) === true)
	        {
	            return sprintf('%d %s', $bytes / pow(1024, $unit), $units[$unit]);
	        }
	    }
	    return $bytes;
	}
}