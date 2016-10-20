<?php
function sh_admin_page_forum() {
	$forumId = get_post_meta ( get_the_ID (), 'sh_parent', true );
	$forum = "";
	if ($forumId != "") {
		$forum = get_post ( $forumId )->post_title;
	}
	
	bbp_dropdown ( array (
			'post_type' => bbp_get_forum_post_type (),
			'selected' => $forumId,
			'numberposts' => - 1,
			'orderby' => 'title',
			'order' => 'ASC',
			'walker' => '',
			
			// Output-related
			'select_id' => 'sh-parent',
			'tab' => bbp_get_tab_index (),
			'options_only' => false,
			'show_none' => __ ( '&mdash; No parent &mdash;', 'bbpress' ),
			'disable_categories' => false,
			'disabled' => '' 
	) );
}
function sh_admin_page_sidebar() {
	echo ("<select id='sh_sidebar' name='sh_sidebar'>");
	$sidebars = array_keys ( $GLOBALS ['wp_registered_sidebars'] );
	
	$current = get_post_meta ( get_the_ID (), 'sh_sidebar', true );
	$page_sidebar = 'page-' . get_the_ID () . '-sidebar';
	if (! array_key_exists ( $page_sidebar, $sidebars )) {
		array_push ( $sidebars, $page_sidebar );
	}
	foreach ( $sidebars as $sidebar ) {
		echo ("<option value='" . $sidebar . "'");
		if ($sidebar == $current) {
			echo (" selected='selected'");
		}
		echo (">");
		echo ($sidebar);
		echo ("</option>");
	}
	echo ("</select>");
}
function sh_admin_page_save($id, $post) {
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
