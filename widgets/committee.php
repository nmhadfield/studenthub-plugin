<div id="committee-widget" class="widget">
	<span class="title">Committee</span>
	<ul class='browse'>
		<?php foreach ($committee as $role => $members) { ?>
			<li class="title"><?php echo($role); ?></li>
				<?php foreach ($members as $member) { ?>
					<li><?php echo($member); ?></li>
				<?php } ?>
		<?php } ?>
	</ul>
</div>