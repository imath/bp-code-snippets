<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

function bp_code_snippets_directory_load_snippet_cssjs(){
	
	if( is_active_widget( false, false, 'bp_code_snippets_most_fav' ) ){
		wp_enqueue_style( 'bp-cs-widget-style', BP_CS_PLUGIN_URL_CSS . '/bp-cs-widget.css');
	}
	
	if( bp_is_current_component( 'snippets' ) || (bp_is_current_component( 'groups' ) && bp_is_current_action( 'snippets' ) ) ){
		if( !in_array( bp_current_action(), array( 'embed', 'add' ) ) )
			wp_enqueue_script( 'bp-code-snippets', BP_CS_PLUGIN_URL_JS . '/bp-code-snippets.js', array('jquery') );
			
		if( bp_is_current_action( 'embed' ) )
			wp_enqueue_script( 'bp-cs-oembed', BP_CS_PLUGIN_URL_JS . '/bp-cs-oembed.js', array('jquery') );
			
		if( bp_is_current_action( 'add' ) )
			wp_enqueue_script( 'bp-cs-add', BP_CS_PLUGIN_URL_JS . '/bp-cs-add.js', array('jquery') );
		
		
		wp_enqueue_style( 'bp-cs-style', BP_CS_PLUGIN_URL_CSS . '/bp-cs.css');
		add_action( 'wp_head', 'bp_code_snippets_form_error_messages');
		
		/* if on single template */
		if( bp_is_current_action( 'permasnipt' ) || bp_is_current_action( 'embed' ) || is_numeric( bp_action_variable( 0 ) ) ){
			
			if( bp_action_variable( 1 ) == 'edit')
				return false;
			
			$theme_activated = get_option( 'cs-setting-theme' );
			
			wp_enqueue_script( 'shCore', BP_CS_PLUGIN_URL_JS . '/highlighter/shCore.js' );
			wp_enqueue_script( 'shAutoloader', BP_CS_PLUGIN_URL_JS . '/highlighter/shAutoloader.js' );
			
			wp_enqueue_style('shCore', BP_CS_PLUGIN_URL_CSS . '/shCore.css');
			wp_enqueue_style('shTheme', BP_CS_PLUGIN_URL_CSS . '/theme/'.$theme_activated);
			
			// if we're loading comment template, then load thickbox to allow adding snippets in comment.
			if( !in_array( bp_current_action(), array( 'embed', 'add' ) ) ) {
				wp_enqueue_style('thickbox');
				wp_enqueue_script('thickbox');
				
				add_action( 'wp_footer', 'bp_code_snippets_fix_thickbox_ui' );
			}
			
			add_action( 'wp_footer', 'bp_code_snippets_autoload_brushes');
		}
		
	}
	
	if( bp_is_forums_component() || (bp_is_group_forum() && bp_is_current_action( 'forum' ) ) ){
		global $bp;
		
		if( bp_displayed_user_id() )
			return false;
			
		if( !bp_code_snippets_is_forum_snippets_enable() )
			return false;
			
		if( !empty( $bp->groups->current_group->id ) && !bp_group_snippets_is_snippet_enabled( $bp->groups->current_group->id ) )
			return false;
			
		wp_enqueue_style('thickbox');
		wp_enqueue_script('thickbox');
		wp_enqueue_script( 'bp-cs-forum', BP_CS_PLUGIN_URL_JS . '/bp-cs-forum.js', array('jquery') );
		wp_enqueue_style( 'bp-cs-style', BP_CS_PLUGIN_URL_CSS . '/bp-cs.css');
		
		add_action( 'wp_head', 'bp_code_snippets_form_error_messages');
		add_action( 'wp_footer', 'bp_code_snippets_fix_thickbox_ui' );
	}

}

add_action( 'bp_actions', 'bp_code_snippets_directory_load_snippet_cssjs', 11 );
add_action( 'bp_forums_directory_forums_setup', 'bp_code_snippets_directory_load_snippet_cssjs', 11);
add_action( 'groups_screen_group_forum', 'bp_code_snippets_directory_load_snippet_cssjs', 11);

function bp_code_snippets_load_topic_screen_cssjs(){
	global $bp;
	if( bp_is_group_forum() && bp_is_current_action( 'forum' ) && bp_is_action_variable( 'topic', 0 ) ) {

		if( !bp_code_snippets_is_forum_snippets_enable() )
			return false;
		
		if( !bp_group_snippets_is_snippet_enabled( $bp->groups->current_group->id ) )
			return false;
		
		
		wp_enqueue_style('thickbox');
		wp_enqueue_style( 'bp-cs-style', BP_CS_PLUGIN_URL_CSS . '/bp-cs.css');
		wp_enqueue_script('thickbox');
		wp_enqueue_script( 'bp-cs-forum', BP_CS_PLUGIN_URL_JS . '/bp-cs-forum.js', array('jquery') );

		if( !bp_is_action_variable( 'edit' ) ){
			wp_enqueue_script( 'bp-code-snippets', BP_CS_PLUGIN_URL_JS . '/bp-code-snippets.js', array('jquery') );
			
			$theme_activated = get_option( 'cs-setting-theme' );

			wp_enqueue_script( 'shCore', BP_CS_PLUGIN_URL_JS . '/highlighter/shCore.js' );
			wp_enqueue_script( 'shAutoloader', BP_CS_PLUGIN_URL_JS . '/highlighter/shAutoloader.js' );
			
			wp_enqueue_style('shCore', BP_CS_PLUGIN_URL_CSS . '/shCore.css');
			wp_enqueue_style('shTheme', BP_CS_PLUGIN_URL_CSS . '/theme/'.$theme_activated);
			
			add_action( 'wp_footer', 'bp_code_snippets_autoload_brushes');
		}
		add_action( 'wp_head', 'bp_code_snippets_form_error_messages');
		add_action( 'wp_footer', 'bp_code_snippets_fix_thickbox_ui' );
		
	}
}

