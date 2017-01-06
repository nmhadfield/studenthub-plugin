<?php
add_action ( 'save_post', 'sh_admin_forum_save', 10, 2 );
function sh_admin_forum_theme() {
}
function sh_admin_forum_theme_save() {
}
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
			if (array_key_exists ( 'sh-forum-post-require-subjects', $_POST )) {
				update_post_meta ( $id, 'sh_new_post_requires_subjects', true );
			} 
			else {
				update_post_meta ( $id, 'sh_new_post_requires_subjects', false );
			}
		}
	}
}

/**
 * Defines the features of the new post form for this forum.
 */
function sh_admin_forum_post_metaboxes() {
	?>
<p>
	<label>Post Title</label> <input name='sh-forum-post-title'
		value='<?php echo(get_post_meta(get_the_ID(), 'sh_new_post_title', true)); ?>'></input>
</p>
<?php $checked = get_post_meta(get_the_ID(), 'sh_new_post_requires_subjects', true) ? 'checked': ''?>
<input type='checkbox' name='sh-forum-post-require-subjects'
	<?php echo($checked); ?>>
Require Subjects
</input>
<?php
}