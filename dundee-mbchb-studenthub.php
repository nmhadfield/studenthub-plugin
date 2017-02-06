<?php

/**
 * Plugin Name: Dundee MBChB StudentHub
 * Plugin URI: http://studenthub.dundee.ac.uk
 * Description: Functionality for StudentHub
 * Version: 1.0.0
 * Author: Natasha Hadfield
 * License: GPL2
 */
require_once ('admin/audit/audit.php');
require_once (ABSPATH . 'wp-content/plugins/buddypress/bp-forums/bbpress/bb-includes/functions.bb-topics.php');
require_once ("classes/topic-loop.php");

require_once ("admin/page/page-admin.php");
require_once ("admin/deadlines/deadlines-admin.php");
require_once ('admin/societies/societies-admin.php');
require_once ('admin/forum/forum-admin.php');
require_once ('admin/topic/topic-admin.php');

require_once ('widgets/search-resources-widget.php');
require_once ('widgets/category-filter-widget.php');
require_once ('widgets/deadlines-widget.php');
require_once ('widgets/societies-widget.php');
require_once ('widgets/committee-widget.php');
require_once ('widgets/peer-mentors-groups-widget.php');
require_once ('widgets/society-contact-widget.php');

register_activation_hook ( __FILE__, 'sh_activate' );
// remove this as it blocks us from ever accessing the admin pages given we can't have admin roles!
remove_filter ( 'map_meta_cap', '_bp_enforce_bp_moderate_cap_for_admins', 10, 4 );

add_action ( 'init', 'sh_init' );
add_action ( 'admin_init', 'sh_admin_init' );
add_action ( 'admin_menu', 'sh_admin_menu' );
add_action ( 'admin_enqueue_scripts', 'sh_admin_scripts' );
add_action ( 'wp_enqueue_scripts', 'sh_widget_scripts' );
add_action ( 'wp_ajax_studenthub_admin_import_elective', 'sh_admin_import_elective' );
add_action ( 'wp_ajax_studenthub_admin_test_email', 'sh_admin_test_email' );
add_action ( 'wp_ajax_wp_ulike_process', 'sh_log_wp_ulike_process');
add_action ( 'add_meta_boxes', 'sh_admin_metaboxes' );
add_action ( 'save_post', 'sh_admin_page_save', 10, 2 );
add_action ( 'save_post', 'sh_admin_societies_save', 10, 2 );
add_action ( 'save_post', 'sh_admin_deadlines_save', 10, 2 );
add_action('wp_mail_failed', 'sh_admin_error', 10, 1);

add_action ( 'bbp_new_topic', 'sh_save_topic', 10, 4 );
add_action ( 'bbp_new_reply', 'sh_save_reply', 10, 4 );

add_filter ( 'query_vars', 'studenthub_add_query_vars_filter' );
add_filter( 'wp_nav_menu_items', 'sh_logout_menu_link', 10, 2 );


function sh_logout_menu_link( $items, $args ) {
	if ($args->theme_location == 'fixed-menu' ) {
		$items .= '<li class="right"><a href="'. wp_logout_url() .'">'. __("Log Out") .'</a></li>';
	}
	return $items;
}
function studenthub_add_query_vars_filter($vars) {
	$vars [] = 'sh_scope';
	$vars [] = 'sh_action';
	$vars [] = 'sh_post_type';
	$vars [] = 'sh_part';
	return $vars;
}

add_action ( 'widgets_init', function () {
	$sidebars = get_option ( 'sh_sidebars', array () );
	foreach ( $sidebars as $sidebar ) {
		register_sidebar ( array (
				'id' => $sidebar,
				'name' => $sidebar 
		) );
	}
	register_widget ( 'search_resources_widget' );
	register_widget ( 'category_filter_widget' );
	register_widget ( 'deadlines_widget' );
	register_widget ( 'societies_widget' );
	register_widget ( 'comments_widget' );
	register_widget ( 'committee_widget' );
	register_widget ( 'peer_mentors_groups_widget' );
	register_widget ( 'favourite_widget' );
	register_widget ( 'category_logo_widget' );
	register_widget ( 'link_widget' );
	register_widget ( 'society_contact_widget' );
} );

