<?php
namespace Resmush\Controller;

use \reSmushitUI as reSmushitUI;
use \Resmush\ShortPixelLogger\ShortPixelLogger as Log;

if (! defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

// EVerything to do with AdminActions / WordPress
class AdminController
{
  protected static $instance;


  public static function getInstance()
  {
    if (is_null(self::$instance))
     self::$instance = new AdminController();

    return self::$instance;
  }

  public function __construct()
  {
      $this->initHooks();
  }

  protected function initHooks()
  {

      add_action( 'admin_menu', array($this, 'add_menu') );
      add_action( 'admin_init', array($this, 'settings_declare') );
      add_filter( 'manage_media_columns', array($this, 'media_list_add_column') );
      add_filter( 'manage_upload_sortable_columns', array($this,'media_list_sort_column') );
      add_action( 'manage_media_custom_column', array($this,'media_list_add_column_value'), 10, 2 );
      add_filter("attachment_fields_to_edit", array($this, 'image_attachment_add_status_button'), null, 2);
      add_action( 'admin_head', array($this,'register_plugin_assets') );

      add_filter('plugin_action_links_' . plugin_basename(RESMUSH_PLUGIN_FILE), array($this, 'add_plugin_page_settings_link'));
  }

  /**
  *
  * Create menu entries and routing
  *
  * @param none
  * @return none
  */
  public function add_menu() {
    if ( ! current_user_can( 'manage_options' ) ) {
  		return;
  	}

  		add_submenu_page( 'options-general.php', 'reSmush.it', 'reSmush.it', 'manage_options', 'resmushit_options', array($this, 'settings_page'));
  }

  /**
  *
  * Declares settings entries
  *
  * @param none
  * @return none
  */
  public function settings_declare() {
  	register_setting( 'resmushit-settings', 'resmushit_on_upload' );
  	register_setting( 'resmushit-settings', 'resmushit_qlty' );
  	register_setting( 'resmushit-settings', 'resmushit_statistics' );
  	register_setting( 'resmushit-settings', 'resmushit_logs' );
  	register_setting( 'resmushit-settings', 'resmushit_cron' );
  	register_setting( 'resmushit-settings', 'resmushit_preserve_exif' );
  	register_setting( 'resmushit-settings', 'resmushit_remove_unsmushed' );
  	register_setting( 'resmushit-settings', 'resmushit_notice_close' );
  }



  /**
  *
  * Add Columns to the media panel
  *
  * @param array $columns
  * @return $columns
  */
  public function media_list_add_column( $columns ) {
  	$columns["resmushit_disable"] 	= __('Disable of reSmush.it', 'resmushit-image-optimizer');
  	$columns["resmushit_status"] 	= __('reSmush.it status', 'resmushit-image-optimizer');
  	return $columns;
  }



  /**
  *
  * Sort Columns to the media panel
  *
  * @param array $columns
  * @return $columns
  */
  public function media_list_sort_column( $columns ) {
  	$columns["resmushit_disable"] 	= "resmushit_disable";
  	$columns["resmushit_status"] 	= "resmushit_status";
  	return $columns;
  }



  /**
  *
  * Add Value to Columns of the media panel
  *
  * @param string $column_name
  * @param string $identifier of the column
  * @return none
  */
  public function media_list_add_column_value( $column_name, $id ) {
  	if ( $column_name == "resmushit_disable" )
  		reSmushitUI::mediaListCustomValuesDisable($id);
  	else if ( $column_name == "resmushit_status" )
  		reSmushitUI::mediaListCustomValuesStatus($id);
  }


  /**
  *
  * Add custom field to attachment
  *
  * @param array $form_fields
  * @param object $post
  * @return array
  */
  public function image_attachment_add_status_button($form_fields, $post) {
  	if ( !preg_match("/image.*/", $post->post_mime_type) )
  		return $form_fields;

  	$form_fields["rsmt-disabled-checkbox"] = array(
  		"label" => __("Disable of reSmush.it", "resmushit-image-optimizer"),
  		"input" => "html",
  		"value" => '',
  		"html"  => reSmushitUI::mediaListCustomValuesDisable($post->ID, true)
  	);

  	$form_fields["rsmt-status-button"] = array(
  		"label" => __("reSmush.it status", "resmushit-image-optimizer"),
  		"input" => "html",
  		"value" => '',
  		"html"  => reSmushitUI::mediaListCustomValuesStatus($post->ID, true)
  	);
  	return $form_fields;
  }



  /**
  *
  * Settings page builder
  *
  * @param none
  * @return none
  */
  public function settings_page() {
  	?>
    <div class='rsmt-panels'>
      <div class='rmst-panel w100'>
  			<?php reSmushitUI::headerPanel();?>
      </div>
    </div>
  	<div class='rsmt-panels resmush-settings-ui'>

     <div class="rsmt-cols w10 iln-block nav-block">
        <ul class='rsmt-tabs-nav'>
          <li data-tab='actions' class='active'>Actions</li>
          <li data-tab='settings'>Settings</li>
          <li><a href="<?php echo RESMUSHIT_FEEDBACK_URL ?>" target="_blank">Feedback</a></li>
        </ul>

     </div>

  		<div class="rsmt-cols w60 iln-block rsmt-tab rsmt-tab-actions active">
  			<?php reSmushitUI::alertPanel();?>
  			<?php reSmushitUI::bulkPanel();?>
  			<?php reSmushitUI::bigFilesPanel();?>
  			<?php reSmushitUI::statisticsPanel();?>
  			<?php reSmushitUI::restorePanel();?>
  		</div>

      <div class='rsmt-cols w60 iln-block rsmt-tab rsmt-tab-settings' style='display:none'>
        <?php reSmushitUI::settingsPanel();?>
      </div>

      <div class='rsmt-cols w60 iln-block rsmt-tab rsmt-tab-feedback' style='display:none'>
        <?php reSmushitUI::feedbackPanel();?>
      </div>

      <div class="rsmt-cols w30 iln-block">
          <h1>ADS</h1>
      </div>
  	</div>
  	<?php
  }



  /**
  *
  * Assets declaration
  *
  * @param none
  * @return none
  */
  public function register_plugin_assets(){
   	/*$allowed_pages = array(
  						//	'media_page_resmushit_options',
  							'settings_page_resmushit_options',
   							'upload',
   							'dashboard',
   							'post',
  							'plugins',
  							'edit-post',
  							'media',
   							'attachment');
    */
    $admin_pages = array(
              'settings_page_resmushit_options',
    );

    $media_pages = array(
      'upload',
      'post',
      'attachment',
      'media'
    );

  		$current_page = get_current_screen();

  		wp_register_style( 'resmushit-css', plugins_url( 'css/resmushit.css', RESMUSH_PLUGIN_FILE ) );

      wp_register_style('resmushit-admin-css', plugins_url('css/resmush_admin.css', RESMUSH_PLUGIN_FILE));
      wp_register_style('resmushit-media-css', plugins_url('css/resmush_media.css', RESMUSH_PLUGIN_FILE));

  	    wp_register_script( 'resmushit-js', plugins_url( 'js/script.js?' . hash_file('crc32',  RESMUSH_PLUGIN_PATH . '/js/script.js'), RESMUSH_PLUGIN_FILE ) );

        $translations = array(
            'restore_all_confirm' => __("You're about to restore ALL your original image files. Are you sure to perform this operation ?", "resmushit-image-optimizer"),
            'images_restored' => __('images successfully restored', "resmushit-image-optimizer"),
            'backupfiles_removed' => __('backup files successfully removed', "resmushit-image-optimizer"),
            'remove_backup_confirm' => __("You're about to delete your image backup files. Are you sure to perform this operation ?", "resmushit-image-optimizer"),
            'removing_backups' => __('Removing backups...', "resmushit-image-optimizer"),
            'reduced_by' => __('Reduced by', "resmushit-image-optimizer"),
            'optimizing' => __('Optimizing...', "resmushit-image-optimizer"),
            'attachments_found' => __('attachment(s) found, starting optimization...', "resmushit-image-optimizer"),
            'no_attachments_found' => __('There are no existing attachments that requires optimization.', "resmushit-image-optimizer"),
            'examing_attachments' => __('Examining existing attachments. This may take a few moments...', "resmushit-image-optimizer"),
            'picture_too_big' => __('picture(s) cannot be optimized (> 5MB). All others have been optimized', "resmushit-image-optimizer"),
            'error_webservice' => __('An error occured when contacting webservice. Please try again later.', "resmushit-image-optimizer"),
            'restoring' => __('Restoring...', 'resmushit-image-optimizer'),
            ''

        );
        wp_localize_script('resmushit-js', 'reSmush', array(
          'strings' => $translations,
        ));

        //Admin
        if (in_array( $current_page->id, $admin_pages ) ) {

          wp_enqueue_script( 'resmushit-js' );
          wp_enqueue_style( 'resmushit-css' );
          wp_enqueue_style('resmushit-admin-css');
          wp_enqueue_style( 'prefix-style', esc_url_raw( 'https://fonts.googleapis.com/css?family=Roboto+Slab:700' ), array(), null  );
  	     }
         elseif (in_array( $current_page->id, $media_pages ) )
         {
            wp_enqueue_script('resmushit-js');
            wp_enqueue_style('resmushit-media-css');
            wp_enqueue_style( 'resmushit-css' );

         }
  }

  /**
   * add 'Settings' link to options page from Plugins
   * @param array $links
   * @return string
   */
  public function add_plugin_page_settings_link($links) {
  	if(is_string($links)) {
  		$oneLink = $links;
  		$links = array();
  		$links[] = $oneLink;
  	}
  	$links[] = '<a href="' . admin_url( 'upload.php?page=resmushit_options' ) . '">' . __('Settings', "resmushit-image-optimizer") . '</a>';
  	return $links;
  }


} // class