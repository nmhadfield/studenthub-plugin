<?php 
add_filter('manage_edit-sh_deadline_columns', 'sh_admin_deadline_columns' ) ;
add_action('manage_sh_deadline_posts_custom_column', 'sh_admin_deadline_columns_fill');

function sh_admin_deadline_columns( $columns ) {
	$columns = array(
			'cb' => '<input type="checkbox" />',
			'title' => __( 'Title' ),
			'year' => __( 'Year' ),
			'deadline' => __( 'Deadline' ),
			'group' => __( 'Group' ),
			'date' => __('Date'),
	);

	return $columns;
}

function sh_admin_deadline_columns_fill( $column ) {
	global $post;
	$post_id = $post -> ID;
	switch ($column) {
		case 'year' : 
			echo(get_post_meta($post_id, 'sh_deadline_yeargroup', true));
			break;
		case 'deadline':
			$dateString = get_post_meta($post_id, 'sh_deadline_date', true); 
			$deadline = new DateTime($dateString);
			echo(date_format($deadline, "d-m-Y"));
			break;
		case 'group':
			echo(get_post_meta($post_id, 'sh_deadline_group', true));
			break;
		default: break;
	}
}

function sh_admin_deadlines_metaboxes() {
	$year = $date = $group = '';
	$year = get_post_meta(get_the_ID(), 'sh_deadline_yeargroup', true);
	$date = get_post_meta(get_the_ID(), 'sh_deadline_date', true);
	$group = get_post_meta(get_the_ID(), 'sh_deadline_group', true);
	include('deadlines.php');
}

function sh_admin_deadlines_save($id, $post) {
	if (defined ( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE) {
		return;
	}
	if (! current_user_can ( 'edit_post', $id )) {
		return;
	}
	
	$type = array_key_exists('post_type', $_POST) ?  $_POST ['post_type'] : $post->post_type;
	if ($type == 'sh_deadline') {
		if (array_key_exists('sh-deadline-year', $_POST)) {
			update_post_meta ( $id, 'sh_deadline_yeargroup', $_POST ['sh-deadline-year']);
		}
		if (array_key_exists('sh-deadline-date', $_POST)) {
			update_post_meta ( $id, 'sh_deadline_date', date_format(new DateTime($_POST ['sh-deadline-date']), 'Ymd'));
		}
		if (array_key_exists('sh-deadline-group', $_POST)) {
			update_post_meta ( $id, 'sh_deadline_group', $_POST ['sh-deadline-group'] );
		}
	}
}