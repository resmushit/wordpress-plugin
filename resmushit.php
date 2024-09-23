<?php
/**
 * @package   resmushit
 * @author    ShortPixel, Charles Bourgeaux <hello@resmush.it>
 * @license   GPL-2.0+
 * @link      http://www.resmush.it
 * @copyright 2024 Resmush.it
 *
 * @wordpress-plugin
 * Plugin Name:       reSmush.it Image Optimizer
 * Plugin URI:        https://wordpress.org/plugins/resmushit-image-optimizer/
 * Description:       100% Free Image Optimizer and Compressor plugin. Fast JPEG/PNG and GIF compression.
 * Version:           1.0.4
 * Timestamp:         2024.09.23
 * Author:            reSmush.it
 * Author URI:        https://resmush.it
 * Author:            Charles Bourgeaux, ShortPixel
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * GitHub Plugin URI: https://github.com/resmushit/wordpress-plugin
 * Domain Path:       /languages
 * Text Domain:	      resmushit-image-optimizer
 */

require('resmushit.inc.php');

define( 'RESMUSH_PLUGIN_VERSION', '1.0.4');
define( 'RESMUSH_PLUGIN_FILE', __FILE__ );
define( 'RESMUSH_PLUGIN_PATH', plugin_dir_path(__FILE__) );


// The Real stuff
require_once(RESMUSH_PLUGIN_PATH . 'build/shortpixel/autoload.php');

$loader = new \Resmush\Build\PackageLoader();
$loader->setComposerFile(RESMUSH_PLUGIN_PATH . 'classes/plugin.json');
$loader->load(RESMUSH_PLUGIN_PATH);


\Resmush\Plugin::checkLogger();

function Resmush()
{
   return \Resmush\Plugin::getInstance();
}

Resmush();
/**
*
* Registering language plugin
*
* @param none
* @return none
*/
/*
function resmushit_load_plugin_textdomain() {
    load_plugin_textdomain( 'resmushit', FALSE, plugin_basename( dirname( __FILE__ ) ) . '/languages' );
}
add_action( 'plugins_loaded', 'resmushit_load_plugin_textdomain' );
*/


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
		if(get_option('resmushit_notice_close_eoldec23') === false || get_option('resmushit_notice_close_eoldec23') == "")
			update_option( 'resmushit_notice_close_eoldec23', 0 );
	}
}
register_activation_hook( __FILE__, 'resmushit_activate' );
add_action( 'admin_init', 'resmushit_activate' );



/**
*
* add Ajax action to close permanently notice
*
* @param none
* @return json object
*/
/*
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
  if(update_option( 'resmushit_notice_close_eoldec23', 1 )) {
    $return = TRUE;
  }
  wp_send_json(json_encode(array('status' => $return)));
  die();
}
add_action( 'wp_ajax_resmushit_notice_close', 'resmushit_notice_close' );
*/

/**
*
* add Notice information for Shortpixel offer
*
* @param none
* @return json object
*/
/*
function resmushit_general_admin_notice(){
	// Expired offer
	if(time() > strtotime("31 December 2023")) {
		return FALSE;
	}
	// Already seen notice
	if(get_option('resmushit_notice_close_eoldec23') == 1) {
		return FALSE;
	}
	$allowed_pages = array(
		'media_page_resmushit_options',
		'dashboard',
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
			<div class='txt-center'><img src='". RESMUSHIT_BASE_URL . "images/patreon.png' /></div>
				<div class='extra-padding'><h4 class='no-uppercase'>🫶Thanks a lot! reSmush.it will continue for now!</h4>
				<p>First, we'd really like to thank members of the community who have supported us by participating to the patreon. As you may know, over the last months, the cost of servers has increased and we were not able to maintain this service without the community's help. reSmush.it has been provided for FREE during 7 years</p>
				<p>As we're getting very close to the target, we're able to preserve reSmush.it for now. <em>However, we still need your financial support to enhance new features on reSmush.it, such as faster servers, webp and next generations format support and new exciting features!</em></p>
				<p>So, we're kindly asking our community to participate for those who haven't, in order to preserve blazing fast images optimizations for everyone !</p>
				<p>Your help will be deeply appreciated to continue this fabulous adventure! 🚀</p>
				<p></p>
				<p>Kind regards ❤️,</p>
				<p>Charles, <i>founder of reSmush.it</i></p>
				</div>
				<div class='txt-center'><a class='button button-primary' target='_blank' href='https://www.patreon.com/resmushit' title='Help us to maintain the service'>Support us on Patreon from $5/mo</a></div>

		</div>";
	}
}
add_action('admin_notices', 'resmushit_general_admin_notice');
*/
