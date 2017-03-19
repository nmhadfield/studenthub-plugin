<?php 
	function sh_page_select($year) {
		$pageId = get_option('sh_front_page_year_'.$year, null);
		echo("<select name='sh_front_page_year_".$year."'>");
		$pages = get_posts(array('post_type' => 'page', 'orderby' => 'post_name', 'order' => 'ASC', 'numberposts' => 0));
		echo("<option></option>");
		foreach ($pages as $page) {
			echo("<option value='".$page -> ID."'");
			if ($page -> ID == $pageId) {
				echo(" selected = 'selected'");
			}
			echo(">");
			echo($page -> post_name);
			echo("</option>");
		}
		echo("</select>");
	}
?>
<div class='wrap'>
	<h1>StudentHub Settings</h1>
	<form action="options.php" method="POST">
		<?php settings_fields('sh_front_page_options'); ?>
		<?php do_settings_sections( 'sh_front_page_options' ); ?>
		
		<p>Select the front page for each year group</p>
		<label>First Year</label>: <?php sh_page_select(1); ?><br>
		<label>Second Year</label>: <?php sh_page_select(2); ?><br>
		<label>Third Year</label>: <?php sh_page_select(3); ?><br>
		<label>Fourth Year</label>: <?php sh_page_select(4); ?><br>
		<label>Fifth Year</label>: <?php sh_page_select(5); ?><br>
		<?php submit_button(); ?>
	</form>
</div>