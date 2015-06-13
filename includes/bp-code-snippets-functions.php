<?php
/* functions */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

function bp_code_snippets_is_forum_snippets_enable() {
	if( '1' == get_option('cs-ef-enable') )
		return true;
	else
		return false;
}

function bp_code_snippets_is_group_snippets_enable() {
	if( '1' == get_option('cs-enable') )
		return true;
	else
		return false;
}

function bp_code_snippets_is_blog_snippets_enable() {
	
	if( is_multisite() ) {
		if( '1' == get_blog_option( bp_get_root_blog_id(), 'cs-ep-enable') )
			return true;
		else
			return false;
	
	} else {
		
		if( '1' == get_option('cs-ep-enable') )
			return true;
		else
			return false;
	}
	
}

function bp_code_snippets_is_oembed_enable() {
	
	if( is_multisite() )
		$oembed = get_blog_option(bp_get_root_blog_id(),  'cs-oembed' );
	else
		$oembed = get_option( 'cs-oembed' );
	
	if( "1" == $oembed )
		return true;
	else
		return false;
}

function bp_code_snippets_iframe_activity() {
	if( '1' == get_option('cs-iframe-activity-enable') )
		return true;
	else
		return false;
}

function bp_code_snippets_child_blog_snpt_ok() {
	if( !is_multisite() ) return true;
	else {
		if( '1' == get_option('cs-child-enable')  )
			return true;
		else
			return false;
	}
	
}


function bp_code_snippets_form_action() {
	echo site_url() . remove_query_arg( array('s','snptcat','n') );
}

function bp_code_snippets_is_edit_snippet() {
	if ( !bp_displayed_user_id() && bp_is_current_component( 'snippets' ) && bp_is_current_action( 'permasnipt' ) && bp_action_variable( 1 ) == 'edit')
		return true;
	
	if( !bp_displayed_user_id() && bp_is_current_component( 'groups' ) && bp_is_current_action( 'snippets' ) && bp_action_variable( 1 ) == 'edit' )
		return true;
	
	return false;
}

function bp_dir_snippets_selected_item( $action=false ){
	if( !$action && bp_is_current_component( 'snippets' ) && !bp_current_action() ) {
		echo 'class="selected"';
	}
	if( !empty( $action ) && bp_is_current_action( $action ) ){
		echo 'class="selected"';
	}
}

function bp_group_snippets_is_snippet_enabled( $group = false ) {
	global $groups_template;

	if ( !$group && bp_is_active( 'groups' ) )
		$group =& $groups_template->group;
		
	if (!empty($group) && is_numeric($group) )
		$grp = $group;
	else $grp = bp_is_active( 'groups' ) ? $group->id : false ;
		
	$is_snippet_active = bp_is_active( 'groups' ) ? groups_get_groupmeta( $grp, 'snippets_code_ok' ) : false ;

	if ( $is_snippet_active && is_array($is_snippet_active) && count($is_snippet_active) >= 1 )	
		return true;

	return false;
}


function bp_group_snippets_get_available_languages(){
	
	$available = array();
	
	if( is_multisite() ){
		$available_default = get_blog_option( 1, 'bp_code_snippets_available_language' );
		$available_admin = get_blog_option( 1, 'cs-admin-selected-languages' );
	} else {
		$available_default = get_option('bp_code_snippets_available_language');
		$available_admin = get_option('cs-admin-selected-languages');
	}
	
	
	if( $available_admin && is_array($available_admin) && count($available_admin) >= 1 ){
		
		foreach($available_default as $k => $v){
				
			if( in_array( $v,  $available_admin))
					$available[$k] = $v ;
					
		}
		
	}else{
		
		$available = $available_default;
		
	}
	
	return $available;
}

function bp_group_snippets_get_available_languages_for_group($group_id = 0){
	
	$available = array();
	$available_by_admin = bp_group_snippets_get_available_languages();
	$available_for_group = groups_get_groupmeta($group_id, 'snippets_code_ok');
	
	foreach($available_by_admin as $k => $v){
			
		if( in_array( $v,  (array)$available_for_group))
				$available[$k] = $v ;
				
	}
	if( count($available) == 0 ){
		
		$available = $available_by_admin;

	}
	
	return $available;
}

function bp_code_snippets_cat_is_available( $cat = "all" ){
	global $bp;
	
	if( $cat == "all")
		return false;
		
	if( bp_is_current_component( 'groups' ) )
		$available = bp_group_snippets_get_available_languages_for_group( $bp->groups->current_group->id );
	
	else $available = bp_group_snippets_get_available_languages();
	
	if( in_array($cat, $available) )
		return true;
	
	else return false;
}

/* code snippets meta functions */
function bp_code_snippets_delete_meta( $snippet_id, $meta_key = '', $meta_value = '' ) {
	global $wpdb, $bp;

	// Return false if any of the above values are not set
	if ( !is_numeric( $snippet_id ) )
		return false;

	// Sanitize key
	$meta_key = preg_replace( '|[^a-z0-9_]|i', '', $meta_key );

	if ( is_array( $meta_value ) || is_object( $meta_value ) )
		$meta_value = serialize( $meta_value );

	// Trim off whitespace
	$meta_value = trim( $meta_value );

	// Delete all for snippet_id
	if ( empty( $meta_key ) )
		$retval = $wpdb->query( $wpdb->prepare( "DELETE FROM {$bp->snippets->table_cs_meta} WHERE snippet_id = %d", $snippet_id ) );

	// Delete only when all match
	else if ( $meta_value )
		$retval = $wpdb->query( $wpdb->prepare( "DELETE FROM {$bp->snippets->table_cs_meta} WHERE snippet_id = %d AND meta_key = %s AND meta_value = %s", $snippet_id, $meta_key, $meta_value ) );

	// Delete only when snippet_id and meta_key match
	else
		$retval = $wpdb->query( $wpdb->prepare( "DELETE FROM {$bp->snippets->table_cs_meta} WHERE snippet_id = %d AND meta_key = %s", $snippet_id, $meta_key ) );

	// Success
	if ( !is_wp_error( $retval ) )
		return true;

	// Fail
	else
		return false;
}

