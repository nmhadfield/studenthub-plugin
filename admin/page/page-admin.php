<?php
function sh_admin_page_forum() {
	echo('Show posts from the following forum(s):<br>');
	// saved as [[forumId, enable_post]]
	$forumIds = get_post_meta ( get_the_ID (), 'sh_forums', true );
	
	echo('<ul id="sh-forum-list">');
	$index = 0;
	if ($forumIds) {
		foreach ($forumIds as $forumInfo) {
			sh_admin_page_create_forum_entry($forumInfo, $index);
			$index++;
		}
	}
	if ($index == 0) {
		sh_admin_page_create_forum_entry(null, $index);
	}
	
	echo('</ul>');
	echo('<p>');
	echo('<input type="button" id="sh-add-another" value="Add Forum"></input>');
	echo('</p>');
}

function sh_admin_page_categories() {
	echo('Show posts from the following categories:<br>');
	$categories = get_post_meta(get_the_ID(), 'sh_categories',true);
	echo('<ul id="sh-categories">');
	$index = 0;
	foreach ($categories as $cat) {
		sh_admin_page_create_cat_entry($cat, $index);
		$index++;
	}
	if ($index == 0) {
		sh_admin_page_create_cat_entry(null, $index);
	}
	
	echo('</ul>');
	echo('<p>');
	echo('<input type="button" id="sh-add-another-cat" value="Add Category"></input>');
	echo('</p>');
}

function sh_admin_page_create_forum_entry($forumInfo, $index) {
	echo('<li>');
	$forumId = $forumInfo ? $forumInfo['id'] : '';
	bbp_dropdown ( array (
			'post_type' => bbp_get_forum_post_type (),
			'selected' => $forumId,
			'numberposts' => - 1,
			'orderby' => 'title',
			'order' => 'ASC',
			'walker' => '',
	
			// Output-related
			'select_id' => 'sh-forums['.$index.'][id]',
			'tab' => bbp_get_tab_index (),
			'options_only' => false,
			'show_none' => __ ( '&mdash; No parent &mdash;', 'bbpress' ),
			'disable_categories' => false,
			'disabled' => ''
	) );
	echo('<label>Enable posting in forum</label>');
	$checked = $forumInfo && array_key_exists('can_post', $forumInfo) && $forumInfo['can_post'] ? ' checked' : '';
	echo('<input type="checkbox" name="sh-forums['.$index.'][can_post]"'.$checked.'></input>');
	echo('</li>');
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
		if ($sidebar == $current || $current == null) {
			echo (" selected='selected'");
		}
		echo (">");
		if (array_key_exists($sidebar, $GLOBALS['wp_registered_sidebars'])) {
			echo ($GLOBALS['wp_registered_sidebars'][$sidebar]['name']);
		}
		else {
			echo(get_the_title());
		}
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
			$forums = $_POST['sh-forums'];
			$result = array();
			foreach ($forums as $entry) {
				if ($entry['id'] != '') {
					$result[] = $entry;
				}
			}
			update_post_meta ( $id, 'sh_forums', $result);
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
