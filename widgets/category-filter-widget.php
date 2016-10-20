<?php 
class Category_Filter_Widget extends WP_Widget {

	/**
	 * Sets up the widgets name etc
	 */
	public function __construct() {
		$widget_ops = array( 
			'classname' => 'category_filter_widget',
			'description' => 'Filters search output according to selected categories',
		);
		parent::__construct( 'category_filter_widget', '(StudentHub) Category Filter Widget', $widget_ops );
	}

	/**
	 * Outputs the content of the widget
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {
		include('category-filter.php');
	}
	
	public function form($instance) {
		echo("<label>Category</label>");
		
		$category = array_key_exists("sh_category", $instance) ? $instance['sh_category'] : '';
		echo("<select id='".$this->get_field_id('sh_category')."' name='".$this->get_field_name('sh_category')."'>");
		
		$categories = get_terms(array('taxonomy'=>'category', 'parent' => 0, 'hide_empty' => false));
		foreach ($categories as $cat) {
			echo("<option value='".$cat->term_id."'");
			if ($category == $cat->term_id) {
				echo(" selected='selected'");
			}
			echo(">");
			echo($cat->name);
			echo("</option>");
		}
		echo("</select>");
	}

}
?>