<?php 
class Peer_Mentors_Groups_Widget extends WP_Widget {

	/**
	 * Sets up the widgets name etc
	 */
	public function __construct() {
		$widget_ops = array( 
			'classname' => 'peer_mentors_groups_widget',
			'description' => 'Shows the list of peer mentors groups',
		);
		parent::__construct( 'peer_mentors_groups_widget', 'Peer Mentors Groups Widget', $widget_ops );
	}

	/**
	 * Outputs the content of the widget
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {
		locate_template( array( 'widgets/peer-mentors-groups.php'), true );
	}
}
?>