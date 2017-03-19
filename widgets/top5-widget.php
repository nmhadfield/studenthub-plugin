<?php 
class Top5_Widget extends WP_Widget {

	/**
	 * Sets up the widgets name etc
	 */
	public function __construct() {
		$widget_ops = array( 
			'classname' => 'top5_widget',
			'description' => 'Filters search output according to selected categories',
		);
		parent::__construct( 'top5_widget', '(StudentHub) Top 5 Widget', $widget_ops );
	}

	/**
	 * Outputs the content of the widget
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {
		$query_args = array(
				'post_status' =>'published',
				'post_type' =>'topic',
		);

		$cat = array_key_exists('sh_category', $instance) ? $instance['sh_category'] : 0;
		if ($cat != 0) {
			$query_args['tax_query'] = array(array(
					'taxonomy' => 'category',
					'field'    => 'term_id',
					'terms'    => array( $cat )),
			);
		}
		if (array_key_exists('sh_show_all', $instance)) {
			$query_args['posts_per_page'] =  0;
			$query_args['orderby'] = 'post_name';
			$query_args['order'] = 'ASC';
		}
		else {
			$query_args['posts_per_page'] =  5;
			$query_args['orderby'] ='meta_value_num';
			$query_args['meta_key'] = '_topicliked';
		}

		$query = new WP_Query($query_args);
		include('top5.php');
	}
	
	public function form($instance) {
		echo("<p><label>Title</label>");
		
		$title = array_key_exists("sh_title", $instance) ? $instance['sh_title'] : '';
		echo("<input id='".$this->get_field_id('sh_title')."' name='".$this->get_field_name('sh_title')."' value='".$title."'></input>");
		echo("</p></p><label>Category</label>");
		
		$category = array_key_exists("sh_category", $instance) ? $instance['sh_category'] : '';
		echo("<select id='".$this->get_field_id('sh_category')."' name='".$this->get_field_name('sh_category')."'>");
		echo("<option value='0'>-- all --</option>");
		$categories = get_terms(array('taxonomy'=>'category', 'parent' => 0, 'hide_empty' => false));
		foreach ($categories as $cat) {
			echo("<option value='".$cat->term_id."'");
			if ($category == $cat->term_id) {
				echo(" selected='selected'");
			}
			echo(">");
			echo($cat->name);
			echo("</option>");
			$children = get_terms(array('taxonomy'=>'category', 'parent' => $cat->term_id, 'hide_empty' => false));
			foreach ($children as $child) {
				echo("<option value='".$child->term_id."'");
				if ($category == $child->term_id) {
					echo(" selected='selected'");
				}
				echo(">");
				echo("&nbsp;&nbsp;&nbsp;".$child->name);
				echo("</option>");
			}
		}
		echo("</select></p>");
		echo("<p>");
		echo("<input type='checkbox' id='".$this->get_field_id('sh_show_all')."' name='".$this->get_field_name('sh_show_all')."'");
		if (array_key_exists('sh_show_all', $instance)) {
			echo(" checked");
		}
		echo(">Show All</input>");
		echo("</p>");
	}
}
?>