function bp_code_snippets_get_meta( $snippet_id = 0, $meta_key = '' ) {
	global $wpdb, $bp;

	// Make sure snippet_id is valid
	if ( empty( $snippet_id ) || !is_numeric( $snippet_id ) )
		return false;

	// We have a key to look for
	if ( !empty( $meta_key ) ) {

		// Sanitize key
		$meta_key = preg_replace( '|[^a-z0-9_]|i', '', $meta_key );

		$metas = $wpdb->get_col( $wpdb->prepare("SELECT meta_value FROM {$bp->snippets->table_cs_meta} WHERE snippet_id = %d AND meta_key = %s", $snippet_id, $meta_key ) );

	// No key so get all for snippet_id
	} else {
		$metas = $wpdb->get_col( $wpdb->prepare( "SELECT meta_value FROM {$bp->snippets->table_cs_meta} WHERE snippet_id = %d", $snippet_id ) );
	}

	// No result so return false
	if ( empty( $metas ) )
		return false;

	// Maybe, just maybe... unserialize
	$metas = array_map( 'maybe_unserialize', (array)$metas );

	// Return first item in array if only 1, else return all metas found
	$retval = ( 1 == count( $metas ) ? $metas[0] : $metas );

	// Filter result before returning
	return apply_filters( 'bp_code_snippets_get_meta', $retval, $snippet_id, $meta_key );
}

function bp_code_snippets_update_meta( $snippet_id, $meta_key, $meta_value ) {
	global $wpdb, $bp;

	// Make sure snippet_id is valid
	if ( !is_numeric( $snippet_id ) )
		return false;

	// Sanitize key
	$meta_key = preg_replace( '|[^a-z0-9_]|i', '', $meta_key );

	// Sanitize value
	if ( is_string( $meta_value ) )
		$meta_value = stripslashes( $wpdb->escape( $meta_value ) );

	// Maybe, just maybe... serialize
	$meta_value = maybe_serialize( $meta_value );

	// If value is empty, delete the meta key
	if ( empty( $meta_value ) )
		return bp_code_snippets_delete_meta( $snippet_id, $meta_key );

	// See if meta key exists for snippet_id
	$cur = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$bp->snippets->table_cs_meta} WHERE snippet_id = %d AND meta_key = %s", $snippet_id, $meta_key ) );

	// Meta key does not exist so INSERT
	if ( empty( $cur ) )
		$wpdb->query( $wpdb->prepare( "INSERT INTO {$bp->snippets->table_cs_meta} ( snippet_id, meta_key, meta_value ) VALUES ( %d, %s, %s )", $snippet_id, $meta_key, $meta_value ) );

	// Meta key exists, so UPDATE
	else if ( $cur->meta_value != $meta_value )
		$wpdb->query( $wpdb->prepare( "UPDATE {$bp->snippets->table_cs_meta} SET meta_value = %s WHERE snippet_id = %d AND meta_key = %s", $meta_value, $snippet_id, $meta_key ) );

	// Weirdness, so return false
	else
		return false;

	// Victory is ours!
	return true;
}


function bp_code_snippets_post_snippet( $args = '' ) {
	global $bp;

	$defaults = array(
		'snippet_edit_id' => false,
		'title'           => false,
		'type'            => false,
		'purpose'         => "",
		'content'         => false,
		'comment_count'   => 0,
		'user_id'         => $bp->loggedin_user->id,
		'object'          => 'directory',
	);
	$r = wp_parse_args( $args, $defaults );
	extract( $r, EXTR_SKIP );
	
	if ( empty( $title ) || !strlen( trim( $title ) ) )
		return false;
	
	if ( empty( $type ) || !strlen( trim( $type ) ) )
		return false;

	if ( empty( $content ) || !strlen( trim( $content ) ) )
		return false;

	if ( bp_is_user_spammer( $user_id ) || bp_is_user_deleted( $user_id ) )
		return false;


	// Now save the snippet
	$snippet_id = bp_code_snippet_add( array(
		'id'            => $snippet_edit_id,
		'object'        => $object,
		'user_id'       => $user_id,
		'title'         => $title,
		'type'          => $type,
		'purpose'       => $purpose,
		'content'       => $content,
		'comment_count' => $comment_count,
	) );
	
	if( $snippet_id && $object == 'directory' ) {
		
		if( $snippet_edit_id )
			$snippet_action = __('edited a', 'bp-code-snippets');	
		else
			$snippet_action = __('posted a new', 'bp-code-snippets');
		
		// Prepare activity record
		$args = array(
			'action'            => sprintf( __( '%s %s %s snippet %s', 'bp-code-snippets' ), bp_core_get_userlink( $user_id ), $snippet_action, ucfirst( $type ), '<a href="'. bp_get_root_domain() . '/' . bp_get_snippet_slug() . '/permasnipt/' . $snippet_id .'/">' . wp_filter_kses( $title ) . '</a>' ),
			'content'           => bp_create_excerpt( wp_filter_kses( $purpose ) ),
			'component'         => 'snippets',
			'type'              => 'snippet_dir_update',
			'primary_link'      => bp_get_root_domain() . '/' . bp_get_snippet_slug() . '/permasnipt/' . $snippet_id .'/',
			'user_id'           => $user_id,
			'item_id' 			=> $snippet_id
		);
		
		if( bp_is_active( 'activity' ) ) {
			
			// record the snippet activity !
			$activity_id = bp_activity_add( $args );

			if( $activity_id )
				bp_update_user_meta( $user_id, 'bp_latest_update', array( 'id' => $activity_id, 'content' => $args['content'] ) );
			
		}
		
	}

	do_action( 'bp_code_snippets_posted_snippet', $purpose, $user_id, $snippet_id );

	return $snippet_id;
}

