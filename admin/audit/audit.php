<?php 
	
function sh_settings_page_statistics() {
	$result = bp_activity_get(array());
	foreach ($result['activities'] as $activity) {
		echo($activity->type.' ');
		echo($activity->user_login.' ');
		if ($activity -> item_id != 0) {
		//	echo();
		}
	}
}
?>