
/**
 * Bulk Resize admin javascript functions
 */
var bulkCounter = 0;
var bulkTotalimages = 0;
var next_index = 0;
var file_too_big_count = 0;


/**
 * Notice
 */
jQuery(document).delegate(".rsmt-notice button.notice-dismiss","click",function(e){
	var current = this;
	var csrf_token = jQuery(current).parent().attr('data-csrf');
	jQuery.post(
		ajaxurl, {
			action: 'resmushit_notice_close',
			csrf: csrf_token
		},
		function(response) {
			var data = JSON.parse(response);
		}
	);
});


/*Hide notice about 5 quality setting, after one of them gets selected*/

document.addEventListener('DOMContentLoaded', function() {

	const qualityButtons = document.querySelectorAll('.quality-button');
	const qualityInput = document.querySelector('input[name="resmushit_qlty"]');
	const notification = document.querySelector('.update-nag');

	qualityButtons.forEach(button => {
		button.addEventListener('click', function() {
			qualityButtons.forEach(btn => btn.classList.remove('active'));
			this.classList.add('active');
			qualityInput.value = this.getAttribute('data-value');
			if (notification) {
				notification.style.display = 'none';
			}
		});
	});
});



/**
 * Form Validators
 */
/*jQuery("#rsmt-options-form").submit(function(){
	jQuery("#resmushit_qlty").removeClass('form-error');
	var qlty = jQuery("#resmushit_qlty").val();
	if(!jQuery.isNumeric(qlty) || qlty > 100 || qlty < 0){
		jQuery("#resmushit_qlty").addClass('form-error');
		return false;
	}
});*/

jQuery(document).ready(function($) {
	$('.quality-button').on('click', function() {
		$('.quality-button').removeClass('active');
		$(this).addClass('active');
		var value = $(this).data('value');
		$('#resmushit_qlty').val(value);
	});
});



jQuery( ".list-accordion h4" ).on('click', function(){
	if(jQuery(this).parent().hasClass('opened')){
		jQuery(".list-accordion ul").slideUp();
		jQuery('.list-accordion').removeClass('opened');

	} else {
		jQuery(".list-accordion ul").slideDown();
		jQuery('.list-accordion').addClass('opened');
	}
});

// NAVIGATION


function ChangePanelEvent(event)
{
		event.preventDefault();
		var target = event.target;
		var tabTarget = target.dataset.tab;

		// This can be done better if something was decided.
		var tabNavs = document.querySelectorAll('.rsmt-tabs-nav li');
		for (var i = 0; i < tabNavs.length; i++)
		{
			 tabNavs[i].classList.remove('active');
			 if (tabNavs[i].dataset.tab == tabTarget)
			 {
				 tabNavs[i].classList.add('active');
			 }
		}

		var searchClass = 'rsmt-tab-' + tabTarget;

		// Hide everything else.
		var tabs = document.querySelectorAll('.rsmt-panels .rsmt-tab');
		for (var i = 0; i < tabs.length; i++)
		{
			 tabs[i].style.display = 'none';
			 tabs[i].classList.remove('active');
			 if (tabs[i].classList.contains(searchClass))
			 {
				  tabs[i].style.display = 'block';
					tabs[i].classList.add('active');
			 }
		}
}

var tabNavs = document.querySelectorAll('.rsmt-tabs-nav li');
for (var i = 0; i < tabNavs.length; i++)
{
		if (tabNavs[i].dataset.tab !== undefined)
	 		tabNavs[i].addEventListener('click', ChangePanelEvent);
}

updateDisabledState();
optimizeSingleAttachment();
restoreSingleAttachment();
removeBackupFiles();
restoreBackupFiles();


/**
 * recursive function for resizing images
 */
