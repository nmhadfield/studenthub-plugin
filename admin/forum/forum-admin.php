<?php
function sh_admin_forum_theme() {
	
}

function sh_admin_forum_theme_save() {
	
}

/** Defines the features of the new post form for this forum. */
function sh_admin_forum_post_metaboxes() { 
	$groups = get_terms('category', array('parent' => 0));
?>
	<div>
		<label>Form Title</label>
		<input id='sh_forum_post_title' name='sh_forum_post_title'></input>
	</div>
	<div>
		<label>Categories</label>
		<ul>
		<?php foreach ($groups as $group) { ?>
			<li><input type='checkbox' name='sh_forum_post_cat[<?php echo($group -> name); ?>]'><?php echo($group -> name); ?></li>
		<?php } ?>	
		</ul>
	</div>	
<?php 
}