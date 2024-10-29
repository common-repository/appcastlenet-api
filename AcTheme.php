<?php
/**
 * handles all logic concerning Wordpress themes itself
 */
class AcTheme
{

	public static function getList()
	{
		$themeList = wp_get_themes();
		//echo json_encode(var_dump($themeList));
		// Get the active theme
		$active  = get_option( 'current_theme' );

		delete_transient( 'update_themes' );

		// Force a theme update check
		wp_update_themes();

		$current = get_site_transient( 'update_themes' );

		$themes = array();
		foreach ( $themeList as $slug => $objTheme )
		{
			if ( !is_object( $objTheme ) )
				continue;

			$theme = array();
			/** note: returned theme name, hence usage of slug not possible **/
			//$theme['slug'] = $slug; 
			$theme['slug'] = $objTheme->Template;
			//$theme['slug'] = $objTheme->get( 'TextDomain' );
			$theme['name'] = $objTheme->get( 'Name' );
			$theme['current_version'] = $objTheme->get( 'Version' );

			$new_version = isset($current->response[$theme['slug']]) ? $current->response[$theme['slug']]['new_version'] : null;
			$theme['new_version'] = $new_version ? $new_version : $objTheme->get( 'Version' );

			if ( $active == $slug )
				$theme['active'] = true;
			else
				$theme['active'] = false;

			$themes[] = $theme;
		}

		return $themes;
	}

	public static function upgradeThemesBulk(){
		$themes = self::getList();
		$bulk_themes = array();
		foreach($themes as $theme){
			$bulk_themes[] = $theme['slug'];
		}

		include_once ( ABSPATH . 'wp-admin/includes/admin.php' );
		// check for access before doing anything
		if(!AcSystem::check_filesystem_status()){
			return 'filesystem not writable with supplied credentials';
		}

		$skin = new AcTheme_Upgrader_Skin();
		$upgrader = new Theme_Upgrader( $skin );
		// do the upgrade

		ob_start();
		$result = $upgrader->bulk_upgrade( $bulk_themes );
		$data = ob_get_contents();
		ob_clean(); 

		if ( ( ! $result && ! is_null( $result ) ) || $data )
			return 'file_permissions_error';

		elseif ( is_wp_error( $result ) )
			return $result->get_error_code;

		if ( $skin->error )
			return $skin->error;

		return 'success';

	}

	public static function upgradeTheme( $theme ){
		include_once ( ABSPATH . 'wp-admin/includes/admin.php' );
		// check for access before doing anything
		if(!AcSystem::check_filesystem_status()){
			return 'filesystem not writable with supplied credentials';
		}

		$skin = new AcTheme_Upgrader_Skin();
		$upgrader = new Theme_Upgrader( $skin );
		// do the upgrade

		ob_start();
		$result = $upgrader->upgrade( $theme );
		$data = ob_get_contents();
		ob_clean(); 

		if ( ( ! $result && ! is_null( $result ) ) || $data )
			return 'file_permissions_error';

		elseif ( is_wp_error( $result ) )
			return $result->get_error_code;

		if ( $skin->error )
			return $skin->error;

		return 'success';
	}
}
