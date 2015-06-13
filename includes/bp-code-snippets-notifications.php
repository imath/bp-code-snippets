<?php
/* notifications */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

function bp_code_snippets_send_notification( $snippet_id, $snippet_author_id, $item_id, $user_id,  $content = '') {
	
	
	if ( $snippet_author_id == $user_id )
		return false;
	
	// first screen notification
	bp_core_add_notification( $snippet_id, $snippet_author_id, 'snippets', 'comment', $item_id );
	
	//second mail notification if author haven't disabled mail notification.
	if ( 'no' == bp_get_user_meta( $snippet_author_id, 'snippet_mail_comment', true ) )
		return false;

	$author_ud = bp_core_get_core_userdata( $snippet_author_id );
	
	$comment_author = bp_core_get_user_displayname( $user_id );

	$settings_link = bp_core_get_user_domain( $snippet_author_id ) . bp_get_settings_slug() . '/notifications/';
	
	if(is_numeric( $item_id ) && $item_id > 0 )
		$snippet_link = bp_code_snippets_build_perma( array('id' => $snippet_id, 'item_id' => $item_id, 'object' => 'group') );
	
	else
		$snippet_link = bp_code_snippets_build_perma( array('id' => $snippet_id, 'object' => 'directory') );

	// Set up and send the message
	$to       = $author_ud->user_email;
	$sitename = wp_specialchars_decode( get_blog_option( bp_get_root_blog_id(), 'blogname' ), ENT_QUOTES );
	$subject  = '[' . $sitename . '] ' . __( 'New comment on one of your snippets', 'bp-code-snippets' );

	$message = sprintf( __(
'%1$s added a comment to your snippet.

Permalink to your snippet : %2$s

content of the comment: 

%3$s

---------------------
', 'bp-code-snippets' ), $comment_author, $snippet_link, $content );

	$message .= sprintf( __( 'To disable these notifications please log in and go to: %s', 'bp-code-snippets' ), $settings_link );

	/* Send the message */
	$to      = apply_filters( 'bp_code_snippets_notification_mail_to', $to );
	$subject = apply_filters( 'bp_code_snippets_notification_mail_subject', $subject );
	$message = apply_filters_ref_array( 'bp_code_snippets_notification_mail_message', array( $message, $comment_author, $snippet_link, $content, $settings_link ) );

	wp_mail( $to, $subject, $message );

	do_action( 'bp_code_snippets_send_notification', $snippet_author_id, $user_id, $subject, $message, $snippet_link );
	
}

function bp_code_snippets_format_notifications($action, $item_id, $secondary_item_id, $total_items, $format = "string" ) {
	global $bp;

	// Set up the string and the filter
	if ( (int)$total_items > 1 ) {
		$link = bp_loggedin_user_domain() . BP_CS_SLUG . '/mine/?n=1';
		$text = sprintf( __( '%d new comments on your snippet(s)', 'bp-code-snippets' ), (int)$total_items );
		$filter = 'bp_code_snippets_multiple_comments';
	} else {
		
		if(is_numeric( $secondary_item_id ) && $secondary_item_id > 0 )
			$link = bp_code_snippets_build_perma( array('id' => $item_id, 'item_id' => $secondary_item_id, 'object' => 'group') );
		
		else
			$link = bp_code_snippets_build_perma( array('id' => $item_id, 'object' => 'directory') );
			
			
		$link .= '?n=1';
		$text = __( '1 new comment on your snippet', 'bp-code-snippets' );
		$filter = 'bp_code_snippets_single_comment';
	}

	// Return either an HTML link or an array, depending on the requested format
	if ( 'string' == $format ) {
		$return = apply_filters( $filter, '<a href="' . $link . '">' . $text . '</a>', (int)$total_items );
	} else {
		$return = apply_filters( $filter, array(
			'link' => $link,
			'text' => $text
		), (int)$total_items );
	}

	do_action( 'bp_code_snippets_format_notifications', $action, $item_id, $secondary_item_id, $total_items, $return );

	return $return;
}

?>