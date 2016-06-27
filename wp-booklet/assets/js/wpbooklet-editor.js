jQuery(document).ready( function() {
		
	/* Set vars */
	var newPage = '<div class="wp-booklet-portlet">' +
						'<div class="wp-booklet-portlet-header">' +
							'Page' +
							'<span class="wp-booklet-portlet-header-buttons">' +
								'<span class="wp-booklet-header-visibility"></span>' +
								'<span class="wp-booklet-header-remove"></span>' +
							'</span>' +
						'</div>' +
						'<div class="wp-booklet-portlet-content">' +
							'<div class="wp-booklet-portlet-content-left">' +
								'<div class="wp-booklet-page-placeholder"></div>' +
								'<input class="wp-booklet-attachment-id" name="wp-booklet-attachment[]" type="hidden"/>' +
								'<input class="button-secondary wp-booklet-image-upload" type="button" value="Upload image"/>' +
							'</div>' +
							'<div class="wp-booklet-portlet-content-right">' +
								'<p>' +
									'<label>Page Link</label><br/>' +
									'<input class="widefat" type="text" value="" name="wp-booklet-attachment-properties[wp-booklet-page-link][]"/>' +
								'</p>' +
							'</div>' +
							'<div class="clearfix"></div>' +
						'</div>' +
					'</div>';
					
	var sortable = jQuery(".wp-booklet-sortable");
	var postId = sortable.attr("data-post-id");
	var pdfLimit = sortable.attr("data-pdf-limit");
	
	/* Sortable */
	sortable.sortable();
	
	jQuery("body").on('click','.wp-booklet-sortable .wp-booklet-portlet-header', function(e){
		jQuery(e.currentTarget).parent().toggleClass("wp-booklet-portlet-hidden");
	});
	
	jQuery("body").on('click','.wp-booklet-sortable .wp-booklet-header-remove', function(e) {
		jQuery(e.currentTarget).parents('.wp-booklet-portlet').remove();
	});
	
	/* Add pages */
	
	var pages_uploader = wp.media({
		'title':'Choose images',
		'button':{
			'text':'Choose images'
		},
		'library':{
			'type':'image'
		},
		'multiple':true
	});
	
	pages_uploader.on('select', function() {
		attachments = pages_uploader.state().get('selection').toJSON();
		for( var ctr = 0; ctr < attachments.length; ctr++ ) {
			jQuery(".wp-booklet-sortable").append(newPage);
			var currPage = jQuery(".wp-booklet-sortable .wp-booklet-portlet").last();
			console.log(attachments[ctr]);
			currPage.find('.wp-booklet-portlet-content-left').prepend('<img data-height='+attachments[ctr].height+' data-width='+attachments[ctr].width+' src='+attachments[ctr].url+' class="wp-booklet-img"/> ');
			currPage.find('.wp-booklet-attachment-id').val(attachments[ctr].id);
			currPage.find('.wp-booklet-image-upload').val('Replace image');
			currPage.find('.wp-booklet-page-placeholder').remove();
		}
	});
	
	jQuery(".wp-booklet-sortable-add-pages").on('click', function(e) {
		e.preventDefault();
		
		if ( pages_uploader ) {
			pages_uploader.open();
			return;
		}
		
		pages_uploader.open();
	});
	
	/* PDF upload */
	
	var pdf_uploader = wp.media({
		'title':'Choose PDF',
		'button': {
			text:'Choose PDF'
		},
		multiple:false
	});
	
	pdf_uploader.on('select', function() {
		attachment = pdf_uploader.state().get('selection').first().toJSON();
		processPDF(attachment);
	});
	
	jQuery(".wp-booklet-sortable-upload-pdf").on("click",function(e) {
		e.preventDefault();
		
		pdf_uploader.open();
		
	});	
	
	function showPreloader(text) {
		updatePreloader(text);
		jQuery(".wp-booklet-preloader-overlay").show().animate({opacity:0.8},200);
	}
	function updatePreloader(text) {
		jQuery(".wp-booklet-preloader-overlay .wp-booklet-preloader-note").html(text);
	}	
	function hidePreloader() {
		jQuery(".wp-booklet-preloader-overlay").animate({opacity:0},200).hide();
	}
	
	function processPDF(attachment) {
		
		showPreloader('Validating PDF. Please keep this window open');
		
		var data = {
			'pdf_id':attachment.id,
			'action':'verify_pdf'
		}
		
		jQuery.post(ajaxurl,data,function(verification) {
			if ( verification.wpb_success ) {
				showPreloader('Processing PDF. Please keep this window open.');
				var data = {
					'pdf_id':attachment.id,
					'post_id':postId,
					'action':'process_pdf'
				}
				var pageCount = verification.wpb_page_count;
				var pageOffset = -1;
				
				function processPage() {
					
					pageOffset = pageOffset + 1;
					data.pdf_offset = pageOffset;
					
					updatePreloader('Processing page ' + (pageOffset + 1) + '/' + pageCount + '. Please keep this window open.');

					jQuery.post(ajaxurl,data,function(response) {
						if ( response.wpb_success ) {
							for( var ctr = 0; ctr < response.images.length; ctr++ ) {
								jQuery(".wp-booklet-sortable").append(newPage);
								var currPage = jQuery(".wp-booklet-sortable .wp-booklet-portlet").last();
								currPage.find('.wp-booklet-portlet-content-left').prepend('<img data-width='+response.images[ctr].width+' data-height='+response.images[ctr].height+' src='+response.images[ctr].src+' class="wp-booklet-img"/> ');
								currPage.find('.wp-booklet-attachment-id').val(response.images[ctr].id);
								currPage.find('.wp-booklet-image-upload').val('Replace image');
								currPage.find('.wp-booklet-page-placeholder').remove();
							}
							
							setTimeout(processPage,500);
							
							if (pageOffset == ( pageCount - 1 ) ) {
								hidePreloader();
							}
								
						}
						else {
							alert(response.wpb_message);
							hidePreloader();
						}
						
					},'json');

				}

				processPage();
				
			}
			else {
				hidePreloader();
				alert(verification.wpb_message);
			}
		},'json');
	}
	
	/* Replace image */
	
	var current_page;
	var current_page_frame;
	
	jQuery("body").on("click",".wp-booklet-image-upload",function(e) {
		e.preventDefault();
		
		current_page = jQuery(e.currentTarget).parents(".wp-booklet-portlet-content");
		
		if ( current_page_frame ) {
			current_page_frame.open();
			return;
		};
		
		current_page_frame = wp.media({
			multiple: false,
			title: 'Select image',
			library: {
				type:'image'
			},
			button: {
				text:'Use image'
			}
		});
		
		current_page_frame.on('select',function() {
			var media_attachment;
			
			media_attachment = current_page_frame.state().get('selection').first().toJSON();
			
			if( current_page.find(".wp-booklet-img").length > 0 ) {
				
				var img = current_page.find('.wp-booklet-img');
				
				img.attr('src',media_attachment.url);
				img.attr('data-width',media_attachment.width);
				img.attr('data-height',media_attachment.height);
				
			}
			else {
				current_page.find('.wp-booklet-portlet-content-left').prepend('<img data-width='+media_attachment.width+' data-height='+media_attachment.height+' src='+media_attachment.url+' class="wp-booklet-img"/> ');
			}
			
			current_page.find('.wp-booklet-attachment-id').val(media_attachment.id);
			current_page.find('.wp-booklet-image-upload').val('Replace image');
			current_page.find('.wp-booklet-page-placeholder').remove();
		});
		
		current_page_frame.open();
	});
	
	/* Initialize auto-calculate buttons */
	
	jQuery(".wp-booklet-property-calculate-width").click( function(e) {
		
		e.preventDefault();
		
		var optimalWidth = 600;
		var highestProportion = 0;
		
		jQuery(".wp-booklet-img").each( function(i,v) {
			
			var width = parseInt( jQuery(v).attr("data-width") );
			var height = parseInt( jQuery(v).attr("data-height") );
			
			var imgProportion = height / width;
			
			if ( imgProportion > highestProportion ) {
				highestProportion = imgProportion;
				optimalWidth = width;
			}
			
		});
		
		jQuery("input[name='wp-booklet-metas[wp-booklet-width]']").val(optimalWidth);
		
	});
	
	jQuery(".wp-booklet-property-calculate-height").click( function(e) {
		
		e.preventDefault();
		
		var optimalHeight = 400;
		var highestProportion = 0;
		
		jQuery(".wp-booklet-img").each( function(i,v) {
			
			var width = parseInt( jQuery(v).attr("data-width") );
			var height = parseInt( jQuery(v).attr("data-height") );
			
			var imgProportion = height / width;
			
			
			console.log(imgProportion + '>' + highestProportion);
			if ( imgProportion > highestProportion ) {
				highestProportion = imgProportion;
				optimalHeight = height;
			}
			
		});
		
		jQuery("input[name='wp-booklet-metas[wp-booklet-height]']").val(optimalHeight);
		
	});
	
	/* Shortcode auto-select */
	
	jQuery(".wp-booklet-shortcode-display").click( function(e) {
		jQuery(e.currentTarget).select();
	});
	
});