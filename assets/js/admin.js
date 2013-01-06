jQuery(document).ready(function($) {
	
	$('#sb_upload_file').click(function() {
		tb_show( 'Upload an image' , 'media-upload.php?referer=seriouslysimple-file&type=image&TB_iframe=true&post_id=0' , false );
		return false;
	});

	$('#sb_reset_image').click(function() {
		var default_image = $( '#sb_default_image' ).val();
		$( '#ss_spamblocker_image' ).val( default_image );
		$( '#ss_spamblocker_image_preview' ).attr( 'src' , default_image );
		return false;
	});

	if( jQuery( '#sb_upload_file' ).length > 0 ) {
		window.send_to_editor = function(html) {
			var file_url = jQuery( html ).attr( 'href' );
			jQuery( '#ss_spamblocker_image' ).val( file_url );
			jQuery( '#ss_spamblocker_image_preview' ).attr( 'src' , file_url );
			tb_remove();
		}
	}

});