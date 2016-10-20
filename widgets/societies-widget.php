<?php 
class Societies_Widget extends WP_Widget {

	/**
	 * Sets up the widgets name etc
	 */
	public function __construct() {
		$widget_ops = array( 
			'classname' => 'societies_widget',
			'description' => 'Shows the list of societies',
		);
		parent::__construct( 'societies_widget', 'Societies Widget', $widget_ops );
	}

	/**
	 * Outputs the content of the widget
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {
		include('societies.php');
	}
}
?>