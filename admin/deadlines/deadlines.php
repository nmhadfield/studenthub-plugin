<div>
	<label>Due</label>
	<input id='sh-deadline-date' name='sh-deadline-date' value='<?php echo($date); ?>'></input>
</div>
<div>
	<label>Year</label>
	<select id="sh-deadline-year" name='sh-deadline-year'>
	<?php for ($i = 1; $i <=5; $i++) { ?>
		<option value='<?php echo($i); ?>' <?php if ($i == $year) {echo(" selected = 'selected'");} ?>><?php echo($i); ?></option>
	<?php } ?>
	</select>
</div>
<div>
	<label>Group</label>
	<input id="sh-deadline-group" name="sh-deadline-group" value='<?php echo($group); ?>'></input>
</div>