add_action( 'bp_actions', 'bp_code_snippets_load_topic_screen_cssjs', 10);

function bp_code_snippets_load_blogpost_screen_cssjs() {
	
	if( !bp_code_snippets_child_blog_snpt_ok() && !bp_is_root_blog() )
		return false;
	
	if( is_single() || ( is_page() && !is_front_page() ) ) {
		
		if( !bp_code_snippets_is_blog_snippets_enable() )
			return false;
			
		if( is_multisite() )
			$theme_activated = get_blog_option( 1, 'cs-setting-theme' );
		
		else
			$theme_activated = get_option( 'cs-setting-theme' );
		
		wp_enqueue_script( 'bp-cs-oembed', BP_CS_PLUGIN_URL_JS . '/bp-cs-oembed.js', array('jquery') );
		wp_enqueue_style( 'bp-cs-child', BP_CS_PLUGIN_URL_CSS . '/bp-cs-child.css');

		wp_enqueue_script( 'shCore', BP_CS_PLUGIN_URL_JS . '/highlighter/shCore.js' );
		wp_enqueue_script( 'shAutoloader', BP_CS_PLUGIN_URL_JS . '/highlighter/shAutoloader.js' );
		wp_enqueue_style( 'shCore', BP_CS_PLUGIN_URL_CSS . '/shCore.css');
		wp_enqueue_style( 'shTheme', BP_CS_PLUGIN_URL_CSS . '/theme/'.$theme_activated);
		
		add_action( 'wp_footer', 'bp_code_snippets_autoload_brushes');
		
	}
}

add_action( 'template_redirect', 'bp_code_snippets_load_blogpost_screen_cssjs');

function bp_code_snippets_autoload_brushes(){
	global $bp;
	$autoloaded = array();
	$liste_js = array("php"=>"shBrushPhp", "js"=>"shBrushJscript", "css"=>"shBrushCss", "as3"=>"shBrushAS3", "sql"=>"shBrushSql", "xml"=>"shBrushXml", "java"=>"shBrushJava", "jfx"=>"shBrushJavaFX", "pl"=>"shBrushPerl", "py"=>"shBrushPython", "ruby"=>"shBrushRuby", "cf"=>"shBrushColdFusion", "applescript"=>"shBrushAppleScript", "cpp"=>"shBrushCpp", "csharp"=>"shBrushCSharp", "vb"=>"shBrushVb", "bash"=>"shBrushBash");
	
	if( bp_is_active( 'groups' ) && bp_is_current_component( 'groups' ) && !bp_group_snippets_is_snippet_enabled($bp->groups->current_group->id) )
		return false;
	if( bp_is_active( 'groups' ) && bp_is_current_component( 'groups' ) )
		$cs_languages = bp_group_snippets_get_available_languages_for_group( $bp->groups->current_group->id );
	if( !bp_is_current_component( 'groups' ) )
		$cs_languages = bp_group_snippets_get_available_languages();
	
	foreach($cs_languages as $code){
		$autoload[] = "'".$code."  " . BP_CS_PLUGIN_URL_JS . "/highlighter/".$liste_js[$code].".js'";
	}
	?>
	<script type="text/javascript">
	SyntaxHighlighter.autoloader(
	  <?php echo implode(",\n", $autoload);?>
	);

	SyntaxHighlighter.all();
	</script>
	<?php
}

function bp_code_snippets_fix_thickbox_ui() {
	?>
	<script type="text/javascript">
		if ( typeof tb_pathToImage != 'string' )
		{
		    var tb_pathToImage = "<?php echo get_bloginfo('url').'/wp-includes/js/thickbox'; ?>/loadingAnimation.gif";
		}
		if ( typeof tb_closeImage != 'string' )
		{
		    var tb_closeImage = "<?php echo get_bloginfo('url').'/wp-includes/js/thickbox'; ?>/tb-close.png";
		}
		</script>
	<?php
}

function bp_code_snippets_form_error_messages() {
	?>
	<script type="text/javascript">
		var bpcs_message_title = "<?php _e('Please, add a title to your snippet','bp-code-snippets');?>";
		var bpcs_message_desc = "<?php _e('Please, add a description of your snippet','bp-code-snippets');?>";
		var bpcs_message_cat = "<?php _e('Please, select a language category for your snippet','bp-code-snippets');?>";
		var bpcs_message_content = "<?php _e('Please, add the snippet you want to share','bp-code-snippets');?>";
		var bpcs_message_select_group = "<?php _e('Please select a group for your forum post', 'bp-code-snippets');?>";
	</script>
	<?php
}

function bp_code_snippets_oembed_only_in_iframe() {
	?>
	<script type="text/javascript">
	jQuery(document).ready(function($){
		// only in an iframe...
		if( top.location == window.location) 
			window.location.href = $('.snipt-perma').val();
	});	
	</script>
	<?php
}

