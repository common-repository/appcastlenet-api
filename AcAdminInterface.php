<?php
if(isset($_POST['submit'])){
	if(isset($_POST['appcastle_activated']) && $_POST['appcastle_activated'] == 1){
		update_option('appcastle_enable_access', true);
	}else{
		update_option('appcastle_enable_access', false);
	}
	
	if(isset($_POST['appcastle_token'])){
		update_option('appcastle_api_key', $_POST['appcastle_token']);
	}
	$message = __( 'Changes saved successfully', 'appcastle' );
}

$token = get_option('appcastle_api_key');
$checked = get_option('appcastle_enable_access') ? 'checked' : '';

?>

<div class="icon32" id="icon-options-general"><br></div>
<h2><?php _e( 'appcastle.net Settings', 'appcastle' ); ?></h2>
<?php if ($message) : ?>
<div id="message" class="updated fade"><p><strong><?php echo $message; ?></strong></p></div>
<?php endif; ?>
<form action="" method="post">
	<table class="form-table">
		<tbody>
			<tr valign="top">
				<th scope="row"><label>Status</label></th>
				<td>
					<input type="checkbox" name="appcastle_activated" id="appcastle_activated" value="1" <?php echo $checked; ?>>
					<label for="appcastle_activated"><?php _e( 'API activated', 'appcastle' ); ?></th>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="appcastle_token"><?php _e( 'API Token', 'appcastle' ); ?></label></th>
				<td>
					<input type="text" class="regular-text ltr" value="<?php echo $token; ?>" id="appcastle_token" name="appcastle_token">
				</td>
			</tr>
		</tbody>
	</table>
	<p class="submit"><input type="submit" value="<?php _e( 'Save Changes', 'appcastle' ); ?>" class="button button-primary" id="submit" name="submit"></p>
</form>