function bp_code_snippets_item_post_update( $args = '' ) {
	global $bp;

	$defaults = array(
		'snippet_edit_id' => false,
		'title'           => false,
		'type'            => false,
		'purpose'         => "",
		'content'         => false,
		'comment_count'   => 0,
		'user_id'         => $bp->loggedin_user->id,
		'item_id'         => false,
		'object'          => false,
		'secondary_id'    => false,
		'hide'            => false,
		'is_draft'        => false
	);

	$r = wp_parse_args( $args, $defaults );
	extract( $r, EXTR_SKIP );

	if ( empty( $item_id ) && !empty( $bp->groups->current_group->id ) )
		$item_id = $bp->groups->current_group->id;

	if ( empty( $content ) || !strlen( trim( $content ) ) || empty( $user_id ) || empty( $item_id ) || empty( $object ) || empty( $type ) || empty( $title ) )
		return false;
		
	// Check for the visibility of the component !
	if( strpos( $object, 'group' ) > - 1 ) {
		
		// Be sure the user is a member of the group before posting.
		if ( !is_super_admin() && !groups_is_user_member( $user_id, $item_id ) )
			return false;
		
		if( isset( $bp->groups->current_group->status ) ){
			$status = $bp->groups->current_group->status;
		}
		else{
			$group = groups_get_group( array( 'group_id' => $item_id ) );
			$status = $group->status;
		}
		
		if ( 'public' == $status )
			$hide = false;
		else
			$hide = true;
	}
	
	if( strpos( $object, 'blog' ) > - 1 ) {
		
		$is_blog_public = apply_filters( 'bp_is_blog_public', (int)get_blog_option( $item_id, 'blog_public' ) );
		
		if( $is_blog_public )
			$hide = false;
		else
			$hide = true;
		
		/* Multisite ? */	
		if( is_multisite() ) {
			
			// Be sure the user can add a post...
			if( !current_user_can_for_blog( $item_id, 'publish_posts' ) )
				return false;
			
			switch_to_blog( $item_id );
				
			if( 'publish' != get_post_status( $secondary_id ) )
				$is_draft = true;
			else
				$is_draft = false;
			
			restore_current_blog();
			
		} else {
			
			// Be sure the user can publish a post...
			if( !current_user_can( 'publish_posts' ) )
				return false;
				
			if( 'publish' != get_post_status( $secondary_id ) )
				$is_draft = true;
			else
				$is_draft = false;
			
		}
		
	}
	
	// Now write the values
	$snippet_id = bp_code_snippet_add( array(
		'id'             => $snippet_edit_id,
		'user_id'        => $user_id,
		'item_id'	     => $item_id,
		'object'	     => $object,
		'secondary_id'	 => $secondary_id,
		'hide'           => $hide,
		'is_draft'       => $is_draft,
		'title'          => $title,
		'type'           => $type,
		'purpose'        => $purpose,
		'content'        => $content,
		'comment_count'  => $comment_count
	) );
	
	if( $snippet_id ){
		
		/* 
		Record this in activity streams !
		if was published in a group as a new snippet (and not a snippet added to a comment)
		i don't record snippets added to blog/forum post activities
		as BuddyPress already handles this.
		*/
		if( $object == 'group' && bp_is_active( 'activity' ) ) {
			
			if( $snippet_edit_id )
				$snippet_action = __('edited a', 'bp-code-snippets');	
			else
				$snippet_action = __('posted a new', 'bp-code-snippets');
			
			if( !empty( $bp->groups->current_group ) ){
				$permalink = bp_get_group_permalink( $bp->groups->current_group );
				$group_name = $bp->groups->current_group->name;
			} elseif( isset( $group ) ) {
				$permalink = bp_get_group_permalink( $group );
				$group_name = $group->name;
			} else {
				$group = groups_get_group( array( 'group_id' => $item_id ) );
				$permalink = bp_get_group_permalink( $group );
				$group_name = $group->name;
			}
			
			// prepare activity
			$snippet_permalink = bp_code_snippets_build_perma( array('id' => $snippet_id, 'item_id' => $item_id, 'object' => $object) );
			
			$args = array(
				'action'            => sprintf( __( '%1$s %2$s snippet %3$s in the group %4$s', 'bp-code-snippets'), bp_core_get_userlink( $user_id ), $snippet_action, '<a href="' . $snippet_permalink . '">' . esc_attr( $title ) . '</a>', '<a href="' . $permalink . '">' . esc_attr( $group_name ) . '</a>' ),
				'content'           => bp_create_excerpt( wp_filter_kses( $purpose ) ),
				'component'         => 'groups',
				'type'              => 'snippet_group_update',
				'primary_link'      => $snippet_permalink,
				'user_id'           => $user_id,
				'item_id' 			=> $item_id,
				'secondary_item_id' => $snippet_id,
				'hide_sitewide'     => $hide
			);
			
			$activity_id = bp_activity_add( $args );
			
			if( $activity_id )
				bp_update_user_meta( $user_id, 'bp_latest_update', array( 'id' => $activity_id, 'content' => $args['content'] ) );
			
		}
		
	}

	do_action( 'bp_code_snippets_group_posted_snippet', $purpose, $user_id, $item_id, $snippet_id );

	return $snippet_id;
}

