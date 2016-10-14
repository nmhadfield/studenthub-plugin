<div id='committee-metabox'>
	<table>
	<tbody>
		<?php 
			$members = get_post_meta(get_the_ID(), 'sh_committee', false);
			for ($i = 0; $i < count($members); $i++) {
				echo($members[$i]);
				$user = get_user_by('id', $members[$i]); ?> <tr>
					<td><?php echo($user -> display_name); ?></td>
					<td><?php echo(get_post_meta(get_the_ID(), 'role'.$user -> ID, true)); ?></td>
				</tr> <?php }
		?>
	</tbody>
	</table>
	
	<div>
		<label>Find user by email: </label><input id="sh-email"></input><button id="sh-search" type="button">Search</button>
		<div id="sh-found-user">
		</div>
	</div>
</div>