add_filter ( 'template_include', 'sh_include_template', 99 );
function sh_include_template($template) {
	$new_template = locate_template ( $template );
	if ('' != $new_template) {
		return $new_template;
	}
	return $template;
}
function sh_init() {
	// make sure that all our custom post types are registered
	register_post_type ( 'societies', array (
			'labels' => array (
					'name' => __ ( 'Societies' ),
					'singular_name' => __ ( 'Society' ) 
			),
			'public' => true,
			'has_archive' => false 
	) );
	register_post_type ( 'sh_deadline', array (
			'labels' => array (
					'name' => __ ( 'Deadlines' ),
					'singular_name' => __ ( 'Deadline' ) 
			),
			'public' => true,
			'has_archive' => true 
	) );
	flush_rewrite_rules ( true );
	show_admin_bar ( true );
}
function sh_save_topic($topic_id, $forum_id, $anonymous_data, $topic_author) {
	// set the categories for the topic
	if (array_key_exists ( "studenthub-subject-select", $_POST )) {
		$categories = $_POST ["studenthub-subject-select"];
		$categoryIds = [ ];
		foreach ( $categories as $name ) {
			$categoryIds [$name] = get_cat_ID ( $name );
		}

		wp_set_object_terms ( $topic_id, $categoryIds, "category" );
	}

	// set the topic-type
	$type = get_term_by ( 'name', $_POST ["resource-type"], 'topic-type' );
	if ($type) {
		wp_set_object_terms ( $topic_id, $type->name, "topic-type" );
	}
	// save link as post metadata
	if (! empty ( $_POST ["studenthub-url"] )) {
		add_post_meta ( $topic_id, "link", $_POST ["studenthub-url"] );
	}
	
	if (array_key_exists('mbchbYearOfStudyInLatestAcademicYear', $_COOKIE)) {
		add_post_meta($topic_id, "sh_user_group", $_COOKIE['mbchbYearOfStudyInLatestAcademicYear']);
	}
}

function sh_save_reply($reply_id, $topic_id, $forum_id, $anonymous_data, $reply_author, $param, $reply_to ) {
	if (array_key_exists('mbchbYearOfStudyInLatestAcademicYear', $_COOKIE)) {
		add_post_meta($reply_id, "sh_user_group", $_COOKIE['mbchbYearOfStudyInLatestAcademicYear']);
	}
}

