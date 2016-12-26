<?php 
class Committee_Widget extends WP_Widget {

	/**
	 * Sets up the widgets name etc
	 */
	public function __construct() {
		$widget_ops = array( 
			'classname' => 'committee_widget',
			'description' => 'Shows the upcoming deadlines for the current student',
		);
		parent::__construct( 'committee_widget', 'Committee Widget', $widget_ops );
	}

	/**
	 * Outputs the content of the widget
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {
		$results = array();
		$committee = get_post_meta($args['post-id'], 'sh_committee', false);
		
		for ($i = 0; $i < count($committee); $i++) {
			$user = get_user_by('id', $committee[$i]['id']);
			$role = $committee[$i]['role'];
			
			array_push($results, array(
					'role' => $role, 
					'name' => $user->display_name,
					'email' => $user->user_email));
		}

		
		
		include('committee.php');
	}
		
}
?>