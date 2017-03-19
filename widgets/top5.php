<?php $cat = array_key_exists('sh_category', $instance) ? $instance['sh_category'] : 0; ?>
<?php $title = array_key_exists('sh_title', $instance) ? $instance['sh_title'] : ""?>
<div class="widget browse-category">
	<div class="title"><?php echo($title); ?>
		<a href="#" onclick="sh_expandCollapse(event, 'top5-<?php echo($cat); ?>')" class="expand-collapse"></a>
	</div>
	<div id="top5-<?php echo($instance['sh_category'].'-widget-content'); ?>" class="widget-content">
		<ul class="browse">
		<?php while ($query -> have_posts()) { ?>
			<?php $query -> the_post(); ?>
			<li><a href='<?php the_permalink()?>'><?php echo(the_title());?></a></li>
		<?php } ?>
		<?php wp_reset_postdata(); ?>
		</ul>
	</div>
</div>