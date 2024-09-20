<?php


if (! defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}



use \Resmush\Controller\CronController as CronController;

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
	// @todo This function only used by one other function ..
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

		/*if($border) {
			$borderClass = ' brdr-'.$border;
		} */

		$titleClass = str_replace(' ', '', $title);
		$titleClass = strtolower($titleClass);
		echo wp_kses_post("<div class='rsmt-panel w100 $borderClass'><h2 class='" . $titleClass . "'>$title</h2>");
	}

	/**
	 *
	 * Create a new panel wrapper (end)
	 *
	 * @param  none
	 * @return none
	 */
	public static function fullWidthPanelEndWrapper() {
		echo wp_kses_post("</div>");
	}

	/**
	 *
	 * Generate Header panel
	 *
	 * @param  none
	 * @return none
	 */
	public static function headerPanel() {
		//$html = "<img src='". RESMUSHIT_BASE_URL . "images/header.png' />";
		$html = sprintf(esc_html__("%s By %s ShortPixel %s", 'resmushit-image-optimizer' ), '<span class="byline">', '<a href="https://shortpixel.com/" target="_blank">', '</a></span>');
		self::fullWidthPanel('reSmush.it', $html);
	}

	/**
	 *
	 * Generate Settings panel
	 *
	 * @param  none
	 * @return none
	 */
	public static function settingsPanel() {
		$allowed_html = array(
			'input' => array(
				'type'      => array(),
				'name'      => array(),
				'value'     => array(),
				'checked'   => array(),
				'class'   => array(),
				'id'   => array()
			),
			'form' => array(
				'method'      => array(),
				'action'      => array(),
				'id'     => array()
			),
			'div' => array(
			  'class'      => array(),
			),
			'span' => array(
			  'class'      => array(),
			),
			'table' => array(
			  'class'      => array(),
			),
			'label' => array(
			  'class'      => array(),
			),
			'p' => array()
		);

		self::fullWidthPanelWrapper(__('Settings', 'resmushit-image-optimizer'), null, 'orange');
		echo wp_kses('<div class="rsmt-settings">
			<form method="post" action="options.php" id="rsmt-options-form">', $allowed_html);
		settings_fields( 'resmushit-settings' );
		do_settings_sections( 'resmushit-settings' );

        $current_quality = get_option('resmushit_qlty');
        $new_quality_values = array(87, 80, 74, 65, 58);

        if (!in_array($current_quality, $new_quality_values)) {
            echo '<div class="update-nag">' . esc_html__( 'Please select one of the 5 new image quality settings below. Your current quality settings will be kept for previously uploaded images. If you need to set a value outside those 5 values, you can add a filter.', 'resmushit-image-optimizer' ) . '</div>';
        }


		echo '<table class="form-table">'
            //. self::addSetting("number", __("Image quality", 'resmushit-image-optimizer'), __("A lower value means a smaller image size, a higher value means better image quality. A value between 50 and 85 is normally recommended.", 'resmushit-image-optimizer'), "resmushit_qlty")
            . self::addSetting("radio", __("Image quality", 'resmushit-image-optimizer'), __("Choose the compression level for images. A lower value means a smaller image size, a higher value means better image quality.", 'resmushit-image-optimizer'), "resmushit_qlty")

            . self::addSetting("checkbox", __("Optimize on upload", 'resmushit-image-optimizer'), __("Once activated, newly uploaded images are automatically optimized.", 'resmushit-image-optimizer'), "resmushit_on_upload")
            . self::addSetting("checkbox", __("Preserve EXIF", 'resmushit-image-optimizer'), __("Activate this option to retain the original EXIF data in the images.", 'resmushit-image-optimizer'), "resmushit_preserve_exif")
            . self::addSetting("checkbox",  __("Deactivate backup", 'resmushit-image-optimizer'), sprintf(__("If you select this option, you choose not to keep the original version of the images. This is helpful to save disk space, but we strongly recommend having a backup of the entire website on hand. <a href='%s' title='Should I remove backups?' target='_blank'>More information</a>.", "resmushit-image-optimizer"), "https://resmush.it/why-preserving-backup-files/"), "resmushit_remove_unsmushed")
            . self::addSetting("checkbox",  __("Optimize images using CRON", 'resmushit-image-optimizer'), sprintf(__("Image optimization is performed automatically via CRON tasks. <a href='%s' title='How to configure Cronjobs?' target='_blank'>More information</a>", 'resmushit-image-optimizer'), 'https://resmush.it/how-to-configure-cronjobs/'), "resmushit_cron")
        . self::addSetting("checkbox", __("Generate WebP/AVIF", 'resmushit-image-optimizer'), sprintf(__("Create WebP/AVIF versions of the images. %s Premium Conversion Access %s ", 'resmushit-image-optimizer'), '<a href="https://shortpixel.com/compare/resmushit-vs-shortpixel" target="_blank">', '</a>'), "resmushit_webpavif")
        . self::addSetting("checkbox", __("CDN Delivery", 'resmushit-image-optimizer'), sprintf(__("Deliver optimized images using our CDN. %s Get CDN Access %s ", 'resmushit-image-optimizer'), '<a href="https://shortpixel.com/compare/resmushit-vs-shortpixel" target="_blank">', '</a>'), "resmushit_webpavif")
        . self::addSetting("checkbox", __("SmartCropping", 'resmushit-image-optimizer'), sprintf(__("Generate subject-centered thumbnails using ShortPixel's AI Engine. %s Get Access %s ", 'resmushit-image-optimizer'), '<a href="https://shortpixel.com/compare/resmushit-vs-shortpixel" target="_blank">', '</a>'), "resmushit_webpavif")

				. self::addSetting("checkbox", __("Activate statistics", 'resmushit-image-optimizer'), __("Generates statistics about optimized images.", 'resmushit-image-optimizer'), "resmushit_statistics")
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
		self::fullWidthPanelWrapper(__('Optimize Media Library', 'resmushit-image-optimizer'), null, 'blue');

    $totalresult = $dataCountNonOptimizedPictures['totalresult'];
    $limitReached = false;

		$additionnalClassNeedOptimization = NULL;
		$additionnalClassNoNeedOptimization = 'disabled';
		if(!$countNonOptimizedPictures) {
			$additionnalClassNeedOptimization = 'disabled';
			$additionnalClassNoNeedOptimization = NULL;
		} else if ($totalresult == reSmushit::MAX_ATTACHMENTS_REQ) {
			$countNonOptimizedPictures .= '+';
      $limitReached = true;
		}

		echo wp_kses_post("<div class='rsmt-bulk' data-csrf='" . wp_create_nonce( 'bulk_process_image' ) . "'><div class='non-optimized-wrapper $additionnalClassNeedOptimization'><h3 class='icon_message warning'>");

		if(get_option('resmushit_cron') && get_option('resmushit_cron') == 1) {
			echo  wp_kses_post("<em>$countNonOptimizedPictures "
			. __('unoptimized images will be optimized automatically.', 'resmushit-image-optimizer')
			. "</em></h3><p>"
			. __('These images will be optimized automatically using scheduled tasks (cronjobs).', 'resmushit-image-optimizer')
			. " "
			. __('You can also start the image optimization <b>manually</b> by clicking on the button below:', 'resmushit-image-optimizer'));
		} else {
			echo  wp_kses_post(__('There are currently', 'resmushit-image-optimizer')
			. " <em>$countNonOptimizedPictures "
			. __('images that need optimization.', 'resmushit-image-optimizer')
			. "</em></h3><p>"
			. __('This action resmushes all images that have not yet been optimized with the image quality specified in the settings. If the image quality has been changed and backups are activated, images that have already been optimized are resmushed with the new image quality rate.', 'resmushit-image-optimizer'));
      if ($limitReached)
      {
          echo wp_kses_post('<p>' . __('The plugin optimizes batches of up to 1000 images at a time. After each batch is completed, refresh this page and you can cont
inue the process.', 'resmushit-image-optimizer') . '</p>');
      }
		}

		$allowed_html = array_merge(wp_kses_allowed_html( 'post' ), array(
			'button' => array(
				'class'      => array(),
				'onclick'      => array()
			)));

		echo wp_kses("</p><p class='submit' id='bulk-resize-examine-button'><button class='button-primary' onclick='resmushit_bulk_resize(\"bulk_resize_image_list\", \"" . wp_create_nonce( 'bulk_resize' ) . "\");'>", $allowed_html);

		if(get_option('resmushit_cron') && get_option('resmushit_cron') == 1) {
			echo wp_kses_post(__('Optimize all images manually', 'resmushit-image-optimizer'));
		} else {
			echo wp_kses_post(__('Optimize all images', 'resmushit-image-optimizer'));
		}

		echo ("</button></p><div id='bulk_resize_image_list'></div></div>"
		. "<div class='optimized-wrapper $additionnalClassNoNeedOptimization'><h3 class='icon_message ok'>"
		. __('Congratulations! All your images are optimized correctly!', 'resmushit-image-optimizer')
		. "</h3>
      <h3 class='support'><a href='https://wordpress.org/support/plugin/resmushit-image-optimizer/reviews/#new-post' target='_blank'>" . __('Please support us! Leave a review','resmushit-image-optimizer') . " ★★★★★</a></h3>
    </div></div>");
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

		self::fullWidthPanelWrapper(__('Unoptimized images', 'resmushit-image-optimizer'), null, 'grey');

		$additionnalClass = NULL;
		if(!$countfilesTooBigPictures) {
			$additionnalClass = 'disabled';
		}

		echo wp_kses_post("<div class='rsmt-bigfiles'><div class='optimized-wrapper $additionnalClass'>
					<h3 class='icon_message info'>");

		if($countfilesTooBigPictures > 1) {
			echo esc_html($countfilesTooBigPictures . ' ' . __('images are too large (>5MB) to be optimized', 'resmushit-image-optimizer'));
		} else {
			echo esc_html($countfilesTooBigPictures . ' ' . __('image is too large (>5MB) to be optimized', 'resmushit-image-optimizer'));
		}
		echo wp_kses_post("</h3><div class='list-accordion'><h4>"
				. __('List of images above 5MB', 'resmushit-image-optimizer')
				. "</h4><ul>");

        echo "<li><h3>" .
          sprintf(__('You can optimize these images with %s ShortPixel Image Optimizer %s','resmushit-image-optimizer'), '<a href="https://shortpixel.com/wp/af/ZGBQINU28044" target="_blank">', '</a>') . "</h3></li>";

		foreach($getNonOptimizedPictures->filestoobig as $file){
			$fileInfo = pathinfo(get_attached_file( $file->ID ));
			$filesize = reSmushitUI::sizeFormat(filesize(get_attached_file( $file->ID )));


			echo wp_kses_post("<li><a href='"
					. esc_url(wp_get_attachment_url( $file->ID ))
					. "' target='_blank'>"
					. wp_get_attachment_image($file->ID, 'thumbnail')
					. "<span>"
					. $fileInfo['basename'] . ' (' . $filesize . ').</span></a></li>');
		}
		echo wp_kses_post('</ul></div></div></div>');

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
		self::fullWidthPanelWrapper(__('Statistics', 'resmushit-image-optimizer'), null, 'green');
		$resmushit_stat = reSmushit::getStatistics();

		echo wp_kses_post("<div class='rsmt-statistics'>");

		if($resmushit_stat['files_optimized'] != 0) {
			echo wp_kses_post("<p><strong>"
					. __('Storage saved:', 'resmushit-image-optimizer')
					. "</strong> <span id='rsmt-statistics-space-saved'>"
					. self::sizeFormat($resmushit_stat['total_saved_size'])
					. "</span></p><p><strong>"
					. __('Total reduction:', 'resmushit-image-optimizer')
					. "</strong> <span id='rsmt-statistics-percent-reduction'>"
					. $resmushit_stat['percent_reduction']
					. "</span></p><p><strong>"
					. __('Attachments optimized:', 'resmushit-image-optimizer')
					. "</strong> <span id='rsmt-statistics-files-optimized'>"
					. $resmushit_stat['files_optimized']
					. "</span>/<span id='rsmt-statistics-total-picture'>"
					. $resmushit_stat['total_pictures']
					. "</span></p><p><strong>"
					. __('Optimized images (including thumbnails):', 'resmushit-image-optimizer')
					. "</strong> <span id='rsmt-statistics-files-optimized'>"
					. $resmushit_stat['files_optimized_with_thumbnails']
					. "</span>/<span id='rsmt-statistics-total-pictures'>"
					. $resmushit_stat['total_pictures_with_thumbnails']
					. "</span></p><p><strong>"
					. __('Total optimized images:', 'resmushit-image-optimizer')
					. "</strong> <span id='rsmt-statistics-total-optimizations'>"
					. $resmushit_stat['total_optimizations']
					. "</span></p>");
			} else {
				echo wp_kses_post("<p>" . __('No image has been optimized yet! Add images to your WordPress\' Media Library.', 'resmushit-image-optimizer') . "</p>");
			}
		echo wp_kses_post("</div>");
		self::fullWidthPanelEndWrapper();
	}

  public static function feedbackPanel()
  {
    self::fullWidthPanelWrapper(__('Support', 'resmushit-image-optimizer'), null, 'green');


    ?>
    <ul>
    <li>
      <a href="https://resmush.it/contact/" target="_blank">
      <?php _e('Contact support', 'resmushit-image-optimizer'); ?></a>
    </li>
    <li>
      <a href="https://resmush.it/features/" target="_blank"><?php _e('Plugin features','resmushit-image-optimizer'); ?></a>
    </li>
    <li>
      <a href="https://resmush.it/why-preserving-backup-files/" target="_blank"><?php _e('Backup information','resmushit-image-optimizer'); ?></a>
    </li>
    <li>
      <a href="https://resmush.it/how-to-configure-cronjobs/" target="_blank"><?php _e('How to configure CRON jobs','resmushit-image-optimizer'); ?></a>
    </li>
    <li>
      <a href="https://shortpixel.com/knowledge-base/article/582-an-error-occured-during-the-optimization-self-debugging" target="_blank"><?php _e('"An error occured during the optimization" - Self debugging','resmushit-image-optimizer'); ?></a>
    </li>
    <p>&nbsp;</p>

    <?php
    self::fullWidthPanelEndWrapper();
    self::fullWidthPanelWrapper(__('Feedback', 'resmushit-image-optimizer'), null, 'orange');


    $html = '
    <p>' . esc_html__( 'Leave us feedback or suggest a new feature!', 'resmushit-image-optimizer' ) . '</p>
    <ul><li><a href='. RESMUSHIT_FEEDBACK_URL . ' target="_blank">' . esc_html__( 'Feedback form', 'resmushit-image-optimizer' ) . '</a></li></ul>';


    echo wp_kses_post($html);
    self::fullWidthPanelEndWrapper();

  }


/**
	 *
	 * Generate Statistics panel
	 *
	 * @param  none
	 * @return none
	 */
	public static function restorePanel() {
		if(get_option('resmushit_remove_unsmushed') == 1 ){
			return FALSE;
		}
		self::fullWidthPanelWrapper(__('Restore Media Library', 'resmushit-image-optimizer'), null, 'black');
		$allowed_html = array_merge(wp_kses_allowed_html( 'post' ), array(
		'input' => array(
			'type'      => array(),
			'value'      => array(),
			'class'      => array(),
			'name'      => array(),
			'data-csrf'      => array(),
		)));

		echo wp_kses("<div class='rsmt-restore'>"
			. '<p><strong>'
			. __('Warning! By clicking the button below, all original images will revert to the state they were in before they were optimized with reSmush.it Image Optimizer!', 'resmushit-image-optimizer')
			. '</strong></p><p>'
			. '<input type="button" data-csrf="'. wp_create_nonce( 'restore_library' ) .'" value="'. __('Restore ALL my original images', 'resmushit-image-optimizer') .'" class="rsmt-trigger--restore-backup-files button media-button  select-mode-toggle-button" name="resmushit" class="button wp-smush-send" />'
			. '</div>', $allowed_html);
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
		return;
		global $wp_version;

		echo wp_kses_post("<div class='rsmt-news'>");

		self::fullWidthPanelWrapper(__('News', 'resmushit-image-optimizer'), null, 'red');
		if(in_array('curl', get_loaded_extensions())){
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, RESMUSHIT_NEWSFEED);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
			$data_raw = curl_exec($ch);
			curl_close($ch);
			$data = json_decode($data_raw);
		} else {
			$data = [];
		}
		if($data) {
			foreach($data as $i=>$news) {
				if($i > 2){
					break;
				}

				echo wp_kses_post("<div class='news-item'><span class='news-date'>"
						. date('d/m/Y', $news->date)
						. "</span>");
				if($news->picture) {
					echo wp_kses_post("<div class='news-img'><a href='"
							. esc_url($news->link)
							. "' target='_blank'><img src='"
							. esc_url($news->picture)
							. "' /></a></div>");
				}
				echo wp_kses_post("<h3><a href='"
						. esc_url($news->link)
						. "' target='_blank'>"
						. $news->title
						. "</a></h3><div class='news-content'>"
						. $news->content
						. "</div>");
			}
		}

		echo wp_kses_post("<div class='social'>"
				. "<p class='datainformation'>"
				. __('No user data nor any information is collected while requesting this news feed.', 'resmushit-image-optimizer')
				. "<p>"
				. "<a class='social-resmushit' title='"
				. __('Visit resmush.it for more information', 'resmushit-image-optimizer')
				. "' href='https://resmush.it' target='_blank'>"
				. "<img src='"
				. RESMUSHIT_BASE_URL . "images/logo.png' /></a>"
				. "<a class='social-twitter' title='"
				. __('Follow reSmush.it on Twitter', 'resmushit-image-optimizer')
				. "' href='https://www.twitter.com/resmushit' target='_blank'>"
				. "<img src='"
				. RESMUSHIT_BASE_URL . "images/twitter.png' /></a></div></div>");

		self::fullWidthPanelEndWrapper();
	}


	/**
	 *
	 * Generate ALERT panel
	 *
	 * @param  none
	 * @return none
	 */
	public static function alertPanel() {

		$cronController = CronController::getInstance();

		$cron_status = $cronController->get_cron_status();

		if (
				(	get_option('resmushit_remove_unsmushed') == 0
					|| (get_option('resmushit_remove_unsmushed') == 1 && get_option('resmushit_has_no_backup_files') == 1))
				&& ($cron_status == 'DISABLED' || $cron_status == 'OK')) {
			return TRUE;
		}

		self::fullWidthPanelWrapper(__('Important information', 'resmushit-image-optimizer'), null, 'red');

		if($cron_status != 'DISABLED' && $cron_status != 'OK') {

			echo wp_kses_post("<div class='rsmt-alert'>"
			. "<h3 class='icon_message warning'>"
			. __('Cronjobs are not configured correctly.', 'resmushit-image-optimizer')
			. "</h3>");

			if ($cron_status == 'MISCONFIGURED') {
				echo wp_kses_post("<p>"
					. __('Cronjobs are not configured correctly. The variable <em>DISABLE_WP_CRON</em> should be set to <em>TRUE</em> in <em>wp-config.php</em>. Please configure them using the following <a href="https://resmush.it/how-to-configure-cronjobs/" target="_blank">documentation</a>.', 'resmushit-image-optimizer')
					. "</p><p>"
					. __('We recommend deactivating the option "Optimize images using CRON" until the cronjobs are configured correctly.', 'resmushit-image-optimizer')
					. "</p>");
			} else if ($cron_status == 'NEVER_RUN') {
				echo wp_kses_post("<p>"
					. __('The Cronjobs were never started. Please configure them using the following <a href="https://resmush.it/how-to-configure-cronjobs/" target="_blank">documentation</a>.', 'resmushit-image-optimizer')
					. "</p>");
			} else if ($cron_status == 'NO_LATELY_RUN') {
				echo wp_kses_post("<p>"
					. __('Cronjobs have not been executed recently. Please configure them using the following <a href="https://resmush.it/how-to-configure-cronjobs/" target="_blank">documentation</a>.', 'resmushit-image-optimizer')
					. "<ul><li><em>" . __('Expected Frequency :', 'resmushit-image-optimizer') . "</em> " . __('Every', 'resmushit-image-optimizer') . " " . time_elapsed_string(RESMUSHIT_CRON_FREQUENCY) . "</li>"
					. "<li><em>" . __('Last run :', 'resmushit-image-optimizer') . "</em> " . time_elapsed_string(time() - get_option('resmushit_cron_lastrun')) . " " . __('ago', 'resmushit-image-optimizer') . "</li></ul>"
					. "</p>");
			}
			echo wp_kses_post("</div>");
		}
		if(get_option('resmushit_remove_unsmushed') == 1 && get_option('resmushit_has_no_backup_files') == 0) {
			$files_to_delete = count(reSmushit::detect_unsmushed_files());

			if($files_to_delete) {
				$allowed_html = array_merge(wp_kses_allowed_html( 'post' ), array(
				'input' => array(
					'type'      => array(),
					'value'      => array(),
					'class'      => array(),
					'name'      => array(),
					'data-csrf'      => array()
				)));
				echo wp_kses("<div class='rsmt-alert'>"
				. "<h3 class='icon_message warning'>"
				. __('Backup files can be removed.', 'resmushit-image-optimizer')
				. "</h3>"
				.	'<p>'
				. sprintf(__('Keep these files and turn off the option "Disable backup" if you want to restore your unoptimized files in the future. Please <a href="%s" title="Should I remove backups?" target="_blank">read instructions</a> before clicking.', 'resmushit-image-optimizer'), 'https://resmush.it/why-preserving-backup-files/')
				. '</p><p>'
				. sprintf( __( 'We have found %s files ready to be removed', 'resmushit-image-optimizer' ), count(reSmushit::detect_unsmushed_files()) )
				. '</p><p>'
				. '<input type="button" value="'. __('Remove backup files', 'resmushit-image-optimizer') .'" data-csrf="'. wp_create_nonce( 'remove_backup' ) .'" class="rsmt-trigger--remove-backup-files button media-button  select-mode-toggle-button" name="resmushit" class="button wp-smush-send" />'
				. "</div>", $allowed_html);
			}
		}


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
        $output = "<div class='setting-row type-$type'>";
        $label = "<label for='$machine_name'>$name<p>$extra</p></label>";

        switch ($type) {
            case 'text':
                $output .= $label . "<input type='text' name='$machine_name' id='$machine_name' value='" . esc_attr(get_option($machine_name)) . "'/>";
                break;
            case 'number':
                $more = ($machine_name == 'resmushit_qlty') ? '&nbsp;&nbsp;<a href="https://shortpixel.com/compare/resmushit-vs-shortpixel" target="_blank">' . __('What is the best way to optimize images?', 'resmushit-image-optimizer') . '</a></p></div>' : '';
                $output .= $label . "<span><input type='number' class='number-small' name='$machine_name' id='$machine_name' value=''" . esc_attr(get_option($machine_name)) . "'/>$more</span>";
                break;
            case 'radio':
                if ($machine_name === 'resmushit_qlty' && has_filter('resmushit_image_quality')) {
                    // preparing for user filter here: if filter is in use, display the message, else, no filter == continue as normal
                    $output .= $label . "<p>" . __('Quality level is set through a filter and cannot be changed here.', 'resmushit-image-optimizer') . "</p>";
                } else {
                    $output .= $label;
                    $compression_levels = array(
                        array('name' => __('Best Quality', 'resmushit-image-optimizer'), 'value' => '87'),
                        array('name' => __('Good Quality', 'resmushit-image-optimizer'), 'value' => '80'),
                        array('name' => __('Balanced', 'resmushit-image-optimizer'), 'value' => '74'),
                        array('name' => __('Good Compression', 'resmushit-image-optimizer'), 'value' => '65'),
                        array('name' => __('Best Compression', 'resmushit-image-optimizer'), 'value' => '58'),
                    );

                    $current_value = get_option($machine_name);

                    $output .= "<div class='quality-buttons'>";
                    foreach ($compression_levels as $level) {
                        $checked = ($current_value == $level['value']) ? 'checked' : '';
                        $active_class = ($current_value == $level['value']) ? 'active' : '';

                        $title = sprintf(esc_html__('Quality level: %s', 'resmushit-image-optimizer'), esc_attr($level['value']) );

                        $output .= "<button type='button' class='quality-button $active_class'
                        data-value='" . esc_attr($level['value']) . "'
                        title='" . $title . "'>{$level['name']}</button>";
                    }
                    $output .= "</div>";
                    $output .= "<input type='hidden' name='$machine_name' id='$machine_name' value='$current_value'>";
                }
                break;
            case 'checkbox':
                $additionnal = null;
                if (1 == get_option($machine_name)) $additionnal = 'checked="checked"';
                $disabled = ($machine_name == 'resmushit_webpavif') ? 'disabled' : '';
                $output .= "<input type='checkbox' name='$machine_name' id='$machine_name' $disabled value='1' " . $additionnal . "/>";
                $output .= $label;
                break;
            default:
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

		$post = get_post($id);
		if ( !preg_match("/image.*/", $post->post_mime_type) )
			return;

		if (reSmushit::isImageOptimized($id) === true)
		{ return;
		}

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

		$output = '<label>' . __('Exclude Image', 'resmushit-image-optimizer') . ' <input type="checkbox" data-attachment-id="'. $id .'"" data-csrf="'. wp_create_nonce( 'single_attachment' ) .'"" class="rsmt-trigger--disabled-checkbox" '. $attachment_resmushit_disabled .'  /></label>';

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
		$post = get_post($attachment_id);
		if ( !preg_match("/image.*/", $post->post_mime_type) )
			return;	//

//var_dump(reSmushit::getAttachmentQuality($attachment_id));
//var_dump(reSmushit::getPictureQualitySetting());

	 	$current_quality = reSmushit::getAttachmentQuality($attachment_id);
		$setting_quality = reSmushit::getPictureQualitySetting();

		if(reSmushit::getDisabledState($attachment_id)){
			$output = __('Image excluded from optimization','resmushit-image-optimizer');
		}
		else if(is_null($current_quality))
			$output = '<button type="button" data-csrf="' . wp_create_nonce( 'single_attachment' ) . '"  class="rsmt-trigger--optimize-attachment button media-button  select-mode-toggle-button" name="resmushit" data-attachment-id="'. $attachment_id .'" class="button wp-smush-send">'. __('Optimize', 'resmushit-image-optimizer') .'</button>';
		else{
			$statistics = reSmushit::getStatistics($attachment_id);
			$output = __('Reduced by', 'resmushit-image-optimizer') . " ". $statistics['total_saved_size_nice'] ." <br>(". $statistics['percent_reduction'] . ' ' . __('saved', 'resmushit-image-optimizer') . ")";

			if (reSmushit::hasBackup($attachment_id)) {
				$output .= '<p><button type="button" data-csrf="' . wp_create_nonce( 'single_attachment' ) . '" class="rsmt-trigger--optimize-attachment button media-button  select-mode-toggle-button" name="resmushit" data-attachment-id="'. $attachment_id .'" class="button wp-smush-send">'. __('Force re-optimize', 'resmushit-image-optimizer') .'</button></p>';
				$output .= '<p><button type="button" data-csrf="' . wp_create_nonce('single_attachment') . '" class="rsmt-trigger--restore-attachment button media-button  select-mode-toggle-button" name="resmushit" data-attachment-id="' . $attachment_id . '" class="button wp-smush-send">' .
				__('Restore', 'resmushit-image-optimizer') . '</button></p>';

				if ($current_quality <> $setting_quality)
				{
					$output .= "<div>" . 	sprintf(__('The optimized quality (%s) differs from the setting (%s). You can change the optimization to the current setting by clicking on "Force re-optimize". ', 'resmushit-image-optimizer'), $current_quality, $setting_quality) . '</div>';
				}

			}

		}

		if($return)
			return $output;
		$allowed_html = array_merge(wp_kses_allowed_html( 'post' ), array(
			'input' => array(
				'type'      => array(),
				'value'      => array(),
				'class'      => array(),
				'name'      => array(),
				'data-csrf'      => array(),
				'data-attachment-id'      => array(),
				'checked'   => array(),
		)));
		echo wp_kses($output, $allowed_html);

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
