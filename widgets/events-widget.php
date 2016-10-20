<?php 
class Events_Widget extends WP_Widget {

	/**
	 * Sets up the widgets name etc
	 */
	public function __construct() {
		$widget_ops = array( 
			'classname' => 'events_widget',
			'description' => 'Shows the upcoming deadlines for the current student',
		);
		parent::__construct( 'events_widget', 'Events Widget', $widget_ops );
	}

	/**
	 * Outputs the content of the widget
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {
		locate_template( array( 'widgets/events.php'), true );
	}
}
?>