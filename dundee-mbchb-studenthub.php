<?php

/**
 * Plugin Name: Dundee MBChB StudentHub
 * Plugin URI: http://studenthub.dundee.ac.uk
 * Description: Functionality for StudentHub
 * Version: 1.0.0
 * Author: Natasha Hadfield
 * License: GPL2
 */
require_once (ABSPATH . 'wp-content/plugins/buddypress/bp-forums/bbpress/bb-includes/functions.bb-topics.php');
require_once ("classes/topic-loop.php");

require_once ("admin/page/page-admin.php");
require_once ("admin/deadlines/deadlines-admin.php");
require_once ('admin/societies/societies-admin.php');
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
add_action ( 'admin_menu', 'studenthub_admin_menu' );
add_action ( 'admin_enqueue_scripts', 'studenthub_admin_scripts' );
add_action ( 'wp_enqueue_scripts', 'sh_widget_scripts' );
add_action ( 'wp_ajax_studenthub_admin_import_elective', 'studenthub_admin_import_elective' );
add_action ( "add_meta_boxes", "studenthub_admin_metaboxes" );
add_action ( "save_post", "sh_admin_page_save", 10, 2 );
add_action ( "save_post", "sh_admin_deadlines_save", 10, 2 );

add_action ( 'widgets_init', function () {
	$sidebars = get_option ( 'sh_sidebars', array () );
	foreach ( $sidebars as $sidebar ) {
		register_sidebar ( array (
				'id' => $sidebar,
				'name' => $sidebar 
		) );
	}
	register_sidebar ( array (
			'id' => 'home-sidebar',
			'name' => "Home Page" 
	) );
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
function sh_activate() {
	// make sure that StudentHubAdmin can access forums & groups
	$admin_role = get_role ( "student_hub_admin" );
	if ($admin_role == null) {
		$admin_role = add_role ( "student_hub_admin", "Student Hub Admin" );
	}
	
	$admin_role->add_cap ( 'moderate', true );
	$admin_role->add_cap ( 'bp_moderate', true );
	$admin_role->add_cap ( 'keep_gate', true );
}
function sh_init() {
	// make sure that all our custom post types are registered
	if (! post_type_exists ( "societies" )) {
		register_post_type ( 'societies', array (
				'labels' => array (
						'name' => __ ( 'Societies' ),
						'singular_name' => __ ( 'Society' ) 
				),
				'public' => true,
				'has_archive' => true 
		) );
	}
	if (! post_type_exists ( "sh_deadline" )) {
		register_post_type ( 'sh_deadline', array (
				'labels' => array (
						'name' => __ ( 'Deadlines' ),
						'singular_name' => __ ( 'Deadline' ) 
				),
				'public' => true,
				'has_archive' => true 
		) );
	}
	flush_rewrite_rules ( true );
}

function studenthub_admin_scripts() {
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
}
function sh_widget_scripts() {
	wp_register_script ( 'sh_widgets', plugins_url ( 'widgets/scripts/widget.js', __FILE__ ) );
	wp_enqueue_script ( 'sh_widgets' );
}
function studenthub_admin_metaboxes() {
	add_meta_box ( "sh_admin_topic_links", "Links", "sh_admin_topic_links", "topic", "normal", "high" );
	add_meta_box ( "sh_admin_page_sidebar", "SideBar", "sh_admin_page_sidebar", "page", "side", "high" );
	add_meta_box ( "sh_admin_page_forum", "Forum", "sh_admin_page_forum", "page", "normal", "high" );
	add_meta_box ( "sh_admin_page_forum", "Forum", "sh_admin_page_forum", "societies", "normal", "high" );
	add_meta_box ( "sh_admin_topic", "Topics", "sh_admin_topic_metabox", "page", "side", "high" );
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
function studenthub_admin_menu() {
	add_menu_page ( 'StudentHub', 'StudentHub', 'read', 'studenthub-plugin-settings', 'studenthub_settings_page_electives', 'dashicons-admin-generic' );
}
function studenthub_settings_page_electives() {
	include (plugin_dir_path ( __FILE__ ) . 'admin/electives/import-electives.php');
}
function studenthub_admin_import_elective() {
	$parent = get_page_by_title ( "Electives", OBJECT, 'forum' )->ID;
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