function sh_admin_init() {
	// make sure that StudentHubAdmin can access forums & groups
	$admin_role = get_role ( "student_hub_admin" );
	if ($admin_role == null) {
		$admin_role = add_role ( "student_hub_admin", "Student Hub Admin" );
	}
	
	$admin_role->add_cap ( 'moderate', true );
	$admin_role->add_cap ( 'bp_moderate', true );
	$admin_role->add_cap ( 'keep_gate', true );
	$admin_role->add_cap ( 'edit_forums', true );
	$admin_role->add_cap ( 'edit_topics', true );
	$admin_role->add_cap ( 'delete_others_topics', true );
	$admin_role->add_cap ( 'edit_others_forums', true );
	$admin_role->add_cap ( 'view_trash', true );
	$admin_role->add_cap ( 'publish_forums', true );
	$admin_role->add_cap ( 'delete_forums', true );
	$admin_role->add_cap ( 'delete_topics', true );
	$admin_role->add_cap ( 'edit_others_topics', true );
	$admin_role->add_cap ( 'publish_replies', true );
	$admin_role->add_cap ( 'delete_others_forums', true );
	$admin_role->add_cap ( 'edit_replies', true );
	$admin_role->add_cap ( 'publish_topics', true );
	$admin_role->add_cap ( 'delete_others_pages', true );
	$admin_role->add_cap ( 'delete_others_posts', true );
	$admin_role->add_cap ( 'delete_pages', true );
	$admin_role->add_cap ( 'delete_posts', true );
	$admin_role->add_cap ( 'delete_private_pages', true );
	$admin_role->add_cap ( 'delete_private_posts', true );
	$admin_role->add_cap ( 'delete_published_pages', true );
	$admin_role->add_cap ( 'delete_published_posts', true );
	$admin_role->add_cap ( 'edit_others_pages', true );
	$admin_role->add_cap ( 'edit_others_posts', true );
	$admin_role->add_cap ( 'edit_pages', true );
	$admin_role->add_cap ( 'edit_posts', true );
	$admin_role->add_cap ( 'edit_published_pages', true );
	$admin_role->add_cap ( 'edit_published_posts', true );
	$admin_role->add_cap ( 'manage_categories', true );
	$admin_role->add_cap ( 'publish_pages', true );
	$admin_role->add_cap ( 'publish_posts', true );
	$admin_role->add_cap ( 'manage_options', true );
	$admin_role->add_cap ( 'wpcf_custom_post_type_view', true );
	$admin_role->add_cap ( 'wpcf_custom_post_type_edit', true );
	$admin_role->add_cap ( 'wpcf_custom_post_type_edit_others', true );
	$admin_role->add_cap ( 'wpcf_custom_taxonomy_view', true );
	$admin_role->add_cap ( 'wpcf_custom_taxonomy_edit', true );
	$admin_role->add_cap ( 'wpcf_custom_taxonomy_edit_others', true );
	$admin_role->add_cap ( 'wpcf_custom_field_view', true );
	$admin_role->add_cap ( 'wpcf_custom_field_edit', true );
	$admin_role->add_cap ( 'wpcf_custom_field_edit_others', true );
	$admin_role->add_cap ( 'wpcf_user_meta_field_view', true );
	$admin_role->add_cap ( 'wpcf_user_meta_field_edit', true );
	$admin_role->add_cap ( 'wpcf_user_meta_field_edit_others', true );
	$admin_role->add_cap ( 'wpcf_user_meta_field_view', true );
	$admin_role->add_cap ( 'wpcf_user_meta_field_edit', true );
	$admin_role->add_cap ( 'wpcf_user_meta_field_edit_others', true );
}
function sh_admin_scripts() {
	wp_register_style ( 'studenthub_admin_styles', plugins_url ( 'style.css', __FILE__ ) );
	wp_enqueue_style ( 'studenthub_admin_styles' );
	
	wp_register_script ( 'studenthub_admin_electives', plugins_url ( 'admin/electives/electives.js', __FILE__ ) );
	wp_enqueue_script ( 'studenthub_admin_electives' );
	
	wp_register_script ( 'studenthub_admin_page', plugins_url ( 'admin/page/page.js', __FILE__ ) );
	wp_enqueue_script ( 'studenthub_admin_page' );
	
	wp_register_script ( 'studenthub_admin_deadlines', plugins_url ( 'admin/deadlines/deadlines.js', __FILE__ ) );
	wp_enqueue_script ( 'studenthub_admin_deadlines' );
	
	wp_localize_script ( 'ajax-feed', 'ajaxfeed', array (
			'ajaxurl' => admin_url ( 'admin-ajax.php' ) 
	) );
	
	wp_enqueue_script ( 'jquery-ui-datepicker' );
	wp_enqueue_script ( 'jquery-form' );
}
function sh_widget_scripts() {
	wp_enqueue_script ( 'sh_widgets', plugins_url ( 'widgets/scripts/widget.js', __FILE__ ) );
	wp_enqueue_script ( 'sh_societies', plugins_url ( 'admin/societies/societies-admin.js', __FILE__ ) );
}
function sh_admin_metaboxes() {
	add_meta_box ( "sh_admin_page_sidebar", "SideBar", "sh_admin_page_sidebar", "page", "side", "high" );
	add_meta_box ( "sh_admin_page_forum", "Forum", "sh_admin_page_forum", "page", "normal", "high" );
	add_meta_box ( "sh_admin_topic", "Topics", "sh_admin_topic_metabox", "page", "side", "high" );
	// add_meta_box ( "sh_admin_page_postform", "Post Form", "sh_admin_page_postform", "page", "normal", "high" );
	
	add_meta_box ( "sh_admin_page_forum", "Forum", "sh_admin_page_forum", "societies", "normal", "high" );
	
	// add_meta_box ( "sh_admin_forum_theme", "Theme", "sh_admin_forum_theme", "forum", "side", "high" );
	add_meta_box ( "sh_admin_forum_post", "New Post Form", "sh_admin_forum_post_metaboxes", "forum", "side", "high" );
	add_meta_box ( "sh_admin_topic_links", "Links", "sh_admin_topic_links", "topic", "normal", "high" );
	
	add_meta_box ( "sh_admin_deadlines_metaboxes", "Info", "sh_admin_deadlines_metaboxes", "sh_deadline", "side", "high", null );
}
function sh_admin_topic_links() {
	echo ("<ul>");
	$links = get_post_meta ( get_the_ID (), "link", false );
	foreach ( $links as $link ) {
		echo ("<li><a href='" . $link . "' target='_blank'>" . $link . "</a></li>");
	}
	echo ("</ul>");
}
function sh_admin_menu() {
	add_menu_page ( 'StudentHub', 'StudentHub', 'read', 'studenthub-plugin-settings', 'sh_settings_page_electives', 'dashicons-admin-generic' );
	add_submenu_page ( 'studenthub-plugin-settings', 'Import Electives', 'Import Electives', 'read', 'sh-settings-page-electives', 'sh_settings_page_electives' );
	add_submenu_page ( 'studenthub-plugin-settings', 'Societies', 'Societies', 'read', 'sh-settings-page-societies', 'sh_settings_page_societies' );
}
function sh_settings_page_electives() {
	include (plugin_dir_path ( __FILE__ ) . 'admin/electives/import-electives.php');
}
function sh_admin_create_forum($forumName, $parent = NULL) {
	$forum = get_page_by_title ( $forumName, OBJECT, "forum" );
	if ($forum == null) {
		$data = array (
				'post_title' => $forumName 
		);
		if ($parent) {
			$data ['post_parent'] = $parent;
		}
		return bbp_insert_forum ( $data );
	} else {
		return $forum->ID;
	}
}
function sh_admin_import_elective() {
	$parent = sh_admin_create_forum ( "Electives" );
	$entries = new SimpleXMLElement ( $_POST ['xml'] );
	
	foreach ( $entries->entry as $entry ) {
		$keywords = array ();
		foreach ( $entry->keywords->keyword as $cat ) {
			array_push ( $keywords, $cat );
		}
		
		$id = wp_insert_post ( array (
				'post_title' => $entry->title,
				'post_type' => 'topic',
				'post_status' => 'publish',
				'post_parent' => $parent 
		) );
		
		add_post_meta ( $id, '_bbp_forum_id', $parent );
		add_post_meta ( $id, 'link', '$poster' );
		add_post_meta ( $id, 'poster', trim ( $entry->poster ) );
		if (trim ( $entry->summary ) != "") {
			add_post_meta ( $id, 'summary', trim ( $entry->summary ) );
			add_post_meta ( $id, 'link', '$summary' );
		}
		
		wp_set_object_terms ( $id, $keywords, 'category', false );
	}
}

function sh_admin_test_email() {
	$result = wp_mail($_POST['email'], 'Test Email', 'Hello from the StudentHub!');
	if ($result) {
		error_log("woohoo");
	}
	else {
		error_log('rats');
	}
}

function sh_admin_error($error) {
	error_log($error -> get_error_message());
}

function sh_log_wp_ulike_process() {
	if (array_key_exists('type', $_POST) && $_POST['type'] == 'likeThisTopic') {
	bp_activity_add(array(
			'action' => 'Like a post',
			'component' => 'StudentHub',
			'type' => 'sh_wpulike',
			'item_id' => $POST['id']));
	}
}
