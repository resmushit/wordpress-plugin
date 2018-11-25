<?php

 /**
   * ReSmushit Admin UI class
   * 
   * 
   * @package    Resmush.it
   * @subpackage UI
   * @author     Charles Bourgeaux <contact@resmush.it>
   */
Class reSmushitUI {

	/**
	 *
	 * Create a new panel
	 *
	 * @param  string 	$title 	Title of the pane
	 * @param  string 	$html 	HTML content
	 * @param  string 	$border Color of the border
	 * @return none
	 */
	public static function fullWidthPanel($title = null, $html = null, $border = null) {
		self::fullWidthPanelWrapper($title, $html, $border);
		echo $html;
		self::fullWidthPanelEndWrapper();
	}




	/**
	 *
	 * Create a new panel wrapper (start)
	 *
	 * @param  string 	$title 	Title of the pane
	 * @param  string 	$html 	HTML content
	 * @param  string 	$border Color of the border
	 * @return none
	 */
	public static function fullWidthPanelWrapper($title = null, $html = null, $border = null) {
		$borderClass = NULL;

		if($border) {
			$borderClass = 'brdr-'.$border;
		}
		echo "<div class='rsmt-panel w100 $borderClass'><h2>$title</h2>";
	}




	/**
	 *
	 * Create a new panel wrapper (end)
	 *
	 * @param  none
	 * @return none
	 */
	public static function fullWidthPanelEndWrapper() {
		echo "</div>";
	}




	/**
	 *
	 * Generate Header panel
	 *
	 * @param  none
	 * @return none
	 */
	public static function headerPanel() {
		$html = "<img src='". RESMUSHIT_BASE_URL . "images/header.jpg' />";
		self::fullWidthPanel($html);
	}





	/**
	 *
	 * Generate Settings panel
	 *
	 * @param  none
	 * @return none
	 */
	public static function settingsPanel() {
		self::fullWidthPanelWrapper(__('Settings', 'resmushit'), null, 'orange');
		
		echo '<div class="rsmt-settings">
			<form method="post" action="options.php" id="rsmt-options-form">';
		settings_fields( 'resmushit-settings' );
		do_settings_sections( 'resmushit-settings' );
		
		echo '<table class="form-table">' 
				. self::addSetting("text", __("Image quality", 'resmushit'), __("Default value is 92. The quality factor must be between 0 (very weak) and 100 (best quality)", 'resmushit'), "resmushit_qlty")
				. self::addSetting("checkbox", __("Optimize on upload", 'resmushit'), __("All future images uploaded will be automatically optimized", 'resmushit'), "resmushit_on_upload")
				. self::addSetting("checkbox", __("Enable statistics", 'resmushit'), __("Generates statistics about optimized pictures", 'resmushit'), "resmushit_statistics")
				. self::addSetting("checkbox", __("Enable logs", 'resmushit'), __("Enable file logging (for developers)", 'resmushit'), "resmushit_logs")
				. '</table>';
		submit_button();
		echo '</form></div>';
		self::fullWidthPanelEndWrapper(); 		
	}



	/**
	 *
	 * Generate Bulk panel
	 *
	 * @param  none
	 * @return none
	 */
	public static function bulkPanel() {
		$dataCountNonOptimizedPictures = reSmushit::getCountNonOptimizedPictures();
		$countNonOptimizedPictures = $dataCountNonOptimizedPictures['nonoptimized'];
		self::fullWidthPanelWrapper(__('Optimize unsmushed pictures', 'resmushit'), null, 'blue');
		
		$additionnalClassNeedOptimization = NULL;
		$additionnalClassNoNeedOptimization = 'disabled';
		if(!$countNonOptimizedPictures) {
			$additionnalClassNeedOptimization = 'disabled';
			$additionnalClassNoNeedOptimization = NULL;
		} 

		echo "<div class='rsmt-bulk'><div class='non-optimized-wrapper $additionnalClassNeedOptimization'><h3 class='icon_message warning'>"
			. __('There is currently', 'resmushit')
			. " <em>$countNonOptimizedPictures "
			. __('non optimized pictures', 'resmushit')
			. "</em>.</h3><p>"
			. __('This action will resmush all pictures which have not been optimized to the good Image Quality Rate.', 'resmushit')
			. "</p><p class='submit' id='bulk-resize-examine-button'><button class='button-primary' onclick='resmushit_bulk_resize(\"bulk_resize_image_list\");'>"
			. __('Optimize all pictures', 'resmushit')
			. "</button></p><div id='bulk_resize_image_list'></div></div>"
			. "<div class='optimized-wrapper $additionnalClassNoNeedOptimization'><h3 class='icon_message ok'>"
			. __('Congrats ! All your pictures are correctly optimized', 'resmushit')
			. "</h3></div></div>";
		self::fullWidthPanelEndWrapper(); 		
	}


	/**
	 *
	 * Generate Bulk panel
	 *
	 * @param  none
	 * @return none
	 */
	public static function bigFilesPanel() {
		$getNonOptimizedPictures = json_decode(reSmushit::getNonOptimizedPictures());
		$countfilesTooBigPictures = is_array($getNonOptimizedPictures->filestoobig) ? sizeof($getNonOptimizedPictures->filestoobig) : 0;

		if(!$countfilesTooBigPictures)
			return false;

		self::fullWidthPanelWrapper(__('Files non optimized', 'resmushit'), null, 'grey');

		$additionnalClass = NULL;
		if(!$countfilesTooBigPictures) {
			$additionnalClass = 'disabled';
		}

		echo "<div class='rsmt-bigfiles'><div class='optimized-wrapper $additionnalClass'>
					<h3 class='icon_message info'>";

		if($countfilesTooBigPictures > 1) {
			echo $countfilesTooBigPictures . ' ' . __('pictures are too big (> 5MB) for the optimizer', 'resmushit');
		} else {
			echo $countfilesTooBigPictures . ' ' . __('picture is too big (> 5MB) for the optimizer', 'resmushit');
		}
		echo "</h3><div class='list-accordion'><h4>"
				. __('List of files above 5MB', 'resmushit')
				. "</h4><ul>";

		foreach($getNonOptimizedPictures->filestoobig as $file){
			$fileInfo = pathinfo(get_attached_file( $file->ID )); 
			$filesize = reSmushitUI::sizeFormat(filesize(get_attached_file( $file->ID ))); 

			echo "<li><a href='"
					. wp_get_attachment_url( $file->ID )
					. "' target='_blank'>"
					. wp_get_attachment_image($file->ID, 'thumbnail')
					. "<span>"
					. $fileInfo['basename'] . ' (' . $filesize . ').</span></a></li>';
		}
		echo '</ul></div></div></div>';
		
		self::fullWidthPanelEndWrapper(); 		
	}




	/**
	 *
	 * Generate Statistics panel
	 *
	 * @param  none
	 * @return none
	 */
	public static function statisticsPanel() {
		if(!get_option('resmushit_statistics'))
			return false;
		self::fullWidthPanelWrapper(__('Statistics', 'resmushit'), null, 'green');
		$resmushit_stat = reSmushit::getStatistics();

		echo "<div class='rsmt-statistics'>";

		if($resmushit_stat['files_optimized'] != 0) {
			echo "<p><strong>"
					. __('Space saved :', 'resmushit')
					. "</strong> <span id='rsmt-statistics-space-saved'>"
					. self::sizeFormat($resmushit_stat['total_saved_size'])
					. "</span></p><p><strong>"
					. __('Total reduction :', 'resmushit')
					. "</strong> <span id='rsmt-statistics-percent-reduction'>"
					. $resmushit_stat['percent_reduction']
					. "</span></p><p><strong>"
					. __('Attachments optimized :', 'resmushit')
					. "</strong> <span id='rsmt-statistics-files-optimized'>"
					. $resmushit_stat['files_optimized']
					. "</span>/<span id='rsmt-statistics-total-picture'>"
					. $resmushit_stat['total_pictures']
					. "</span></p><p><strong>"
					. __('Image optimized (including thumbnails) :', 'resmushit') 
					. "</strong> <span id='rsmt-statistics-files-optimized'>"
					. $resmushit_stat['files_optimized_with_thumbnails']
					. "</span>/<span id='rsmt-statistics-total-pictures'>"
					. $resmushit_stat['total_pictures_with_thumbnails']
					. "</span></p><p><strong>"
					. __('Total images optimized :', 'resmushit')
					. "</strong> <span id='rsmt-statistics-total-optimizations'>"
					. $resmushit_stat['total_optimizations'] 
					. "</span></p>";
			} else {
				echo "<p>" . __('No picture has been optimized yet ! Add pictures to your Wordpress Media Library.', 'resmushit') . "</p>";
			}
		echo "</div>";
		self::fullWidthPanelEndWrapper(); 		
	}



	/**
	 *
	 * Generate News panel
	 *
	 * @param  none
	 * @return none
	 */
	public static function newsPanel() {
		global $wp_version;
		
		echo "<div class='rsmt-news'>";
		
		self::fullWidthPanelWrapper(__('News', 'resmushit'), null, 'red');
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, RESMUSHIT_NEWSFEED);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
		$data_raw = curl_exec($ch);
		curl_close($ch);
		$data = json_decode($data_raw);
		
		if($data) {
			foreach($data as $i=>$news) {
				if($i > 2){
					break;
				}

				echo "<div class='news-item'><span class='news-date'>"
						. date('d/m/Y', $news->date)
						. "</span>";
				if($news->picture) {
					echo "<div class='news-img'><a href='" 
							. $news->link
							. "' target='_blank'><img src='"
							. $news->picture
							. "' /></a></div>";
				}
				echo "<h3><a href='"
						. $news->link
						. "' target='_blank'>"
						. $news->title
						. "</a></h3><div class='news-content'>"
						. $news->content 
						. "</div>";
			}
		}

		echo "<div class='social'>"
				. "<a class='social-maecia' title='"
				. __('Maecia Agency - Paris France', 'resmushit')
				. "' href='https://www.maecia.com' target='_blank'>"
				. "<img src='"
				. RESMUSHIT_BASE_URL . "images/maecia.png' /></a>"
				. "<a class='social-resmushit' title='"
				. __('Visit resmush.it for more informations', 'resmushit')
				. "' href='https://resmush.it' target='_blank'>"
				. "<img src='"
				. RESMUSHIT_BASE_URL . "images/logo.png' /></a>"
				. "<a class='social-twitter' title='"
				. __('Follow reSmush.it on Twitter', 'resmushit')
				. "' href='https://www.twitter.com/resmushit' target='_blank'>"
				. "<img src='"
				. RESMUSHIT_BASE_URL . "images/twitter.png' /></a></div></div>";
		
		self::fullWidthPanelEndWrapper(); 		
	}




	/**
	 *
	 * Helper to generate multiple settings fields
	 *
	 * @param  string $type 	type of the setting
	 * @param  string $name 	displayed name of the setting
	 * @param  string $extra 	additionnal informations about the setting
	 * @param  string $machine_name 	setting machine name
	 * @return none
	 */
	public static function addSetting($type, $name, $extra, $machine_name) {
		$output = "	<div class='setting-row type-$type'>
					<label for='$machine_name'>$name<p>$extra</p></label>";
		switch($type){
			case 'text':
				$output .= "<input type='text' name='$machine_name' id='$machine_name' value='". get_option( $machine_name ) ."'/>";
				break;
			case 'checkbox':
				$additionnal = null;
				if ( 1 == get_option( $machine_name ) ) $additionnal = 'checked="checked"'; 
				$output .= "<input type='checkbox' name='$machine_name' id='$machine_name' value='1' ".  $additionnal ."/>";
				break;
		}
		$output .= '</div>';
		return $output;
	}





	/**
	 *
	 * Generate checkbox "disabled" on media list
	 *
	 * @param  int 		$id 	Post ID associated to postmetas
	 * @return none
	 */
	public static function mediaListCustomValuesDisable($id, $return = false) {
		global $wpdb;
		$query = $wpdb->prepare( 
			"select
				$wpdb->posts.ID as ID, $wpdb->postmeta.meta_value
				from $wpdb->posts
				inner join $wpdb->postmeta on $wpdb->posts.ID = $wpdb->postmeta.post_id and $wpdb->postmeta.meta_key = %s and $wpdb->postmeta.post_id = %s",
				array('resmushed_disabled', $id)
		);	
		$attachment_resmushit_disabled = null;
		if($wpdb->get_results($query))
			$attachment_resmushit_disabled = 'checked';

		$output = '<input type="checkbox" data-attachment-id="'. $id .'"" class="rsmt-trigger--disabled-checkbox" '. $attachment_resmushit_disabled .'  />';
		
		if($return)
			return $output;
		echo $output;
	}





	/**
	 *
	 * Generate status info OR button on media list
	 *
	 * @param  int 		$attachment_id	Post ID associated to postmetas
	 * @return none
	 */
	public static function mediaListCustomValuesStatus($attachment_id, $return = false) {
		if(reSmushit::getDisabledState($attachment_id)){
			$output = '-';
		}
		else if(reSmushit::getAttachmentQuality($attachment_id) != reSmushit::getPictureQualitySetting())
			$output = '<input type="button" value="'. __('Optimize', 'resmushit') .'" class="rsmt-trigger--optimize-attachment button media-button  select-mode-toggle-button" name="resmushit" data-attachment-id="'. $attachment_id .'" class="button wp-smush-send" />';
		else{
			$statistics = reSmushit::getStatistics($attachment_id);
			$output = __('Reduced by', 'resmushit') . " ". $statistics['total_saved_size_nice'] ." (". $statistics['percent_reduction'] . ' ' . __('saved', 'resmushit') . ")";
			$output .= '<input type="button" value="'. __('Force re-optimize', 'resmushit') .'" class="rsmt-trigger--optimize-attachment button media-button  select-mode-toggle-button" name="resmushit" data-attachment-id="'. $attachment_id .'" class="button wp-smush-send" />';
		}

		if($return)
			return $output;
		echo $output;
	}




	/**
	 *
	 * Helper to format size in bytes
	 *
	 * @param  int $bytes filesize in bytes
	 * @return string rendered filesize
	 */
	public static function sizeFormat($bytes) {
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