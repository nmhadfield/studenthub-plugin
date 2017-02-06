<?php
add_action( 'wp_insert_post', 'sh_audit_insert_post', 10, 3 );

function sh_audit_insert_post($postId, $post, $update) {
	if (wp_is_post_revision($post)) {
		return;
	}
	$year = array_key_exists('mbchbYearOfStudyInLatestAcademicYear', $_COOKIE) ? $_COOKIE['mbchbYearOfStudyInLatestAcademicYear'] : "";
	
}