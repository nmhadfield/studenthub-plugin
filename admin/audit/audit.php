<?php
if (substr ( $_SERVER ['REQUEST_URI'], - 9 ) == 'audit.csv') {
	add_action ( 'template_redirect', 'sh_settings_page_statistics' );
	add_filter ( 'wp_headers', 'sh_settings_page_statistics_headers' );
}
function sh_settings_page_statistics_headers($headers) {
	status_header(200);
	$headers["Cache-Control"] = "public";
	$headers["Content-Type"] = "text/plain";
	$headers["Content-Transfer-Encoding"] = "binary";
	$headers["Pragma"] = "no-cache";
	return $headers;
}
function sh_settings_page_statistics() {
	$result = bp_activity_get ( array () );
	foreach ( $result ['activities'] as $activity ) {
		echo ($activity->type . ';');
		if (property_exists ( $activity, 'user_login' )) {
			echo ($activity->user_login);
		}
		echo (';' . $activity->item_id . ';');
		echo ($activity->secondary_item_id . ';');
		echo ($activity->date_recorded . ';');
		echo (get_post_meta ( $activity->item_id, 'sh_user_group', true ) . ';');
		echo ("\n");
	}
	status_header(200);
	die();
}
?>
