<?php
/**
 * @package   resmushit
 * @author    Charles Bourgeaux <hello@resmush.it>
 * @license   GPL-2.0+
 * @link      http://www.resmush.it
 * @copyright 2019 Resmush.it
 *
 * @wordpress-plugin
 * Plugin Name:       reSmush.it Image Optimizer
 * Plugin URI:        https://resmush.it
 * Description:       Image Optimization API. Provides image size optimization
 * Version:           0.1.22
 * Timestamp:         2019.01.20
 * Author:            reSmush.it
 * Author URI:        https://resmush.it
 * Author:            Charles Bourgeaux
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path: 	  /languages
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
		if(!get_option('resmushit_qlty'))
			update_option( 'resmushit_qlty', RESMUSHIT_DEFAULT_QLTY );
		if(!get_option('resmushit_on_upload'))
			update_option( 'resmushit_on_upload', '1' );
		if(!get_option('resmushit_statistics'))
			update_option( 'resmushit_statistics', '1' );
		if(!get_option('resmushit_total_optimized'))
			update_option( 'resmushit_total_optimized', '0' );
	}
}
register_activation_hook( __FILE__, 'resmushit_activate' );





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

	$fileInfo = pathinfo(get_attached_file( $attachment_id ));
	$basepath = $fileInfo['dirname'] . '/';
	$extension = isset($fileInfo['extension']) ? $fileInfo['extension'] : NULL;
	$basefile = basename($attachments[ 'file' ]);

	// Optimize only pictures/files accepted by the API
	if( !in_array($extension, resmushit::authorizedExtensions()) ) {
		return $attachments;	
	}

	$statistics[] = reSmushit::optimize($basepath . $basefile, $force_keep_original );

	foreach($attachments['sizes'] as $image_style)
		$statistics[] = reSmushit::optimize($basepath . $image_style['file'], FALSE );
	
	$count = 0;
	foreach($statistics as $stat){
		if($stat && !isset($stat->error)){
			$cumulated_original_sizes += $stat->src_size;
			$cumulated_optimized_sizes += $stat->dest_size;
			$count++;
		} else
			$error = TRUE;
	}
	if(!$error) {
		$optimizations_successful_count = get_option('resmushit_total_optimized');
		update_option( 'resmushit_total_optimized', $optimizations_successful_count + $count );

		update_post_meta($attachment_id,'resmushed_quality', resmushit::getPictureQualitySetting());
		if(get_option('resmushit_statistics')){
			update_post_meta($attachment_id,'resmushed_cumulated_original_sizes', $cumulated_original_sizes);
			update_post_meta($attachment_id,'resmushed_cumulated_optimized_sizes', $cumulated_optimized_sizes);
		}
	}
	return $attachments;
}
//Automatically optimize images if option is checked
if(get_option('resmushit_on_upload') OR ( isset($_POST['action']) AND $_POST['action'] === "resmushit_bulk_process_image" ))
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
	echo reSmushit::getNonOptimizedPictures();
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
	if(isset($_POST['data']['id']) && $_POST['data']['id'] != null && isset($_POST['data']['disabled'])){
		echo reSmushit::updateDisabledState(sanitize_text_field($_POST['data']['id']), sanitize_text_field($_POST['data']['disabled']));
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
	if(isset($_POST['data']['id']) && $_POST['data']['id'] != null){
		reSmushit::revert(sanitize_text_field($_POST['data']['id']));
		echo json_encode(reSmushit::getStatistics($_POST['data']['id']));
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
	rlog('Bulk optimization launched for file : ' . get_attached_file( sanitize_text_field($_POST['data']['ID']) ));
	echo reSmushit::revert(sanitize_text_field($_POST['data']['ID']));
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
	$output = reSmushit::getStatistics();
	$output['total_saved_size_formatted'] = reSmushitUI::sizeFormat($output['total_saved_size']);
	echo json_encode($output);
	die();
}
add_action( 'wp_ajax_resmushit_update_statistics', 'resmushit_update_statistics' );	