function bp_code_snippet_add( $args = '' ) {
	global $bp;

	$defaults = array(
		'id'                 => false,                 // Pass an existing snippet ID to update an existing entry.
		'title'              => '',                    // The snippet title
		'type'               => false,                 // type of snippet
		'purpose'            => '',                    // what is the goal of the code (optional)
		'content'            => false,                 // The snippet
		'comment_count'      => 0,                     // number of comments

		'user_id'           => $bp->loggedin_user->id, // Optional: The user to record the snippet for.
		'item_id'           => false,                  // Optional: The ID of the specific group being recorded
		'object'			=> 'directory',
		'secondary_id'      => false,                  // Optionnal Id of forum topic or blog post
		'date_recorded'     => bp_core_current_time(), // The GMT time that this activity was recorded
		'hide'              => false,
		'is_draft'          => false
	);
	$params = wp_parse_args( $args, $defaults );
	extract( $params, EXTR_SKIP );

	// Setup snippet to be added
	$snippet                 = new BP_Code_Snippets( $id );
	$snippet->item_id        = $item_id;
	$snippet->object         = $object;
	$snippet->secondary_id   = $secondary_id;
	$snippet->user_id        = $user_id;
	$snippet->hide           = $hide;
	$snippet->is_draft       = $is_draft;
	$snippet->date_recorded  = $date_recorded;
	$snippet->title          = $title;
	$snippet->type           = $type;
	$snippet->purpose        = $purpose;
	$snippet->content        = $content;
	$snippet->comment_count  = $comment_count;
	
	if ( !$snippet->save() )
		return false;
		
	do_action( 'bp_code_snippet_add', $params );

	return $snippet->id;
}

function bp_code_snippets_get( $args = '' ) {
	$defaults = array(
		'max'              => false,  // Maximum number of results to return
		'page'             => 1,      // page 1 without a per_page will result in no pagination.
		'per_page'         => false,  // results per page
		'sort'             => 'DESC', // sort ASC or DESC
		'display_comments' => false,  // false for no comments.
		'search_terms'     => false,  // Pass search terms as a string
		'show_hidden'      => false,  // Show snippets that includes in private or hidden group ?
		'is_draft'         => false,  // in case a blog post is not published
		'exclude'          => false,  // Comma-separated list of snippet IDs to exclude
		'in'               => false,  // Comma-separated list or array of snippet IDs to which you want to limit the query
		'filter' => array()
	);
	$r = wp_parse_args( $args, $defaults );
	extract( $r, EXTR_SKIP );
	
	$snippet = BP_Code_Snippets::get( $max, $page, $per_page, $sort, $search_terms, $filter, $display_comments, $show_hidden, $exclude, $in, $is_draft );

	return apply_filters_ref_array( 'bp_code_snippet_get_filters', array( &$snippet, &$r ) );
}

function bp_code_snippets_get_specific( $args = '' ) {
	$defaults = array(
		'snippet_ids'     => false,    // A single snippet_id or array of IDs.
		'page'             => 1,       // page 1 without a per_page will result in no pagination.
		'per_page'         => false,   // results per page
		'max'              => false,   // Maximum number of results to return
		'sort'             => 'DESC',  // sort ASC or DESC
		'display_comments' => false,   // true or false to display threaded comments for these specific snippet items
		'show_hidden'      => true,    // When fetching specific items, show all
		'is_draft'         => true,    // in case a blog post is not published
		'filter' => array()
	);
	$r = wp_parse_args( $args, $defaults );
	extract( $r, EXTR_SKIP );

	return apply_filters( 'bp_code_snippets_get_specific_filters', BP_Code_Snippets::get( $max, $page, $per_page, $sort, false, $filter, $display_comments, $show_hidden, false, $snippet_ids, $is_draft ) );
}

function bp_code_snippets_get_all_count( $args = '' ){
	$defaults = array(
		'snippet_ids'     => false,   // A single snippet_id or array of IDs.
		'page'             => 1,      // page 1 without a per_page will result in no pagination.
		'per_page'         => false,  // results per page
		'max'              => false,  // Maximum number of results to return
		'sort'             => 'DESC', // sort ASC or DESC
		'display_comments' => false,  // true or false to display threaded comments for these specific activity items
		'show_hidden'      => false,  // When fetching specific items, show all
		'is_draft'         => false,  // in case a blog post is not published
		'filter' => array()
	);
	$r = wp_parse_args( $args, $defaults );
	extract( $r, EXTR_SKIP );
	
	$count_snippets = BP_Code_Snippets::get( $max, $page, $per_page, $sort, false, $filter, $display_comments, $show_hidden, false, $snippet_ids );
	

	return apply_filters( 'bp_code_snippets_get_all_count', $count_snippets['total'] );
}

function bp_code_snippets_get_all_count_for( $args = '' ){
	$defaults = array(
		'max'              => false,  // Maximum number of results to return
		'page'             => 1,      // page 1 without a per_page will result in no pagination.
		'per_page'         => false,  // results per page
		'sort'             => 'DESC', // sort ASC or DESC
		'display_comments' => false,  // false for no comments
		'search_terms'     => false,  // Pass search terms as a string
		'show_hidden'      => false,  // Show snippets that includes in private or hidden group ?
		'is_draft'         => false,  // in case a blog post is not published
		'exclude'          => false,  // Comma-separated list of activity IDs to exclude
		'in'               => false,  // Comma-separated list or array of activity IDs to which you want to limit the query
		'filter' => array()
	);
	$r = wp_parse_args( $args, $defaults );
	extract( $r, EXTR_SKIP );

	
	$count_snippets = BP_Code_Snippets::get( $max, $page, $per_page, $sort, $search_terms, $filter, $display_comments, $show_hidden, $exclude, $in, $is_draft );

	return apply_filters( 'bp_code_snippets_get_all_count_for', $count_snippets['total'] );
}

