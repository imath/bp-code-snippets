<?php
// ajax handling

function bp_code_snippets_ajax_select_lg_handle(){
	
	$group_id = $_POST['group'];
	
	if($group_id > 0){
		
		$available_for_group = bp_group_snippets_get_available_languages_for_group($group_id) ;
		echo json_encode($available_for_group);
		
	}else{
		
		$available_by_admin = bp_group_snippets_get_available_languages() ;
		echo json_encode($available_by_admin);
		
	}
	
	die();
}

add_action( 'wp_ajax_bp_cs_filter_language_dd', 'bp_code_snippets_ajax_select_lg_handle');

/* AJAX mark an activity as a favorite */
function bp_code_snippets_mark_snippet_favorite() {
	
	check_ajax_referer( '_snippet_fav', 'nonce' );

	bp_code_snippets_add_user_favorite( $_POST['snippet'] );
	$response = array('label' => __( 'Remove Favorite', 'bp-code-snippets' ), 'url' => wp_nonce_url( home_url( bp_get_snippet_slug() . '/unfavorite/' . $_POST['snippet'] . '/' ), '_snippet_unfav' ), 'title' => __('Remove Favorite', 'bp-code-snippets') );
	echo json_encode($response);
	
	die();
}
add_action( 'wp_ajax_snippet_mark_fav', 'bp_code_snippets_mark_snippet_favorite' );

/* AJAX mark an activity as not a favorite */
function bp_code_snippets_unmark_snippet_favorite() {
	
	check_ajax_referer( '_snippet_unfav', 'nonce' );

	bp_code_snippets_remove_user_favorite( $_POST['snippet'] );
	$response = array('label' => __( 'Favorite', 'bp-code-snippets' ), 'url' => wp_nonce_url( home_url( bp_get_snippet_slug() . '/favorite/' . $_POST['snippet'] . '/' ), '_snippet_fav' ), 'title' => __('Mark as Favorite', 'bp-code-snippets') );
	echo json_encode($response);
	
	die();
}
add_action( 'wp_ajax_snippet_mark_unfav', 'bp_code_snippets_unmark_snippet_favorite' );


/* Ajax load oldest favorite */

function bp_code_snippets_favorites_get_older(){
	ob_start();
	bp_code_snippets_load_template( 'snippets-loop.php' );
	$result['contents'] = ob_get_contents();
	ob_end_clean();
	
	echo $result['contents'];
	
	die();
}

add_action('wp_ajax_snippet_fav_loadmore', 'bp_code_snippets_favorites_get_older');
add_action('wp_ajax_snippet_fav_cat', 'bp_code_snippets_favorites_get_older');


/* Ajax remove comment */

function bp_code_snippets_ajax_remove_snippet_comment(){
	
	check_ajax_referer( '_snippet_del_comment', 'nonce' );
	
	$response = bp_code_snippets_remove_comment( $_POST['snippet_comment'], $_POST['snippet'] );
	
	echo $response;
	
	die();
}

add_action('wp_ajax_snippet_delete_comment', 'bp_code_snippets_ajax_remove_snippet_comment');
?>