<?php
/*
Plugin Name: appcastle.net API
Plugin URI: https://appcastle.net/en/wordpress
Description: Use appcastle.net to recieve notification of update and / or install them automatically
Version: 0.7.28
Author: appcastle.net
Author URI: http://www.appcastle.net
License: GPL2
Text Domain: appcastle
*/

/*
    Copyright 2013  codecastle.net, Mario Hiller (email : mario.hiller@codecastle.de)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

function api_call()
{
    if( !isset( $_POST['appcastle-api-key'] ) )
        return;
    
	if ( empty( $_POST[ 'appcastle-api-key' ] ) ){
		echo json_encode( array('message' => 'invalid token') );
        exit();
    }

    if( get_option('appcastle_enable_access') == false ){
        echo json_encode( array('message' => 'access deactivated') );
        exit();
    }

    if( $_POST['appcastle-api-key'] != get_option('appcastle_api_key') ){
        echo json_encode( array('message' => 'invalid token') );
        exit();
    }

	require_once 'appcastle_api.php';

	exit();
}

/* first time install actions */
function appcastle_activate(){
    add_option('appcastle_api_key', '');
    add_option('appcastle_enable_access', true);
}

// hooks

define('AC_WORDPRESS_ID', 'appcastle');
define('AC_IS_RELEASE', false);

register_activation_hook( __FILE__, 'appcastle_activate' );

add_action( 'wp_loaded', 'api_call', 1 );
add_action( 'plugins_loaded', 'appcastle_admin_init');
add_action( 'admin_menu', 'appcastle_admin_menu');

function appcastle_set_filesystem_credentials( $credentials ) {

    if ( empty( $_POST['filesystem_details'] ) )
        return $credentials;

    $_credentials = array(
        'username' => $_POST['filesystem_details']['credentials']['username'],
        'password' => $_POST['filesystem_details']['credentials']['password'],
        'hostname' => $_POST['filesystem_details']['credentials']['hostname'],
        'connection_type' => $_POST['filesystem_details']['method']
    );

        // check whether the credentials can be used
    if ( ! WP_Filesystem( $_credentials ) ) {
        return $credentials;
    }

    return $_credentials;
}


add_filter( 'request_filesystem_credentials', 'appcastle_set_filesystem_credentials' );

require_once ( ABSPATH . 'wp-admin/includes/class-wp-upgrader.php' );

function appcastle_admin_init(){
    load_plugin_textdomain('appcastle', false, basename(dirname( __FILE__ )) . '/lang');
}

function appcastle_admin_menu(){
    global $menu, $submenu;
    /* Configuration Page */

    add_options_page(__('appcastle.net Settings', 'appcastle'), __('appcastle.net Settings', 'appcastle'), 'manage_options', 'appcastle', 'appcastle_conf');
    add_filter( 'plugin_action_links', 'appcastle_register_options_link', 10, 2);
}

function appcastle_register_options_link($links, $file){
    static $this_plugin;
    if (!$this_plugin) $this_plugin = plugin_basename(__FILE__);
    if ($file == $this_plugin){
        $settings_link = '<a href="' . admin_url( 'options-general.php?page='. AC_WORDPRESS_ID ) . '">' . __( 'Settings' ) . '</a>';
        array_unshift($links, $settings_link);
    }
    return $links;
}

function appcastle_conf(){
    include(dirname(__FILE__) . '/AcAdminInterface.php');
}

class AcPlugin_Upgrader_Skin extends Plugin_Installer_Skin {

    var $feedback;
    var $error;

    function error( $error ) {
        $this->error = $error;
    }

    function feedback( $feedback ) {
        $this->feedback = $feedback;
    }

    function before() { }

    function after() { }

    function header() { }

    function footer() { }

}

class AcTheme_Upgrader_Skin extends Theme_Installer_Skin {

    var $feedback;
    var $error;

    function error( $error ) {
        $this->error = $error;
    }

    function feedback( $feedback ) {
        $this->feedback = $feedback;
    }

    function before() { }

    function after() { }

    function header() { }

    function footer() { }

    function bulk_header() { }

    function bulk_footer() { }

}

class AcCore_Upgrader_Skin extends WP_Upgrader_Skin {

    var $feedback;
    var $error;

    function error( $error ) {
        $this->error = $error;
    }

    function feedback( $feedback ) {
        $this->feedback = $feedback;
    }

    function before() { }

    function after() { }

    function header() { }

    function footer() { }

}
