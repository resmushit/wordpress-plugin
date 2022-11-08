<?php
/**
 * @package   resmushit
 * @author    Charles Bourgeaux <hello@resmush.it>
 * @license   GPL-2.0+
 * @link      http://www.resmush.it
 * @copyright 2022 Resmush.it
 *
 * @wordpress-plugin
 * Plugin Name:       reSmush.it Image Optimizer
 * Plugin URI:        https://wordpress.org/plugins/resmushit-image-optimizer/
 * Description:       Image Optimization API. Provides image size optimization
 * Version:           0.4.11
 * Timestamp:         2022.11.08
 * Author:            reSmush.it
 * Author URI:        https://resmush.it
 * Author:            Charles Bourgeaux
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path: 	  /languages
 * Text Domain:		  resmushit-image-optimizer
 */

require('resmushit.inc.php'); 
/**
* 
* Registering language plugin
*
* @param none
* @return none
*/
function resmushit_load_plugin_textdomain() {
    load_plugin_textdomain( 'resmushit', FALSE, plugin_basename( dirname( __FILE__ ) ) . '/languages' );
}
add_action( 'plugins_loaded', 'resmushit_load_plugin_textdomain' );





/**
* 
* Registering settings on plugin installation
*
* @param none
* @return none
*/
function resmushit_activate() {
	if ( is_super_admin() ) {
		if(get_option('resmushit_qlty') === false)
			update_option( 'resmushit_qlty', RESMUSHIT_DEFAULT_QLTY );
		if(get_option('resmushit_on_upload') === false)
			update_option( 'resmushit_on_upload', '1' );
		if(get_option('resmushit_statistics') === false)
			update_option( 'resmushit_statistics', '1' );
		if(get_option('resmushit_total_optimized') === false || get_option('resmushit_total_optimized') == "")
			update_option( 'resmushit_total_optimized', '0' );
		if(get_option('resmushit_cron') === false || get_option('resmushit_cron') == "")
			update_option( 'resmushit_cron', 0 );
		if(get_option('resmushit_cron_lastaction') === false  || get_option('resmushit_cron_lastaction') == "")
			update_option( 'resmushit_cron_lastaction', 0 );
		if(get_option('resmushit_cron_lastrun') === false || get_option('resmushit_cron_lastrun') == "")
			update_option( 'resmushit_cron_lastrun', 0 );
		if(get_option('resmushit_cron_firstactivation') === false || get_option('resmushit_cron_firstactivation') == "")
			update_option( 'resmushit_cron_firstactivation', 0 );
		if(get_option('resmushit_preserve_exif') === false || get_option('resmushit_preserve_exif') == "")
			update_option( 'resmushit_preserve_exif', 0 );
		if(get_option('resmushit_remove_unsmushed') === false || get_option('resmushit_remove_unsmushed') == "")
			update_option( 'resmushit_remove_unsmushed', 0 );
		if(get_option('resmushit_has_no_backup_files') === false || get_option('resmushit_has_no_backup_files') == "")
			update_option( 'resmushit_has_no_backup_files', 0 );
		if(get_option('resmushit_notice_close') === false || get_option('resmushit_notice_close') == "")
			update_option( 'resmushit_notice_close', 0 );
	}
}
register_activation_hook( __FILE__, 'resmushit_activate' );
add_action( 'admin_init', 'resmushit_activate' );


/**
 * Run using the 'init' action.
 */
