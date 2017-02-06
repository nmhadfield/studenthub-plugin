<p>
<label>Post Title</label> <input name='sh-forum-post-title' value='<?php echo(get_post_meta(get_the_ID(), 'sh_new_post_title', true)); ?>'></input>
</p>
<?php $checked = get_post_meta(get_the_ID(), 'sh_new_post_requires_subjects', true) ? 'checked': ''?>
<input type='checkbox' name='sh-forum-post-require-subjects' <?php echo($checked); ?>>Require Subjects</input>
<p>
<?php 
	$cats = get_categories(array('parent' => 0, 'exclude' => '1'));
	$selected_cats = get_post_meta(get_the_ID(), 'sh_forum_subject_areas', true); 
	if (!$selected_cats) {
		$selected_cats = array();
	}
?>
<label>Select from categories: </label>
<ul>
<?php foreach ($cats as $cat) { ?>
	<?php $checked = in_array($cat -> term_id, $selected_cats) ? 'checked': ''; ?>
	<li><input type='checkbox' name='sh-forum-post-subjects[<?php echo($cat -> term_id);?>]' <?php echo($checked); ?>><?php echo($cat -> name);?></input></li>
<?php }?>
</ul>
</p>