function bp_code_snippets_what_component(){
	global $bp;
	
	$filter = false;
	
	if( !bp_displayed_user_id() && bp_is_current_component( 'groups' ) && bp_is_current_action( 'snippets' ) ){
		
		if( is_numeric( bp_action_variable( 0 ) ) )
			$filter = array( "include" => bp_action_variable( 0 ) ,"filter" => array( "primary_id" => $bp->groups->current_group->id, "object" => 'group' ) );
			
		else $filter = array( "filter" => array( "primary_id" => $bp->groups->current_group->id, "object" => 'group' ) );
	}
	
	if( !bp_displayed_user_id() && bp_is_current_component( 'snippets' ) && bp_is_current_action( 'mine' ) ){
		
		$filter = array( "filter" => array( "user_id" => bp_loggedin_user_id() ) );
		
	}
	
	if(bp_displayed_user_id()){
		
			$filter = array( "filter" => array( "user_id" => bp_displayed_user_id() ) );
	
	}
	
	if( !empty( $_REQUEST['snptcat'] ) && $_REQUEST['snptcat'] != "all" && bp_code_snippets_cat_is_available( $_REQUEST['snptcat'] ) ){
		
		if( $filter && is_array($filter) )
			$filter['filter']['category'] = $_REQUEST['snptcat'];
		
		else $filter = array('category' => $_REQUEST['snptcat']);
	}
	
	return $filter;
	
	
}

function bp_code_snippets_get_user_favorites( $user_id = 0 ) {
	global $bp;

	// Fallback to logged in user if no user_id is passed
	if ( empty( $user_id ) )
		$user_id = $bp->loggedin_user->id;

	// Get favorites for user
	$favs = bp_get_user_meta( $user_id, 'bp_favorite_snippets', true );

	return apply_filters( 'bp_code_snippets_get_user_favorites', $favs );
}

function bp_code_snippets_get_favorite_count( $user_id = 0 ) {
	global $bp;
	
	if ( empty( $user_id ) )
		$user_id = ( $bp->displayed_user->id ) ? $bp->displayed_user->id : $bp->loggedin_user->id;
	
	return apply_filters( 'bp_code_snippets_get_favorite_count', BP_Code_Snippets::total_favorite_count( $user_id ) );
}

function bp_code_snippets_add_user_favorite( $snippet_id, $user_id = 0 ) {
	global $bp;

	// Favorite snippet stream items are for logged in users only
	if ( !is_user_logged_in() )
		return false;

	// Fallback to logged in user if no user_id is passed
	if ( empty( $user_id ) )
		$user_id = $bp->loggedin_user->id;

	// Update the user's personal favorites
	$my_favs   = bp_get_user_meta( $bp->loggedin_user->id, 'bp_favorite_snippets', true );
	$my_favs[] = $snippet_id;

	// Update the total number of users who have favorited this snippet
	$fav_count = bp_code_snippets_get_meta( $snippet_id, 'favsnipt_count' );
	$fav_count = !empty( $fav_count ) ? (int)$fav_count + 1 : 1;

	// Update user meta
	bp_update_user_meta( $bp->loggedin_user->id, 'bp_favorite_snippets', $my_favs );

	// Update snippet meta counts
	if ( true === bp_code_snippets_update_meta( $snippet_id, 'favsnipt_count', $fav_count ) ) {

		// Execute additional code
		do_action( 'bp_code_snippets_add_user_favorite', $snippet_id, $user_id );

		// Success
		return true;

	// Saving meta was unsuccessful for an unknown reason
	} else {
		// Execute additional code
		do_action( 'bp_code_snippets_add_user_favorite_fail', $snippet_id, $user_id );

		return false;
	}
}

function bp_code_snippets_remove_user_favorite( $snippet_id, $user_id = 0 ) {
	global $bp;

	// Favorite activity stream items are for logged in users only
	if ( !is_user_logged_in() )
		return false;

	// Fallback to logged in user if no user_id is passed
	if ( empty( $user_id ) )
		$user_id = $bp->loggedin_user->id;

	// Remove the fav from the user's favs
	$my_favs = bp_get_user_meta( $user_id, 'bp_favorite_snippets', true );
	$my_favs = array_flip( (array) $my_favs );
	unset( $my_favs[$snippet_id] );
	$my_favs = array_unique( array_flip( $my_favs ) );

	// Update the total number of users who have favorited this snippet
	if ( $fav_count = bp_code_snippets_get_meta( $snippet_id, 'favsnipt_count' ) ) {

		// Deduct from total favorites
		if ( bp_code_snippets_update_meta( $snippet_id, 'favsnipt_count', (int)$fav_count - 1 ) ) {

			// Update users favorites
			if ( bp_update_user_meta( $user_id, 'bp_favorite_snippets', $my_favs ) ) {

				// Execute additional code
				do_action( 'bp_code_snippets_remove_user_favorite', $snippet_id, $user_id );

				// Success
				return true;

			// Error updating
			} else {
				return false;
			}

		// Error updating favorite count
		} else {
			return false;
		}

	// Error getting favorite count
	} else {
		return false;
	}
}

