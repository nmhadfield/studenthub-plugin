<?php

/**
 * Plugin Name: Dundee MBChB StudentHub
 * Plugin URI: http://studenthub.dundee.ac.uk
 * Description: Functionality for StudentHub
 * Version: 1.0.0
 * Author: Natasha Hadfield
 * License: GPL2
 */
require_once(ABSPATH.'wp-content/plugins/buddypress/bp-forums/bbpress/bb-includes/functions.bb-topics.php');
require_once('admin/societies/societies-admin.php');

add_action('admin_menu', 'studenthub_admin_menu');
add_action('admin_enqueue_scripts', 'studenthub_admin_scripts');
add_action('wp_ajax_studenthub_admin_import_elective', 'studenthub_admin_import_elective');
add_action('init', 'studenthub_init');
add_action("add_meta_boxes", "studenthub_admin_metaboxes");

function studenthub_init() {
	$admin_role = get_role("student_hub_admin"); 
	if ($admin_role == null) {
		$admin_role = add_role("student_hub_admin", "Student Hub Admin");
	}
	
	if (!post_type_exists("sh_elective")) {
		register_post_type(
				'sh_elective',
				array('labels' => array('name' => __('Draft Electives'), 'singular_name' => __('Draft Elective')),'public' => true,'has_archive' => true,));
		flush_rewrite_rules(true);
	}
	if (!post_type_exists("societies")) {
		register_post_type(
				'societies',
				array('labels' => array('name' => __('Societies'), 'singular_name' => __('Society')),'public' => true,'has_archive' => true,));
		flush_rewrite_rules(false);
	}
}

function studenthub_admin_scripts() {
	wp_register_style ('studenthub_admin_styles', plugins_url('style.css', __FILE__));
	wp_enqueue_style ('studenthub_admin_styles');
	
	wp_register_script ('studenthub_admin_electives', plugins_url('admin/electives/electives.js', __FILE__));
	wp_enqueue_script ('studenthub_admin_electives');
	
	wp_localize_script('ajax-feed', 'ajaxfeed', array(
			'ajaxurl' => admin_url('admin-ajax.php')
	));
}

function studenthub_admin_metaboxes() {
	add_meta_box("sh_admin_topic_links", "Links", "sh_admin_topic_links", "topic", "normal", "high");
}

function sh_admin_topic_links() {
	echo("<ul>");
	$links = get_post_meta(get_the_ID(), "link", false); 
	foreach ($links as $link) {
		echo("<li><a href='".$link."' target='_blank'>".$link."</a></li>");
	}
	echo("</ul>");
}

function studenthub_admin_menu() {
	add_menu_page('StudentHub', 'StudentHub', 'administrator', 'studenthub-plugin-settings', 'studenthub_settings_page', 'dashicons-admin-generic', 1);
	add_submenu_page("studenthub-plugin-settings", "Import Electives", "Import Electives", "administrator", 'studenthub-plugin-settings-electives',"studenthub_settings_page_electives");
}

function studenthub_settings_page() {
	
}

function studenthub_settings_page_electives() {
	include(plugin_dir_path(__FILE__).'admin/electives/import-electives.php');
}

function studenthub_admin_import_elective() {
	$id = wp_insert_post(array(
						'post_title' => $_POST['student'],
						'post_type' => 'topic',
						'post_status' => 'pending',
	));
	add_post_meta($id, '_bbp_forum_id', get_page_by_title("Electives", OBJECT, 'forum')->ID);
	add_post_meta($id, 'link', $_POST['poster']);
	if ($_POST['summary'] != "") {
		add_post_meta($id, 'link', $_POST['summary']);
	}
	
	foreach ($_POST['keywords'] as $value) {
		add_post_meta($id, 'sh_keyword', $value);
	}
}
