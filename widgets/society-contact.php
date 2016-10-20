<div class="widget">
	<?php foreach($links as $key => $link) ?>
	<a href='<?php echo($link);?>' class='link'><img src='<?php echo(get_stylesheet_directory_uri().'/images/icons/links/'.$key.'.png') ?>'></img></a>
	
	<?php echo(get_post(get_the_ID()) -> post_content) ?>
</div>