function bp_code_snippet_add_comment( $args = '' ) {
	global $bp;

	$defaults = array(
		'id'                 => false, // an existing snippet ID.
		'comment_count'      => 0,    // number of comments
		'user_id'            => $bp->loggedin_user->id, // Optional: The user to record the snippet for.
		'date_comment'       => bp_core_current_time(), // The GMT time that this snippet was recorded
		'comment_content'    => false, // The commment
	);
	$params = wp_parse_args( $args, $defaults );
	extract( $params, EXTR_SKIP );

	// Setup comment to be added
	$snippet                  = new BP_Code_Snippets( $id );
	$snippet->comment_count   = $snippet->comment_count + $comment_count;
	$snippet->commenter_id    = $user_id;
	$snippet->date_comment    = $date_comment;
	$snippet->comment_content = $comment_content;
	
	
	if ( !$snippet->save_comment() )
		return false;
		
	if ( $snippet->comment_id && bp_is_active( 'activity' ) ) {
		/* let's record an activity */
		
		$snippet_permalink = bp_code_snippets_build_perma( array('id' => $snippet->id, 'item_id' => $snippet->item_id, 'object' => $snippet->object) ) . '#snippet-comment-' . $snippet->comment_id;
		$content = bp_create_excerpt( wp_filter_kses( $comment_content ) );
		
		if( $snippet->object == 'directory' ) {
			
			// prepare activity
			$args = array(
				'action'            => sprintf( __( '%1$s commented on the snippet %2$s', 'bp-code-snippets'), bp_core_get_userlink( $user_id ), '<a href="' . $snippet_permalink . '">' . esc_attr( $snippet->title ) . '</a>'),
				'content'           => $content,
				'component'         => 'snippets',
				'type'              => 'snippet_dir_comment',
				'primary_link'      => $snippet_permalink,
				'user_id'           => $user_id,
				'item_id' 			=> $snippet->id,
				'secondary_item_id' => $snippet->comment_id,
			);
		}
		
		if( $snippet->object == 'group' ) {
			
			if( isset( $bp->groups->current_group->status ) ){
				$status = $bp->groups->current_group->status;
				$permalink = bp_get_group_permalink( $bp->groups->current_group );
				$group_name = $bp->groups->current_group->name;
			}
			else{
				$group = groups_get_group( array( 'group_id' => $item_id ) );
				$status = $group->status;
				$permalink = bp_get_group_permalink( $group );
				$group_name = $group->name;
			}

			if ( 'public' == $status )
				$hide = false;
			else
				$hide = true;
			
			// prepare activity
			$args = array(
				'action'            => sprintf( __( '%1$s commented on the snippet %2$s in the group %3$s', 'bp-code-snippets'), bp_core_get_userlink( $user_id ), '<a href="' . $snippet_permalink . '">' . esc_attr( $snippet->title ) . '</a>', '<a href="' . $permalink . '">' . esc_attr( $group_name ) . '</a>' ),
				'content'           => $content,
				'component'         => 'groups',
				'type'              => 'snippet_group_comment',
				'primary_link'      => $snippet_permalink,
				'user_id'           => $user_id,
				'item_id' 			=> $snippet->item_id,
				'secondary_item_id' => $snippet->id,
				'hide_sitewide'     => $hide
			);
		}
		
		$activity_id = bp_activity_add( $args );
		
		if( $activity_id )
			bp_update_user_meta( $user_id, 'bp_latest_update', array( 'id' => $activity_id, 'content' => $content ) );
			
		/* What about sending a notification to snippet author ? */
		bp_code_snippets_send_notification( $snippet->id, $snippet->user_id, $snippet->item_id, $user_id, $content);
		
	}
		
	do_action( 'bp_code_snippet_add_comment', $params );

	return $snippet->comment_id;
}

function bp_code_snippets_remove_comment( $comment_id = 0, $snippet_id = 0 ){
	
	// Setup comment to remove
	$snippet             = new BP_Code_Snippets( $snippet_id );
	$snippet->comment_id = $comment_id;
	
	if ( !$snippet->remove_comment() ) {
		return __('Comment could not be removed.. Reload the page and try again..', 'bp-code-snippets');
	} else {
		// as we created activities, we need to delete them !
		if( $snippet->object == "group" && bp_is_active( 'activity' ) ){
			
			$permacomment_args = array( 'id' => $snippet->id, 'item_id' => $snippet->item_id, 'object' => $snippet->object);
			
			$primary_link = bp_code_snippets_build_perma( $permacomment_args ) . '#snippet-comment-' . $comment_id ;
			
			bp_activity_delete( array(
								'component'         => 'groups',
								'type'              => 'snippet_group_comment',
								'primary_link'      => $primary_link,
								'item_id'           => $snippet->item_id,
								'secondary_item_id' => $snippet->id
								) ) ;
			
		}
		if( $snippet->object == "directory" && bp_is_active( 'activity' ) ) {
			
			$activity_args  = array( 'item_id' => $snippet->id, 'component' => 'snippets', 'type' => 'snippet_dir_comment', 'secondary_item_id' => $comment_id );
			bp_activity_delete_by_item_id( $activity_args );
			
		}
		
		return 1;
	}
}

/**
 * Retrieve the last time a snippet was posted
 *
 * @since 2.0
 *
 * @uses BP_Code_Snippets::get_last_updated() {@link BP_Code_Snippets}
 *
 * @return string Date last updated
 */
function bp_code_snippets_get_last_updated() {
	return apply_filters( 'bp_code_snippets_get_last_updated', BP_Code_Snippets::get_last_updated() );
}

function bp_code_snippets_delete_snippet( $args = '' ) {
	global $bp;

	// Pass one or more the of following variables to delete by those variables
	$defaults = array(
		'id'            => false,
		'item_id'       => false,
		'object'        => false,
		'secondary_id'  => false,
		'user_id'       => false,
		'date_recorded' => false,
		'title'         => false,
		'type'          => false,
		'purpose'       => false,
		'content'       => false,
		'comment_count' => false
	);

	$args = wp_parse_args( $args, $defaults );

	if ( !$snippet_ids_deleted = BP_Code_Snippets::delete( $args ) )
		return false;

	/* delete activity if snippet id, user id & object is defined... */
	if( !empty( $args['id']) && !empty($args['object']) && !empty($args['user_id']) && bp_is_active( 'activity' ) ) {
		
		if( $args['object'] == 'directory' ) {
			$activity_args  = array( 'item_id' => $args['id'], 'component' => 'snippets' );
			bp_activity_delete_by_item_id( $activity_args );
		}
			
		if( $args['object'] == 'group' ) {
			
			if( !empty( $args['item_id'] ) ){
				// the snippet
				$activity_args  = array( 'item_id' => $args['item_id'], 'component' => 'groups', 'type' => 'snippet_group_update', 'secondary_item_id' => $args['id']);
				
				bp_activity_delete_by_item_id( $activity_args );
				
				//now the comment...
				$activity_args['type'] = 'snippet_group_comment';
				bp_activity_delete_by_item_id( $activity_args );
			}
			
			
		}
		
	}

	do_action( 'bp_snippet_delete', $args );

	return true;
}

