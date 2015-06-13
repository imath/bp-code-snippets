<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) 
	exit;

if( !defined( 'WP_UNINSTALL_PLUGIN' ) )
	exit ();

// On efface les options insérées
delete_option( 'bp-code-snippets-version' );
delete_option( 'bp-code-snippets-old-version' );
delete_option( 'cs-child-enable' );
delete_option( 'cs-bkmklet-enable' );
delete_option( 'cs-setting-theme' );
delete_option( 'cs-enable' );
delete_option( 'cs-ep-enable' );
delete_option( 'cs-ef-enable' );
delete_option( 'cs-oembed' );
delete_option( 'cs-iframe-activity-enable' );
delete_option( 'cs-ma-enable' );
delete_option( 'cs-homenav-enable' );
delete_option( 'cs-admin-selected-languages' );
delete_option( 'bp_code_snippets_available_language' );

//on supprime les tables insérées

global $wpdb;

$cs_table = $wpdb->base_prefix . 'code_snippets';
$cs_comment_table = $wpdb->base_prefix . 'code_snippets_comments';
$cs_meta_table = $wpdb->base_prefix . 'code_snippets_meta';

$wpdb->query( "DROP TABLE IF EXISTS `$cs_table`" );
$wpdb->query( "DROP TABLE IF EXISTS `$cs_comment_table`" );
$wpdb->query( "DROP TABLE IF EXISTS `$cs_meta_table`" );

//delete users meta
delete_metadata( 'user', false, 'bp_favorite_snippets', '', true );

if( function_exists( 'bp_init' ) ) {
	
	if( bp_is_active( 'groups' ) ){
		// delete groupmeta
		$group_ids = $wpdb->get_col("SELECT id FROM {$wpdb->base_prefix}bp_groups");

		if( count( $group_ids ) >= 1) {
			foreach( $group_ids as $group_id ){
				groups_delete_groupmeta( $group_id, 'snippets_code_ok');
			}
		}
	}
	
	//delete activities !
	if( bp_is_active( 'activity' ) ) {
		bp_activity_delete( array( 'component' => 'snippets' ) ) ;
		bp_activity_delete( array( 'component' => 'groups', 'type' => 'snippet_group_update' ) ) ;
		bp_activity_delete( array( 'component' => 'groups', 'type' => 'snippet_group_comment' ) ) ;
	}
	
}
?>