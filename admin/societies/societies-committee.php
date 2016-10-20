<div id='committee-metabox'>
	<table>
	<tbody>
		<?php 
			$members = get_post_meta(get_the_ID(), 'sh_committee', false);
			for ($i = 0; $i < count($members); $i++) {
				$user = get_user_by('login', $members[$i]); ?> <tr>
					<td><?php echo($user == null ? $members[$i] :$user -> display_name); ?></td>
					<td><?php echo(get_post_meta(get_the_ID(), 'role-'.$members[i], true)); ?></td>
				</tr> <?php }
		?>
	</tbody>
	</table>
	
	<div>
		<label>user id: </label><input id="sh-login"></input><label>role:</label><input id="sh-role"></input><button id="sh-add" type="button">Add</button>
	</div>
</div>