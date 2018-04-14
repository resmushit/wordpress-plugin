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
		?>
		<div class='rsmt-panel w100 <?php if($border) echo 'brdr-'.$border; ?>'>
			<h2><?php echo $title; ?></h2>
		<?php
	}




	/**
	 *
	 * Create a new panel wrapper (end)
	 *
	 * @param  none
	 * @return none
	 */
	public static function fullWidthPanelEndWrapper() {
		?>
		</div>
		<?php
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
		?>
		<div class="rsmt-settings">
			<form method="post" action="options.php" id="rsmt-options-form">
			    <?php settings_fields( 'resmushit-settings' ); ?>
			    <?php do_settings_sections( 'resmushit-settings' ); ?>
				<table class="form-table">
					<?php self::addSetting("text", __("Image quality", 'resmushit'), __("Default value is 92. The quality factor must be between 0 (very weak) and 100 (best quality)", 'resmushit'), "resmushit_qlty") ?>
					<?php self::addSetting("checkbox", __("Optimize on upload", 'resmushit'), __("All future images uploaded will be automatically optimized", 'resmushit'), "resmushit_on_upload") ?>
					<?php self::addSetting("checkbox", __("Enable statistics", 'resmushit'), __("Generates statistics about optimized pictures", 'resmushit'), "resmushit_statistics") ?>
					<?php self::addSetting("checkbox", __("Enable logs", 'resmushit'), __("Enable file logging (for developers)", 'resmushit'), "resmushit_logs") ?>
				</table>
			    <?php submit_button(); ?>
			 </form>
		</div>
		<?php self::fullWidthPanelEndWrapper(); 		
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
		?>

		<div class="rsmt-bulk">
			<div class="non-optimized-wrapper <?php if(!$countNonOptimizedPictures) echo 'disabled' ?>">
				<h3 class="icon_message warning"><?php _e('There is currently', 'resmushit') ?> <em><?php echo $countNonOptimizedPictures; ?> <?php _e('non optimized pictures', 'resmushit') ?></em>.</h3>
				<p><?php _e('This action will resmush all pictures which have not been optimized to the good Image Quality Rate.', 'resmushit') ?></p>
				<p class="submit" id="bulk-resize-examine-button">
					<button class="button-primary" onclick="resmushit_bulk_resize('bulk_resize_image_list');"><?php _e('Optimize all pictures', 'resmushit') ?></button>
				</p>
				<div id='bulk_resize_image_list'></div>
			</div>

			<div class="optimized-wrapper <?php if($countNonOptimizedPictures) echo 'disabled' ?>">
				<h3 class="icon_message ok"><?php _e('Congrats ! All your pictures are correctly optimized', 'resmushit') ?></h3>
			</div>
		</div>
		<?php self::fullWidthPanelEndWrapper(); 		
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
;
		$countfilesTooBigPictures = sizeof($getNonOptimizedPictures->filestoobig);
		if(!$countfilesTooBigPictures)
			return false;

		self::fullWidthPanelWrapper(__('Files non optimized', 'resmushit'), null, 'grey');
		?>

		<div class="rsmt-bigfiles">
			<div class="optimized-wrapper <?php if(!$countfilesTooBigPictures) echo 'disabled' ?>">
				<h3 class="icon_message info">
					<?php if($countfilesTooBigPictures > 1): ?>
						<?php echo $countfilesTooBigPictures . ' ' ?><?php _e('pictures are too big (> 5MB) for the optimizer', 'resmushit') ?>
					<?php else: ?>
						<?php echo $countfilesTooBigPictures . ' ' ?><?php _e('picture is too big (> 5MB) for the optimizer', 'resmushit') ?>
					<?php endif ?>
				</h3>
				<div class="list-accordion">
					<h4><?php _e('List of files above 5MB', 'resmushit') ?></h4>
					<ul>
						<?php foreach($getNonOptimizedPictures->filestoobig as $file): ?>
							<?php 
								$fileInfo = pathinfo(get_attached_file( $file->ID )); 
								$filesize = reSmushitUI::sizeFormat(filesize(get_attached_file( $file->ID ))); 
							?>
							<li><a href="<?php echo wp_get_attachment_url( $file->ID ); ?>" target="_blank"><?php echo wp_get_attachment_image($file->ID, 'thumbnail'); ?><span><?php echo $fileInfo['basename'] . ' (' . $filesize . ').' ?></span></a></li>
						<?php endforeach; ?>
					</ul>
				</div>
			</div>
		</div>
		<?php self::fullWidthPanelEndWrapper(); 		
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
		?>

		<div class="rsmt-statistics">
			<?php $resmushit_stat = reSmushit::getStatistics();
			if($resmushit_stat['files_optimized'] != 0):
			?>
			<p><strong><?php _e('Space saved :', 'resmushit') ?></strong> <span id="rsmt-statistics-space-saved"><?php echo self::sizeFormat($resmushit_stat['total_saved_size'])?></span></p>
			<p><strong><?php _e('Total reduction :', 'resmushit') ?></strong> <span id="rsmt-statistics-percent-reduction"><?php echo $resmushit_stat['percent_reduction'] ?></span></p>
			<p><strong><?php _e('Attachments optimized :', 'resmushit') ?></strong> <span id="rsmt-statistics-files-optimized"><?php echo $resmushit_stat['files_optimized'] ?></span>/<span id="rsmt-statistics-total-pictures"><?php echo $resmushit_stat['total_pictures'] ?></span></p>
			<p><strong><?php _e('Image optimized (including thumbnails) :', 'resmushit') ?></strong> <span id="rsmt-statistics-files-optimized"><?php echo $resmushit_stat['files_optimized_with_thumbnails'] ?></span>/<span id="rsmt-statistics-total-pictures"><?php echo $resmushit_stat['total_pictures_with_thumbnails'] ?></span></p>
			<p><strong><?php _e('Total images optimized :', 'resmushit') ?></strong> <span id="rsmt-statistics-total-optimizations"><?php echo $resmushit_stat['total_optimizations'] ?></span></p>
			<?php else: ?>
			<p><?php _e('No picture has been optimized yet ! Add pictures to your Wordpress Media Library.', 'resmushit') ?></p>
			<?php endif; ?>
		</div>
		<?php self::fullWidthPanelEndWrapper(); 		
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
		?>
		<div class="rsmt-news">
		
		<?php
		self::fullWidthPanelWrapper(__('News', 'resmushit'), null, 'red');
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, RESMUSHIT_NEWSFEED);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
		$data_raw = curl_exec($ch);
		curl_close($ch);
		$data = json_decode($data_raw);
		if($data):
			foreach($data as $i=>$news):
				if($i > 2)
					break;
			?>
				<div class="news-item">
					<span class="news-date"><?php echo date('d/m/Y', $news->date) ?></span>
					<?php if($news->picture): ?>
					<div class="news-img">
						<a href="<?php echo $news->link ?>" target="_blank">
							<img src="<?php echo $news->picture ?>" />
						</a>
					</div>
					<?php endif; ?>
					<h3><a href="<?php echo $news->link ?>" target="_blank"><?php echo $news->title ?></a></h3>
					<div class="news-content">
						<?php echo $news->content ?>
					</div>
				</div>
			
			<?php endforeach; ?>
		<?php endif; ?>
		<div class="social">
			<a class="social-maecia" title="<?php _e('Maecia Agency - Paris France', 'resmushit') ?>" href="https://www.maecia.com" target="_blank">
				<img src="<?php echo RESMUSHIT_BASE_URL ?>images/maecia.png" />
			</a>
			<a class="social-resmushit" title="<?php _e('Visit resmush.it for more informations', 'resmushit') ?>" href="https://www.resmush.it" target="_blank">
				<img src="<?php echo RESMUSHIT_BASE_URL ?>images/logo.png" />
			</a>
			<a class="social-twitter" title="<?php _e('Follow reSmush.it on Twitter', 'resmushit') ?>" href="https://www.twitter.com/resmushit" target="_blank">
				<img src="<?php echo RESMUSHIT_BASE_URL ?>images/twitter.png" />
			</a>
		</div>
		</div>
		<?php self::fullWidthPanelEndWrapper(); 		
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
		echo "<div class='setting-row type-$type'>";
		echo "<label for='$machine_name'>$name<p>$extra</p></label>";
		switch($type){
			case 'text':
				echo "<input type='text' name='$machine_name' id='$machine_name' value='". get_option( $machine_name ) ."'/>";
				break;
			case 'checkbox':
				$additionnal = null;
				if ( 1 == get_option( $machine_name ) ) $additionnal = 'checked="checked"'; 
				echo "<input type='checkbox' name='$machine_name' id='$machine_name' value='1' ".  $additionnal ."/>";
				break;
		}
		echo '</div>';
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