/* shortcode etc... */

function bp_code_snippets_build_perma( $args ='' ) {
	$defaults = array(
		'id'           => false, // an existing snippet ID.
		'item_id'      => 0,    // number of comments
		'object'       => false,
		'secondary_id' => 0
	);
	
	$params = wp_parse_args( $args, $defaults );
	extract( $params, EXTR_SKIP );
	
	$permalink = false;
	
	
	if( empty( $id ) )
		return false;
	
	if( $item_id >= 0 && !empty( $object ) ){
		$component = explode( "_", $object );
		
		if( $component[0] == 'group' ) {
			
			if ( $group = groups_get_group( array( 'group_id' => $item_id ) ) ) {
				$permalink = bp_get_group_permalink( $group ) . bp_get_snippet_slug() . '/' . $id . '/';
				
				if( $secondary_id > 0 && $object != "group_comment" ){
					
					$topic_id = intval( $secondary_id );
					$topic = bp_forums_get_topic_details( $topic_id );

					$permalink =  bp_get_group_permalink( $group ) . 'forum/topic/' . $topic->topic_slug;
				}
			}
		}
		
		if( $component[0] == 'blog' ) {
			
			if ( $item_id > 0 && $secondary_id > 0 ){
				if( is_multisite() )
					$permalink = get_blog_permalink( $item_id, $secondary_id ) ;
					
				else $permalink = get_permalink( $secondary_id ) ;
				
			}

		}
		
		if( $component[0] == 'directory' )
			$permalink = bp_get_root_domain() . '/' . bp_get_snippet_slug() . '/permasnipt/' . $id .'/';
	}
	return $permalink;
}

function bp_code_snippets_shortcode_handler($atts) {
	global $post, $bp;

	extract(shortcode_atts(array('id' => '0'), $atts));
	
	if( !bp_code_snippets_is_blog_snippets_enable() || ( !bp_code_snippets_child_blog_snpt_ok() && !bp_is_root_blog() ) )
		return false;
	
	if( !empty($post->ID) && !is_single() && (!is_page() || is_front_page() ) ){
		$snippet = new BP_Code_Snippets( $id );
		
		if( empty( $snippet->object ) )
			return '';
		
		$link = bp_code_snippets_build_perma( array('id' => $snippet->id, 'item_id' => $snippet->item_id, 'object' => $snippet->object, 'secondary_id' => $snippet->secondary_id ) ) ;
		
		return '<p class="snippet_excerpt_link"><a href="'.$link.'" title="'. esc_html( $snippet->title) .'">['.$snippet->type.'] '. esc_html( $snippet->title) .'</a></p>';
	}	
	else return bp_code_snippets_build_shortcode_response( 'id='.$id );
	
}

add_shortcode('snippet', 'bp_code_snippets_shortcode_handler');

function bp_code_snippets_build_shortcode_response( $args='' ) {
	
	$defaults = array(
		'id' => false,
		'type' => 'content',
	);
 
	$r = wp_parse_args( $args, $defaults );
	extract( $r, EXTR_SKIP );
	
	$snippets = bp_code_snippets_get_specific( array( 'snippet_ids' => $id, 'show_hidden' => true, 'display_comments' => false ) );
	
	$snippet = $snippets['snippets'][0];
	
	if( empty($snippet) )
		return '';
	
	$html = '<div class="snippet-embed" id="snippet-' . $snippet->id_cs .'">';
	
	if( $type == 'content' && bp_code_snippets_is_oembed_enable() && !$snippet->hide ) {
		$html .= '<div class="embed-action">';
		
		if( bp_code_snippets_iframe_activity() && is_user_logged_in() ) {
			$html .= '<a href="#get-shortcode" class="copy-shortcode">'. __('Get Shortcode', 'bp-code-snippets') .'</a>';
		}
		
		$html .= '<a href="#get-permalink" class="copy-link">'. __('Get Permalink', 'bp-code-snippets') .'</a>';
		$html .= '<a href="#get-embed-code" class="copy-code">'. __('Get Embed Code', 'bp-code-snippets') .'</a>';
		
		if( bp_code_snippets_iframe_activity() && is_user_logged_in() ){
			$html .= '<div style="display:none" class="copy-shortcode-snippet"><input type="text" value="[snippet id=&#34;'.$snippet->id_cs.'&#34;]" class="snipt-shortcode" readonly/></div>';
		}
		
		$html .= '<div style="display:none" class="copy-link-snippet">';
		$html .= '<input type="text" value="'. bp_code_snippets_build_perma( array('id' => $snippet->id_cs, 'item_id' => $snippet->item_id, 'object' => $snippet->object, 'secondary_id' => $snippet->secondary_id ) ) .'" class="snipt-perma" readonly/></div>';
		$html .= '<div style="display:none" class="copy-embed-snippet">';
		$html .= '<textarea class="snipt-code" readonly>&lt;iframe src="'. bp_get_root_domain() . '/' . bp_get_snippet_slug() . '/embed/' . $snippet->id_cs .'/' . '" frameborder="0" width="100%" height="100px"&gt;&lt;/iframe&gt;</textarea></div></div>';
	}
	
	$html .= '<div class="code_in_content"><pre class="brush: '. $snippet->snippet_type .'">' . stripslashes( $snippet->snippet_content) . '</pre></div></div>';
		
	return $html;
}


