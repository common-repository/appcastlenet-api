<?php
/**
 * handles all logic concerning Wordpress plugins itself
 */
class AcPlugin
{

	public static function getList()
	{
		require_once ABSPATH . '/wp-admin/includes/plugin.php';

		// _wpr_add_non_extend_plugin_support_filter();

		// Get all plugins
		$plugins = get_plugins();
		if ( !is_array( $plugins ) )
			return array();

		// truncate cache for plugin data
		delete_site_transient( 'update_plugins' );

		wp_update_plugins();

		// Different versions of wp store the updates in different places
		// TODO can we depreciate
		if ( function_exists( 'get_site_transient' ) && $transient = get_site_transient( 'update_plugins' ) )
			$current = $transient;

		elseif ( $transient = get_transient( 'update_plugins' ) )
			$current = $transient;

		else
			$current = get_option( 'update_plugins' );

		foreach ( (array)$plugins as $plugin_file => $plugin )
		{
			$new_version = isset( $current->response[ $plugin_file ] )
				? $current->response[ $plugin_file ]->new_version
				: null;

			$plugins[ $plugin_file ][ 'active' ] = is_plugin_active( $plugin_file );

			if ( $new_version )
			{
				$plugins[ $plugin_file ][ 'latest_version' ] = $new_version;
				$plugins[ $plugin_file ][ 'latest_package' ] = $current->response[ $plugin_file ]->package;
				$plugins[ $plugin_file ][ 'filename' ] = $plugin_file;
				$plugins[ $plugin_file ][ 'slug' ] = $current->response[ $plugin_file ]->slug;
			}
			else
			{
				$plugins[ $plugin_file ][ 'latest_version' ] = $plugin[ 'Version' ];
			}

		}

		return $plugins;
	}

	public static function upgradePlugins(){
		$plugins = self::getList();
		$status = array();
		foreach( $plugins as $plugin ){
			if(isset($plugin['slug']))
				$status[] = array('plugin' => $plugin['filename'], 'status' => self::upgradePlugin( $plugin['filename'] ));
		}

		return $status;
	}

	public static function upgradePlugin( $plugin ){
		include_once ( ABSPATH . 'wp-admin/includes/admin.php' );

		if( !AcSystem::check_filesystem_status() )
			return 'filesystem not writable with supplied credentials';

		$skin = new AcPlugin_Upgrader_Skin();
		$upgrader = new Plugin_Upgrader( $skin );
		$is_active = is_plugin_active( $plugin );

		// force plugin update check
		wp_update_plugins();

		// run the upgrade
		ob_start();
		$result = $upgrader->upgrade( $plugin );
		$data = ob_get_contents();
		ob_clean();

		if ( ( ! $result && ! is_null( $result ) ) || $data )
			return 'file_permissions_error';

		elseif ( is_wp_error( $result ) )
			return $result->get_error_code();

		if ( $skin->error )
			return $skin->error;

		// if plugin was activated, we gotta turn it back on
		if( $is_active ){
			$result = self::activatePlugin( $plugin );
		}
		return 'success';
	}

	public static function activatePlugin( $plugin ) {

		include_once ABSPATH . 'wp-admin/includes/plugin.php';

		$result = activate_plugin( $plugin );

		if ( is_wp_error( $result ) )
			return array( 'status' => 'error', 'error' => $result->get_error_code() );

		return array( 'status' => 'success' );
	}

}
