<?php
add_action('init', 'sh_societies_init');
add_action ( 'admin_init', 'sh_societies_admin_settings' );
add_action ( "add_meta_boxes", "sh_societies_admin_metaboxes" );
add_action ( 'admin_enqueue_scripts', 'societies_admin_js' );
add_action ( 'wp_ajax_studenthub_find_user', 'sh_find_user_by_email' );
add_action ( 'wp_ajax_studenthub_add_user_to_committee', 'sh_add_user_to_committee' );
add_action ( 'wp_ajax_studenthub_register_society', 'sh_admin_register_society' );

function sh_societies_init() {
	$GLOBALS ['sh_societies_roles'] = array (
			'President',
			'Co-President',
			'Vice President',
			'Secretary',
			'Treasurer',
			'1st Year Rep',
			'2nd Year Rep',
			'3rd Year Rep',
			'4th Year Rep',
			'5th Year Rep',
			'BMSc Rep',
			'General Member',
			'Events Co-ordinator'
	);
}
function sh_societies_admin_settings() {
	register_setting ( 'sh_societies_options', 'sh_societies_contact_methods' );
	register_setting ( 'sh_societies_options', 'sh_societies_roles[]' );
}
function sh_settings_page_societies() {
	include ('societies-settings.php');
}
function societies_admin_js() {
	wp_register_script ( 'studenthub-societies-admin', plugins_url ( 'societies-admin.js', __FILE__ ) );
	wp_enqueue_script ( 'studenthub-societies-admin' );
}
function sh_societies_admin_metaboxes() {
	add_meta_box ( "sh_societies_contacts_metabox", "Contacts", "sh_societies_contacts_metabox", "societies", "normal", "high", null );
	add_meta_box ( "sh_societies_committee_metabox", "Committee Members", "sh_societies_committee_metabox", "societies", "normal", "high", null );
}
function sh_societies_contacts_metabox() {
	include ("societies-contacts.php");
}
function sh_societies_committee_metabox() {
	include ('societies-committee.php');
}
function sh_societies_add_committee_member() {
	$login = $_GET ['sh_login'];
	
	// $user = get_user_by('login', $_GET['sh_login']);
	
	if ($user != null) {
	}
	
	echo ('<input type="hidden" id="sh-userid" value="' . $user->ID . '"></input>');
	echo ('<label>' . $user->display_name . '</label>');
	echo ('<label>Role:</label>');
	echo ('<input type="text" id="sh-role"></input>');
	echo ('<button type="button" id="sh-add">Add to Committee</button>');
	
	die ();
}
function sh_add_user_to_committee() {
	add_post_meta ( $_POST ['sh_society'], 'sh_committee', $_GET ['sh_user'] );
	add_post_meta ( $_GET ['sh_society'], 'sh_role' . $_GET ['sh_user'], $_GET ['sh_role'] );
	
	include ('societies-committee.php');
	die ();
}
function sh_can_edit_committee($societyId) {
	if (sh_user_is_admin ()) {
		return true;
	}
	$committee = get_post_meta ( $societyId, 'committee', false );
	return in_array ( get_current_user_id (), $committee );
}
function sh_admin_societies_save($id, $post) {
	if (defined ( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE) {
		return;
	}
	if (! current_user_can ( 'edit_post', $id )) {
		return;
	}
	
	$type = array_key_exists ( 'post_type', $_POST ) ? $_POST ['post_type'] : $post->post_type;
	if ($type == 'societies') {
		
		if (array_key_exists ( 'sh-admin-societies-email', $_POST )) {
			update_post_meta ( $id, 'sh_email', $_POST ['sh-admin-societies-email'] );
		}
		if (array_key_exists ( 'sh-admin-societies-facebook', $_POST )) {
			update_post_meta ( $id, 'sh_facebook', $_POST ['sh-admin-societies-facebook'] );
		}
		if (array_key_exists ( 'sh-admin-societies-twitter', $_POST )) {
			update_post_meta ( $id, 'sh_twitter', $_POST ['sh-admin-societies-twitter'] );
		}
	}
}
function sh_admin_register_society() {
	
	// create the post for the society
	$title = $_POST ['sh_register_society_name'];
	$desc = $_POST ['sh_register_society_desc'];
	$args = array (
			'post_title' => $title,
			'post_content' => $desc,
			'post_type' => 'societies' 
	);
	$post = wp_insert_post ( $args );
	
	// create the forum for the society
	$forum = sh_admin_create_forum ( $title, get_page_by_title ( 'Societies', OBJECT, "forum" ) );
	add_post_meta ( $post, 'sh_parent', $forum );
	
	// add contact methods as metadata
	add_post_meta ( $post, 'sh_email', $_POST ['sh_register_society_email'] );
	add_post_meta ( $post, 'sh_facebook', $_POST ['sh_register_society_fb'] );
	add_post_meta ( $post, 'sh_twitter', $_POST ['sh_register_society_twitter'] );
	
	// register the creator as a committee member
	$role = $_POST ['sh_register_society_role'];
	add_post_meta ( $post, 'sh_committee', array (
			'role' => $role,
			'id' => get_current_user_id () 
	) );
	
	wp_update_post ( array (
			'ID' => $post,
			'post_status' => 'publish' 
	) );
	
	wp_redirect(get_permalink($post));
	die ();
}