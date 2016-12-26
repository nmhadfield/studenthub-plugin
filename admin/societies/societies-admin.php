<?php

add_action("add_meta_boxes", "sh_societies_admin_metaboxes");
add_action('admin_enqueue_scripts', 'societies_admin_js' );
add_action('wp_ajax_studenthub_find_user', 'sh_find_user_by_email');
add_action('wp_ajax_studenthub_add_user_to_committee', 'sh_add_user_to_committee');
add_action('wp_ajax_studenthub_register_society', 'sh_admin_register_society');

function societies_admin_js() {
	wp_register_script ( 'studenthub-societies-admin', plugins_url('societies-admin.js', __FILE__));
	wp_enqueue_script ( 'studenthub-societies-admin' );
}

function sh_societies_admin_metaboxes() {
	add_meta_box("sh_societies_committee_metabox", "Committee Members", "sh_societies_committee_metabox", "societies", "normal", "high", null);
}

function sh_societies_committee_metabox() {
	include('societies-committee.php');
}

function sh_societies_add_committee_member() {
	$login = $_GET['sh_login'];
	
	//$user = get_user_by('login', $_GET['sh_login']);
	
	if ($user != null) {
		
	}

	echo('<input type="hidden" id="sh-userid" value="'.$user->ID.'"></input>');
	echo('<label>'.$user-> display_name.'</label>');
	echo('<label>Role:</label>');
	echo('<input type="text" id="sh-role"></input>');
	echo('<button type="button" id="sh-add">Add to Committee</button>');
	
	die();
}

function sh_add_user_to_committee() {
	add_post_meta($_POST['sh_society'], 'sh_committee', $_GET['sh_user']);
	add_post_meta($_GET['sh_society'], 'sh_role'.$_GET['sh_user'], $_GET['sh_role']);
	
	include('societies-committee.php');
	die();
}

function sh_can_edit_committee($societyId) {
	if (sh_user_is_admin()) {
		return true;
	}
	$committee = get_post_meta($societyId, 'committee', false);
	return in_array(get_current_user_id(), $committee);
}

function sh_admin_register_society() {
	
	// create the post for the society
	$title = $_POST['sh_register_society_name'];
	$desc = $_POST['sh_register_society_desc'];
	$args = array('post_title' => $title, 'post_content' => $desc, 'post_type' => 'societies');
	$post = wp_insert_post($args);
	
	// create the forum for the society
	$forum = sh_admin_create_forum($title, get_page_by_title( 'Societies', OBJECT, "forum" ));
	add_post_meta($post, 'sh_parent', $forum);
	
	// add contact methods as metadata
	add_post_meta($post, 'sh_email', $_POST['sh_register_society_email']);
	add_post_meta($post, 'sh_facebook', $_POST['sh_register_society_fb']);
	add_post_meta($post, 'sh_twitter', $_POST['sh_register_society_twitter']);
		
	// register the creator as a committee member
	$role = $_POST['sh_register_society_role'];
	add_post_meta($post, 'sh_committee', array('role' => $role, 'id' => get_current_user_id()));
	
	wp_update_post(array('ID' => $post, 'post_status' => 'publish'));
	
	die();
}