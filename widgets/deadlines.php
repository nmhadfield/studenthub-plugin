<?php

?>
<div id="studenthub-deadlines" class="widget article shadow blog-holder">
	<div class="title expand-collapse">Deadlines
		<a href="#" onclick="sh_expandCollapse(event, 'deadlines')" class="expand-collapse"></a>
	</div>
	<div id="deadlines-widget-content" class="widget-content">
		<ul class="browse">
			<?php foreach ($deadlines as $deadline) { ?>
				<li><?php echo($deadline['title']); ?><span><?php echo($deadline['date']); ?></span></li>
			<?php }?>
		</ul>
	</div>
</div>