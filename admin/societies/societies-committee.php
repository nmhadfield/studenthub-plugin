<div id='committee-metabox'>
	<table>
	<tbody>
		<?php 
			$members = get_post_meta(get_the_ID(), 'sh_committee', false);
			for ($i = 0; $i < count($members); $i++) {
				$user = get_user_by('login', $members[$i]['id']); ?> <tr>
					<td><?php echo($user == null ? $members[$i] :$user -> display_name); ?></td>
					<td><?php echo($members[$i]['role']); ?></td>
				</tr> <?php }
		?>
	</tbody>
	</table>
	
	<div>
		<label>user id: </label><input id="sh-login"></input><label>role:</label><input id="sh-role"></input><button id="sh-add" type="button">Add</button>
	</div>
</div>