add_action( 'bp_code_snippets_head_embed', 'bp_code_snippets_oembed_only_in_iframe' );

function bp_code_snippets_directory_handle_snippet_post(){
	global $bp;

	if ( isset( $_POST['submit_snippet'] ) && bp_is_active( 'snippets' ) ) {
		
		// check snippet nonce
		check_admin_referer( 'bp_snippets_new_snippet' );
		
		$snippet_id = $object = $item_id = $secondary_id = $edit_id = false;
		
		$redirect = remove_query_arg( array('s','snptcat','n'), wp_get_referer() );
		// Get snippet info
		$edit_id = false;
		
		if( !empty( $_POST['snippet_edit_id'] ) ){
			$edit_id = apply_filters( 'bp_code_snippets_edit_snippet_id', $_POST['snippet_edit_id'] );
		}
		
		$title   = apply_filters( 'bp_code_snippets_post_snippet_title', $_POST['snippet_title'] );
		$type    = apply_filters( 'bp_code_snippets_post_snippet_type', $_POST['_bp_cs_source'] );
		$purpose = apply_filters( 'bp_code_snippets_post_snippet_purpose', $_POST['snippet_purpose'] );
		$content = apply_filters( 'bp_code_snippets_post_snippet_content', $_POST['snippet_content'] );
		
		if( isset($_POST['snippet_group_id']) )
			$item_id = apply_filters( 'bp_code_snippets_post_snippet_group_id', $_POST['snippet_group_id'] );
		
		if( isset($_POST['snippet_type_object']) )
			$object = apply_filters( 'bp_code_snippets_post_snippet_object_id', $_POST['snippet_type_object'] );
			
		if( isset($_POST['snippet_secondary_id']) )
			$secondary_id = apply_filters( 'bp_code_snippets_post_snippet_secondary_id', $_POST['snippet_secondary_id'] );

		// No snippet content, type or title so provide feedback and redirect
		if ( empty( $title ) || empty( $type ) || empty( $content ) ) {
			bp_core_add_message( __( 'Please enter some title / category and content to snippet.', 'bp-code-snippets' ), 'error' );
			bp_core_redirect( $redirect . '#message');
		}
		
		// Post to groups or blogs object
		if ( isset( $item_id ) && $item_id > 0 ) {
			
			if ( (int)$item_id >= 1) {
					
				if( empty( $secondary_id ) )
					$secondary_id = 0;
				
				if( bp_is_active( 'groups' ) && $object != "blog_post" ) {
					
					if( empty( $object ) )
						$object = 'group';
					
					// Auto join this user if they are not yet a member of this group
					if( isset( $bp->groups->current_group->status ) ) {
						$status = $bp->groups->current_group->status;
					} else {
						$group = groups_get_group( array( 'group_id' => $item_id ) );
						$status = $group->status;
					}
					if ( bp_groups_auto_join() && !is_super_admin() && 'public' == $status && !groups_is_user_member( $bp->loggedin_user->id, $item_id ) )
						groups_join_group( $item_id, $bp->loggedin_user->id );
					
				}
				
				$snippet_id = bp_code_snippets_item_post_update( array( 'snippet_edit_id' => $edit_id,
																		'title'           => $title,
																		'type'            => $type,
																		'purpose'         => $purpose,
																		'content'         => $content,
																		'item_id'         => $item_id,
																		'object'          => $object,
																		'secondary_id'    => $secondary_id) );
				
				if( $object == 'group' && !empty( $snippet_id ) && !bp_is_current_action( 'add' ) && !bp_is_current_component( 'bookmarklet' ) )														
					$redirect = bp_code_snippets_build_perma( array( 'id' => $snippet_id, 'item_id' => $item_id, 'object' => 'group') );
			}
			
		} else if ( empty($item_id) ) {
			if( empty( $object ) )
				$object = 'directory';
				
			$snippet_id = bp_code_snippets_post_snippet( array( 'snippet_edit_id' => $edit_id,
																'title'           => $title,
																'type'            => $type,
																'purpose'         => $purpose,
																'content'         => $content,
																'object'          => $object ) );
											
			if( $object == 'directory' && !empty( $snippet_id) && !bp_is_current_action( 'add' ) && !bp_is_current_component( 'bookmarklet' ) )
				$redirect = bp_code_snippets_build_perma( array( 'id' => $snippet_id, 'object' => 'directory') );												

		}
		
		if ( !empty( $snippet_id ) ) {
			
			// need to add snippet metas ?
			do_action( 'bp_code_snippets_published_snippet', $snippet_id, $object, $item_id, $secondary_id, $edit_id );
			
			$redirect = apply_filters('bp_code_snippets_redirect_url_after_save', $redirect, $snippet_id, bp_current_action() );
			
			bp_core_add_message( __( 'Snippet Posted!', 'bp-code-snippets' ) );
			bp_core_redirect( $redirect .'#message' );
		}
			
		else{
			
			bp_core_add_message( __( 'There was an error when posting your snippet, please try again.', 'bp-code-snippets' ), 'error' );
			bp_core_redirect( $redirect .'#message' );
		}
				
	}
}

add_action( 'bp_actions', 'bp_code_snippets_directory_handle_snippet_post', 9 );


