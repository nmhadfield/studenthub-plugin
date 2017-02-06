<?php 
class Committee_Widget extends WP_Widget {

	/**
	 * Sets up the widgets name etc
	 */
	public function __construct() {
		$widget_ops = array( 
			'classname' => 'committee_widget',
			'description' => 'Shows the committee for the selected society',
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
		//$committee = get_post_meta(get_the_ID(), 'sh_committee', true);
		//include('committee.php');
	}
}
?>