/* We don't want to add all the WordPress shorcode to certain type of area */
function bp_code_snippets_do_shortcode( $text, $type = false ){
	
	preg_match_all("/\[snippet id\=\"([0-9]+)\"\]/", $text, $matches, PREG_SET_ORDER);
	
	if( $matches ) {
		
		foreach( $matches as $snippet ) {
			$text = str_replace( '[snippet id="'.$snippet[1].'"]', bp_code_snippets_build_shortcode_response( array('id' => $snippet[1], 'type' => $type) ), $text );
			
		}
		
	}
	
	return $text;
}

function bp_code_snippets_do_embed_in_activity( $activity, $type = false ) {
	
	preg_match_all("/\[snippet id\=\"([0-9]+)\"\]/", $activity, $matches, PREG_SET_ORDER);
	
	if( $matches ) {
		
		if( !$type ) {
			foreach( $matches as $snippet ) {
				$activity = str_replace( '[snippet id="'.$snippet[1].'"]', '<iframe src="'. bp_get_root_domain() . '/' . bp_get_snippet_slug() . '/embed/' . $snippet[1] .'/' . '" frameborder="0" width="100%" height="100px"></iframe>', $activity );
			}
		} elseif( $type == 'link' ) {
			/* in case people directly paste shortcode in activity */
			foreach( $matches as $snippet ) {
				$activity_snippet = new BP_Code_Snippets( $snippet[1] );

				if( empty( $activity_snippet->object ) )
					return '';

				$link = bp_code_snippets_build_perma( array('id' => $activity_snippet->id, 'item_id' => $activity_snippet->item_id, 'object' => $activity_snippet->object, 'secondary_id' => $activity_snippet->secondary_id ) ) ;

				$activity = str_replace( '[snippet id="'.$snippet[1].'"]', '<span class="snippet_excerpt_link"><a href="'.$link.'" title="'. esc_html( $activity_snippet->title) .'">['.$activity_snippet->type.'] '. esc_html( $activity_snippet->title) .'</a></span>', $activity);
			}
			
		}
		
		return $activity;
	}
	
	else return $activity;
}

function bp_code_snippets_extract_snippet_id( $snippet_ids = false, $secondary_id = 0 ){
	global $wpdb, $bp;
	
	if( empty($snippet_ids) )
		return false;
		
	$snippet_ids = substr( $snippet_ids, 0, strlen( $snippet_ids ) - 1 );
	

	if ( is_array( $snippet_ids ) ) {
		$snippet_ids = implode ( ',', array_map( 'absint', $snippet_ids ) );
	} else {
		$snippet_ids = implode ( ',', array_map( 'absint', explode ( ',', $snippet_ids ) ) );
	}

	$where_conditions = "id_cs IN ({$snippet_ids})";
		
	$u = $wpdb->prepare( "UPDATE {$bp->snippets->table_cs} SET secondary_id = %d WHERE {$where_conditions}", $secondary_id );
	
	return $wpdb->query( $u );
	
}

function bp_code_snippets_update_blog_post_status( $secondary_id = false, $blog_id = false ){
	global $wpdb, $bp;
	
	if( empty($secondary_id) || empty($blog_id) )
		return false;
	
	$where_conditions = "secondary_id = {$secondary_id} AND item_id = {$blog_id} AND object = 'blog_post'";
	
	$u = $wpdb->prepare( "UPDATE {$bp->snippets->table_cs} SET is_draft = %d WHERE {$where_conditions}", 0 );
	
	return $wpdb->query( $u );
	
}

function bp_code_snippets_get_blog_post_data( $args = '' ){
	$defaults = array(
		'blog_id'      => false, // an existing blog_id.
		'post_id'      => false, // post id
		'comment'      => false,    // number of comments
		'title'        => false
	);
	
	$params = wp_parse_args( $args, $defaults );
	extract( $params, EXTR_SKIP );
	
	$blog_info = array();
	
	if( !$blog_id )
		return false;
	
	if( !$post_id )
		return false;
	
	if( is_multisite() ) {
			
			switch_to_blog( $blog_id );
			
			$status = get_post_status( $post_id );
			
			if( $status == 'publish' ) {
				$blog_info['permalink'] = get_permalink( $post_id ) ;
				$blog_info['title'] = get_the_title( $post_id ) ;
				$blog_info['comment_link'] = $blog_info['permalink'] . '#respond';
				$total_comment = get_comment_count( $post_id );
				$blog_info['comment_count'] = $total_comment['approved'];
			}
			$blog_info['name'] = get_bloginfo( 'name' ) ;
			$blog_info['url'] = get_bloginfo( 'url' ) ;
			
			restore_current_blog();
		
	} else {
		if( $title || $comment ) {
			
			$status = get_post_status( $post_id );
			
			if( $status == 'publish' ) {
				$blog_info['permalink'] = get_permalink( $post_id ) ;
				$blog_info['title'] = get_the_title( $post_id ) ;
				$blog_info['comment_link'] = $blog_info['permalink'] . '#respond';
				$total_comment = get_comment_count( $post_id );
				$blog_info['comment_count'] = $total_comment['approved'];
			}
			
			$blog_info['name'] = get_bloginfo( 'name' ) ;
			$blog_info['url'] = get_bloginfo( 'url' ) ;
			
		} else {
			if( 'publish' == get_post_status( $post_id ) )
				$blog_info['permalink'] = get_permalink( $post_id ) ;
		}
	}
	
	return $blog_info;
}


/** BuddyPress 1.7 Theme Compat **/
function bp_code_snippets_is_bp_default() {

	if( defined( 'BP_VERSION' ) && version_compare( BP_VERSION, '1.7-beta1-6797', '<' )  )
		return true;
		
	else if( in_array( 'bp-default', array( get_stylesheet(), get_template() ) ) )
		return true;
		
	else if( current_theme_supports( 'buddypress' ) )
		return true;
    
    else
        return false;
}

?>