function resmushit_bulk_process(bulk, item){
	var error_occured = false;
	var csrf_token = jQuery('.rsmt-bulk').attr('data-csrf');
	jQuery.post(
		ajaxurl, {
			action: 'resmushit_bulk_process_image',
			data: bulk[item],
			csrf: csrf_token
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
				results_target.html('<div class="bulk--back-progressionbar"><div class="resmushit--progress--bar"></div></div><button id="stopbulk" class="button button-primary" >' + reSmush.strings.stop_optimization + '</button>');
				flag_removed = true;
			}

			jQuery('#stopbulk').on('click', function () {  window.location.reload() });

			bulkCounter++;
			jQuery('.resmushit--progress--bar').html('<p>'+ Math.round((bulkCounter*100/bulkTotalimages)) +'%</p>');
			jQuery('.resmushit--progress--bar').animate({'width': Math.round((bulkCounter*100/bulkTotalimages))+'%'}, 0);

			if(item < bulk.length - 1)
				resmushit_bulk_process(bulk, item + 1);
			else{
				if(error_occured){
					jQuery('.non-optimized-wrapper h3').text(reSmush.strings.error_webservice);
					jQuery('.non-optimized-wrapper > p').remove();
					jQuery('.non-optimized-wrapper > div').remove();
				} else if(file_too_big_count){

					var message = file_too_big_count + ' ' . reSmush.strings.picture_too_big;

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


/**
 * ajax post to return all images that are candidates for resizing
 * @param string the id of the html element into which results will be appended
 */
function resmushit_bulk_resize(container_id, csrf_token) {
	container = jQuery('#'+container_id);
	container.html('<div id="bulk_resize_target">');
	jQuery('#bulk-resize-examine-button').fadeOut(200);
	var target = jQuery('#bulk_resize_target');

	target.html('<div class="loading--bulk"><span class="loader"></span><br />' + reSmush.strings.examing_attachments + '</div>');

	target.animate(
		{ height: [100,'swing'] },
		500,
		function() {
			jQuery.post(
				ajaxurl,
				{ action: 'resmushit_bulk_get_images', csrf: csrf_token },
				function(response) {
					var images = JSON.parse(response);
					if (images.hasOwnProperty('error')) {
						target.html('<div>' + images.error + '.</div>');
					} else if (images.hasOwnProperty('nonoptimized') && images.nonoptimized.length > 0) {
						bulkTotalimages = images.nonoptimized.length;
						target.html('<div class="loading--bulk"><span class="loader"></span><br />' + bulkTotalimages + ' ' + reSmush.strings.attachments_found + '</div>');
						flag_removed = false;
						//start treating all pictures
						resmushit_bulk_process(images.nonoptimized, 0);
					} else {
						target.html('<div>' + reSmush.strings.no_attachments_found + '</div>');
					}
				}
			);
		});
}


/**
 * ajax post to update statistics
 */
function updateStatistics() {
	var csrf_token = jQuery('.rsmt-bulk').attr('data-csrf');
	jQuery.post(
		ajaxurl, {
			action: 'resmushit_update_statistics',
			csrf: csrf_token
		},
		function(response) {
			statistics = JSON.parse(response);
			jQuery('#rsmt-statistics-space-saved').text(statistics.total_saved_size_formatted);
			jQuery('#rsmt-statistics-files-optimized').text(statistics.files_optimized);
			jQuery('#rsmt-statistics-percent-reduction').text(statistics.percent_reduction);
			jQuery('#rsmt-statistics-total-optimizations').text(statistics.total_optimizations);
		}
	);
}


/**
 * ajax post to disabled status (or remove)
 */
function updateDisabledState() {
	jQuery(document).delegate(".rsmt-trigger--disabled-checkbox","change",function(e){
	    e.preventDefault();
		var current = this;
		jQuery(current).addClass('rsmt-disable-loader');
		jQuery(current).prop('disabled', true);
		var disabledState = jQuery(current).is(':checked');
		var postID = jQuery(current).attr('data-attachment-id');
		var csrfToken = jQuery(current).attr('data-csrf');

		jQuery.post(
			ajaxurl, {
				action: 'resmushit_update_disabled_state',
				data: {id: postID, disabled: disabledState, csrf: csrfToken}
			},
			function(response) {
				jQuery(current).removeClass('rsmt-disable-loader');
				jQuery(current).prop('disabled', false);

				if(jQuery(current).parent().hasClass('field')){
					var selector = jQuery(current).parent().parent().next('tr').find('td.field');
				} else {
					var selector = jQuery(current).parent().next('td');
				}

				if(disabledState == true){
					selector.empty().append('-');
				} else {
					selector.empty().append('<input type="button" value="Optimize" class="rsmt-trigger--optimize-attachment button media-button  select-mode-toggle-button" name="resmushit" data-attachment-id="' + postID + '" class="button wp-smush-send" />');
				}
				optimizeSingleAttachment();
			}
		);
	});
}



/**
 * ajax to Optimize a single picture
 */
function optimizeSingleAttachment() {
	jQuery(document).delegate(".rsmt-trigger--optimize-attachment","click",function(e){
	    e.preventDefault();
		var current = this;
		jQuery(current).val(reSmush.strings.optimizing);
		jQuery(current).prop('disabled', true);
		var disabledState = jQuery(current).is(':checked');
		var postID = jQuery(current).attr('data-attachment-id');
		var csrf_token = jQuery(current).attr('data-csrf');

		jQuery.post(
			ajaxurl, {
				action: 'resmushit_optimize_single_attachment',
				data: {id: postID, csrf: csrf_token}
			},
			function(response) {
				var statistics = JSON.parse(response);
				jQuery(current).parent().empty().append(reSmush.strings.reduced_by + ' ' + statistics.total_saved_size_nice + ' (' + statistics.percent_reduction + ' saved)');
			}
		);
	});
}

function restoreSingleAttachment()
{
	jQuery(document).on('click', ".rsmt-trigger--restore-attachment",function(e){
			e.preventDefault();
		var current = this;
		jQuery(current).val(reSmush.strings.restoring);
		jQuery(current).prop('disabled', true);
		var disabledState = jQuery(current).is(':checked');
		var postID = jQuery(current).attr('data-attachment-id');
		var csrf_token = jQuery(current).attr('data-csrf');

		jQuery.post(
			ajaxurl, {
				action: 'resmushit_restore_single_attachment',
				data: {id: postID, csrf: csrf_token}
			},
			function(response) {
			//	var statistics = JSON.parse(response);
			//	jQuery(current).parent().empty().append(reSmush.strings.reduced_by + ' ' + statistics.total_saved_size_nice + ' (' + statistics.percent_reduction + ' saved)');
				var message = 'Restored';
				if (response.message)
				{
						message = response.message;
				}

				jQuery(current).parent().empty().append(message);
			}
		);
	});
}

/**
 * ajax to Optimize a single picture
 */
function removeBackupFiles() {
	jQuery(document).delegate(".rsmt-trigger--remove-backup-files","click",function(e){
		if ( confirm( reSmush.strings.remove_backup_confirm ) ) {

		    e.preventDefault();
			var current = this;
			jQuery(current).val( reSmush.strings.removing_backups);
			jQuery(current).prop('disabled', true);
			var csrf_token = jQuery(current).attr('data-csrf');
			jQuery.post(
				ajaxurl, {
					action: 'resmushit_remove_backup_files',
					csrf: csrf_token
				},
				function(response) {
					var data = JSON.parse(response);
					jQuery(current).val(data.success + ' ' + reSmush.strings.backupfiles_removed);
					setTimeout(function(){ jQuery(current).parent().parent().slideUp() }, 3000);
				}
			);
		}
	});
}


/**
 * ajax to Optimize a single picture
 */
function restoreBackupFiles() {
	jQuery(document).delegate(".rsmt-trigger--restore-backup-files","click",function(e){
		if ( confirm( reSmush.strings.restore_all_confirm) ) {

		    e.preventDefault();
			var current = this;
			jQuery(current).val('Restoring backups...');
			jQuery(current).prop('disabled', true);
			var csrf_token = jQuery(current).attr('data-csrf');
			jQuery.post(
				ajaxurl, {
					action: 'resmushit_restore_backup_files',
					csrf: csrf_token
				},
				function(response) {
					var data = JSON.parse(response);
					jQuery(current).val(data.success + ' ' +  reSmush.strings.images_restored );
				}
			);
		}
	});
}
