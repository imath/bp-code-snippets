<?php

/**
 * BP Code Snippets Classes
 *
 * @package BP Code Snippets
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

Class BP_Code_Snippets {
	var $id;
	var $item_id;
	var $object;
	var $secondary_id;
	var $user_id;
	var $hide;
	var $is_draft;
	var $date_recorded;
	var $title;
	var $type;
	var $purpose;
	var $content;
	var $comment_count;
	var $comment_id;
	var $commenter_id;
	var $date_comment;
	var $comment_content;


	function bp_code_snippets( $id = false ) {
		$this->__construct( $id );
	}

	function __construct( $id = false ) {
		global $bp;

		if ( !empty( $id ) ) {
			$this->id = $id;
			$this->populate();
		}
	}

	function populate() {
		global $wpdb, $bp;

		if ( $row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$bp->snippets->table_cs} WHERE id_cs = %d", $this->id ) ) ) {
			$this->id                = $row->id_cs;
			$this->item_id           = $row->item_id;
			$this->object            = $row->object;
			$this->secondary_id      = $row->secondary_id;
			$this->user_id           = $row->id_user;
			$this->hide	             = $row->hide;
			$this->is_draft	         = $row->is_draft;
			$this->date_recorded     = $row->date_cs;
			$this->title             = $row->snippet_title;
			$this->type              = $row->snippet_type;
			$this->purpose           = $row->snippet_purpose;
			$this->content           = $row->snippet_content;
			$this->comment_count	 = $row->cs_comment_count;
		}
	}

	function save() {
		global $wpdb, $bp, $current_user;

		$this->id                = apply_filters_ref_array( 'bp_code_snippets_id_before_save',                array( $this->id,                &$this ) );
		$this->item_id           = apply_filters_ref_array( 'bp_code_snippets_item_id_before_save',           array( $this->item_id,           &$this ) );
		$this->object            = apply_filters_ref_array( 'bp_code_snippets_object_before_save',            array( $this->object,           &$this ) );
		$this->secondary_id      = apply_filters_ref_array( 'bp_code_snippets_object_before_save',            array( $this->secondary_id,           &$this ) );
		$this->user_id           = apply_filters_ref_array( 'bp_code_snippets_user_id_before_save',           array( $this->user_id,           &$this ) );
		$this->hide              = apply_filters_ref_array( 'bp_code_snippets_hide_before_save',              array( $this->hide,           &$this ) );
		$this->is_draft          = apply_filters_ref_array( 'bp_code_snippets_is_draft_before_save',          array( $this->is_draft,           &$this ) );
		$this->date_recorded     = apply_filters_ref_array( 'bp_code_snippets_date_recorded_before_save',     array( $this->date_recorded, &$this ) );
		$this->title             = apply_filters_ref_array( 'bp_code_snippets_title_before_save',             array( $this->title,  &$this ) );
		$this->type              = apply_filters_ref_array( 'bp_code_snippets_type_before_save',              array( $this->type,              &$this ) );
		$this->purpose           = apply_filters_ref_array( 'bp_code_snippets_purpose_before_save',           array( $this->purpose,            &$this ) );
		$this->content           = apply_filters_ref_array( 'bp_code_snippets_content_before_save',           array( $this->content,           &$this ) );
		$this->comment_count     = apply_filters_ref_array( 'bp_code_snippets_comment_count_before_save',     array( $this->comment_count,     &$this ) );

		// Use this, not the filters above
		do_action_ref_array( 'bp_code_snippets_before_save', array( &$this ) );

		if ( !$this->content || !$this->type )
			return false;

		// If we have an existing ID, update the activity item, otherwise insert it.
		if ( $this->id ) {
			
			$this->comment_count = BP_Code_Snippets::comment_count( $this->id );
			
			$q = $wpdb->prepare( "UPDATE {$bp->snippets->table_cs} SET item_id = %d, object = %s, secondary_id = %d, id_user = %d, hide = %d, is_draft = %d, date_cs = %s, snippet_title = %s, snippet_type = %s, snippet_purpose = %s, snippet_content = %s, cs_comment_count = %d WHERE id_cs = %d", $this->item_id, $this->object, $this->secondary_id, $this->user_id, $this->hide, $this->is_draft, $this->date_recorded, $this->title, $this->type, $this->purpose, $this->content, $this->comment_count, $this->id );
		}	
		else
			$q = $wpdb->prepare( "INSERT INTO {$bp->snippets->table_cs} ( item_id, object, secondary_id, id_user, hide, is_draft, date_cs, snippet_title, snippet_type, snippet_purpose, snippet_content, cs_comment_count ) VALUES ( %d, %s, %d, %d, %d, %d, %s, %s, %s, %s, %s, %d )", $this->item_id, $this->object, $this->secondary_id, $this->user_id, $this->hide, $this->is_draft, $this->date_recorded, $this->title, $this->type, $this->purpose, $this->content, $this->comment_count );

		if ( !$wpdb->query( $q ) )
			return false;

		if ( empty( $this->id ) )
			$this->id = $wpdb->insert_id;

		do_action_ref_array( 'bp_code_snippets_after_save', array( &$this ) );

		return true;
	}
	
	function save_comment() {
		global $wpdb, $bp, $current_user;
		
		$this->comment_id      = apply_filters_ref_array( 'bp_code_snippets_comment_id_before_save_comment',      array( $this->comment_id, &$this ) );
		$this->id              = apply_filters_ref_array( 'bp_code_snippets_id_before_save_comment',              array( $this->id, &$this ) );
		$this->commenter_id    = apply_filters_ref_array( 'bp_code_snippets_commenter_id_before_save_comment',    array( $this->commenter_id, &$this ) );
		$this->date_comment    = apply_filters_ref_array( 'bp_code_snippets_date_comment_before_save_comment',    array( $this->date_comment, &$this ) );
		$this->comment_content = apply_filters_ref_array( 'bp_code_snippets_comment_content_before_save_comment', array( $this->comment_content, &$this ) );
		$this->comment_count   = apply_filters_ref_array( 'bp_code_snippets_comment_count_before_save_comment',   array( $this->comment_count, &$this ) );
		
		// Use this, not the filters above
		do_action_ref_array( 'bp_code_snippets_before_save_comment', array( &$this ) );
		
		if ( !$this->id || !$this->comment_content )
			return false;
			
		if( $this->comment_id )
			return false;
			
		$c = $wpdb->prepare( "INSERT INTO {$bp->snippets->table_cs_comment} ( cs_id, user_id, date_comment, comment_content ) VALUES ( %d, %d, %s, %s )", $this->id, $this->commenter_id, $this->date_comment, $this->comment_content );

		if ( !$wpdb->query( $c ) )
			return false;

		$this->comment_id = $wpdb->insert_id;

		do_action_ref_array( 'bp_code_snippets_after_save_comment', array( &$this ) );
		
		$u = $wpdb->prepare( "UPDATE {$bp->snippets->table_cs} SET cs_comment_count = %d WHERE id_cs = %d", $this->comment_count, $this->id );
		
		$wpdb->query( $u );
		
		do_action_ref_array( 'bp_code_snippets_after_save_update_comment_count', array( &$this ) );

		return true;
		
	}
	
	function remove_comment() {
		global $wpdb, $bp, $current_user;
		
		$this->comment_id = apply_filters_ref_array( 'bp_code_snippets_comment_id_before_del_comment',      array( $this->comment_id, &$this ) );
		$this->id         = apply_filters_ref_array( 'bp_code_snippets_id_before_del_comment',              array( $this->id, &$this ) );
		
		// Use this, not the filters above
		do_action_ref_array( 'bp_code_snippets_before_del_comment', array( &$this ) );
		
		if ( !$this->id || !$this->comment_id )
			return false;
			
		$c = $wpdb->prepare( "DELETE FROM {$bp->snippets->table_cs_comment} WHERE id_cs_comment = %d", $this->comment_id );

		if ( !$wpdb->query( $c ) )
			return false;

		do_action_ref_array( 'bp_code_snippets_after_del_comment', array( &$this ) );
		
		$new_comment_count = $this->comment_count - 1;
		
		$u = $wpdb->prepare( "UPDATE {$bp->snippets->table_cs} SET cs_comment_count = %d WHERE id_cs = %d", $new_comment_count, $this->id );
		
		$wpdb->query( $u );
		
		/*$d = $wpdb->prepare( "DELETE FROM {$bp->snippets->table_cs} WHERE secondary_id = {$this->comment_id} AND object IN('group_comment','directory_comment')" );*/
		$d = $wpdb->prepare( "DELETE FROM {$bp->snippets->table_cs} WHERE secondary_id = %d AND object IN('group_comment','directory_comment')", $this->comment_id );
		
		$wpdb->query( $d );
		
		do_action_ref_array( 'bp_code_snippets_after_del_update_comment_count', array( &$this ) );

		return true;
		
	}

	// Static Functions
	function get( $max = false, $page = 1, $per_page = 25, $sort = 'DESC', $search_terms = false, $filter = false, $display_comments = false, $show_hidden = false, $exclude = false, $in = false, $draft = false ) {
		global $wpdb, $bp;

		// Select conditions
		$select_sql = "SELECT a.*, u.user_email, u.user_nicename, u.user_login, u.display_name";

		$from_sql = " FROM {$bp->snippets->table_cs} a LEFT JOIN {$wpdb->users} u ON a.id_user = u.ID";

		// Where conditions
		$where_conditions = array();

		// Searching
		if ( $search_terms ) {
			$search_terms = $wpdb->escape( $search_terms );
			$where_conditions['search_sql'] = "( a.snippet_purpose LIKE '%%" . like_escape( $search_terms ) . "%%' OR a.snippet_title LIKE '%%" . like_escape( $search_terms ) . "%%' )";
		}

		/* Filtering*/
		
		if ( $filter && $filter_sql = BP_Code_Snippets::get_filter_sql( $filter ) )
			$where_conditions['filter_sql'] = $filter_sql;
			

		// Sorting
		if ( $sort != 'ASC' && $sort != 'DESC' )
			$sort = 'DESC';
			
		if ( !$show_hidden )
			$where_conditions['hidden_sql'] = "a.hide = 0";
			
		if ( !$draft )
			$where_conditions['draft_sql'] = "a.is_draft = 0";
		

		// Exclude specified items
		if ( $exclude )
			$where_conditions['exclude'] = "a.id_cs NOT IN ({$exclude})";

		// The specific ids to which you want to limit the query
		if ( !empty( $in ) ) {
			if ( is_array( $in ) ) {
				$in = implode ( ',', array_map( 'absint', $in ) );
			} else {
				$in = implode ( ',', array_map( 'absint', explode ( ',', $in ) ) );
			}

			$where_conditions['in'] = "a.id_cs IN ({$in})";
		}
		
		if(count($where_conditions) >= 1)
			$where_sql = 'WHERE ' . join( ' AND ', $where_conditions );

		if ( $per_page && $page ) {
			$pag_sql = $wpdb->prepare( "LIMIT %d, %d", intval( ( $page - 1 ) * $per_page ), intval( $per_page ) );
			$snippets = $wpdb->get_results( apply_filters( 'bp_code_snippets_get_user_join_filter', "{$select_sql} {$from_sql} {$where_sql} ORDER BY a.date_cs {$sort} {$pag_sql}", $select_sql, $from_sql, $where_sql, $sort, $pag_sql ) );
			/*$snippets = $wpdb->get_results( apply_filters( 'bp_code_snippets_get_user_join_filter', $wpdb->prepare( "{$select_sql} {$from_sql} {$where_sql} ORDER BY a.date_cs {$sort} {$pag_sql}" ), $select_sql, $from_sql, $where_sql, $sort, $pag_sql ) );*/
		} else {
			$snippets = $wpdb->get_results( apply_filters( 'bp_code_snippets_get_user_join_filter', "{$select_sql} {$from_sql} {$where_sql} ORDER BY a.date_cs {$sort}", $select_sql, $from_sql, $where_sql, $sort ) );
			/*$snippets = $wpdb->get_results( apply_filters( 'bp_code_snippets_get_user_join_filter', $wpdb->prepare( "{$select_sql} {$from_sql} {$where_sql} ORDER BY a.date_cs {$sort}" ), $select_sql, $from_sql, $where_sql, $sort ) );*/
		}

		$total_snippets_sql = apply_filters( 'bp_code_snippets_total_snippets_sql', "SELECT count(a.id_cs) FROM {$bp->snippets->table_cs} a {$where_sql} ORDER BY a.date_cs {$sort}", $where_sql, $sort );
		/*$total_snippets_sql = apply_filters( 'bp_code_snippets_total_snippets_sql', $wpdb->prepare( "SELECT count(a.id_cs) FROM {$bp->snippets->table_cs} a {$where_sql} ORDER BY a.date_cs {$sort}" ), $where_sql, $sort );*/

		$total_snippets = $wpdb->get_var( $total_snippets_sql );

		// Get the fullnames of users so we don't have to query in the loop
		if ( bp_is_active( 'xprofile' ) && $snippets ) {
			foreach ( (array)$snippets as $snippet ) {
				if ( !empty( $snippet->user_id ) )
					$snippet_user_ids[] = intval( $snippet->user_id );
			}

			if ( !empty( $snippet_user_ids ) ) {
				$snippet_user_ids = implode( ',', array_unique( (array)$snippet_user_ids ) );
				
				if ( $names = $wpdb->get_results( "SELECT user_id, value AS user_fullname FROM {$bp->profile->table_name_data} WHERE field_id = 1 AND user_id IN ({$snippet_user_ids})" ) ) {
					foreach ( (array)$names as $name )
						$tmp_names[$name->user_id] = $name->user_fullname;

					foreach ( (array)$snippets as $i => $snippet ) {
						if ( !empty( $tmp_names[$snippet->user_id] ) )
							$snippets[$i]->user_fullname = $tmp_names[$snippet->user_id];
					}

					unset( $names );
					unset( $tmp_names );
				}
			}
		}
			
		//attach favorite count from all users
		if( $snippets )
			$snippets = BP_Code_Snippets::attach_favorites_count( $snippets );
			
		if ( $snippets && $display_comments )
			$snippets = BP_Code_Snippets::append_comments( $snippets );
			

		// If $max is set, only return up to the max results
		if ( !empty( $max ) ) {
			if ( (int)$total_snippets > (int)$max )
				$total_snippets = $max;
		}
		
		return array( 'snippets' => $snippets, 'total' => (int)$total_snippets );
	}

	function delete( $args ) {
		global $wpdb, $bp;

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
		$params = wp_parse_args( $args, $defaults );
		extract( $params );

		$where_args = false;
		$where_comments = false;

		if ( !empty( $id ) )
			$where_args[] = $wpdb->prepare( "id_cs = %d", $id );
			
		if ( !empty( $item_id ) )
			$where_args[] = $wpdb->prepare( "item_id = %d", $item_id );

		if ( !empty( $object ) ) {
			
				if ( is_array( $object ) ) {
					$object = implode ( "','", $object );
				} else {
					$object = implode ( "','", explode ( ',', $object ) );
				}
				$object = "'".$object."'";
				$where_args[] = "object IN ({$object})";
				
		}
			
		if ( !empty( $secondary_id ) )
			$where_args[] = $wpdb->prepare( "secondary_id = %d", $secondary_id );
		
		if ( !empty( $user_id ) )
			$where_args[] = $wpdb->prepare( "id_user = %d", $user_id );

		if ( !empty( $date_recorded ) )
			$where_args[] = $wpdb->prepare( "date_cs = %s", $date_recorded );
			
		if ( !empty( $title ) )
			$where_args[] = $wpdb->prepare( "snippet_title = %s", $title );
			
		if ( !empty( $type ) )
			$where_args[] = $wpdb->prepare( "snippet_type = %s", $type );
			
		if ( !empty( $purpose ) )
			$where_args[] = $wpdb->prepare( "snippet_purpose = %s", $purpose );

		if ( !empty( $content ) )
			$where_args[] = $wpdb->prepare( "content = %s", $content );

		if ( !empty( $comment_count ) )
			$where_args[] = $wpdb->prepare( "comment_count = %d", $comment_count );

		if ( !empty( $where_args ) )
			$where_sql = 'WHERE ' . join( ' AND ', $where_args );
		else
			return false;

		/* Fetch the snippet IDs so we can delete any comments for this activity item
		
			in case a blog, group, forum topic is deleted...
		*/
		
		$snippet_ids = $wpdb->get_col( "SELECT id_cs FROM {$bp->snippets->table_cs} {$where_sql}" );
		/*$snippet_ids = $wpdb->get_col( $wpdb->prepare( "SELECT id_cs FROM {$bp->snippets->table_cs} {$where_sql}" ) );*/
		
		if ( !$wpdb->query( "DELETE FROM {$bp->snippets->table_cs} {$where_sql}" ) )
			return false;
		/*if ( !$wpdb->query( $wpdb->prepare( "DELETE FROM {$bp->snippets->table_cs} {$where_sql}" ) ) )
			return false;*/
		
		if ( $snippet_ids ) {
			BP_Code_Snippets::delete_snippets_item_comments( $snippet_ids );
			BP_Code_Snippets::delete_snippets_meta_entries( $snippet_ids );

			return $snippet_ids;
		}

		return $snippet_ids;
	}

	function delete_snippets_item_comments( $snippet_ids ) {
		global $bp, $wpdb;

		if ( is_array( $snippet_ids ) )
			$snippet_ids = implode ( ',', array_map( 'absint', $snippet_ids ) );
		else
			$snippet_ids = implode ( ',', array_map( 'absint', explode ( ',', $snippet_ids ) ) );
			
		if(!empty($snippet_ids))
			$comment_ids = $wpdb->get_col( "SELECT id_cs_comment FROM {$bp->snippets->table_cs_comment} WHERE cs_id IN ({$snippet_ids})" );
			/*$comment_ids = $wpdb->get_col( $wpdb->prepare( "SELECT id_cs_comment FROM {$bp->snippets->table_cs_comment} WHERE cs_id IN ({$snippet_ids})" ) );*/
			
		if( $comment_ids )
			BP_Code_Snippets::delete_snippets_commented_snippets( $comment_ids );
		
		return $wpdb->query( "DELETE FROM {$bp->snippets->table_cs_comment} WHERE cs_id IN ({$snippet_ids})" );
		/*return $wpdb->query( $wpdb->prepare( "DELETE FROM {$bp->snippets->table_cs_comment} WHERE cs_id IN ({$snippet_ids})" ) );*/
	}

	function delete_snippets_meta_entries( $snippet_ids ) {
		global $bp, $wpdb;

		if ( is_array( $snippet_ids ) )
			$snippet_ids = implode ( ',', array_map( 'absint', $snippet_ids ) );
		else
			$snippet_ids = implode ( ',', array_map( 'absint', explode ( ',', $snippet_ids ) ) );
		
		return $wpdb->query( "DELETE FROM {$bp->snippets->table_cs_meta} WHERE snippet_id IN ({$snippet_ids})" );
		/*return $wpdb->query( $wpdb->prepare( "DELETE FROM {$bp->snippets->table_cs_meta} WHERE snippet_id IN ({$snippet_ids})" ) );*/
	}
	
	function delete_snippets_commented_snippets( $comment_ids ) {
		global $bp, $wpdb;

		if ( is_array( $comment_ids ) )
			$comment_ids = implode ( ',', array_map( 'absint', $comment_ids ) );
		else
			$comment_ids = implode ( ',', array_map( 'absint', explode ( ',', $comment_ids ) ) );

		return $wpdb->query( "DELETE FROM {$bp->snippets->table_cs} WHERE secondary_id IN ({$comment_ids}) AND object IN('group_comment','directory_comment')" );
		/*return $wpdb->query( $wpdb->prepare( "DELETE FROM {$bp->snippets->table_cs} WHERE secondary_id IN ({$comment_ids}) AND object IN('group_comment','directory_comment')" ) );*/
		
	}
	
	function attach_favorites_count( $snippets ) {
		$snippets_favorites_count = array();
		
		foreach( (array)$snippets as $key => $snippet ){
			$snippets_favorites_count = bp_code_snippets_get_meta( $snippet->id_cs, 'favsnipt_count' );
			
			if ( isset( $snippets_favorites_count ) )
				$snippets[$key]->favcount = intval($snippets_favorites_count);
			else $snippets[$key]->favcount = 0;
		}
		
		return $snippets;
	}
	
	function append_comments( $snippets ) {
		global $bp, $wpdb;
		
		$snippet_comments = array();

		/* Now fetch the snippet comments and parse them into the correct position in the snippets array. */
		foreach( (array)$snippets as $snippet ) {
			if( $snippet->cs_comment_count > 0 )
				$snippet_comments[$snippet->id_cs] = BP_Code_Snippets::get_snippet_comments( $snippet->id_cs );
		}

		/* Merge the comments with the snippets items */
		foreach( (array)$snippets as $key => $snippet ) {
			if ( isset( $snippet_comments[$snippet->id_cs] ) )
				$snippets[$key]->comments = $snippet_comments[$snippet->id_cs];
		}
		
		return $snippets;
	}

	function get_snippet_comments( $snippet_id ) {
		global $wpdb, $bp;

		// Select the user's fullname with the query
		if ( bp_is_active( 'xprofile' ) ) {
			$fullname_select = ", pd.value as user_fullname";
			$fullname_from = ", {$bp->profile->table_name_data} pd ";
			$fullname_where = "AND pd.user_id = a.user_id AND pd.field_id = 1";

			// Prevent debug errors
		} else {
			$fullname_select = $fullname_from = $fullname_where = '';
		}

		// Retrieve the comments and the needed commenter infos
		$commenters = $wpdb->get_results( $wpdb->prepare( "SELECT a.*, u.user_email, u.user_nicename, u.user_login, u.display_name{$fullname_select} FROM {$bp->snippets->table_cs_comment} a, {$wpdb->users} u{$fullname_from} WHERE u.ID = a.user_id {$fullname_where} AND a.cs_id = %d ORDER BY a.date_comment ASC", $snippet_id ) );

		// Loop descendants and build an assoc array
		foreach ( (array)$commenters as $c ) {
			//$c->children = array();
			$comments[ $c->id_cs_comment ] = $c;
			$ref[ $c->id_cs_comment ] =& $comments[ $c->id_cs_comment ];
		}

		return $comments;
	}

	function get_in_operator_sql( $field, $items ) {
		global $wpdb;

		// split items at the comma
		$items_dirty = explode( ',', $items );

		// array of prepared integers or quoted strings
		$items_prepared = array();

		// clean up and format each item
		foreach ( $items_dirty as $item ) {
			// clean up the string
			$item = trim( $item );
			// pass everything through prepare for security and to safely quote strings
			$items_prepared[] = ( is_numeric( $item ) ) ? $wpdb->prepare( '%d', $item ) : $wpdb->prepare( '%s', $item );
		}

		// build IN operator sql syntax
		if ( count( $items_prepared ) )
			return sprintf( '%s IN ( %s )', trim( $field ), implode( ',', $items_prepared ) );
		else
			return false;
	}
	
	function get_filter_sql( $filter_array ) {
		global $wpdb;

		if ( !empty( $filter_array['user_id'] ) ) {
			$user_sql = BP_Code_Snippets::get_in_operator_sql( 'a.id_user', $filter_array['user_id'] );
			if ( !empty( $user_sql ) )
				$filter_sql[] = $user_sql;
		}

		if ( !empty( $filter_array['object'] ) ) {
			$object_sql = BP_Code_Snippets::get_in_operator_sql( 'a.object', $filter_array['object'] );
			if ( !empty( $object_sql ) )
				$filter_sql[] = $object_sql;
		}
		
		if ( !empty( $filter_array['category'] ) ) {
			$category_sql = BP_Code_Snippets::get_in_operator_sql( 'a.snippet_type', $filter_array['category'] );
			if ( !empty( $category_sql ) )
				$filter_sql[] = $category_sql;
		}

		if ( !empty( $filter_array['primary_id'] ) ) {
			$pid_sql = BP_Code_Snippets::get_in_operator_sql( 'a.item_id', $filter_array['primary_id'] );
			if ( !empty( $pid_sql ) )
				$filter_sql[] = $pid_sql;
		}

		if ( !empty( $filter_array['secondary_id'] ) ) {
			$sid_sql = BP_Code_Snippets::get_in_operator_sql( 'a.secondary_id', $filter_array['secondary_id'] );
			if ( !empty( $sid_sql ) )
				$filter_sql[] = $sid_sql;
		}

		if ( empty($filter_sql) )
			return false;

		return join( ' AND ', $filter_sql );
	}
	
	function get_last_updated() {
		global $bp, $wpdb;

		return $wpdb->get_var( "SELECT date_cs FROM {$bp->snippets->table_cs} ORDER BY date_cs DESC LIMIT 1" );
		/*return $wpdb->get_var( $wpdb->prepare( "SELECT date_cs FROM {$bp->snippets->table_cs} ORDER BY date_cs DESC LIMIT 1" ) );*/
	}
	
	function comment_count( $snippet_id ) {
		global $bp, $wpdb;
		
		if( empty( $snippet_id ) )
			return false;
			
		return $wpdb->get_var( $wpdb->prepare( "SELECT cs_comment_count FROM {$bp->snippets->table_cs} WHERE id_cs = %d", intval( $snippet_id ) ) );
	}
	
	function total_favorite_count( $user_id ) {
		global $wpdb, $bp;
		if ( !$favorite_snippet_entries = bp_get_user_meta( $user_id, 'bp_favorite_snippets', true ) )
			return 0;
		
		// Let's check if snippet has not been deleted !
		if ( !empty( $favorite_snippet_entries ) ) {
			$where = false;
			if ( is_array( $favorite_snippet_entries ) ) {
				$in = implode ( ',', array_map( 'absint', $favorite_snippet_entries ) );
			}
			
			$where = "WHERE id_cs IN ({$in})";
			
			$snippet_in_db = $wpdb->get_col( "SELECT id_cs FROM {$bp->snippets->table_cs} {$where}" );
			/*$snippet_in_db = $wpdb->get_col( $wpdb->prepare( "SELECT id_cs FROM {$bp->snippets->table_cs} {$where}" ) );*/
			
			if( count( $snippet_in_db ) != count( $favorite_snippet_entries ) ){
				
				$favorite_snippet_entries = $snippet_in_db;
				bp_update_user_meta( $user_id, 'bp_favorite_snippets', $favorite_snippet_entries );
				
			}
	
		}

		return count( $favorite_snippet_entries );
	}
	
	function widget_most_favorited( $limit ) {
		global $wpdb, $bp;

		return $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$bp->snippets->table_cs_meta} m LEFT JOIN {$bp->snippets->table_cs} s ON( m.snippet_id = s.id_cs ) WHERE m.meta_key = 'favsnipt_count' AND s.hide = 0 AND s.is_draft = 0 ORDER BY m.meta_value DESC LIMIT %d", intval( $limit ) ) );
	}

}

?>