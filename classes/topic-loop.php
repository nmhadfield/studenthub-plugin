<?php 
add_action('wp_ajax_studenthub_reload_feed', 'studenthub_reload_feed');
add_action('wp_ajax_studenthub_feed', 'studenthub_reload_feed');

class TopicLoop {

	/**
	 * Prepares the WP_Query then delegates output to the template
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function output($args) {
		$paginate = true;
		$query_args = array(
				'post_type'       => "topic",
				'posts_per_page'  => 20,
				'order'           => 'DESC',
				'tax_query'       => array(),
		);
		
		// loading earlier posts (infinite scrolling)
		if (array_key_exists('sh_before', $args)) {
			$query_args['date_query'] = array(array('before' => $args['sh_before']));
		}
		
		// loading newer posts (after new post, or posts from others)
		if (array_key_exists('sh_after', $args)) {
			$query_args['date_query'] = array(array('after' => $args['sh_after']));
		
			// don't want pagination if we're adding to the top of the page
			$query_args['posts_per_page'] = -1;
			$paginate = false;
		}
		
		// if there are filters supplied
		if (array_key_exists('sh_category', $args)) {
			$query_args['category_name'] = $args['sh_category'];
		}
		
		if (array_key_exists('sh_searchterms', $args)) {
			$query_args['s'] = $args['sh_searchterms'];
		}
		
		if ((array_key_exists('sh_scope', $args) && args['sh_scope'] == 'favourite') || get_query_var('sh_scope') == 'favourite') {
			$query_args['post__in'] = get_user_meta(get_current_user_id(), 'favourite', false);
		}
		
		if (array_key_exists('sh_type', $args)) {
			array_push($query_args['tax_query'], array('taxonomy' => 'topic-type', 'field' => 'slug', 'terms' => explode(',', $args['sh_type'])));
		}
		
		// which forums to include
		if (array_key_exists('sh_parent', $args) && $args['sh_parent'] != 0) {
			$parents = studenthub_sh_parent($args['sh_parent']);
			
			if (count($parents) > 0) {
				$query_args['post_parent__in'] = $parents;
			}
			else {
				$query_args['post_parent'] = $args['sh_parent'];
			}
		}
		else {
			// exclude all private forums
			$query_args['post_parent__not_in'] = bbp_get_private_forum_ids();
		}
		
		include(locate_template('content/topic-loop.php', false ));
	}
}

function studenthub_sh_parent($id) {
	$result = array();
	array_push($result, $id);
	
	$query = new WP_Query(array('post_parent' => $id, 'post_type' => 'forum'));
	
	while ($query->have_posts()) : $query->the_post();
		$result = array_merge($result, studenthub_sh_parent(get_the_ID()));
	endwhile;
	
	return $result;
}

/* Ajax function for reloading the feed after posting. */
function studenthub_reload_feed() {
	$loop = new TopicLoop();
	$loop->output($_GET);
	die();
}

function is_image($url) {
	return str_ends_with($url, '.png') || str_ends_with($url, '.jpg') || str_ends_with($url, '.gif');
}

function str_ends_with($str, $token) {
	$end = substr($str, strlen($str) - 4);
	return $end == $token;
}
?>