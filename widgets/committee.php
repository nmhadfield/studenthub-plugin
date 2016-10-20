<div id="committee-widget" class="widget">
	<span class="title">Committee</span>
	<ul class='browse'>
		<?php foreach ($results as $person) { ?>
			<li class="title"><?php echo($person['role']); ?></li>
			<li><a href='mailto:<?php echo($person['email']); ?>'><?php echo($person['name']); ?></a></li>
		<?php } ?>
	</ul>
</div>