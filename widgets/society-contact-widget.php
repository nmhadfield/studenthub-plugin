<?php 
class Society_Contact_Widget extends WP_Widget {

	/**
	 * Sets up the widgets name etc
	 */
	public function __construct() {
		$widget_ops = array( 
			'classname' => 'society_contact_widget',
			'description' => 'Shows contact information for a society',
		);
		parent::__construct( 'society_contact_widget', 'Society Contact Widget', $widget_ops );
	}

	/**
	 * Outputs the content of the widget
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {
		$email = get_post_meta($args['post-id'], 'email', true);
		$fb = get_post_meta($args['post-id'], 'facebook', true);
		$twitter = get_post_meta($args['post-id'], 'twitter', true);
		get_post_meta($args['post-id'], 'web', true);
		
		$links = array('facebook' => 'http://www.facebook.com');
		
		include('society-contact.php');
	}
}
?>