function bp_code_snippets_handle_snippet_comment_post(){
	global $bp;

	if ( isset( $_POST['save_snippet_comment'] ) && bp_is_active( 'snippets' ) ) {
		
		// check comment snippet nonce
		check_admin_referer( 'bp_snippets_comment_snippet' );
		
		$redirect = remove_query_arg( array('s','snptcat','n'), wp_get_referer() );
		
		// Get snippet comment info
		$snippet_id = apply_filters( 'bp_code_snippets_comment_snippet_id', $_POST['_snippet_id'] );
		
		if( !$snippet_id )
			$snippet_id = bp_action_variable( 0 ) ;
			
		$comment_content = apply_filters( 'bp_code_snippets_comment_snippet_comment_content', $_POST['_comment_content'] );
		$attached_snippets = $_POST['comment_new_snippet_ids'];

		// No comment content so provide feedback and redirect
		if ( empty( $snippet_id ) || empty( $comment_content ) ) {
			bp_core_add_message( __( 'Please enter some content in your comment.', 'bp-code-snippets' ), 'error' );
			bp_core_redirect( $redirect . '#s-reply');
		}
		
		// Auto join this user if they are not yet a member of this group
		if ( bp_groups_auto_join() && !is_super_admin() && 'public' == $bp->groups->current_group->status && !groups_is_user_member( $bp->loggedin_user->id, $bp->groups->current_group->id ) )
			groups_join_group( $bp->groups->current_group->id, $bp->loggedin_user->id );
		
		$comment_id = bp_code_snippet_add_comment( array( 'id' => $snippet_id, 'comment_count' => 1, 'comment_content' => $comment_content) );
		
		if ( !empty( $comment_id ) ) {
			
			if( !empty($attached_snippets) )
				bp_code_snippets_extract_snippet_id( $attached_snippets, $comment_id );
			
			do_action( 'bp_code_snippets_comment_posted', $comment_id, $snippet_id );
			
			bp_core_add_message( __( 'Comment saved!', 'bp-code-snippets' ) );
			bp_core_redirect( $redirect .'#snippet-comment-'.$comment_id );
		}
			
		else{
			
			do_action( 'bp_code_snippets_published_snippet_comment', $comment_id, $snippet_id );
			
			bp_core_add_message( __( 'There was an error when posting your comment, please try again.', 'bp-code-snippets' ), 'error' );
			bp_core_redirect( $redirect .'#message' );
		}
	}
}

add_action( 'bp_actions', 'bp_code_snippets_handle_snippet_comment_post', 10 );

