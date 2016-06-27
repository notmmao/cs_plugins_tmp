jQuery(document).ready( function() {

	jQuery("#wp-booklet2-import-form").submit( function(e) {
		
		e.preventDefault();
		
		do_import();
		
		
	});
	
	function do_import() {
		
		var data = {
			'action':'import_booklets'
		};
		
		jQuery.post(ajaxurl,data,function(result) {
			
			jQuery(".remaining-count").text( result.remaining );
			
			if ( parseInt( result.remaining ) > 0 ) {
				setTimeout( function() {
					do_import();
				},1);
			}
			else {
				jQuery(".importer-page-message.success").show();
			}
			
		},'json');
		
	}
	
});