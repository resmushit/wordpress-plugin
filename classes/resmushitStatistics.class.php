<?php

 /**
   * ReSmushit
   * 
   * 
   * @package    Resmush.it
   * @subpackage Controller
   * @author     Charles Bourgeaux <hello@resmush.it>
   */
Class reSmushitStatistics {

	public static function getUnoptimizedAttachmentsCount() {
		$totalOptimizedAttachments = (int)self::getOptimizedAttachmentsCount();
		$totalAllAttachments = (int)self::getAllAttachmentsCount();
		
		return (int)($totalAllAttachments - $totalOptimizedAttachments);
	}

	/**
      * 
      * Get the Percent of Unoptimized attachments
      *
      * @param none
      * @return int : number of optimized attachments
      */
	public static function getUnoptimizedAttachmentsPercent() {
		$totalUnoptimizedAttachments = (int)self::getUnoptimizedAttachmentsCount();
		$totalAllAttachments = (int)self::getAllAttachmentsCount();

		if($totalAllAttachments != 0) {
			return round(100*((int)($totalUnoptimizedAttachments)/$totalAllAttachments), 2) . '%';
		} else {
			return '0%';
		}
	}
	
	/**
      * 
      * Get the count of all attachments
      *
      * @param none
      * @return int : number of optimized attachments
      */
	public static function getOptimizedAttachmentsCount() {
		$qltyFactor = resmushit::getPictureQualitySetting();
		global $wpdb;

		$query = $wpdb->prepare( 
			"select
				Count($wpdb->posts.ID) as result
				from $wpdb->posts
				inner join $wpdb->postmeta as MetaQlty on $wpdb->posts.ID = MetaQlty.post_id and MetaQlty.meta_key = %s
				WHERE MetaQlty.meta_value= %s
				",
				array('resmushed_quality', $qltyFactor)
		);	
		$data = $wpdb->get_results($query);

		if(isset($data[0]) && isset($data[0]->result)) {
			return (int)$data[0]->result;
		}
		return 0;
	}

	/**
      * 
      * Get the Percent of Optimized attachments
      *
      * @param none
      * @return int : number of optimized attachments
      */
	public static function getOptimizedAttachmentsPercent() {
		$totalOptimizedAttachments = (int)self::getOptimizedAttachmentsCount();
		$totalAllAttachments = (int)self::getAllAttachmentsCount();

		if($totalAllAttachments != 0) {
			return round(100*((int)($totalOptimizedAttachments)/$totalAllAttachments), 2) . '%';
		} else {
			return '0%';
		}
	}

	/**
      * 
      * Get the count of all images linked to all attachments
      *
      * @param none
      * @return int : number of all optimized images for all attachments
      */
	public static function getOptimizedImagesCount() {
		return self::getOptimizedAttachmentsCount() * (sizeof(get_intermediate_image_sizes()) + 1);
	}

	/**
      * 
      * Get the size of Original Pictures Processed
      *
      * @param none
      * @return int : number of optimized attachments
      */
	public static function getSizeofOriginalPicturesProcessed() {
		$qltyFactor = resmushit::getPictureQualitySetting();
		global $wpdb;

		$query = $wpdb->prepare( 
			"select
				SUM(MetaOrSize.meta_value) as result
				from $wpdb->posts
				inner join $wpdb->postmeta as MetaOrSize on $wpdb->posts.ID = MetaOrSize.post_id and MetaOrSize.meta_key = %s
				inner join $wpdb->postmeta as MetaQlty on $wpdb->posts.ID = MetaQlty.post_id and MetaQlty.meta_key = %s
				WHERE MetaQlty.meta_value=%s
				",
				array('resmushed_cumulated_original_sizes', 'resmushed_quality', $qltyFactor)
		);	
		$data = $wpdb->get_results($query);

		if(isset($data[0]) && isset($data[0]->result)) {
			return (int)$data[0]->result;
		}
		return 0;
	}


	/**
      * 
      * Get the size of Optimized Pictures Processed
      *
      * @param none
      * @return int : number of optimized attachments
      */
	public static function getSizeofOptimizedPicturesProcessed() {
		$qltyFactor = resmushit::getPictureQualitySetting();
		global $wpdb;

		$query = $wpdb->prepare( 
			"select
				SUM(MetaOrSize.meta_value) as result
				from $wpdb->posts
				inner join $wpdb->postmeta as MetaOrSize on $wpdb->posts.ID = MetaOrSize.post_id and MetaOrSize.meta_key = %s
				inner join $wpdb->postmeta as MetaQlty on $wpdb->posts.ID = MetaQlty.post_id and MetaQlty.meta_key = %s
				WHERE MetaQlty.meta_value=%s
				",
				array('resmushed_cumulated_optimized_sizes', 'resmushed_quality', $qltyFactor)
		);	
		$data = $wpdb->get_results($query);

		if(isset($data[0]) && isset($data[0]->result)) {
			return (int)$data[0]->result;
		}
		return 0;
	}



	/**
      * 
      * Get the total spaced save for this quality
      *
      * @param none
      * @return int : space saved
      */
	public static function getSpaceSaved() {
		$totalSizeOriginalPictures = (int)self::getSizeofOriginalPicturesProcessed();
		$totalSizeOptimizedPictures = (int)self::getSizeofOptimizedPicturesProcessed();
		$data = [];
		$data['totalBytesSaved'] = (int)($totalSizeOriginalPictures - $totalSizeOptimizedPictures);
		if($totalSizeOriginalPictures != 0) {
			$data['percentSaved'] = round(100*($data['totalBytesSaved']/$totalSizeOriginalPictures), 2) . '%';
		} else {
			$data['percentSaved'] = 0;
		}
		return $data;
	}

	/**
      * 
      * Get the count of all attachments
      *
      * @param none
      * @return int : number of all attachments
      */
	public static function getAllAttachmentsCount(){
		global $wpdb;

		$query = $wpdb->prepare( 
			"select
				Count($wpdb->posts.ID) as result
				from $wpdb->posts
				inner join $wpdb->postmeta on $wpdb->posts.ID = $wpdb->postmeta.post_id and $wpdb->postmeta.meta_key = %s
				where $wpdb->posts.post_type = %s
				and $wpdb->posts.post_mime_type like %s
				and ($wpdb->posts.post_mime_type = 'image/jpeg' OR $wpdb->posts.post_mime_type = 'image/gif' OR $wpdb->posts.post_mime_type = 'image/png')",
				array('_wp_attachment_metadata','attachment', 'image%')
			);
		$data = $wpdb->get_results($query);
		
		if(isset($data[0]) && isset($data[0]->result)) {
			return (int)$data[0]->result;
		}
		return 0;
	}

	/**
      * 
      * Get the count of all API calls already processed
      *
      * @param none
      * @return int : number of API call since installation
      */
	public static function getTotalApiCallCount() {
		return (int)get_option('resmushit_total_optimized');
	}
}