function bp_code_snippets_url_router(){
	global $bp;
		
	if ( !bp_is_current_component( 'snippets' ) )
		return;
		
	if ( bp_is_current_component( 'snippets' ) && is_numeric( bp_current_action() )  ){
		bp_do_404();
		return;
	}
	
	if ( bp_is_current_component( 'snippets' ) && bp_is_current_action( 'embed' ) && !bp_action_variable( 0 )  ) {
		
		wp_die( printf( __('Request unallowed. Please, you can search <a href="%s" target="top">this site</a>', 'bp-code-snippets'), site_url() ) );
		
	}
	
	if ( bp_is_current_component( 'snippets' ) && bp_is_current_action( 'permasnipt' ) && is_numeric( bp_action_variable( 0 ) ) ){
		
		$snippet = new BP_Code_Snippets( bp_action_variable( 0 ) );
		
		if( $snippet->is_draft == 1 && $snippet->user_id != $bp->loggedin_user->id ) {
			bp_do_404();
			return;
		}
		
		if( $snippet->item_id != 0 ) {
			$redirect = bp_code_snippets_build_perma( array( 'id' => $snippet->id, 'item_id' => $snippet->item_id, 'object' => $snippet->object, 'secondary_id' => $snippet->secondary_id ) );
			bp_core_redirect( $redirect );
		}

	}
	
	if( bp_is_current_action( 'delete' ) ){
		// No snippet to delete
		if ( !bp_action_variable( 0 ) || !is_numeric( bp_action_variable( 0 ) ) )
			return false;
			
		$snippet_id = (int) bp_action_variable( 0 );

		if ( empty( $snippet_id ) )
			return false;
			
		check_admin_referer( 'bp_snippet_delete_link' );
		
		$redirect = remove_query_arg( array('s','snptcat','n'), wp_get_referer() );

		// Load up the snippet item
		$snippet = new BP_Code_Snippets( $snippet_id );

		// Check access
		if ( empty( $snippet->user_id ) || !bp_code_snippets_can_delete( $snippet_id, $snippet->object, $snippet->user_id ) )
			return false;

		// Call the action before the delete so plugins can still fetch information about it
		do_action( 'bp_code_snippets_before_action_delete_snippet', $snippet_id, $snippet->user_id );

		// Delete the snippet and provide user feedback
		if ( intval($snippet->item_id) == 0 ) {
			
			bp_code_snippets_delete_snippet( array( 'id' => $snippet_id, 'user_id' => $snippet->user_id, 'object' => $snippet->object) );
			bp_core_add_message( __( 'Snippet deleted successfully', 'bp-code-snippets' ) );
			
		} elseif( intval($snippet->item_id) > 0 ) {
			
			bp_code_snippets_delete_snippet( array( 'id' => $snippet_id, 'user_id' => $snippet->user_id, 'object' => $snippet->object, 'item_id' => $snippet->item_id ) );
			bp_core_add_message( __( 'Group Snippet deleted successfully', 'bp-code-snippets' ) );
			
		} else {
			bp_core_add_message( __( 'There was an error when deleting that snippet', 'bp-code-snippets' ), 'error' );
		}

		do_action( 'bp_code_snippets_action_delete_snippet', $snippet_id, $snippet->user_id );
		
		bp_core_redirect( $redirect .'#message' );
		
	}
	
	/***** start NOJS Only called if javascript is disabled *****/
	
	if( bp_is_current_action( 'moderate' ) ){
		
		if ( !bp_action_variable( 0 ) || !is_numeric( bp_action_variable( 0 ) ) )
			return false;
			
		$comment_id = (int) bp_action_variable( 0 );
		$referrer = remove_query_arg( array('s','snptcat','n'), wp_get_referer() );
		
		preg_match("/\/snippets\/([0-9]+)\//", $referrer, $match);
		
		if(!$match)
			preg_match("/\/permasnipt\/([0-9]+)\//", $referrer, $match);
			
		$snippet_id = $match[1];
		
		if ( empty( $comment_id ) || empty( $snippet_id ))
			return false;
			
		check_admin_referer( '_snippet_del_comment' );
		
		$deleted_comment = bp_code_snippets_remove_comment( $comment_id, $snippet_id );

		if ( 1 == $deleted_comment )
			bp_core_add_message( __( 'Comment deleted successfully', 'bp-code-snippets' ) );
		else
			bp_core_add_message( $deleted_comment, 'error' );

		do_action( 'bp_code_snippets_comment_delete_snippet', $comment_id, $snippet_id );
		
		bp_core_redirect( $referrer .'#message' );
		
	}
	
	if( bp_is_current_action( 'favorite' ) ){
		
		if ( !bp_action_variable( 0 ) || !is_numeric( bp_action_variable( 0 ) ) )
			return false;
			
		$snippet_id = (int) bp_action_variable( 0 );
		$referrer = remove_query_arg( array('s','snptcat','n'), wp_get_referer() );
		
		if ( empty( $snippet_id ))
			return false;
			
		check_admin_referer( '_snippet_fav' );

		if ( bp_code_snippets_add_user_favorite( $snippet_id ) )
			bp_core_add_message( __( 'Snippet added as a favorite', 'bp-code-snippets' ) );
		else
			bp_core_add_message( __( 'Could not added the snippet as a favorite', 'bp-code-snippets' ), 'error' );

		do_action( 'bp_code_snippets_favorite_add_snippet', $snippet_id );
		
		bp_core_redirect( $referrer .'#message' );
		
	}
	
	if( bp_is_current_action( 'unfavorite' ) ){
		
		if ( !bp_action_variable( 0 ) || !is_numeric( bp_action_variable( 0 ) ) )
			return false;
			
		$snippet_id = (int) bp_action_variable( 0 );
		$referrer = remove_query_arg( array('s','snptcat','n'), wp_get_referer() );
		
		if ( empty( $snippet_id ))
			return false;
			
		check_admin_referer( '_snippet_unfav' );

		if ( bp_code_snippets_remove_user_favorite( $snippet_id ) )
			bp_core_add_message( __( 'Snippet removed from favorite(s)', 'bp-code-snippets' ) );
		else
			bp_core_add_message( __( 'Could not removed the snippet from favorite(s)', 'bp-code-snippets' ), 'error' );

		do_action( 'bp_code_snippets_unfavorite_add_snippet', $snippet_id );
		
		bp_core_redirect( $referrer .'#message' );
		
	}
	
	/***** End NOJS Only called if javascript is disabled *****/

}

add_action( 'bp_actions', 'bp_code_snippets_url_router', 8);

function bp_code_snippets_action_directory_feed() {
	global $bp, $wp_query;

	if ( !bp_is_current_component( 'snippets' ) || !bp_is_current_action( 'feed' ) || bp_is_user() || !empty( $bp->groups->current_group ) )
		return false;

	$wp_query->is_404 = false;
	status_header( 200 );

	include_once( BP_CS_PLUGIN_DIR. '/feeds/bp-code-snippets-dir-feed.php' );
	die;
}

add_action( 'bp_actions', 'bp_code_snippets_action_directory_feed' );

function bp_code_snippets_action_group_feed() {
	global $bp, $wp_query;

	if ( !bp_is_active( 'snippets' ) || !bp_is_groups_component() || !isset( $bp->groups->current_group ) || !bp_is_current_action( 'snippets' ) || 'feed' != bp_action_variable( 0 ) )
		return false;
	
	$wp_query->is_404 = false;
	status_header( 200 );
	
	/* BuddyPress is redirecting to global activity anyway ! */
	if ( 'public' != $bp->groups->current_group->status ) {
		if ( !groups_is_user_member( $bp->loggedin_user->id, $bp->groups->current_group->id ) )
			return false;
	}

	include_once( BP_CS_PLUGIN_DIR. '/feeds/bp-code-snippets-group-feed.php' );
	die;
}
add_action( 'bp_actions', 'bp_code_snippets_action_group_feed' );


