<?php
/**
* 
* Create menu entries and routing
*
* @param none
* @return none
*/
function resmushit_create_menu() {
	if ( is_super_admin() )
		add_media_page( 'reSmush.it', 'reSmush.it', 'manage_options', 'resmushit_options', 'resmushit_settings_page');
}
add_action( 'admin_menu','resmushit_create_menu');





/**
* 
* Declares settings entries
*
* @param none
* @return none
*/
function resmushit_settings_declare() {
	register_setting( 'resmushit-settings', 'resmushit_on_upload' );
	register_setting( 'resmushit-settings', 'resmushit_qlty' );
	register_setting( 'resmushit-settings', 'resmushit_statistics' );
	register_setting( 'resmushit-settings', 'resmushit_logs' );
}
add_action( 'admin_init', 'resmushit_settings_declare' );



function resmushit_media_list_add_column( $columns ) {
	$columns["resmushit_disable"] 	= __('Disable of reSmush.it', 'resmushit');
	$columns["resmushit_status"] 	= __('reSmush.it status', 'resmushit');
	return $columns;
}
add_filter( 'manage_media_columns', 'resmushit_media_list_add_column' );


function resmushit_media_list_sort_column( $columns ) {
	$columns["resmushit_disable"] 	= "resmushit_disable";
	$columns["resmushit_status"] 	= "resmushit_status";
	return $columns;
}
add_filter( 'manage_upload_sortable_columns', 'resmushit_media_list_sort_column' );


function resmushit_media_list_add_column_value( $column_name, $id ) {
	if ( $column_name == "resmushit_disable" )
		reSmushitUI::mediaListCustomValuesDisable($id);
	else if ( $column_name == "resmushit_status" ) 
		reSmushitUI::mediaListCustomValuesStatus($id);
}
add_action( 'manage_media_custom_column', 'resmushit_media_list_add_column_value', 10, 2 );


/* Add custom field to attachment */
function resmushit_image_attachment_add_status_button($form_fields, $post) {
	$form_fields["rsmt-disabled-checkbox"] = array(
		"label" => __("Disable of reSmush.it"),
		"input" => "html",
		"value" => '',
		"html"  => reSmushitUI::mediaListCustomValuesDisable($post->ID, true)
	);

	$form_fields["rsmt-status-button"] = array(
		"label" => __("reSmush.it status"),
		"input" => "html",
		"value" => '',
		"html"  => reSmushitUI::mediaListCustomValuesStatus($post->ID, true)
	);
	return $form_fields;
}
add_filter("attachment_fields_to_edit", "resmushit_image_attachment_add_status_button", null, 2);



/**
* 
* Settings page builder
*
* @param none
* @return none
*/
function resmushit_settings_page() {
	?>
	<div class='rsmt-panels'>	
		<div class="rsmt-cols w66 iln-block">
			<?php reSmushitUI::headerPanel();?>
			<?php reSmushitUI::bulkPanel();?>
			<?php reSmushitUI::bigFilesPanel();?>
			<?php reSmushitUI::statisticsPanel();?>
		</div>
		<div class="rsmt-cols w33 iln-block">
			<?php reSmushitUI::settingsPanel();?>
			<?php reSmushitUI::newsPanel();?>
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
function resmushit_register_plugin_assets(){

	wp_register_style( 'resmushit-css', plugins_url( 'css/resmushit.css', __FILE__ ) );
	wp_enqueue_style( 'resmushit-css' );
    wp_enqueue_style( 'prefix-style', esc_url_raw( 'https://fonts.googleapis.com/css?family=Roboto+Slab:700' ), array(), null  );

    wp_register_script( 'resmushit-js', plugins_url( 'js/script.js', __FILE__ ) );
    wp_enqueue_script( 'resmushit-js' );
}
add_action( 'admin_head', 'resmushit_register_plugin_assets' );