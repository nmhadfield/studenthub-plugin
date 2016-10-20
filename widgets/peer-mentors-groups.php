<?php
	$groups = groups_get_groups()['groups'];
?>
<div id="studenthub-peer-mentor-groups" class="widget article shadow blog-holder">
	<span class="title">Peer Mentor Communities</span>
	<ul class="browse">
		<?php for ($i = 0; $i < count($groups); $i++) { ?>
			<li><a href="<?php echo(bp_get_group_permalink($groups[$i])); ?>"><?php echo($groups[$i] -> name); ?></a></li>
		<?php } ?>
	</ul>
</div>