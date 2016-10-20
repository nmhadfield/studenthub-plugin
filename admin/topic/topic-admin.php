<?php 
function sh_admin_topic_metabox() {
	
}

function sh_admin_topic_save($id, $post) {
	if (defined ( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE) {
		return;
	}
	if (! current_user_can ( 'edit_post', $id )) {
		return;
	}
	if (array_key_exists('post_type', $_POST)) {
		if ($_POST ['post_type'] == 'page' || $_POST ['post_type'] == 'societies') {
			update_post_meta ( $id, 'sh_parent', $_POST ['sh-parent'] );
		}
		
		if ($_POST ['post_type'] == 'page') {
			if (array_key_exists('sh_sidebar', $_POST)) {
				update_post_meta ( $id, 'sh_sidebar', $_POST ['sh_sidebar'] );
			
				if (! array_key_exists ( $_POST ['sh_sidebar'], $GLOBALS ['wp_registered_sidebars'] )) {
					$sidebars = get_option ( 'sh_sidebars', array () );
					array_push ( $sidebars, $_POST ['sh_sidebar'] );
					update_option ( 'sh_sidebars', $sidebars );
				}
			}
		}
	}
}