function bp_code_snippets_thickbox_new_comment_snippet( $request ) {
	global $default_purpose_data;
	
	$action_types = array( 'comment' => __('snippet comment', 'bp-code-snippets'), 
						   'forum_topic' => __('forum topic snippet', 'bp-code-snippets'), 
						   'forum_reply' => __('forum reply snippet', 'bp-code-snippets'),
						   'post' => __('blog post snippet', 'bp-code-snippets') );
	
	if( !empty( $request['action'] ) && in_array( $request['action'], array('comment', 'forum_topic', 'forum_reply','post') ) ) {
		$default_purpose_data['title'] = $action_types[ $request['action'] ];
		$default_purpose_data['action'] = '<a href="#snippet_purpose" title="'. __('Add a description (optionnal)', 'bp-code-snippets') .'" id="purpose_action">' . __('Add a description (optionnal)', 'bp-code-snippets') .'</a>';
		$default_purpose_data['class'] = 'class="hidep"';
		$default_purpose_data['ta_value'] = __('No description given', 'bp-code-snippets');
	}
}

add_action( 'snippets_new_snippet_thickbox_specific', 'bp_code_snippets_thickbox_new_comment_snippet', 1, 1 );


function bp_code_snippets_fix_posting_snippet_from_goup() {
	if( bp_is_current_component( 'groups' ) ){
		?>
		<input type="hidden" value="<?php bp_group_id();?>" name="snippet_group_id">
		<?php
	}
}

add_action( 'snippets_new_snippet_after', 'bp_code_snippets_fix_posting_snippet_from_goup', 1 );
add_action( 'snippets_edit_snippet_after', 'bp_code_snippets_fix_posting_snippet_from_goup', 1 );


function bp_code_snippets_script_head() {
	?>
	<script type="text/javascript">
	jQuery(window).load(function() {
            // remove loader
            jQuery("body").addClass('loaded');
			//display snippets.
			jQuery('.snippet-embed').css('visibility', 'visible');
			jQuery('.snippet-embed').fadeIn(500);
    });
	</script>
	<?php
}

add_action( 'bp_code_snippets_head_embed', 'bp_code_snippets_script_head', 1 );

/**
* load the snippets thickbox button at the left of snippet & group comments
*/
function bp_code_snippets_add_snippet_in_comment() {
	global $snippets_template;
	$snippet_id = $snippets_template->snippet->id_cs;
	$item_id = $snippets_template->snippet->item_id;
	$object = $snippets_template->snippet->object;
	$snptcat = $snippets_template->snippet->snippet_type;
	$add_to = __('your comment', 'bp-code-snippets');
	
	$url = bp_get_code_snippets_thickbox_button_href() . '?action=comment&snippet_id='.$snippet_id.'&item_id=' . $item_id . '&object=' . $object . '&snptcat=' . $snptcat . '&TB_iframe=true&height=500&width=640';
	//$url = bp_get_code_snippets_thickbox_button_href() .'7/';
	?>
	<input type="hidden" name="comment_new_snippet_ids" id="comment_new_snippet_ids">
	<a href="<?php echo $url;?>" class="button thickbox" title="<?php printf(__('Add a snippet to %s', 'bp-code-snippets'), $add_to);?>"><?php _e('Add Snippet', 'bp-code-snippets');?></a>
	<?php
}

add_action( 'bp_code_snippets_before_submit_comment_button', 'bp_code_snippets_add_snippet_in_comment', 1);


/**
* adds extra parameters (item_id, action, object, secondary_id) to save snippets handling
*/
function bp_code_snippets_thickbox_snippet_after() {
	if( $_REQUEST['item_id'] != 0){
		?>
		<input type="hidden" name="snippet_group_id" value="<?php esc_html_e( $_REQUEST['item_id']);?>">
		<input type="hidden" name="snippet_object" value="<?php esc_html_e( $_REQUEST['object']);?>">
		<?php
	}
	// now add snippet action value in order to avoid activity recording of the snippet..
	$snippet_type_object = esc_html( $_REQUEST['object']) . '_' . esc_html( $_REQUEST['action']);
	?>
	<input type="hidden" name="snippet_type_object" value="<?php echo $snippet_type_object;?>">
	<?php if( !empty( $_REQUEST['secondary_id'] ) ){
		?>
	<input type="hidden" name="snippet_secondary_id" value="<?php esc_html_e( $_REQUEST['secondary_id']);?>">
		<?php
	}
}

add_action( 'snippets_new_thickbox_snippet_after', 'bp_code_snippets_thickbox_snippet_after', 1);


/**
* load the snippets thickbox button at the left of topic/reply submit button
*/
function bp_code_snippets_add_snippet_in_topic() {
	global $bp;
	$item_id = !empty( $bp->groups->current_group->id ) ? $bp->groups->current_group->id : false ;
	$secondary_id = false;
	
	if( !bp_code_snippets_is_forum_snippets_enable() )
		return false;
		
	if( !bp_group_snippets_is_snippet_enabled( $item_id ) )
		return false;
	
	if( bp_is_action_variable( 'edit' ) || bp_is_action_variable( 'topic', 0 ) )
		$secondary_id = bp_get_the_topic_id();
		
	if( bp_is_action_variable( 'topic', 0 ) && !bp_is_action_variable( 'edit' ) ) {
		$action = "forum_reply";
		$title = __('your reply', 'bp-code-snippets');
	} else {
		$action = "forum_topic";
		$title = __('your topic', 'bp-code-snippets');
	}
		
	$url = bp_get_code_snippets_thickbox_button_href() . '?action=' . $action . '&item_id=' . $item_id . '&object=group&secondary_id=' . $secondary_id . '&TB_iframe=true&height=500&width=640';
	
	?>
		<input type="hidden" name="topic_new_snippet_ids" id="topic_new_snippet_ids">
		
	<?php if( !bp_is_action_variable( 'topic', 0 ) || bp_is_action_variable( 'edit' ) ):?>
		<p>&nbsp;</p>
	<?php endif;?>
	<div class="thick-snippet"><a href="<?php echo $url;?>" class="button thickbox" title="<?php printf(__('Add a snippet to %s', 'bp-code-snippets'), $title );?>"><?php _e('Add Snippet', 'bp-code-snippets');?></a></div>
	<?php
}

