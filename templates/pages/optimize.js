/** 
 * ajax post to return all images that are candidates for resizing
 * @param string the id of the html element into which results will be appended
 */
function resmushit_bulk_resize() {
	var message_title = jQuery('.rsmt-status h3');
	message_title.text('Get image list...');
	jQuery.post(
		ajaxurl, 
		{ action: 'resmushit_bulk_get_images' }, 
		function(response) {
			message_title.text('Image list received!');
			var images = JSON.parse(response);			
			if (images.nonoptimized.length > 0) {	
				bulkTotalimages = images.nonoptimized.length;
				target.html('<div class="loading--bulk"><span class="loader"></span><br />' + bulkTotalimages + ' attachment(s) found, starting optimization...</div>');
				flag_removed = false;
				//start treating all pictures
				resmushit_bulk_process(images.nonoptimized, 0);
			} else {
				target.html('<div>There are no existing attachments that requires optimization.</div>');
			}
		}
	);
}



/** 
 * recursive function for resizing images
 */
function resmushit_bulk_process(bulk, item){
	var error_occured = false;	
	jQuery.post(
		ajaxurl, { 
			action: 'resmushit_bulk_process_image', 
			data: bulk[item]
		}, 
		function(response) {
			if(response == 'failed')
				error_occured = true;
			else if(response == 'file_too_big')
				file_too_big_count++;

			if(!flag_removed){
				jQuery('#bulk_resize_target').remove();
				container.append('<div id="smush_results" style="padding: 20px 5px; overflow: auto;" />');
				var results_target = jQuery('#smush_results'); 
				results_target.html('<div class="bulk--back-progressionbar"><div <div class="resmushit--progress--bar"</div></div>');
				flag_removed = true;
			}

			bulkCounter++;
			jQuery('.resmushit--progress--bar').html('<p>'+ Math.round((bulkCounter*100/bulkTotalimages)) +'%</p>');
			jQuery('.resmushit--progress--bar').animate({'width': Math.round((bulkCounter*100/bulkTotalimages))+'%'}, 0);

			if(item < bulk.length - 1)
				resmushit_bulk_process(bulk, item + 1);
			else{
				if(error_occured){
					jQuery('.non-optimized-wrapper h3').text('An error occured when contacting webservice. Please try again later.');
					jQuery('.non-optimized-wrapper > p').remove();
					jQuery('.non-optimized-wrapper > div').remove();
				} else if(file_too_big_count){
					
					var message = file_too_big_count + ' picture cannot be optimized (> 5MB). All others have been optimized';
					if(file_too_big_count > 1)
						var message = file_too_big_count + ' pictures cannot be optimized (> 5MB). All others have been optimized';

					jQuery('.non-optimized-wrapper h3').text(message);
					jQuery('.non-optimized-wrapper > p').remove();
					jQuery('.non-optimized-wrapper > div').remove();
				} else{
					jQuery('.non-optimized-wrapper').addClass('disabled');
					jQuery('.optimized-wrapper').removeClass('disabled');
					updateStatistics();
				}
			}
		}
	);
}