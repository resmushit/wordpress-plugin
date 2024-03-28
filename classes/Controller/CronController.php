<?php
namespace Resmush\Controller;

use \reSmushitUI as reSmushitUI;
use \reSmushit as reSmushit;
use \Resmush\ShortPixelLogger\ShortPixelLogger as Log;

if (! defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}


class CronController
{
  protected static $instance;

	protected $doing_cron = false;


  public static function getInstance()
  {
    if (is_null(self::$instance))
     self::$instance = new static();

    return self::$instance;
  }

  public function __construct()
  {
     $this->initHooks();
     $this->checkSchedule();
  }

  protected function initHooks()
  {
      add_action('update_option_resmushit_cron', array($this,'on_cron_activation'), 100, 2);
      add_filter( 'cron_schedules', array($this,'add_cron_interval') );
      add_action('resmushit_optimize', array($this,'cron_process') );
      add_action('update_option_resmushit_remove_unsmushed', array($this,'on_remove_unsmushed_change'), 100, 2);


  }

  protected function checkSchedule()
  {
      if(! get_option('resmushit_cron') || get_option('resmushit_cron') === 0) {
       if (wp_next_scheduled ( 'resmushit_optimize' )) {
         wp_clear_scheduled_hook('resmushit_optimize');
       }
      } else {
       if (! wp_next_scheduled ( 'resmushit_optimize' )) {
           wp_schedule_event(time(), 'resmushit_interval', 'resmushit_optimize');
       }
      }
  }

  /**
   * Trigger when the cron are activated for the first time
   * @param mixed old value for cron_activation option
   * @param mixed new value for cron_activation option
   */

  public function on_cron_activation($old_value, $value) {
  	if($value == 1 && (!get_option('resmushit_cron_firstactivation') || get_option('resmushit_cron_firstactivation') === 0)) {
  		update_option( 'resmushit_cron_firstactivation', time() );
  	}
  }

  /**
   * Declare a new time interval to run Cron
   * @param array $schedules
   * @return array
   */
  public function add_cron_interval( $schedules ) {
  	$schedules['resmushit_interval'] = array(
  		'interval' => RESMUSHIT_CRON_FREQUENCY,
  		'display' => esc_html__( __('Every', 'resmushit-image-optimizer') . ' ' . time_elapsed_string(RESMUSHIT_CRON_FREQUENCY) ),
  	);
  	return $schedules;
  }

	public function doing_cron()
	{
		 return $this->doing_cron;
	}

  /**
   * Declare a new crontask for optimization bulk
   */
  public function cron_process() {

		$this->doing_cron = true;

  	if((time() - get_option('resmushit_cron_lastaction')) < RESMUSHIT_CRON_TIMEOUT) {
  		Log::addWarn('Another CRON process is running, process aborted.');
  		return FALSE;
  	}
  	update_option( 'resmushit_cron_lastrun', time() );
  	update_option( 'resmushit_cron_lastaction', time() );

  	// required if launch through wp-cron.php
  	include_once( ABSPATH . 'wp-admin/includes/image.php' );

  	add_filter('wp_generate_attachment_metadata', array(\Resmush()->process(), 'process_images'), 10, 2);
  	Log::addDebug('Gathering unoptimized pictures from CRON');
  	$unoptimized_pictures = json_decode(reSmushit::getNonOptimizedPictures(TRUE));
  	Log::addDebug('Found ' . count($unoptimized_pictures->nonoptimized) . ' attachments');

  	foreach($unoptimized_pictures->nonoptimized as $el) {
  		if (wp_next_scheduled ( 'resmushit_optimize' )) {
  			//avoid to collapse two crons
  			wp_unschedule_event(wp_next_scheduled('resmushit_optimize'), 'resmushit_optimize');
  		}
  		Log::addDebug('CRON Processing attachments #' . $el->ID);
  		update_option( 'resmushit_cron_lastaction', time() );
  		reSmushit::revert((int)$el->ID);
  	}
  }

  /**
   * Return the RESMUSHIT CRON status according to last_execution variables
   * @return string
   */
   public function get_cron_status() {
    	if(get_option('resmushit_cron') == 0) {
    		return 'DISABLED';
    	}
    	if(!defined('DISABLE_WP_CRON') OR DISABLE_WP_CRON == false) {
    		return 'MISCONFIGURED';
    	}

    	if(get_option('resmushit_cron_lastrun') == 0 && (time() - get_option('resmushit_cron_firstactivation') > 2*RESMUSHIT_CRON_FREQUENCY)) {
    		return 'NEVER_RUN';
    	}
    	if(get_option('resmushit_cron_lastrun') != 0 && (time() - get_option('resmushit_cron_lastrun') > 2*RESMUSHIT_CRON_FREQUENCY)) {
    		return 'NO_LATELY_RUN';
    	}
    	return 'OK';
  }

  /**
   * Trigger when the cron are activated for the first time
   * @param mixed old value for cron_activation option
   * @param mixed new value for cron_activation option
   */

  public function on_remove_unsmushed_change($old_value, $value) {
  	$old_value = (boolean)$old_value;
  	$value = (boolean)$value;
  	if($old_value == $value) {
  		return TRUE;
  	} else {
  		//if remove backup is activated
  		if($value === TRUE) {
  			if(!resmushit::hasAlreadyRunOnce()) {
  				update_option( 'resmushit_has_no_backup_files', 1);
  			} else {
  				update_option( 'resmushit_has_no_backup_files', 0);
  			}
  		} else {
  			update_option( 'resmushit_has_no_backup_files', 0);
  		}
  	}
  }


} // class
