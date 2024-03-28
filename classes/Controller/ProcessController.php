<?php
namespace Resmush\Controller;

use \reSmushitUI as reSmushitUI;
use \reSmushit as reSmushit;
use \Resmush\ShortPixelLogger\ShortPixelLogger as Log;

if (! defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}


class ProcessController
{

  protected static $instance;


  public static function getInstance()
  {
    if (is_null(self::$instance))
     self::$instance = new static();

    return self::$instance;
  }

  public function __construct()
  {
      $this->initHooks();
  }

  protected function initHooks()
  {
    add_action( 'delete_attachment', array($this,'delete_attachment') );

    if(get_option('resmushit_on_upload'))
    {
    	add_action('add_attachment', array($this,'get_meta_id') );
    }

		$cronController = CronController::getInstance();
		$doing_cron = $cronController->doing_cron();

    //Automatically optimize images if option is checked
    if(get_option('resmushit_on_upload') OR ( isset($_POST['action']) AND ($_POST['action'] === "resmushit_bulk_process_image" OR $_POST['action'] === "resmushit_optimize_single_attachment" )) OR (defined( 'WP_CLI' ) && WP_CLI ) OR ($doing_cron) )
    {
    	add_filter('wp_generate_attachment_metadata', array($this,'process_images'), 10, 2);
    }
  }

  public function unHookProcessor()
  {
    Log::addTemp('Unhooking Process Filter');
    remove_filter('wp_generate_attachment_metadata', array($this,'process_images'), 10, 2 );

  }

  /**
  *
  * Delete also -unsmushed file (ie. Original file) when deleting an attachment
  *
  * @param int postID
  * @return none
  */
  public function delete_attachment($postid) {
  	reSmushit::deleteOriginalFile($postid);
  }

  /**
  *
  * Make current attachment available
  *
  * @param attachment object
  * @return attachment object
  */
  public function get_meta_id($result){
  	global $attachment_id;
  	$attachment_id = $result;
  }
  //Automatically retrieve image attachment ID if option is checked

  /**
  *
  * Call resmush.it optimization for attachments
  *
  * @param attachment object
  * @param boolean preserve original file
  * @return attachment object
  */
  public function process_images($attachments, $attachment_id) {
  	$cumulated_original_sizes = 0;
  	$cumulated_optimized_sizes = 0;
  	$error = FALSE;

  	if(reSmushit::getDisabledState($attachment_id))
  		return $attachments;

  	if(empty($attachments)) {
		Log::addError("Error! The image #$attachment_id has no corresponding file on disk.", 'WARNING');
  		return $attachments;
  	}

  	$fileInfo = pathinfo(get_attached_file( $attachment_id ));
  	if(!isset($fileInfo['dirname'])) {
		Log::addError("Error! Incorrect file provided." . print_r($fileInfo, TRUE), 'WARNING');
  		return $attachments;
  	}
  	$basepath = $fileInfo['dirname'] . '/';
  	$extension = isset($fileInfo['extension']) ? $fileInfo['extension'] : NULL;

  	// Optimize only pictures/files accepted by the API
  	if( !in_array(strtolower($extension), resmushit::authorizedExtensions()) ) {
  		return $attachments;
  	}

  	if(!isset($attachments[ 'file' ])) {
		Log::addError("Error! Incorrect image " . print_r($attachments, TRUE), 'WARNING');
  		return $attachments;
  	}
  	$basefile = basename($attachments[ 'file' ]);

  	$statsObj = reSmushit::optimize($basepath . $basefile,true );
    $attachments['filesize'] = $statsObj->dest_size;
    $statistics[]  = $statsObj;

  	if(!isset($attachments[ 'sizes' ])) {
		Log::addError("Error! Unable to find image sizes." . print_r($attachments, TRUE), 'WARNING');
  		return $attachments;
  	}
  	foreach($attachments['sizes'] as $thumbnail_name => $image_style) {
        $statsObj = reSmushit::optimize($basepath . $image_style['file'], FALSE );
        // Update Filesize in the WP metadata
        if (isset($attachments['sizes'][$thumbnail_name]))
        {
          $attachments['sizes'][$thumbnail_name]['filesize'] = $statsObj->dest_size;
        }
        $statistics[] = $statsObj;
  	}

  	$count = 0;
  	foreach($statistics as $stat){
  		if($stat && !isset($stat->error)){
  			$cumulated_original_sizes += $stat->src_size;
  			$cumulated_optimized_sizes += $stat->dest_size;
  			$count++;
  		} else {
  			$error = TRUE;
  		}
  	}
  	if(!$error) {
  		$optimizations_successful_count = get_option('resmushit_total_optimized');
  		update_option( 'resmushit_total_optimized', $optimizations_successful_count + $count );
  		update_post_meta($attachment_id,'resmushed_quality', resmushit::getPictureQualitySetting());
  		update_post_meta($attachment_id,'resmushed_cumulated_original_sizes', $cumulated_original_sizes);
  		update_post_meta($attachment_id,'resmushed_cumulated_optimized_sizes', $cumulated_optimized_sizes);
  	}
    update_post_meta( $attachment_id, '_wp_attachment_metadata', $attachments );
  	return $attachments;
  }


} // class
