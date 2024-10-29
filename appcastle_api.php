<?php

// requires
require_once 'AcSystem.php';
require_once 'AcPlugin.php';
require_once 'AcTheme.php';

// TODO: implement
$return = array();
$action = $_POST['action'];

switch ( $action )
{
	case 'get_all':
		$return[ 'get_system_version' ][ 'system_version' ]	 = AcSystem::getSystemVersion();
		$return[ 'get_system_version' ][ 'new_version' ]	 = AcSystem::getNewVersion();
		$return[ 'get_plugin_list' ][ 'plugin_list' ]		 = AcPlugin::getList();
		$return[ 'get_theme_list' ][ 'theme_list' ] 		 = AcTheme::getList();
		$return[ 'get_disk_usage' ][ 'used_disk_space' ] 	 = AcSystem::getUsedDiskSpace();
		$return[ 'get_disk_usage' ][ 'db_size' ] 			 = AcSystem::getDbSize();
		$return[ 'get_comment_info' ]['comments_enabled'] 	 = AcSystem::getCommentsEnabled();
		$return[ 'get_comment_info' ]['unapproved_comments'] = AcSystem::getUnapprovedComments();
		break;

	case 'get_system_version':
		$return[ 'system_version' ] = AcSystem::getSystemVersion();
        $return[ 'new_version' ]    = AcSystem::getNewVersion();
		break;

	case 'upgrade_system':
		$return[ 'update_status' ] = AcSystem::upgrade();
		break;

	case 'upgrade_core':
		$return[ 'update_status' ] = AcSystem::upgradeCore();
		break;

	case 'get_plugin_list':
	default:
		$return[ 'plugin_list' ] = AcPlugin::getList();
		break;

	case 'upgrade_plugins':
		$return[ 'update_status' ] = AcPlugin::upgradePlugins();
		break;

	case 'upgrade_plugin':
		wp_set_current_user( 1 );
		$return[ 'update_status' ] = AcPlugin::upgradePlugin( $_GET['plugin'] );
		break;

	case 'get_theme_list':
		$return[ 'theme_list' ] = AcTheme::getList();
		break;

	case 'upgrade_themes_bulk':
		$return[ 'update_status' ] = AcTheme::upgradeThemesBulk();
		break;

	case 'upgrade_theme':
		$return[ 'update_status' ] = AcTheme::upgradeTheme( $_GET['theme'] );
		break;

	case 'get_disk_usage':
		$return[ 'used_disk_space' ] = AcSystem::getUsedDiskSpace();
		$return[ 'db_size' ] = AcSystem::getDbSize();
		break;

	case 'get_comment_info':
		$return['comments_enabled'] = AcSystem::getCommentsEnabled();
		$return['unapproved_comments'] = AcSystem::getUnapprovedComments();
		break;
}

echo json_encode( $return );