<?php
add_action ( 'save_post', 'sh_admin_forum_save', 10, 2 );

function sh_admin_forum_save($id, $post) {
	if (defined ( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE) {
		return;
	}
	if (! current_user_can ( 'edit_post', $id )) {
		return;
	}
	
	if (array_key_exists ( 'post_type', $_POST )) {
		if ($_POST ['post_type'] == 'forum') {
			update_post_meta ( $id, 'sh_new_post_title', $_POST ['sh-forum-post-title'] );
			$require_subjects = array_key_exists ( 'sh-forum-post-require-subjects', $_POST ) && $_POST['sh-forum-post-require-subjects'] == true;
			update_post_meta ( $id, 'sh_new_post_requires_subjects', $require_subjects );
			
			if (array_key_exists('sh-forum-post-subjects', $_POST)) {
				$selections = array_keys($_POST['sh-forum-post-subjects']);
				update_post_meta($id, 'sh_forum_subject_areas', $selections);
			}
			
			if (array_key_exists('upload_image_id', $_POST)) {
				update_post_meta($id, 'sh_forum_icon', $_POST['upload_image_id'] );
			}
		}
	}
}

/**
 * Defines the features of the new post form for this forum.
 */
function sh_admin_forum_post_metaboxes() {
	include('forum-metabox.php');
}

function sh_admin_forum_icon_metaboxes() {
	global $post;

	$image_src = '';
	$image_id = get_post_meta( $post->ID, 'sh_forum_icon', true );
	$image_src = wp_get_attachment_url( $image_id );

	
	include('forum-icon.php');
}