function resmushit_init() {
	load_plugin_textdomain( 'resmushit-image-optimizer', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}
add_action( 'admin_init', 'resmushit_init' );


/**
* 
* Call resmush.it optimization for attachments
*
* @param attachment object
* @param boolean preserve original file
* @return attachment object
*/
function resmushit_process_images($attachments, $force_keep_original = TRUE) {
	global $attachment_id;
	$cumulated_original_sizes = 0;
	$cumulated_optimized_sizes = 0;
	$error = FALSE;

	if(reSmushit::getDisabledState($attachment_id))
		return $attachments;

	if(empty($attachments)) {
		rlog("Error! Attachment #$attachment_id has no corresponding file on disk.", 'WARNING');
		return $attachments;
	}

	$fileInfo = pathinfo(get_attached_file( $attachment_id ));
	if(!isset($fileInfo['dirname'])) {
		rlog("Error! Incorrect file provided." . print_r($fileInfo, TRUE), 'WARNING');
		return $attachments;
	}
	$basepath = $fileInfo['dirname'] . '/';
	$extension = isset($fileInfo['extension']) ? $fileInfo['extension'] : NULL;
	
	// Optimize only pictures/files accepted by the API
	if( !in_array(strtolower($extension), resmushit::authorizedExtensions()) ) {
		return $attachments;	
	}

	if(!isset($attachments[ 'file' ])) {
		rlog("Error! Incorrect attachment " . print_r($attachments, TRUE), 'WARNING');
		return $attachments;
	}
	$basefile = basename($attachments[ 'file' ]);

	

	$statistics[] = reSmushit::optimize($basepath . $basefile, $force_keep_original );

	if(!isset($attachments[ 'sizes' ])) {
		rlog("Error! Unable to find attachments sizes." . print_r($attachments, TRUE), 'WARNING');
		return $attachments;
	}
	foreach($attachments['sizes'] as $image_style) {
		$statistics[] = reSmushit::optimize($basepath . $image_style['file'], FALSE );
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
	return $attachments;
}
//Automatically optimize images if option is checked
if(get_option('resmushit_on_upload') OR ( isset($_POST['action']) AND ($_POST['action'] === "resmushit_bulk_process_image" OR $_POST['action'] === "resmushit_optimize_single_attachment" )) OR (defined( 'WP_CLI' ) && WP_CLI ) OR ($is_cron) )
	add_filter('wp_generate_attachment_metadata', 'resmushit_process_images');   
 





/**
* 
* Delete also -unsmushed file (ie. Original file) when deleting an attachment
*
* @param int postID
* @return none
*/
function resmushit_delete_attachment($postid) {
	reSmushit::deleteOriginalFile($postid);
}
add_action( 'delete_attachment', 'resmushit_delete_attachment' );	
 




/**
* 
* Make current attachment available
*
* @param attachment object
* @return attachment object
*/
function resmushit_get_meta_id($result){
	global $attachment_id;
	$attachment_id = $result;
}
//Automatically retrieve image attachment ID if option is checked
if(get_option('resmushit_on_upload'))
	add_filter('add_attachment', 'resmushit_get_meta_id');





/**
* 
* add Ajax action to fetch all unsmushed pictures
*
* @param none
* @return json object
*/
function resmushit_bulk_get_images() {
	if ( !isset($_REQUEST['csrf']) || ! wp_verify_nonce( $_REQUEST['csrf'], 'bulk_resize' ) ) {
		wp_send_json(json_encode(array('error' => 'Invalid CSRF token')));
		die();
	}
	if(!is_super_admin() && !current_user_can('administrator')) {
		wp_send_json(json_encode(array('error' => 'User must be at least administrator to retrieve these data')));
		die();
	}
	wp_send_json(reSmushit::getNonOptimizedPictures());
	die();
}	
add_action( 'wp_ajax_resmushit_bulk_get_images', 'resmushit_bulk_get_images' );	




/**
* 
* add Ajax action to change disabled state for an attachment
*
* @param none
* @return json object
*/
function resmushit_update_disabled_state() {
	if ( !isset($_REQUEST['data']['csrf']) || ! wp_verify_nonce( $_REQUEST['data']['csrf'], 'single_attachment' ) ) {
		wp_send_json(json_encode(array('error' => 'Invalid CSRF token')));
		die();
	}
	if(!is_super_admin() && !current_user_can('administrator')) {
		wp_send_json(json_encode(array('error' => 'User must be at least administrator to retrieve these data')));
		die();
	}
	if(isset($_POST['data']['id']) && $_POST['data']['id'] != null && isset($_POST['data']['disabled'])){
		echo wp_kses_post(reSmushit::updateDisabledState(sanitize_text_field((int)$_POST['data']['id']), sanitize_text_field($_POST['data']['disabled'])));
	}	
	die();
}	
add_action( 'wp_ajax_resmushit_update_disabled_state', 'resmushit_update_disabled_state' );	





/**
* 
* add Ajax action to optimize a single attachment in the library
*
* @param none
* @return json object
*/
function resmushit_optimize_single_attachment() {
	if ( !isset($_REQUEST['data']['csrf']) || ! wp_verify_nonce( $_REQUEST['data']['csrf'], 'single_attachment' ) ) {
		wp_send_json(json_encode(array('error' => 'Invalid CSRF token')));
		die();
	}
	if(!is_super_admin() && !current_user_can('administrator')) {
		wp_send_json(json_encode(array('error' => 'User must be at least administrator to retrieve these data')));
		die();
	}
	if(isset($_POST['data']['id']) && $_POST['data']['id'] != null){
		reSmushit::revert(sanitize_text_field((int)$_POST['data']['id']));
		wp_send_json(json_encode(reSmushit::getStatistics(sanitize_text_field((int)$_POST['data']['id']))));
	}	
	die();
}	
add_action( 'wp_ajax_resmushit_optimize_single_attachment', 'resmushit_optimize_single_attachment' );	





/**
* 
* add Ajax action to optimize a picture according to attachment ID
*
* @param none
* @return boolean
*/	
function resmushit_bulk_process_image() {
	if ( !isset($_REQUEST['csrf']) || ! wp_verify_nonce( $_REQUEST['csrf'], 'bulk_process_image' ) ) {
		wp_send_json(json_encode(array('error' => 'Invalid CSRF token')));
		die();
	}
	if(!is_super_admin() && !current_user_can('administrator')) {
		wp_send_json(json_encode(array('error' => 'User must be at least administrator to retrieve these data')));
		die();
	}
	rlog('Bulk optimization launched for file : ' . get_attached_file( sanitize_text_field((int)$_POST['data']['ID']) ));
	echo esc_html(reSmushit::revert(sanitize_text_field((int)$_POST['data']['ID'])));
	die();
}
add_action( 'wp_ajax_resmushit_bulk_process_image', 'resmushit_bulk_process_image' );





/**
* 
* add Ajax action to update statistics
*
* @param none
* @return json object
*/
function resmushit_update_statistics() {
	if ( !isset($_REQUEST['csrf']) || ! wp_verify_nonce( $_REQUEST['csrf'], 'bulk_process_image' ) ) {
		wp_send_json(json_encode(array('error' => 'Invalid CSRF token')));
		die();
	}
	if(!is_super_admin() && !current_user_can('administrator')) {
		wp_send_json(json_encode(array('error' => 'User must be at least administrator to retrieve these data')));
		die();
	}
	$output = reSmushit::getStatistics();
	$output['total_saved_size_formatted'] = reSmushitUI::sizeFormat($output['total_saved_size']);
	wp_send_json(json_encode($output));
	die();
}
add_action( 'wp_ajax_resmushit_update_statistics', 'resmushit_update_statistics' );





/**
 * add 'Settings' link to options page from Plugins
 * @param array $links
 * @return string
 */
function resmushit_add_plugin_page_settings_link($links) {
	if(is_string($links)) {
		$oneLink = $links;
		$links = array();
		$links[] = $oneLink; 
	}
	$links[] = '<a href="' . admin_url( 'upload.php?page=resmushit_options' ) . '">' . __('Settings', "resmushit-image-optimizer") . '</a>';
	return $links;
}
add_filter('plugin_action_links_'.plugin_basename(__FILE__), 'resmushit_add_plugin_page_settings_link');



/**
 * Trigger when the cron are activated for the first time
 * @param mixed old value for cron_activation option
 * @param mixed new value for cron_activation option
 */

function resmushit_on_cron_activation($old_value, $value) {
	if($value == 1 && (!get_option('resmushit_cron_firstactivation') || get_option('resmushit_cron_firstactivation') === 0)) {
		update_option( 'resmushit_cron_firstactivation', time() );
	}
}
add_action('update_option_resmushit_cron', 'resmushit_on_cron_activation', 100, 2);



/**
 * Declare a new time interval to run Cron
 * @param array $schedules
 * @return array
 */
function resmushit_add_cron_interval( $schedules ) {
	$schedules['resmushit_interval'] = array(
		'interval' => RESMUSHIT_CRON_FREQUENCY,
		'display' => esc_html__( __('Every', 'resmushit-image-optimizer') . ' ' . time_elapsed_string(RESMUSHIT_CRON_FREQUENCY) ),
	);
	return $schedules;
}
add_filter( 'cron_schedules', 'resmushit_add_cron_interval' );

if(!get_option('resmushit_cron') || get_option('resmushit_cron') === 0) {
	if (wp_next_scheduled ( 'resmushit_optimize' )) { 
		wp_clear_scheduled_hook('resmushit_optimize');
	}
} else {
	if (! wp_next_scheduled ( 'resmushit_optimize' )) {   
	    wp_schedule_event(time(), 'resmushit_interval', 'resmushit_optimize');
	} 
}



/**
 * Declare a new crontask for optimization bulk
 */
function resmushit_cron_process() {
	global $is_cron;
	$is_cron = TRUE;

	if((time() - get_option('resmushit_cron_lastaction')) < RESMUSHIT_CRON_TIMEOUT) {
		rlog('Another CRON process is running, process aborted.', 'WARNING');
		return FALSE;
	}
	update_option( 'resmushit_cron_lastrun', time() );
	update_option( 'resmushit_cron_lastaction', time() );

	// required if launch through wp-cron.php
	include_once( ABSPATH . 'wp-admin/includes/image.php' );

	add_filter('wp_generate_attachment_metadata', 'resmushit_process_images');
	rlog('Gathering unoptimized pictures from CRON');
	$unoptimized_pictures = json_decode(reSmushit::getNonOptimizedPictures(TRUE));
	rlog('Found ' . count($unoptimized_pictures->nonoptimized) . ' attachments');

	foreach($unoptimized_pictures->nonoptimized as $el) {
		if (wp_next_scheduled ( 'resmushit_optimize' )) { 
			//avoid to collapse two crons
			wp_unschedule_event(wp_next_scheduled('resmushit_optimize'), 'resmushit_optimize');
		}
		rlog('CRON Processing attachments #' . $el->ID);
		update_option( 'resmushit_cron_lastaction', time() );
		reSmushit::revert((int)$el->ID);
	}
}
add_action('resmushit_optimize', 'resmushit_cron_process');



/**
 * Return the RESMUSHIT CRON status according to last_execution variables
 * @return string
 */
function resmushit_get_cron_status() {
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

function resmushit_on_remove_unsmushed_change($old_value, $value) {
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
add_action('update_option_resmushit_remove_unsmushed', 'resmushit_on_remove_unsmushed_change', 100, 2);




/**
* 
* add Ajax action to remove backups (-unsmushed) of the filesystem
*
* @param none
* @return json object
*/
function resmushit_remove_backup_files() {
	$return = array('success' => 0);
	if ( !isset($_REQUEST['csrf']) || ! wp_verify_nonce( $_REQUEST['csrf'], 'remove_backup' ) ) {
		wp_send_json(json_encode(array('error' => 'Invalid CSRF token')));
		die();
	}
	if(!is_super_admin() && !current_user_can('administrator')) {
		wp_send_json(json_encode(array('error' => 'User must be at least administrator to retrieve these data')));
		die();
	}

	$files=detect_unsmushed_files();
	
	foreach($files as $f) {
		if(unlink($f)) {
			$return['success']++;
		}
	}
	update_option( 'resmushit_has_no_backup_files', 1);
	wp_send_json(json_encode($return));

	die();
}	
add_action( 'wp_ajax_resmushit_remove_backup_files', 'resmushit_remove_backup_files' );	


/**
* 
* retrieve Attachment ID from Path
* from : https://pippinsplugins.com/retrieve-attachment-id-from-image-url/
*
* @param imageURL
* @return json object
*/
function resmushit_get_image_id($image_url) {
	global $wpdb;
	$attachment = $wpdb->get_col($wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE guid='%s';", $image_url )); 
        return $attachment[0]; 
}

/**
* 
* add Ajax action to restore backups (-unsmushed) from the filesystem
*
* @param none
* @return json object
*/
function resmushit_restore_backup_files() {
	if ( !isset($_REQUEST['csrf']) || ! wp_verify_nonce( $_REQUEST['csrf'], 'restore_library' ) ) {
		wp_send_json(json_encode(array('error' => 'Invalid CSRF token')));
		die();
	}
	if(!is_super_admin() && !current_user_can('administrator')) {
		wp_send_json(json_encode(array('error' => 'User must be at least administrator to retrieve these data')));
		die();
	}
	$files=detect_unsmushed_files();
	$return = array('success' => 0);
	$wp_upload_dir=wp_upload_dir();

	foreach($files as $f) {
		$dest = str_replace('-unsmushed', '', $f);
		$pictureURL = str_replace($wp_upload_dir['basedir'], $wp_upload_dir['baseurl'], $dest);
		$attachementID = resmushit_get_image_id($pictureURL);

		if(reSmushit::revert($attachementID, true)) {
			if(unlink($f)) {
				$return['success']++;
			}
		}
	}
	wp_send_json(json_encode($return));
	die();
}	
add_action( 'wp_ajax_resmushit_restore_backup_files', 'resmushit_restore_backup_files' );	


/**
* 
* add Ajax action to close permanently notice
*
* @param none
* @return json object
*/
function resmushit_notice_close() {
	$return = FALSE;
	if ( !isset($_REQUEST['csrf']) || ! wp_verify_nonce( $_REQUEST['csrf'], 'notice_close' ) ) {
		wp_send_json(json_encode(array('error' => 'Invalid CSRF token')));
		die();
	}
	if(!is_super_admin() && !current_user_can('administrator')) {
		wp_send_json(json_encode(array('error' => 'User must be at least administrator to retrieve these data')));
		die();
	}
	if(update_option( 'resmushit_notice_close', 1 )) {
		$return = TRUE;
	}
	wp_send_json(json_encode(array('status' => $return)));
	die();
}	
add_action( 'wp_ajax_resmushit_notice_close', 'resmushit_notice_close' );


/**
* 
* add Notice information for Shortpixel offer
*
* @param none
* @return json object
*/
function resmushit_general_admin_notice(){	
	// Expired offer
	if(time() > strtotime("21 November 2022")) {
		return FALSE;
	}
	// Already seen notice
	if(get_option('resmushit_notice_close') == 1) {
		return FALSE;
	}
	$allowed_pages = array(
		'media_page_resmushit_options',
		'upload', 
		'plugins',
		'edit-post',
		'media',
		'attachment');
		
	if ( function_exists( 'get_current_screen' ) ) {
		$current_page = get_current_screen();
	}

	if ( isset( $current_page->id ) && in_array( $current_page->id, $allowed_pages ) ) {
		echo "
			<div class='notice notice-success is-dismissible rsmt-notice' data-csrf='" . wp_create_nonce( 'notice_close' ) . "' data-dismissible='disable-done-notice-forever' data-notice='resmushit-notice-shortpixel'>
			<div class='txt-center'><img src='". RESMUSHIT_BASE_URL . "images/shortpixel-resmushit.png' /></div>
				<div class='extra-padding'><h4 class='no-uppercase'>Limited time, unique offer in partnership with <a target='_blank' href='https://www.shortpixel.com' title='Shortpixel'>ShortPixel</a></h4> <ul><li><em>Unlimited</em> monthly credits</li><li>Optimize All your website's JPEG, PNG, (animated)GIF and PDFs with ShortPixel's SmartCompress algorithms.</li><li>Generate <em>next-gen image</em> <a href='https://shortpixel.com/blog/how-webp-images-can-speed-up-your-site/' title='How WebP can speed up your website' target='_blank'>format WebP</a> for ALL your images.</li><li>No size limit</li><li><em><a href='https://status.shortpixel.com/' target='_blank' title='Status page of Shortpixel'>99.9%</a></em> service availability</li></ul> </div>
				<div class='txt-center'><a class='button button-primary' target='_blank' href='https://unlimited.shortpixel.com' title='Subscribe to the premium offer'>Subscribe for <span class='txt-through'>$41.66</span> $9.99/mo  until Nov 20th</a></div>
			
		</div>";
	}
    
}
add_action('admin_notices', 'resmushit_general_admin_notice');


/**
* 
* Declares WPCLI extension if in WP_CLI context
*
*/
if( defined( 'WP_CLI' ) && WP_CLI ) {
	WP_CLI::add_command( 'resmushit', 'reSmushitWPCLI' );
}
