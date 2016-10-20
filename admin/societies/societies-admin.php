<?php

add_action("add_meta_boxes", "sh_societies_admin_metaboxes");
add_action('admin_enqueue_scripts', 'societies_admin_js' );
add_action('wp_ajax_studenthub_find_user', 'sh_find_user_by_email');
add_action('wp_ajax_studenthub_add_user_to_committee', 'sh_add_user_to_committee');

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