<img id="sh_forum_image" src="<?php echo $image_src ?>" style="max-width:100%;" />
<input type="hidden" name="upload_image_id" id="upload_image_id" value="<?php echo $image_id; ?>" />
<p>
	<a title="<?php esc_attr_e( 'Set icon' ) ?>" href="#" id="set-icon-image"><?php _e( 'Set icon' ) ?></a>
	<a title="<?php esc_attr_e( 'Remove icon' ) ?>" href="#" id="remove-icon-image" style="<?php echo ( ! $image_id ? 'display:none;' : '' ); ?>"><?php _e( 'Remove icon' ) ?></a>
</p>

<script type="text/javascript">
jQuery(document).ready(function($) {
	
	// save the send_to_editor handler function
	window.send_to_editor_default = window.send_to_editor;

	$('#set-icon-image').click(function(){
		
		// replace the default send_to_editor handler function with our own
		window.send_to_editor = window.attach_image;
		tb_show('', 'media-upload.php?post_id=<?php echo $post->ID ?>&amp;type=image&amp;TB_iframe=true');
		
		return false;
	});
	
	$('#remove-icon-image').click(function() {
		
		$('#upload_image_id').val('');
		$('img').attr('src', '');
		$(this).hide();
		
		return false;
	});
	
	// handler function which is invoked after the user selects an image from the gallery popup.
	// this function displays the image and sets the id so it can be persisted to the post meta
	window.attach_image = function(html) {
		
		// turn the returned image html into a hidden image element so we can easily pull the relevant attributes we need
		$('body').append('<div id="temp_image">' + html + '</div>');
			
		var img = $('#temp_image').find('img');
		
		imgurl   = img.attr('src');
		imgclass = img.attr('class');
		imgid    = parseInt(imgclass.replace(/\D/g, ''), 10);

		$('#upload_image_id').val(imgid);
		$('#remove-icon-image').show();

		$('img#book_image').attr('src', imgurl);
		try{tb_remove();}catch(e){};
		$('#temp_image').remove();
		
		// restore the send_to_editor handler function
		window.send_to_editor = window.send_to_editor_default;
		
	}

});
</script>