add_action( 'groups_forum_new_topic_after', 'bp_code_snippets_add_snippet_in_topic');
add_action( 'bp_group_after_edit_forum_topic', 'bp_code_snippets_add_snippet_in_topic');
add_action( 'bp_after_group_forum_post_new', 'bp_code_snippets_add_snippet_in_topic');
add_action( 'groups_forum_new_reply_after', 'bp_code_snippets_add_snippet_in_topic');

function bp_code_snippets_fix_forum_no_snippet() {
	
	$action = !empty( $_GET['action'] ) ? $_GET['action'] : false ;
	$item_id = !empty( $_GET['item_id'] ) ? intval( $_GET['item_id'] ) : false ;
	
	if( strpos( $action, 'forum') > -1 && !bp_group_snippets_is_snippet_enabled( $item_id ) )
		wp_die(__('Sorry, this group do not enable snippet posting','bp-code-snippets'));
}

add_action( 'snippets_new_snippet_before', 'bp_code_snippets_fix_forum_no_snippet');


/**
* if new snippet was attached to topic update code snippets table with the secondary_id
*/
function bp_code_snippets_attach_to_topic( $group_forum, &$topic ) {
	
	if ( isset( $_POST['topic_new_snippet_ids'] ) ) {
		
		bp_code_snippets_extract_snippet_id( $_POST['topic_new_snippet_ids'], $topic->topic_id );
		
	}
}

add_action( 'groups_new_forum_topic', 'bp_code_snippets_attach_to_topic', 9, 2);


function bp_code_snippets_add_media_button() {
	global $blog_id, $post;
	
	if( !bp_code_snippets_is_blog_snippets_enable() )
		return false;
		
	if( !bp_code_snippets_child_blog_snpt_ok() && !bp_is_root_blog() )
		return false;
		
	if( !current_user_can( 'publish_posts' ) )
		return false;
	
	$title = __('blog post', 'bp-code-snippets');
	$url = bp_get_code_snippets_thickbox_button_href() . '?action=post&item_id=' . $blog_id . '&object=blog&secondary_id=' . $post->ID . '&TB_iframe=true&height=500&width=640';
	?>
	<a href="<?php echo $url;?>" class="button thickbox blog-post" title="<?php printf(__('Add a snippet to %s', 'bp-code-snippets'), $title );?>"><?php _e('Snippet', 'bp-code-snippets');?></a>
	<?php
}

/**
* Adding a button to post in order to attach a snippet...
*/
add_action('media_buttons', 'bp_code_snippets_add_media_button', 20);

function bp_code_snippets_blogpost_hidden_snippet_ids(){
	
	if( !bp_code_snippets_is_blog_snippets_enable() || ( !bp_code_snippets_child_blog_snpt_ok() && !bp_is_root_blog() ) )
		return false;
	
	?>
	<input type="hidden" name="blogpost_snippet_ids" id="blogpost_snippet_ids">
	<?php
}

add_action('dbx_post_sidebar', 'bp_code_snippets_blogpost_hidden_snippet_ids');

function bp_code_snippets_attach_snippets_to_blog_post( $post_id ) {
	global $blog_id;
	
	if( !bp_code_snippets_is_blog_snippets_enable()  || ( !bp_code_snippets_child_blog_snpt_ok() && !bp_is_root_blog() ) )
		return $post_id;
	
	if ( isset( $_POST['blogpost_snippet_ids'] ) ) {
		
		bp_code_snippets_extract_snippet_id( $_POST['blogpost_snippet_ids'], $post_id );
		
	}
	
	if( get_post_status( $post_id ) == 'publish' ) {
	
		bp_code_snippets_update_blog_post_status( $post_id, $blog_id );
		
	}
}

add_action('save_post', 'bp_code_snippets_attach_snippets_to_blog_post', 10, 1);

/**
* Handling item deleting
*/

/* forum topic */
function bp_code_snippets_topic_is_deleted( $topic_id ) {
	
	if( !bp_code_snippets_is_forum_snippets_enable() )
		return $topic_id;
	
	bp_code_snippets_delete_snippet( array('secondary_id' => $topic_id, 'object' => 'group_forum_reply,group_forum_topic') );
	return true;
}

add_action( 'groups_delete_group_forum_topic', 'bp_code_snippets_topic_is_deleted', 10, 1);

/* group */
function bp_code_snippets_group_is_deleted( $group_id ) {
	
	if( !bp_code_snippets_is_group_snippets_enable() )
		return $group_id;
	
	bp_code_snippets_delete_snippet( array('item_id' => $group_id, 'object' => 'group,group_comment,group_forum_reply,group_forum_topic') );
	return true;
}

add_action( 'groups_delete_group', 'bp_code_snippets_group_is_deleted', 10, 1);

