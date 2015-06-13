<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

if( ( bp_code_snippets_is_blog_snippets_enable() && bp_is_root_blog() ) || bp_code_snippets_child_blog_snpt_ok() )
	add_filter('the_content', 'bp_code_snippets_pre_replace_blog', 6);

if( bp_code_snippets_is_forum_snippets_enable() )
	add_filter('bp_get_the_topic_post_content', 'bp_code_snippets_pre_replace', 6);


function bp_code_snippets_pre_replace_blog( $pre ) {
	global $post, $blog_id;
	
	if( !empty($post->ID) && !is_single() && (!is_page() || is_front_page() ) ) {
		
		if( is_multisite() )
			$permalink = get_blog_permalink( $blog_id, $post->ID ) ;
			
		else $permalink = get_permalink( $post->ID ) ;
		
		return bp_code_snippets_pre_replace( $pre, $permalink );
		
	} else {
		return bp_code_snippets_pre_replace( $pre );
	}
}

function bp_code_snippets_pre_replace( $pre, $blog_link = false ) {
	
	$replacement = array();
	preg_match_all('#\`pre type=\"[a-z0-9._-]+\"\`(.*)\`\/pre\`#sU', $pre, $matches);
	if($matches){
		
		foreach($matches[0] as $type ){
			preg_match('#\`pre type=\"(.*)\"\`#sU', $type, $types);
			$replacement[]=$types[1];
			
		}
		
		for( $i=0; $i< count($matches[1]);$i++ ) {
			
			if( !$blog_link )
				$pre = str_replace( $matches[0][$i], '<div class="code_in_content"><pre class="brush: '. $replacement[$i] .'">' . stripslashes( $matches[1][$i] ) . '</pre></div>', $pre );
				
			else
				$pre = str_replace( $matches[0][$i], '<p class="snippet_excerpt_link"><a href="'.$blog_link.'">['.$replacement[$i].' code inside]</a></p>', $pre );
		}
	}
	
	return $pre;
	
}
?>