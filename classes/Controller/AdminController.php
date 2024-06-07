<?php
namespace Resmush\Controller;

use \reSmushitUI as reSmushitUI;
use \reSmushit as reSmushit;

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
      if(!is_super_admin() && !current_user_can('administrator')) {
        return;
      }

      $this->initHooks();
  }

  protected function initHooks()
  {

      add_action( 'admin_menu', array($this, 'add_menu') );
      add_action( 'admin_init', array($this, 'settings_declare') );
      add_filter( 'manage_media_columns', array($this, 'media_list_add_column') );
    //  add_filter( 'manage_upload_sortable_columns', array($this,'media_list_sort_column') );
      add_action( 'manage_media_custom_column', array($this,'media_list_add_column_value'), 10, 2 );
    //  add_filter("attachment_fields_to_edit", array($this, 'image_attachment_add_status_button'), null, 2);

      add_action( 'add_meta_boxes_attachment', array( $this, 'addMetaBox') );

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

  // Add metabox for editmediaview sie.
  public function addMetaBox()
  {
      add_meta_box(
          'rsi_info_box',          // this is HTML id of the box on edit screen
          __('reSmush.it', 'resmushit-image-optimizer'),    // title of the box
          array( $this, 'displayMetaBox'),   // function to be called to display the info
          null,//,        // on which edit screen the box should appear
          'side'//'normal',      // part of page where the box should appear
          //'default'      // priority of the box
      );
  }

  public function displayMetaBox($post)
  {
    $post_id = $post->ID;
    if (false === $this->isAllowedExtension($post_id))
    {
       return;
    }

    echo "<div>";
    reSmushitUI::mediaListCustomValuesStatus($post_id);
    echo "</div><div><br>";
    reSmushitUI::mediaListCustomValuesDisable($post_id);
    echo "</div>";

  }

  /**
  *
  * Add Columns to the media panel
  *
  * @param array $columns
  * @return $columns
  */
  public function media_list_add_column( $columns ) {
//  	$columns["resmushit_disable"] 	= __('Disable of reSmush.it', 'resmushit-image-optimizer');
	$columns["resmushit_status"] 	= __('reSmush.it Status', 'resmushit-image-optimizer');
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
  //	$columns["resmushit_disable"] 	= "resmushit_disable";
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

      if ($column_name !== 'resmushit_status')
      {
         return;
      }

      if (false === $this->isAllowedExtension($id))
      {
         return;
      }


      echo "<div>";
  		reSmushitUI::mediaListCustomValuesStatus($id);
echo "</div><div>";
      reSmushitUI::mediaListCustomValuesDisable($id);
echo "</div>";
  }


  public function isAllowedExtension($id)
  {

    $file = get_attached_file($id);
    $fs = Resmush()->fs();

    $fileObj = $fs->getFile($file);
    if (false ===  in_array($fileObj->getExtension(), reSmushit::authorizedExtensions()))
    {
       return false;
    }

    return true;

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

      if (false === $this->isAllowedExtension($post->ID))
      {
         return;
      }

  	$form_fields["rsmt-disabled-checkbox"] = array(
  		"label" => __("Disable of reSmush.it", "resmushit-image-optimizer"),
  		"input" => "html",
  		"value" => '',
  		"html"  => reSmushitUI::mediaListCustomValuesDisable($post->ID, true)
  	);

  	$form_fields["rsmt-status-button"] = array(
		"label" => __("reSmush.it Status", "resmushit-image-optimizer"),
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
    <div class='rsmt-panels header-panel'>
  			<?php reSmushitUI::headerPanel();?>
    </div>
  	<div class='rsmt-panels resmush-settings-ui'>

     <div class="rsmt-cols w10 iln-block nav-block">
        <ul class='rsmt-tabs-nav'>
          <li data-tab='actions' class='active'><?php _e('Dashboard','resmushit-image-optimizer'); ?></li>
          <li data-tab='settings'><?php _e('Settings', 'resmushit-image-optimizer'); ?></li>
          <li data-tab='feedback'><?php _e('Support', 'resmushit-image-optimizer'); ?> </a></li>
          <li class='kofi-li'>
            <span class='kofi-support'>
              <a href="https://ko-fi.com/E1E51PW00" target="_blank">
                <img width=22 height=15 src="<?php echo plugins_url('images/kofi.png', RESMUSH_PLUGIN_FILE); ?>">
                <span class='text'><?php _e('Support Us', 'resmushit-image-optimizer'); ?></span>
              </a>
            </span>
          </li>
          <li>
          <span class="kofi-support">
              <a href="https://wordpress.org/support/plugin/resmushit-image-optimizer/reviews/#new-post" target="_blank">
                <img width="22" height="15" src="<?php echo plugins_url('images/star.png', RESMUSH_PLUGIN_FILE); ?>">
                <span class="text"><?php esc_html_e( 'Rate Us', 'resmushit-image-optimizer' ); ?></span>
              </a>
            </span>
          </li>
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

      <div class="rsmt-cols w30 iln-block message-wrapper">
        <div class='shortpixel-message'>
          <img src='<?php echo RESMUSHIT_BASE_URL  ?>images/shortpixel-text-logo.svg' />
          <ul class='options'>
          <li><?php esc_html_e( 'Unlimited image optimization', 'resmushit-image-optimizer' ); ?></li>
          <li><?php esc_html_e( 'Unlimited domains', 'resmushit-image-optimizer' ); ?></li>
          <li><?php esc_html_e( 'WebP&amp;AVIF conversion', 'resmushit-image-optimizer' ); ?><li>
					<li><?php esc_html_e( '500GB CDN Traffic', 'resmushit-image-optimizer' ); ?></li>
          <li><?php esc_html_e( '$9.99 / month', 'resmushit-image-optimizer' ); ?></li>
        </ul>
        <div class='button-wrapper'>
          <a class='button' href="https://shortpixel.com/ms/af/C34DUIL28044" target="_blank"><?php esc_html_e( 'Buy now', 'resmushit-image-optimizer' ); ?></a>
        </div>
          <div class='link-under'>
            <a href="https://shortpixel.com/compare/resmushit-vs-shortpixel" target="_blank"><?php _e('Why is premium optimization better?', 'resmushit-image-optimizer'); ?></a>
          </div>
      </div>
    </div>

  	</div> <!-- // ui -->
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

  		wp_register_style( 'resmushit-css', plugins_url( 'css/resmushit.css', RESMUSH_PLUGIN_FILE ), array(), RESMUSH_PLUGIN_VERSION);

      wp_register_style('resmushit-admin-css', plugins_url('css/resmush_admin.css', RESMUSH_PLUGIN_FILE), array(), RESMUSH_PLUGIN_VERSION);
      wp_register_style('resmushit-media-css', plugins_url('css/resmush_media.css', RESMUSH_PLUGIN_FILE), array(), RESMUSH_PLUGIN_VERSION);

  	    wp_register_script( 'resmushit-js', plugins_url( 'js/script.js?' . hash_file('crc32',  RESMUSH_PLUGIN_PATH . '/js/script.js'), RESMUSH_PLUGIN_FILE ) );

        $translations = array(
            'restore_all_confirm' => __("You are about to restore ALL your original image files. Are you sure you want to perform this operation?", "resmushit-image-optimizer"),
            'images_restored' => __('images successfully restored!', "resmushit-image-optimizer"),
            'backupfiles_removed' => __('backup files successfully removed!', "resmushit-image-optimizer"),
            'remove_backup_confirm' => __("You are about to delete your image backup files. Are you sure you want to perform this operation?", "resmushit-image-optimizer"),
            'removing_backups' => __('Removing backups...', "resmushit-image-optimizer"),
            'reduced_by' => __('Reduced by', "resmushit-image-optimizer"),
            'optimizing' => __('Optimizing...', "resmushit-image-optimizer"),
            'attachments_found' => __('image(s) found, starting optimization...', "resmushit-image-optimizer"),
            'no_attachments_found' => __('There are no images that need to be optimized.', "resmushit-image-optimizer"),
            'examing_attachments' => __('Checking existing images. This may take a few moments...', "resmushit-image-optimizer"),
            'picture_too_big' => __('image(s) cannot be optimized (>5MB). All others have been optimized.', "resmushit-image-optimizer"),
            'error_webservice' => __('An error occured when contacting the API. Please try again later.', "resmushit-image-optimizer"),
            'restoring' => __('Restoring...', 'resmushit-image-optimizer'),
            'stop_optimization' => __('Stop bulk optimization', 'resmushit-image-optimizer'),

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
	$links[] = '<a href="' . admin_url( 'options-general.php?page=resmushit_options' ) . '">' . __('Settings', "resmushit-image-optimizer") . '</a>';
  	return $links;
  }


} // class