/* blog */
function bp_code_snippets_blogpost_is_deleted( $post_id ){
	global $wpdb;
	
	if( !bp_code_snippets_is_blog_snippets_enable() || ( !bp_code_snippets_child_blog_snpt_ok() && !bp_is_root_blog() ) )
		return $post_id;
	
	if ( empty( $wpdb->blogid ) )
		return false;

	$post_id = (int)$post_id;
	$blog_id = (int)$wpdb->blogid;
	
	bp_code_snippets_delete_snippet( array('item_id' => $blog_id, 'secondary_id' => $post_id) );
	
	return true;
}

add_action('deleted_post', 'bp_code_snippets_blogpost_is_deleted', 10, 1);

/**
* BP Bookmarklet Interface..
*/

function bp_code_snippets_load_bkmklet_js( $page ) {
	?>
		var copied = urlvars['copied'];
		var bpcs_message_title = "<?php _e('Please, add a title to your snippet','bp-code-snippets');?>";
		var bpcs_message_desc = "<?php _e('Please, add a description of your snippet','bp-code-snippets');?>";
		var bpcs_message_cat = "<?php _e('Please, select a language category for your snippet','bp-code-snippets');?>";
		var bpcs_message_content = "<?php _e('Please, add the snippet you want to share','bp-code-snippets');?>";

		if( !copied.length )
			copied = "<?php _e('[Your code here]','bp-code-snippets');?>";

		$("#snippet_title").val( decodeURIComponent(urlvars['title']) );
			
		$("#snippet_content").val( decodeURIComponent(copied) );
			
		$("#snippet_purpose").val( '<?php _e("Originaly posted on", "bp-code-snippets");?> <a href="'+decodeURIComponent(urlvars['url'])+'"><?php esc_html_e($_GET['title']) ;?></a>');

		var end = $("#snippet_purpose").val().length; 

		$("#snippet_purpose").selectRange(0, end);
			
		$('#snippet_group_id').change( function() {
			bp_cs_filter_language_for_group($(this).val());
		});
		
		$('#submit_snippet_cancel').click(function(){
			window.close();
		});
		
		/* handle submit */
		$('form#snippets-form').submit( function() {

			if( $('form#snippets-form #snippet_title').val().length < 3 ) {
				alert(bpcs_message_title);
				$('form#snippets-form #snippet_title').focus();
				return false;
			}
			if( $('form#snippets-form #bp_cs_source').val() == 0 ) {
				alert(bpcs_message_cat);
				$('form#snippets-form #bp_cs_source').focus();
				return false;
			}
			if( $('form#snippets-form #snippet_purpose').val().length < 3 ) {
				alert(bpcs_message_desc);
				$('form#snippets-form #snippet_purpose').focus();
				return false;
			}
			if( $('form#snippets-form #snippet_content').val().length < 3 ) {
				alert(bpcs_message_content);
				$('form#snippets-form #snippet_content').focus();
				return false;
			}

			return true;
		});
			
		function bp_cs_filter_language_for_group(groupid){
			var data = {
		      action: 'bp_cs_filter_language_dd',
		      group: groupid
		    };

		    jQuery.post(ajaxurl, data, function(response) {

				if(response.indexOf('{') != -1) {

					cs_available = jQuery.parseJSON(response);

					jQuery('#bp_cs_source option').each(function() {

						if(jQuery(this).val() != 0) jQuery(this).remove();

					});

					jQuery.each(cs_available, function(label, val) {

					    jQuery('#bp_cs_source').append(
					        jQuery('<option></option>').val(val).html(label)
					    );

					});

				}

		    });
		}
	<?php
}

if( function_exists('bp_bkmklet_load_extra_js') && bp_code_snippets_is_bkmklet_tab_enable() )
	add_action(  'bp_bkmklet_load_plugin_js', 'bp_code_snippets_load_bkmklet_js', 9, 1);


function bp_code_snippets_load_bkmklet_sharing_template( $page ) {
	if($page == 'snippets')
		bp_code_snippets_load_template('snippets-create.php', true);
}

if( function_exists('bp_bkmklet_load_sharing_component') && bp_code_snippets_is_bkmklet_tab_enable() )
	add_action( 'bp_bkmklet_load_extra_sharing_component', 'bp_code_snippets_load_bkmklet_sharing_template', 9, 1);

function bp_code_snippets_enqueue_bkmklet_jscss() {
	global $bkmklet_scripts, $bkmklet_styles;
	
	$bkmklet_scripts[] = 'bp-cs-add';
	$bkmklet_styles[] = 'bp-cs-style';
}

if( function_exists('bp_bkmklet_load_extra_js') && bp_code_snippets_is_bkmklet_tab_enable() )
		add_action(  'bp_bkmklet_screen_index', 'bp_code_snippets_enqueue_bkmklet_jscss', 9, 1);


function bp_code_snippets_bkmklet_new_nav_button( $nav_items ){
	if( !is_array($nav_items) )
		$nav_items = array();

	$nav_items[] = array('slug' => 'snippets', 'name' => __('Share a Snippet', 'bp-code-snippets') );
	return $nav_items;
}

if( function_exists('bp_bkmklet_add_nav_buttons') && bp_code_snippets_is_bkmklet_tab_enable() )
	add_filter( 'bp_bkmklet_add_nav_buttons',    'bp_code_snippets_bkmklet_new_nav_button', 1, 1);

?>