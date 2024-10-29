<?php
/**
 * handles all logic concerning Wordpress itself
 */
class AcSystem
{

	public static function getSystemVersion()
	{
		return get_bloginfo( 'version' );
	}

    public static function getNewVersion(){
        $data = get_site_transient( 'update_core');
        return $data->updates[0]->current;
    }

	public static function getCommentsEnabled(){
		$option = get_option('default_comment_status');
		return ($option == 'open') ? true : false;
	}

	public static function getUnapprovedComments() {
		$comments = get_comments( array( 'status' => 'hold' ) );
		return count($comments);
	}

	/** upgrades core and plugins only! **/
	public static function upgrade()
	{
		$status = array(
			"core" => self::upgradeCore(),
			"plugins" => AcPlugin::upgradePlugins()
		);
		return $status;
	}

	public static function getUsedDiskSpace(){
		$foldersize = self::wpse_67876_foldersize(ABSPATH);
		return $foldersize;
	}

	public static function getDbSize(){
		global $wpdb;
		$db_size = $wpdb->get_var("SELECT sum( data_length + index_length ) as `size` FROM information_schema.TABLES WHERE `table_schema` = '" . DB_NAME . "' GROUP BY table_schema");
		return $db_size;
	}

	/** borrowed from: http://wordpress.stackexchange.com/questions/67876/how-to-check-disk-space-used-by-media-library **/
	static function wpse_67876_foldersize( $path ) 
	{
	    $total_size = 0;
	    $files = scandir( $path );
	    $cleanPath = rtrim( $path, '/' ) . '/';

	    foreach( $files as $t ) {
	        if ( '.' != $t && '..' != $t ) 
	        {
	            $currentFile = $cleanPath . $t;
	            if ( is_dir( $currentFile ) ) 
	            {
	                $size = self::wpse_67876_foldersize( $currentFile );
	                $total_size += $size;
	            }
	            else 
	            {
	                $size = filesize( $currentFile );
	                $total_size += $size;
	            }
	        }   
	    }

	    return $total_size;
	}

	public static function upgradeCore(){
		include_once ( ABSPATH . 'wp-admin/includes/admin.php' );
		include_once ( ABSPATH . 'wp-admin/includes/upgrade.php' );
		include_once ( ABSPATH . 'wp-includes/update.php' );

		// check for access before doing anything
		if(!self::check_filesystem_status()){
			return 'filesystem not writable with supplied credentials';
		}

		// refresh wp update check
		wp_version_check();

		// check for available core update
		$updates = get_core_updates();
		if( is_wp_error( $updates ) || !$updates )
			return 'no update available';

		$update = reset($updates); // there should only be this one
		if( !$update )
			return 'no update available';

		// init upgrader, empty skin necessary
		$skin = new AcCore_Upgrader_Skin();

		$upgrader = new Core_Upgrader( $skin );

		// run update
		$result = $upgrader->upgrade($update);

		if( is_wp_error($result) )
			return $result;

		// we have to reload version.php so $wp_db_version will be updated
		global $wp_current_db_version, $wp_db_version;
		require( ABSPATH . WPINC . '/version.php' );
		wp_upgrade();

		return true;
	}

	/**
	 * check if filesys is writable
	 */
	public static function check_filesystem_status(){
		ob_start();
		$success = request_filesystem_credentials( '' );
		ob_end_clean();
		return (bool) $success;
	}
}




