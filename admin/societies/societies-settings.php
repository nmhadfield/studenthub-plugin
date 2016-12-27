<div class="wrap">
<h1>Societies</h1>
<form method='post' action='options.php'>

<?php 
	settings_fields( 'sh_societies_options' ); 
    do_settings_sections( 'sh_societies_options' );
    $roles = get_option('sh_societies_roles[]');
    
    for ($i = 0; $i < count($roles); $i++) { ?>
    	echo($roles[$i]);
   <?php  }
?>
    <label>Add Role</label><input type='text' id='sh-societies-new-role' value=''></input><button>Add</button>
    
    <?php submit_button(); ?>
</form>
</div>