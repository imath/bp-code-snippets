<?php
/* 
Plugin Name: BP Code Snippets
Plugin URI: http://imathi.eu/2012/01/30/bp-code-snippets-2-0/
Description: BuddyPress 1.6+ plugin to share & highlight code snippets
Version: 2.1
Author: imath
Author URI: http://imathi.eu
License: GPLv2
Network: true
Text Domain: bp-code-snippets
Domain Path: /languages/
*/

/* définition des constantes */
define ( 'BP_CS_SLUG', 'snippets' );
define ( 'BP_CS_PLUGIN_NAME', 'bp-code-snippets' );
define ( 'BP_CS_PLUGIN_URL',  plugins_url('' , __FILE__) );
define ( 'BP_CS_PLUGIN_URL_JS',  plugins_url('js' , __FILE__) );
define ( 'BP_CS_PLUGIN_URL_CSS',  plugins_url('css' , __FILE__) );
define ( 'BP_CS_PLUGIN_URL_IMG',  plugins_url('images' , __FILE__) );
define ( 'BP_CS_PLUGIN_DIR',  WP_PLUGIN_DIR . '/' . BP_CS_PLUGIN_NAME );
define ( 'BP_CS_PLUGIN_VERSION', '2.1');

//widget
require( BP_CS_PLUGIN_DIR . '/includes/bp-code-snippets-widgets.php' );

function bp_code_snippets_init() {
	global $bp;
	
	require( BP_CS_PLUGIN_DIR . '/includes/bp-code-snippets-loader.php' );
	
	if( is_admin() ){
		require( BP_CS_PLUGIN_DIR . '/includes/bp-code-snippets-admin.php' );
	}
	
}

add_action( 'bp_include', 'bp_code_snippets_init', 10 );

function bp_code_snippets_load_textdomain() {
	// try to get locale
	$locale = apply_filters( 'bp_code_snippets_load_textdomain_get_locale', get_locale() );

	// if we found a locale, try to load .mo file
	if ( !empty( $locale ) ) {
		// default .mo file path
		$mofile_default = sprintf( '%s/languages/%s-%s.mo', BP_CS_PLUGIN_DIR, BP_CS_PLUGIN_NAME, $locale );
		// final filtered file path
		$mofile = apply_filters( 'bp_code_snippets_load_textdomain_mofile', $mofile_default );
		// make sure file exists, and load it
		if ( file_exists( $mofile ) ) {
			load_textdomain( BP_CS_PLUGIN_NAME, $mofile );
		}
	}
}

add_action ( 'plugins_loaded', 'bp_code_snippets_load_textdomain', 2 );

function bp_code_snippets_is_bkmklet_tab_enable() {
	if( '1' == get_option( 'cs-bkmklet-enable' ) )
		return true;
	else
		return false;
}

function bp_code_snippets_had_previous_version() {
	if( is_multisite() )
		$had_previous = get_blog_option(bp_get_root_blog_id(),  'bp-code-snippets-old-version' );
	else
		$had_previous = get_option( 'bp-code-snippets-old-version' );
	
	if( "1" == $had_previous )
		return true;
	else
		return false;
}

function bp_code_snippets_install(){
	$pages = bp_get_option( 'bp-pages' );
	$active_components = bp_get_option('bp-active-components');
	
	if( empty( $pages[BP_CS_SLUG] )){
		$page_snippets = wp_insert_post( array( 'comment_status' => 'closed', 'ping_status' => 'closed', 'post_title' => ucwords( BP_CS_SLUG ), 'post_status' => 'publish', 'post_type' => 'page' ) );
		
		$pages[BP_CS_SLUG] = $page_snippets;
		bp_update_option('bp-pages', $pages );
	}
	if( empty( $active_components[BP_CS_SLUG] )){
		$active_components[BP_CS_SLUG] = 1;
		bp_update_option('bp-active-components', $active_components );
	}
}

function bp_code_snippets_activate(){
	// db stuff!
	if( !get_option('bp-code-snippets-version') || get_option('bp-code-snippets-version') != BP_CS_PLUGIN_VERSION ){
		require( BP_CS_PLUGIN_DIR . '/includes/bp-code-snippets-db.php' );
		bp_code_snippets_db_install();
		bp_code_snippets_set_available_lg();
		bp_code_snippets_install();
		update_option('bp-code-snippets-version', BP_CS_PLUGIN_VERSION);
	}
}

register_activation_hook( __FILE__, 'bp_code_snippets_activate' );

function bp_code_snippets_deactivate(){
	$pages = bp_get_option( 'bp-pages' );
	$active_components = bp_get_option('bp-active-components');
	
	if( !empty( $pages[BP_CS_SLUG] ) ){
		wp_delete_post($pages[BP_CS_SLUG], true);
		unset($pages[BP_CS_SLUG]);
		bp_update_option('bp-pages', $pages );
	}
	if( !empty( $active_components[BP_CS_SLUG] ) ){
		unset($active_components[BP_CS_SLUG]);
		bp_update_option('bp-active-components', $active_components );
	}
}

register_deactivation_hook( __FILE__, 'bp_code_snippets_deactivate' );



function bp_code_snippets_update_message() {
	echo '<p style="color: red; margin: 3px 0 0 0; border-top: 1px solid #ddd; padding-top: 3px">' . __( 'IMPORTANT: BP Code Snippets 2.1 requires BuddyPress 1.6.4, make sure you are running this version of BuddyPress before installing or upgrading.', 'bp-code-snippets' ). '</p>';
}

add_action( 'in_plugin_update_message-bp-code-snippets/bp-code-snippets.php', 'bp_code_snippets_update_message' );

?>