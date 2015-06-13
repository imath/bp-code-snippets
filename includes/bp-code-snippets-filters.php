<?php

/**
 * BP Code Snippets filters
 *
 * @package BP Code Snippets
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

add_filter( 'bp_snippets_directory_objects',  'bp_code_snippets_directory_objects_filter', 1);
add_filter( 'bp_snippets_mine_objects',       'bp_code_snippets_directory_objects_filter', 1);
add_filter( 'bp_snippets_permasnipt_objects', 'bp_code_snippets_permasnipt_objects_filter', 1);
add_filter( 'bp_snippets_group_objects',      'bp_code_snippets_group_objects_filter', 1);

add_filter( 'bp_code_snippets_title_before_save',    'bp_code_snippets_title_filter_save', 1 );
add_filter( 'bp_code_snippets_purpose_before_save',  'bp_code_snippets_purpose_filter_save', 1 );
add_filter( 'bp_code_snippets_content_before_save',  'bp_code_snippets_content_filter_save', 1 );

add_filter( 'bp_code_snippets_comment_content_before_save_comment', 'bp_code_snippets_title_filter_save', 1);
add_filter( 'bp_code_snippets_the_comment',                         'bp_code_snippets_format_comment', 1);
add_filter( 'bp_code_snippets_redirect_url_after_save',             'bp_code_snippets_redirect_filter_save', 1, 3);

add_filter( 'bp_get_snippet_excerpt',  		      'stripslashes_deep', 1 );
add_filter( 'bp_get_snippet_purpose',             'stripslashes_deep', 1 );
add_filter( 'bp_get_snippet_excerpt',  		      'convert_smilies', 1 );
add_filter( 'bp_get_snippet_purpose',             'convert_smilies', 1 );
add_filter( 'bp_get_snippet_title',    		      'stripslashes_deep', 1 );
add_filter( 'bp_get_snippet_the_content', 	      'stripslashes_deep', 1 );
add_filter( 'bp_get_snippet_the_snippet_content', 'bp_code_snippets_add_embed_action_to_snippet' ,1 , 6);
add_filter( 'bp_snippet_type_avatar',             'bp_code_snippets_applescript_too_long', 1);

add_filter( 'bp_get_displayed_user_nav_snippets', 'bp_code_snippets_display_member_count_snippets', 9, 2);
add_filter( 'bp_get_options_nav_nav-snippets',    'bp_code_snippets_group_count', 9, 2);

add_filter( 'bp_get_the_topic_post_content', 'bp_code_snippets_forum_render_snippet', 9, 1);
add_filter( 'bp_modify_page_title',          'bp_code_snippets_page_title', 10, 4);
add_filter( 'bp_get_activity_latest_update', 'bp_code_snippets_build_perma_from_activity');
add_filter( 'bp_code_snippets_add_brand',    'bp_code_snippets_default_brand', 1, 2);

add_filter( 'bp_get_activity_content_body', 'bp_code_snippets_activity_render_snippet', 6 );
// wouch ! let's avoid a conflict with user nav if the user tries to choose snippets as the name of his group
add_filter( 'groups_forbidden_names', 'bp_code_snippets_forbidden_names', 10, 1);

function bp_code_snippets_applescript_too_long( $type ) {
	if($type == 'applescript') $type = 'Script';
	return $type;
}

function bp_code_snippets_activity_render_snippet( $activity_content ) {
	
	$activity = stripslashes_deep($activity_content);
	
	if( bp_code_snippets_iframe_activity() ) {
		
		return bp_code_snippets_do_embed_in_activity( $activity );
	}
	else return bp_code_snippets_do_embed_in_activity($activity, 'link' );
	
}

function bp_code_snippets_default_brand( $href, $class ) {
	
	$href = site_url();
	$name = get_bloginfo('name');
	
	return '<span class="default-brand" style="background-image:url('.plugins_url( 'buddypress/bp-core/images/admin_menu_icon.png' ).');"><a href="'.$href.'" class="'.$class.'" target="top">'.$name.'</a></span>';
}

function bp_code_snippets_directory_objects_filter( $objects ) {
	
	if( bp_code_snippets_is_group_snippets_enable() && bp_code_snippets_is_forum_snippets_enable() && bp_code_snippets_is_blog_snippets_enable() )
		return $objects;
	
	$enabled_objects = 'directory';
	
	if( bp_code_snippets_is_group_snippets_enable() )
		$enabled_objects .= ',group';
		
	if( bp_code_snippets_is_forum_snippets_enable() )
		$enabled_objects .= ',group_forum_topic';
		
	if( bp_code_snippets_is_blog_snippets_enable() )
		$enabled_objects .= ',blog_post';
		
	return $enabled_objects;
}

function bp_code_snippets_permasnipt_objects_filter( $objects ) {
	
	if( bp_code_snippets_is_group_snippets_enable() && bp_code_snippets_is_forum_snippets_enable() && bp_code_snippets_is_blog_snippets_enable() )
		return $objects;
		
	$enabled_objects = 'directory,directory_comment';
	
	if( bp_code_snippets_is_group_snippets_enable() )
		$enabled_objects .= ',group,group_comment';
		
	if( bp_code_snippets_is_forum_snippets_enable() )
		$enabled_objects .= ',group_forum_topic';
		
	if( bp_code_snippets_is_blog_snippets_enable() )
		$enabled_objects .= ',blog_post';
		
	return $enabled_objects;
	
}

function bp_code_snippets_group_objects_filter( $objects ) {
	if( bp_code_snippets_is_forum_snippets_enable() )
		return $objects;
	
	else return 'group';
}

function bp_code_snippets_build_perma_from_activity( $latest_update ){
	preg_match("/\/p\/([0-9]+)\//", $latest_update, $match);
	if( $match[1] ){
		$activity_id = intval( $match[1] );
		$activity = new BP_Activity_Activity( $activity_id );
		
		if( in_array( $activity->type, array( 'snippet_dir_update', 'snippet_group_update', 'snippet_dir_comment', 'snippet_group_comment' ) ) ){
			$replace_link = $activity->primary_link;
			return preg_replace( "/href\=\".*\/\"/" , 'href="'.$replace_link.'"', $latest_update );
		}
		else return $latest_update;
	}
	else return $latest_update;
}

function bp_code_snippets_page_title( $title_sep, $title, $sep, $seplocation) {
	if( bp_is_current_action( 'mine' ) )
		return __('My Snippets', 'bp-code-snippets') . " $sep ";
		
	if( bp_is_current_action( 'favs' ) )
		return __('My Favs', 'bp-code-snippets') . " $sep ";
		
	if( bp_is_current_action( 'permasnipt' ) )
		return __('Snippet details', 'bp-code-snippets') . " $sep ";
		
	if( bp_is_current_action( 'embed' ) )
		return __('Embed Snippet', 'bp-code-snippets') . " $sep ";
		
	if( bp_is_current_action( 'add' ) )
		return __('Add Snippet', 'bp-code-snippets') . " $sep ";
		
	else return $title_sep;
}

function bp_code_snippets_forum_render_snippet( $topic ) {
	global $topic_template;
	
	if( !bp_code_snippets_is_forum_snippets_enable() )
		return $topic;
	
	if( "1" == $topic_template->post->post_position)
		$topic = bp_code_snippets_do_shortcode($topic, 'content');
		
	else
	 	$topic = bp_code_snippets_do_shortcode($topic, 'comment'); 
	
	return $topic;
}

function bp_code_snippets_redirect_filter_save( $redirect, $snippet_id, $action) {
	if( in_array( $action, array('add') ) )
		$redirect = bp_get_root_domain() . '/' . bp_get_snippet_slug() . '/add/' . $snippet_id . '/';
	
	return $redirect;
}

function bp_code_snippets_hide_for_embed( $show_admin_bar ){
	return false;
}

function bp_code_snippets_title_filter_save( $title ){
	return wp_kses( $title, array() );
}

function bp_code_snippets_format_comment( $comment ) {
	$comment = stripslashes_deep($comment);
	$comment = bp_code_snippets_do_shortcode($comment, 'comment');
	
	return $comment;
}

function bp_code_snippets_add_embed_action_to_snippet ($html, $snippet, $snippet_type, $snippet_id, $permalink, $hide) {
	
	if( !bp_code_snippets_is_oembed_enable() )
		return $html;
	
	$html = '<div class="snippet-embed" id="snippet-' . $snippet_id .'">';
	$html .= '<div class="embed-action">';
	
	if( bp_code_snippets_iframe_activity() && is_user_logged_in() && !$hide) {
		$html .= '<a href="#get-shortcode" class="copy-shortcode">'. __('Get Shortcode', 'bp-code-snippets') .'</a>';
	}
	
	$html .= '<a href="#get-permalink" class="copy-link">'. __('Get Permalink', 'bp-code-snippets') .'</a>';
	
	if( !$hide ) {
		$html .= '<a href="#get-embed-code" class="copy-code">'. __('Get Embed Code', 'bp-code-snippets') .'</a>';
	}
	
	if( bp_code_snippets_iframe_activity() && is_user_logged_in() ){
		$html .= '<div style="display:none" class="copy-shortcode-snippet"><input type="text" value=\'[snippet id="'.$snippet_id.'"]\' class="snipt-shortcode" readonly/></div>';
	}
	
	$html .= '<div style="display:none" class="copy-link-snippet">';
	$html .= '<input type="text" value="'. $permalink .'" class="snipt-perma" readonly/></div>';
	
	if( !$hide ) {
		$html .= '<div style="display:none" class="copy-embed-snippet">';
		$html .= '<textarea class="snipt-code" readonly>&lt;iframe src="'. bp_get_root_domain() . '/' . bp_get_snippet_slug() . '/embed/' . $snippet_id .'/' . '" frameborder="0" width="100%" height="100px"&gt;&lt;/iframe&gt;</textarea></div>';
	}
	
	$html .= '</div>';
	
	$html .= '<div class="code_in_content"><pre class="brush: '. $snippet_type .'">' . stripslashes( $snippet ) . '</pre></div></div>';
	
	return $html;
}

function bp_code_snippets_purpose_filter_save( $purpose ){
	$cs_allowedtags = array(
		'a' => array(
			'href' => array (),
			'title' => array ()),
		'b' => array(),
		'em' => array (), 'i' => array (),
		'strong' => array(),
		'u' => array()
	);
	
	return wp_kses( $purpose, $cs_allowedtags);
}

function bp_code_snippets_content_filter_save( $content ){
	return esc_html( $content );
}

function bp_code_snippets_display_member_count_snippets($nav_item, $user_nav_item){
	global $bp;
	if ( $user_nav_item['slug'] == $bp->current_component ) {
		$selected = ' class="current selected"';
	} else {
		$selected = '';
	}
	
	$show_hidden = ( $bp->displayed_user->id != $bp->loggedin_user->id ) ? 0 : 1;
	$is_draft = ( $bp->displayed_user->id != $bp->loggedin_user->id ) ? 0 : 1;
	
	$new_html = '<li id="' . $user_nav_item['css_id'] . '-personal-li" ' . $selected . '><a id="user-' . $user_nav_item['css_id'] . '" href="' . $bp->displayed_user->domain . $user_nav_item['slug'] . '/' . '">' . $user_nav_item['name'] . '<span>' . bp_code_snippets_get_all_count_for( array("filter" => array('user_id' => $bp->displayed_user->id, 'object' => apply_filters('bp_snippets_mine_objects', 'directory,group,group_forum_topic,blog_post')), 'show_hidden' => $show_hidden, 'is_draft' => $is_draft ) ) . '</span></a></li>';
	return $new_html;
}


function bp_code_snippets_group_count($html, $subnav_item){
	global $bp;
	
	if( !bp_code_snippets_is_group_snippets_enable() )
		return $html;
	
	if ( $subnav_item['slug'] == $bp->current_action ) {
		$selected = ' class="current selected"';
	} else {
		$selected = '';
	}
	
	// List type depends on our current component
	$list_type = bp_is_group() ? 'groups' : 'personal';
	
	$new_html = '<li id="' . $subnav_item['css_id'] . '-' . $list_type . '-li" ' . $selected . '><a id="' . $subnav_item['css_id'] . '" href="' . $subnav_item['link'] . '">' . $subnav_item['name'] . '<span>' . bp_code_snippets_get_all_count_for( array("filter" => array('primary_id' => $bp->groups->current_group->id, 'object' => apply_filters( 'bp_snippets_group_objects','group,group_forum_topic')) , 'show_hidden' => true ) ) . '</span></a></li>';
	
	return $new_html;
}

function bp_code_snippets_forbidden_names( $forbidden_names ) {
	$forbidden_names[] = 'snippets';
	
	return $forbidden_names;
}
?>