<div class="widget browse-category collapsed">
	<span class="title"><?php echo(get_category($instance['sh_category']) -> name); ?></span>
	<span><a href="#" onclick="sh_expandCollapse(event, '<?php echo($instance['sh_category']); ?>')" class="expand-collapse collapsed"></a></span>
	<div id="<?php echo($instance['sh_category'].'-widget-content'); ?>" class="widget-content collapsed">
		<ul class="browse">
		<?php $categories = get_terms('category', array('hide_empty' => false, 'parent' => $instance['sh_category'])); ?>
		<?php foreach ($categories as $cat) { ?>
			<li>
				<a href="#" onclick="sh_filterResources(event, '<?php echo($cat->name)?>')"><?php echo($cat->name)?></a>
			</li>
		<?php }?>
		</ul>
	</div>
</div>