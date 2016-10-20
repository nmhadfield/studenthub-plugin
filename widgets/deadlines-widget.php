<?php 
class Deadlines_Widget extends WP_Widget {

	/**
	 * Sets up the widgets name etc
	 */
	public function __construct() {
		$widget_ops = array( 
			'classname' => 'deadlines_widget',
			'description' => 'Shows the upcoming deadlines for the current student',
		);
		parent::__construct( 'deadlines_widget', 'Deadlines Widget', $widget_ops );
	}

	/**
	 * Outputs the content of the widget
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {
		$year = array_key_exists('mbchbYearOfStudyInLatestAcademicYear', $_COOKIE) ? $_COOKIE['mbchbYearOfStudyInLatestAcademicYear'] : '1';		
		$query = new WP_Query(array('post_type' => 'sh_deadline', 
									'meta_key' => 'sh_deadline_date', 
									'meta_value' => date("Ymd"), 
									'meta_compare' => '>=', 
									'meta_type' => 'DATE', 
									'meta_query' => array(array('key' => 'sh_deadline_yeargroup', 'value' => $year)), 
									'orderby' => 'sh_deadline_date', 
									'order' => 'ASC'));
		$deadlines = array();
		while ($query -> have_posts()) {
			$query -> the_post();
			$dateString = get_post_meta(get_the_ID(), 'sh_deadline_date', true);
			$deadline = new DateTime($dateString);
			array_push($deadlines, array('title' => get_the_title(), 'date' => date_format($deadline, "d-m-Y")));
		}
		include('deadlines.php');
	}

}
?>