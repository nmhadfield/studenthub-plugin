<div class="widget">
	<div>
		<span class="title">About</span>
	</div>
	<div>
		<?php echo(get_post(get_the_ID()) -> post_content) ?>
	</div>
	<div>
		<?php foreach($links as $key => $link) ?>
		<a href='<?php echo($link);?>' class='link'><img src='<?php echo(get_stylesheet_directory_uri().'/images/icons/links/'.$key.'.png') ?>'></img></a>
	</div>
</div>
