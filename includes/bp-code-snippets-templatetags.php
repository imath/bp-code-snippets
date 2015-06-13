<?php
// templatetags...

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

function bp_snippet_slug(){
	echo bp_get_snippet_slug();
}

	function bp_get_snippet_slug(){
		global $bp;
		if( $bp->pages->snippets->slug != BP_CS_SLUG )
			return $bp->pages->snippets->slug ;

		return BP_CS_SLUG;
	}

function bp_code_snippets_dropdown_lg( $selected = false ){
	echo bp_code_snippets_get_dropdown_lg( $selected );
}

	function bp_code_snippets_fill_dropdown_lg( $selected = false ){
		echo bp_code_snippets_get_fill_dropdown_lg( $selected );
	}
	
	function bp_code_snippets_get_fill_dropdown_lg( $selected = false ){
		global $bp;
		
		$snptcat = !empty( $_REQUEST['snptcat'] ) ? $_REQUEST['snptcat'] : false ;
		$object = !empty( $_REQUEST['object'] ) ? $_REQUEST['object'] : false ;
		$item_id = !empty( $_REQUEST['item_id'] ) ? intval( $_REQUEST['item_id'] ) : false ;
		
		if( $snptcat != "0" && !$selected )
			$selected = $snptcat;
		
		if( bp_is_current_component( 'groups' ) && !bp_is_forums_component() )
			$available = bp_group_snippets_get_available_languages_for_group( $bp->groups->current_group->id );
				
		elseif( !empty( $item_id ) && $object == 'group')
			$available = bp_group_snippets_get_available_languages_for_group( intval( $item_id ) );

		else $available = bp_group_snippets_get_available_languages();

		$cs_dropdown = "";
		
		foreach($available as $k => $v){
			
			if( $selected == $v)
				$cs_dropdown .= '<option value="'.$v.'" selected>'.$k.'</option>';
			
			else $cs_dropdown .= '<option value="'.$v.'">'.$k.'</option>';
			
		}
		
		return $cs_dropdown;
	}
	
	function bp_code_snippets_get_dropdown_lg( $selected = false ){
		$bp_cs_dropdown = '<select name="_bp_cs_source" id="bp_cs_source">';
		$bp_cs_dropdown .= '<option value="0">----</option>';
		
		$bp_cs_dropdown .= bp_code_snippets_get_fill_dropdown_lg( $selected );
		
		$bp_cs_dropdown .= '</select>';
		
		return apply_filters( 'bp_code_snippets_get_dropdown_lg_filter', $bp_cs_dropdown, $selected );
		
	}

function bp_code_snippets_group_lg_li(){
	echo bp_code_snippets_get_group_lg_li();
}

	function bp_code_snippets_get_group_lg_li(){
		global $bp;
		
		$snippets_group_code = groups_get_groupmeta($bp->groups->current_group->id, 'snippets_code_ok');
		
		/** Handling older version of bp code snippets 
		if( $snippets_group_code && !is_array( $snippets_group_code ) ){
			$migre_snippets_group_code = explode("|", $snippets_group_code);
			for($i = 0 ; $i < count($migre_snippets_group_code) ; $i++){
				$code = explode(":", $migre_snippets_group_code[$i]);
				$array_snippets_group_code[] = $code[1];
			}
			if(count($array_snippets_group_code)>=1){
				groups_update_groupmeta( $bp->groups->current_group->id, 'snippets_code_ok', $array_snippets_group_code);
				$snippets_group_code = $array_snippets_group_code;
			}
			
		}**/
		
		/** list of available languages by admin **/
		
		$available = bp_group_snippets_get_available_languages();
		
		$snippet_ul="";
		
		foreach( $available as $k => $v){
			

			if( $snippets_group_code && is_array($snippets_group_code) && in_array($v, $snippets_group_code) ){
				$snippet_ul .= '<li><input type="checkbox" name="_snippets_code_ok[]" value="'.$v.'" checked>'.$k.'</li>';
			}else{
				$snippet_ul .= '<li><input type="checkbox" name="_snippets_code_ok[]" value="'.$v.'">'.$k.'</li>';
			}

		}
		
		return $snippet_ul;
	}


function bp_code_snippets_directory_search_form() {
	global $bp;

	$default_search_value = bp_get_search_default_text();
	$search_value = !empty( $_REQUEST['s'] ) ? stripslashes( $_REQUEST['s'] ) : $default_search_value; ?>

	<form action="" method="get" id="search-snippets-form">
		<label><input type="text" name="s" id="snippets_search" value="<?php echo esc_attr( $search_value ) ?>"  onfocus="if (this.value == '<?php echo $default_search_value ?>') {this.value = '';}" onblur="if (this.value == '') {this.value = '<?php echo $default_search_value ?>';}" /></label>
		<input type="hidden" name="snptcat" id="search-snippets-cat">
		<input type="submit" id="snippets_search_submit" value="<?php _e( 'Search', 'bp-code-snippets' ) ?>" />
	</form>

<?php
}

/**
* BP Code Snippets template loop
*
* Based on BuddyPress BP_Code_Snippets_Template Class
*
* @since 2.0
*/
class BP_Code_Snippets_Template {
	var $current_snippet = -1;
	var $snippet_count;
	var $total_snippet_count;
	var $snippets;
	var $snippet;

	var $in_the_loop;

	var $pag_page;
	var $pag_num;
	var $pag_links;

	var $full_name;

	function bp_code_snippets_template( $page, $per_page, $max, $include, $sort, $filter, $search_terms, $display_comments, $show_hidden, $exclude = false, $in = false, $is_draft = false ) {
		$this->__construct( $page, $per_page, $max, $include, $sort, $filter, $search_terms, $display_comments, $show_hidden, $exclude, $in, $is_draft );
	}

	function __construct( $page, $per_page, $max, $include, $sort, $filter, $search_terms, $display_comments, $show_hidden, $exclude = false, $in = false, $is_draft = false ) {
		global $bp;

		$this->pag_page = isset( $_REQUEST['acpage'] ) ? intval( $_REQUEST['acpage'] ) : $page;
		$this->pag_num  = isset( $_REQUEST['num'] ) ? intval( $_REQUEST['num'] ) : $per_page;

		/* Get an array of the logged in user's favorite snippets */
		$this->my_favs = maybe_unserialize( bp_get_user_meta( $bp->loggedin_user->id, 'bp_favorite_snippets', true ) );

		// Fetch specific snippet items based on ID's
		if ( !empty( $include ) )
			$this->snippets = bp_code_snippets_get_specific( array( 'snippet_ids' => explode( ',', $include ), 'max' => $max, 'page' => $this->pag_page, 'per_page' => $this->pag_num, 'sort' => $sort, 'filter' => $filter, 'display_comments' => $display_comments, 'show_hidden' => $show_hidden, 'is_draft' => $is_draft ) );
		// Fetch all snippet items
		else
			$this->snippets = bp_code_snippets_get( array( 'display_comments' => $display_comments, 'max' => $max, 'per_page' => $this->pag_num, 'page' => $this->pag_page, 'sort' => $sort, 'search_terms' => $search_terms, 'filter' => $filter, 'show_hidden' => $show_hidden, 'exclude' => $exclude, 'in' => $in, 'is_draft' => $is_draft ) );

		if ( !$max || $max >= (int)$this->snippets['total'] )
			$this->total_snippet_count = (int)$this->snippets['total'];
		else
			$this->total_snippet_count = (int)$max;

		$this->snippets = $this->snippets['snippets'];

		if ( $max ) {
			if ( $max >= count($this->snippets) ) {
				$this->snippet_count = count( $this->snippets );
			} else {
				$this->snippet_count = (int)$max;
			}
		} else {
			$this->snippet_count = count( $this->snippets );
		}

		$this->full_name = $bp->displayed_user->fullname;

		if ( (int)$this->total_snippet_count && (int)$this->pag_num ) {
			$this->pag_links = paginate_links( array(
				'base'      => add_query_arg( 'acpage', '%#%' ),
				'format'    => '',
				'total'     => ceil( (int)$this->total_snippet_count / (int)$this->pag_num ),
				'current'   => (int)$this->pag_page,
				'prev_text' => _x( '&larr;', 'Snippet pagination previous text', 'bp-code-snippets' ),
				'next_text' => _x( '&rarr;', 'Snippet pagination next text', 'bp-code-snippets' ),
				'mid_size'  => 1
				) );
		}
	}

	function has_snippets() {
		if ( $this->snippet_count )
			return true;

		return false;
	}

	function next_snippet() {
		$this->current_snippet++;
		$this->snippet = $this->snippets[$this->current_snippet];

		return $this->snippet;
	}

	function rewind_snippets() {
		$this->current_snippet = -1;
		if ( $this->snippet_count > 0 ) {
			$this->snippet = $this->snippets[0];
		}
	}

	function user_snippets() {
		if ( $this->current_snippet + 1 < $this->snippet_count ) {
			return true;
		} elseif ( $this->current_snippet + 1 == $this->snippet_count ) {
			do_action('snippet_loop_end');
			// Do some cleaning up after the loop
			$this->rewind_snippets();
		}

		$this->in_the_loop = false;
		return false;
	}

	function the_snippet() {
		global $snippet;
		

		$this->in_the_loop = true;
		
		$this->snippet = $this->next_snippet();

		if ( is_array( $this->snippet ) )
			$this->snippet = (object) $this->snippet;

		if ( $this->current_snippet == 0 ) // loop has just started
		do_action('snippet_loop_start');
	}
}

/**
* Initializes BP Code Snippets loop.
*
* Based on BuddyPress bp_has_activities().
*
* @since 2.0
*
*/
function bp_has_snippets( $args = '' ) {
	global $snippets_template, $bp;
	/***
	* Set the defaults based on the current page. Any of these will be overridden
	* if arguments are directly passed into the loop. Custom plugins should always
	* pass their parameters directly to the loop.
	*/
	$user_id     = false;
	$include     = false;
	$exclude     = false;
	$in          = false;
	$show_hidden = false;
	$object      = false;
	$primary_id  = false;
	$is_draft    = false;

	// User filtering
	if ( !empty( $bp->displayed_user->id ) )
		$user_id = $bp->displayed_user->id;

	// Group filtering
	if ( !empty( $bp->groups->current_group ) ) {
		$object = $bp->groups->id;
		$primary_id = $bp->groups->current_group->id;

		if ( 'public' != $bp->groups->current_group->status && ( groups_is_user_member( $bp->loggedin_user->id, $bp->groups->current_group->id ) || $bp->loggedin_user->is_super_admin ) )
			$show_hidden = true;
	}
	
	if (bp_is_current_component( 'snippets' ) && !bp_current_action()){
		$scope = 'snippets';
		$display_comments = false;
		$object = apply_filters( 'bp_snippets_directory_objects','directory,group,group_forum_topic,blog_post');
	}
	else
		$scope = bp_current_action();
		

	if ( bp_is_current_action( 'permasnipt' ) || bp_is_current_action( 'embed' ) )
		$include = bp_action_variable( 0 );

	// Note: any params used for filtering can be a single value, or multiple values comma separated.
	$defaults = array(
						'display_comments' => false,   		// false for none
						'include'          => $include,     // pass an snippet_id or string of IDs comma-separated
						'exclude'          => $exclude,     // pass an snippet_id or string of IDs comma-separated
						'in'               => $in,          // comma-separated list or array of snippet IDs among which to search
						'sort'             => 'DESC',       // sort DESC or ASC
						'page'             => 1,            // which page to load
						'per_page'         => 10,           // number of items per page
						'max'              => false,        // max number to return
						'show_hidden'      => $show_hidden, // Show snippet items that are hidden site-wide?
						'scope'            => $scope,

						// Filtering
						'user_id'          => $user_id,     // user_id to filter on
						'object'           => $object,      // object to filter on e.g. directory,group,blog_post...
						'category'         => false,        // language
						'primary_id'       => $primary_id,  // object ID to filter on e.g. a group_id or blog_id etc.
						'secondary_id'     => false,        // secondary object ID to filter on e.g. a post_id

						// Searching
						'search_terms'     => false         // specify terms to search on
					);

	$r = wp_parse_args( $args, $defaults );
	extract( $r );
	
	
	if ( 'permasnipt' == $scope ){
		$display_comments = true;
		$object = apply_filters('bp_snippets_permasnipt_objects', 'directory,group,directory_comment,group_comment,group_forum_topic,blog_post');
		$show_hidden = true;
		$is_draft = true;
	}
		
	if (bp_is_current_component( 'groups' ) && bp_is_current_action( 'snippets' ) ){
		$scope = 'groups';
		if( is_numeric( bp_action_variable( 0 ) ) )
			$display_comments = true;
	}
	
	
	if ( isset($_REQUEST['s']) && strlen($_REQUEST['s']) > 1)
		$search_terms = wp_kses($_REQUEST['s'], array());
		
	
	// If you have passed a "scope" then this will override any filters you have passed.
	if ( 'mine' == $scope || 'add' == $scope || 'groups' == $scope || 'favs' == $scope ) {

		// determine which user_id applies
		if ( empty( $user_id ) )
			$user_id = ( !empty( $bp->displayed_user->id ) ) ? $bp->displayed_user->id : $bp->loggedin_user->id;

		// are we displaying user specific activity?
		if ( is_numeric( $user_id ) ) {
			

			switch ( $scope ) {
				case 'add':
				$object = 'directory,group,directory_comment,group_comment,group_forum_topic,group_forum_reply,blog_post';
				$show_hidden = true;
				$is_draft = true;
				break;
				case 'groups':
				if ( bp_is_active( 'groups' ) ) {
					$primary_id = $bp->groups->current_group->id;
					$object = apply_filters( 'bp_snippets_group_objects','group,group_forum_topic');
					$user_id = 0;
					if( !empty( $filter['category'] ) ) $category = $filter['category'];
				}
				break;
				case 'favs':
				$favs = bp_code_snippets_get_user_favorites( $user_id );
				if ( empty( $favs ) )
					return false;

				$include          = implode( ',', (array)$favs );
				$user_id = false;
				$show_hidden = ( !empty( $bp->displayed_user->id ) && $bp->displayed_user->id != $bp->loggedin_user->id ) ? 0 : 1;
				$is_draft = false;
				if( !empty( $filter['category'] ) ) $category = $filter['category'];
				break;
				case 'mine':
				$object = apply_filters('bp_snippets_mine_objects', 'directory,group,group_forum_topic,blog_post');
				$show_hidden = ( !empty( $bp->displayed_user->id ) && $bp->displayed_user->id != $bp->loggedin_user->id ) ? 0 : 1;
				$is_draft = ( !empty( $bp->displayed_user->id ) && $bp->displayed_user->id != $bp->loggedin_user->id ) ? 0 : 1;
				if( !empty( $filter['category'] ) ) $category = $filter['category'];
				break;
				
			}
		}
	}

	// Do not exceed the maximum per page
	if ( !empty( $max ) && ( (int)$per_page > (int)$max ) )
		$per_page = $max;
	
	// Support for basic filters in earlier BP versions.
	if ( isset( $_GET['afilter'] ) )
		$filter = array( 'object' => $_GET['afilter'] );
	else if ( !empty( $user_id ) || !empty( $object ) || !empty( $category ) || !empty( $primary_id ) || !empty( $secondary_id ) )
		$filter = array( 'user_id' => $user_id, 'object' => $object, 'category' => $category, 'primary_id' => $primary_id, 'secondary_id' => $secondary_id );
	else
		$filter = false;
		
	
	$snippets_template = new BP_Code_Snippets_Template( $page, $per_page, $max, $include, $sort, $filter, $search_terms, $display_comments, $show_hidden, $exclude, $in, $is_draft );

	return apply_filters( 'bp_has_snippets', $snippets_template->has_snippets(), $snippets_template );
}


function bp_snippets() {
	global $snippets_template;
	return $snippets_template->user_snippets();
}

function bp_the_snippet() {
	global $snippets_template;
	return $snippets_template->the_snippet();
}

function bp_snippet_pagination_count() {
	echo bp_get_snippet_pagination_count();
}

function bp_get_snippet_pagination_count() {
	global $bp, $snippets_template;

	$start_num = intval( ( $snippets_template->pag_page - 1 ) * $snippets_template->pag_num ) + 1;
	$from_num  = bp_core_number_format( $start_num );
	$to_num    = bp_core_number_format( ( $start_num + ( $snippets_template->pag_num - 1 ) > $snippets_template->total_snippet_count ) ? $snippets_template->total_snippet_count : $start_num + ( $snippets_template->pag_num - 1 ) );
	$total     = bp_core_number_format( $snippets_template->total_snippet_count );

	return sprintf( __( 'Viewing snippet %1$s to %2$s (of %3$s snippets)', 'bp-code-snippets' ), $from_num, $to_num, $total );
}

function bp_snippet_pagination_links() {
	echo bp_get_snippet_pagination_links();
}

function bp_get_snippet_pagination_links() {
	global $snippets_template;

	return apply_filters( 'bp_get_snippet_pagination_links', $snippets_template->pag_links );
}

function bp_snippet_has_more_items() {
	global $snippets_template;

	$remaining_pages = floor( ( $snippets_template->total_snippet_count - 1 ) / ( $snippets_template->pag_num * $snippets_template->pag_page ) );
	$has_more_items  = (int)$remaining_pages ? true : false;

	return apply_filters( 'bp_snippet_has_more_items', $has_more_items );
}

function bp_snippet_is_favorites_screen() {
	return bp_is_current_action( 'favs' );
}

function bp_snippet_count() {
	echo bp_get_snippet_count();
}

function bp_get_snippet_count() {
	global $snippets_template;

	return apply_filters( 'bp_get_snippet_count', (int)$snippets_template->snippet_count );
}

function bp_snippet_per_page() {
	echo bp_get_snippet_per_page();
}

function bp_get_snippet_per_page() {
	global $snippets_template;

	return apply_filters( 'bp_get_snippet_per_page', (int)$snippets_template->pag_num );
}

function bp_snippets_no_snippet() {
	global $bp_snippet_no_snippet;

	echo bp_get_snippets_no_snippet();
}

function bp_get_snippets_no_snippet() {
	global $bp_snippet_no_snippet;

	return apply_filters( 'bp_get_snippets_no_snippet', $bp_snippet_no_snippet );
}

function bp_snippet_id() {
	echo bp_get_snippet_id();
}

	function bp_get_snippet_id() {
		global $snippets_template;
		return apply_filters( 'bp_get_snippet_id', $snippets_template->snippet->id_cs );
	}

function bp_snippet_action(){
	echo bp_get_snippet_action();
}
	function bp_get_snippet_action(){
		global $snippets_template;
		
		$user = $item_action = false;
		
		// Any group ?
		if ( $snippets_template->snippet->item_id > 0 ){
			
			if ( strpos( $snippets_template->snippet->object, 'group') > -1 && bp_is_active( 'groups' ) ){
				if ( $group = groups_get_group( array( 'group_id' => $snippets_template->snippet->item_id ) ) ) {

					$item_action = sprintf( __('in %s', 'bp-code-snippets'), bp_core_fetch_avatar( array( 'item_id' => $snippets_template->snippet->item_id, 'object' => 'group', 'type' => 'thumb', 'width' => 20, 'height' => 20) ) . '<a href="' . bp_get_group_permalink( $group ) . '">'. $group->name.'</a>' );

					if( $snippets_template->snippet->secondary_id > 0 && $snippets_template->snippet->object != "group_comment" ){
						$topic_id = intval( $snippets_template->snippet->secondary_id );
						$topic = bp_is_active( 'forums' ) ? bp_forums_get_topic_details( $topic_id ) : false ;

						$topic_link =  bp_is_active( 'forums' ) ? bp_get_group_permalink( $group ) . 'forum/topic/' . $topic->topic_slug : false ;
						$topic_title =  bp_is_active( 'forums' ) ? $topic->topic_title : false;
						
						$item_action .= sprintf( __(' to the topic %s', 'bp-code-snippets'), '<a href="' . $topic_link . '" title="' . $topic_title . '">' . $topic_title . '</a>');
					}

				}
			}
			if( strpos( $snippets_template->snippet->object, 'blog' ) > -1  && $snippets_template->snippet->secondary_id > 0 ) {
				$blog_info = bp_code_snippets_get_blog_post_data( array( 'blog_id' => $snippets_template->snippet->item_id, 
																	    'post_id' => $snippets_template->snippet->secondary_id,
																	 	'title' => 1 ));
				
				$item_action = sprintf( __('in %s', 'bp-code-snippets'), bp_core_fetch_avatar( array( 'item_id' => $snippets_template->snippet->item_id, 'object' => 'blog', 'type' => 'thumb', 'width' => 20, 'height' => 20) ) . '<a href="' . $blog_info['url'] . '">'. $blog_info['name'].'</a>' );
				
				if( !empty( $blog_info['permalink'] ) )
					$item_action .= sprintf( __(' to the post %s', 'bp-code-snippets'), '<a href="' . $blog_info['permalink'] . '" title="' . $blog_info['title'] . '">' . $blog_info['title'] . '</a>');
			}
		}
		
		//user
		
		$user = bp_core_fetch_avatar( array( 'item_id' => $snippets_template->snippet->id_user, 'object' => 'user', 'type' => 'thumb', 'width' => 20, 'height' => 20) ) . bp_core_get_userlink($snippets_template->snippet->id_user);
		
		$time_since = apply_filters_ref_array( 'bp_code_snippets_time_since', array( '<span class="time-since">' . bp_core_time_since( $snippets_template->snippet->date_cs ) . '</span>' ) );
		
		$action = sprintf(__('Posted by %s %s %s', 'bp-code-snippets'), $user, $item_action, $time_since);	
		
		return apply_filters('bp_get_snippet_action', '<p>' . $action . '</p>');
	}

function bp_snippet_permasnpt() {
	echo bp_get_snippet_permasnpt();
}
	
	function bp_get_snippet_permasnpt() {
		global $snippets_template;
		
		$redirect = site_url();
		
		if ( $snippets_template->snippet->item_id > 0 ){
			
			
			if( strpos( $snippets_template->snippet->object, 'group' ) > -1 && bp_is_active( 'groups' ) ) {
				
				if ( $group = groups_get_group( array( 'group_id' => $snippets_template->snippet->item_id ) ) ) {
					$group_link = bp_get_group_permalink( $group ) . bp_get_snippet_slug() . '/' . $snippets_template->snippet->id_cs . '/';
					if( $snippets_template->snippet->secondary_id > 0 && $snippets_template->snippet->object != "group_comment" ){
						$topic_id = intval( $snippets_template->snippet->secondary_id );
						$topic = bp_is_active( 'forums' ) ? bp_forums_get_topic_details( $topic_id ) : false ;

						$redirect =  bp_is_active( 'forums' ) ? bp_get_group_permalink( $group ) . 'forum/topic/' . $topic->topic_slug : site_url();
					}
					else $redirect = $group_link;
				}		
				
			}
			
			if( strpos( $snippets_template->snippet->object, 'blog' ) > -1  && $snippets_template->snippet->secondary_id > 0 ) {
				$redirect_info = bp_code_snippets_get_blog_post_data( array( 'blog_id' => $snippets_template->snippet->item_id, 
																	    'post_id' => $snippets_template->snippet->secondary_id ));
				$redirect = $redirect_info['permalink'];
			}
		}
		else $redirect = bp_get_root_domain() . '/' . bp_get_snippet_slug() . '/permasnipt/' . $snippets_template->snippet->id_cs .'/';
		
		return apply_filters( 'bp_get_snippet_permasnpt', $redirect );
	}

function bp_snippet_oembed_link() {
	echo bp_get_snippet_oembed_link();
}

	function bp_get_snippet_oembed_link() {
		global $snippets_template;
		return apply_filters('bp_get_snippet_oembed_link', bp_get_root_domain() . '/' . bp_get_snippet_slug() . '/embed/' . $snippets_template->snippet->id_cs .'/');
	}

function bp_snippet_title() {
	echo bp_get_snippet_title();
}

	function bp_get_snippet_title() {
		global $snippets_template;
		return apply_filters( 'bp_get_snippet_title', $snippets_template->snippet->snippet_title );
	}
	

function bp_snippet_excerpt() {
	echo bp_get_snippet_excerpt();
}

	function bp_get_snippet_excerpt() {
		global $snippets_template;
		return apply_filters( 'bp_get_snippet_excerpt', bp_create_excerpt( $snippets_template->snippet->snippet_purpose ) );
	}
	
function bp_snippet_the_purpose() {
	echo bp_get_snippet_purpose();
}

	function bp_get_snippet_purpose() {
		global $snippets_template;
		return apply_filters( 'bp_get_snippet_purpose', $snippets_template->snippet->snippet_purpose );
	}
	
function bp_snippet_the_content() {
	echo bp_get_snippet_the_content();
}

	function bp_get_snippet_the_content() {
		global $snippets_template;
		return apply_filters( 'bp_get_snippet_the_content', $snippets_template->snippet->snippet_content );
	}

function bp_snippet_type_avatar(){
	echo apply_filters( 'bp_snippet_type_avatar',  bp_get_snippet_type() );
}

function bp_snippet_type() {
	echo bp_get_snippet_type();
}

	function bp_get_snippet_type() {
		global $snippets_template;
			
		return apply_filters( 'bp_get_snippet_type', $snippets_template->snippet->snippet_type );
	}


function bp_snippet_the_snippet_content() {
	echo bp_get_snippet_the_snippet_content();
}
	function bp_get_snippet_the_snippet_content(){
		global $snippets_template;
		$snippet_html = '<div class="code_in_content"><pre class="brush: '.bp_get_snippet_type().'">'.bp_get_snippet_the_content().'</pre></div>';
		return apply_filters( 'bp_get_snippet_the_snippet_content', $snippet_html, bp_get_snippet_the_content(), bp_get_snippet_type(), bp_get_snippet_id(), bp_get_snippet_permasnpt(), $snippets_template->snippet->hide );
	}

function bp_snippet_item_id() {
	echo bp_get_snippet_item_id();
}

	function bp_get_snippet_item_id() {
		global $snippets_template;
		return apply_filters( 'bp_get_snippet_item_id', $snippets_template->snippet->item_id );
	}


function bp_code_snippet_can_comment() {
	global $snippets_template, $bp;

	$can_comment = true;
	
	if( strpos( $snippets_template->snippet->object, 'group_forum' ) > -1 ){
		$topic_id = intval( $snippets_template->snippet->secondary_id );
		$topic = bp_is_active( 'forums' ) ? bp_forums_get_topic_details( $topic_id ) : false ;
		
		if( bp_is_active( 'forums' )  && $topic->topic_open == 0)
			$can_comment = false;
	}
	
	if( strpos( $snippets_template->snippet->object, 'blog' ) > -1 ){
		if( is_multisite() ) {

				switch_to_blog( $snippets_template->snippet->item_id );
				$can_comment = comments_open( $snippets_template->snippet->secondary_id );
				
				
				restore_current_blog();
		} else {
			$can_comment = comments_open( $snippets_template->snippet->secondary_id );
		}
		
	}

	return apply_filters( 'bp_code_snippet_can_comment', $can_comment );
}

function bp_snippet_comment_count() {
	echo bp_get_snippet_comment_count();
}

	function bp_get_snippet_comment_count() {
		global $snippets_template;
		
		$comment_count = (int)$snippets_template->snippet->cs_comment_count;
		
		if ( $snippets_template->snippet->item_id > 0 ){
			
			
			if( strpos( $snippets_template->snippet->object, 'group' ) > -1  ) {
				
					if( $snippets_template->snippet->secondary_id > 0 && $snippets_template->snippet->object != "group_comment" ){
						$topic_id = intval( $snippets_template->snippet->secondary_id );
						$topic = bp_is_active( 'forums' ) ? bp_forums_get_topic_details( $topic_id ) : false ;

						$comment_count = bp_is_active( 'forums' ) ? intval($topic->topic_posts) - 1 : false ;
					}
					
			}		
			
			if( strpos( $snippets_template->snippet->object, 'blog' ) > -1  && $snippets_template->snippet->secondary_id > 0) {
				$blog_info = bp_code_snippets_get_blog_post_data( array( 'blog_id' => $snippets_template->snippet->item_id, 
																	    'post_id' => $snippets_template->snippet->secondary_id,
																	 	'comment' => 1 ));
																	
				$comment_count = $blog_info['comment_count'];
			}
		} 

		return apply_filters( 'bp_get_snippet_comment_count', $comment_count );
	}

function bp_snippet_comment_link() {
	echo bp_get_snippet_comment_link();
}

	function bp_get_snippet_comment_link() {
		global $snippets_template;
		
		$comment_link = '#s-reply';
		
		if( strpos( $snippets_template->snippet->object, 'group_forum') > -1 && $snippets_template->snippet->secondary_id > 0 )
			$comment_link = '#post-reply';
			
		if( strpos( $snippets_template->snippet->object, 'blog_post') > -1 && $snippets_template->snippet->secondary_id > 0 )
			$comment_link = '#respond';
		
		if( !bp_action_variable( 0 ) || !is_numeric( bp_action_variable( 0 ) ) )
			$comment_link = bp_get_snippet_permasnpt() . $comment_link;
		
		return apply_filters( 'bp_get_activity_comment_link', $comment_link );
	}

function bp_snippets_have_comments() {
	global $snippets_template;
	
	if($snippets_template->snippet->cs_comment_count > 0) return true;
	else return false;
}

function bp_snippets_list_comments() {
	global $snippets_template;
	
	$comments = $snippets_template->snippet->comments;
	
	foreach($comments as $comment) {
		?>
		<li class="comment" id="snippet-comment-<?php echo $comment->id_cs_comment;?>">
			<div class="comment-avatar-box">
				<div class="avb">
					<a href="<?php echo bp_core_get_userlink($comment->user_id, false, true) ?>" rel="nofollow">
						
						<?php echo bp_core_fetch_avatar( array( 'item_id' => $comment->user_id, 'object' => 'user', 'type' => 'thumb', 'width' => 50, 'height' => 50) ); ?>
						
					</a>
				</div>
			</div>

			<div class="comment-content">
				<div class="comment-meta">
					<p>
						<?php
							printf( __( '<a href="%1$s" rel="nofollow">%2$s</a> said <span class="time-since">%3$s</span>', 'bp-code-snippets' ), bp_core_get_userlink($comment->user_id, false, true), $comment->display_name, bp_core_time_since( $comment->date_comment ) );
						?>
					</p>
				</div>

				<div class="comment-entry">

					<?php echo apply_filters('bp_code_snippets_the_comment', $comment->comment_content); ?>
					
				</div>
				
				<div class="comment-options">

						<?php if ( bp_code_snippets_can_delete() ) : ?>
							<?php printf( '<a class="button comment-delete-link bp-secondary-action" href="%1$s" title="%2$s">%3$s</a> ', bp_code_snippets_get_delete_comment_link( $comment->id_cs_comment ), esc_attr__( 'Delete comment', 'bp-code-snippets' ), __( 'Delete', 'bp-code-snippets' ) ) ?>
						<?php endif; ?>

				</div>
			</div>
		</li>
		<?php
	}
}

function bp_code_snippets_get_delete_comment_link( $comment_id ) {
	
		return apply_filters( 'bp_code_snippets_get_delete_comment_link', wp_nonce_url( home_url( bp_get_snippet_slug() . '/moderate/' . $comment_id . '/' ), '_snippet_del_comment' ) );
}

function bp_code_snippets_is_favorite() {
	echo bp_get_code_snippets_is_favorite();
}

	function bp_get_code_snippets_is_favorite() {
		global $bp, $snippets_template;

 		return apply_filters( 'bp_get_code_snippets_is_favorite', in_array( $snippets_template->snippet->id_cs, (array)$snippets_template->my_favs ) );
	}


function bp_code_snippets_unfavorite_link() {
	echo bp_get_code_snippets_unfavorite_link();
}

	function bp_get_code_snippets_unfavorite_link() {
		global $bp, $snippets_template;
		return apply_filters( 'bp_get_activity_unfavorite_link', wp_nonce_url( home_url( bp_get_snippet_slug() . '/unfavorite/' . $snippets_template->snippet->id_cs . '/' ), '_snippet_unfav' ) );
	}

function bp_code_snippets_favorite_link() {
	echo bp_get_code_snippets_favorite_link();
}

	function bp_get_code_snippets_favorite_link() {
		global $bp, $snippets_template;
		return apply_filters( 'bp_get_code_snippets_favorite_link', wp_nonce_url( home_url( bp_get_snippet_slug() . '/favorite/' . $snippets_template->snippet->id_cs . '/' ), '_snippet_fav' ) );
	}

function bp_code_snippets_favorite_count_for_snippet() {
	echo bp_get_code_snippets_favorite_count_for_snippet();
}
	function bp_get_code_snippets_favorite_count_for_snippet() {
		global $snippets_template;
		return apply_filters('bp_get_code_snippets_favorite_count_for_snippet', '<s></s><i></i><span>'.$snippets_template->snippet->favcount.'</span>');
	}

function bp_code_snippets_can_delete( $snippet = false, $object = false, $snippet_user_id = false ) {
	global $snippets_template, $bp;
 	
	if ( !$snippet )
		$snippet = $snippets_template->snippet;
		
	if( !$object )
		$object = $snippets_template->snippet->object;
		
	if( !$snippet_user_id )
		$snippet_user_id = $snippet->id_user;

	$can_delete = false;

	if ( $bp->loggedin_user->is_super_admin )
		$can_delete = true;

	if ( $snippet_user_id == $bp->loggedin_user->id && $object != 'group' )
		$can_delete = true;
		
	if ( ( $bp->is_item_admin || $bp->is_item_mod ) && $bp->is_single_item )
		$can_delete = true;

	return apply_filters( 'bp_code_snippets_can_delete', $can_delete );
}

function bp_code_snippet_delete_link() {
	echo bp_get_code_snippet_delete_link();
}

	function bp_get_code_snippet_delete_link() {
		global $snippets_template, $bp;

		$url   = bp_get_root_domain() . '/' . bp_get_snippet_slug() . '/delete/' . $snippets_template->snippet->id_cs;
		$class = 'delete-snippet';

		// Determine if we're on a single activity page, and customize accordingly
		if ( bp_is_current_component( 'snippets' ) && is_numeric( bp_current_action() ) ) {
			$url   = add_query_arg( array( 'redirect_to' => wp_get_referer() ), $url );
			$class = 'delete-snippet-single';
		}

		$link = '<a href="' . wp_nonce_url( $url, 'bp_snippet_delete_link' ) . '" class="button item-button bp-secondary-action ' . $class . ' confirm" rel="nofollow">' . __( 'Delete', 'bp-code-snippets' ) . '</a>';
		return apply_filters( 'bp_get_code_snippet_delete_link', $link );
	}
	

function bp_code_snippets_can_edit( $snippet = false, $snippet_user_id = false ) {
	global $snippets_template, $bp;
 	
	if ( !$snippet )
		$snippet = $snippets_template->snippet;
		
	if( !$snippet_user_id )
		$snippet_user_id = $snippet->id_user;

	$can_edit = false;

	if ( $bp->loggedin_user->is_super_admin )
		$can_edit = true;

	if ( $snippet_user_id == $bp->loggedin_user->id )
		$can_edit = true;
		
	if ( ( $bp->is_item_admin || $bp->is_item_mod ) && $bp->is_single_item )
		$can_edit = true;

	return apply_filters( 'bp_code_snippets_can_edit', $can_edit );
}

function bp_code_snippet_edit_link() {
	echo bp_get_code_snippet_edit_link();
}

	function bp_get_code_snippet_edit_link() {
		global $snippets_template, $bp;
		
		$permasnpt = bp_get_snippet_permasnpt();
		
		if( in_array( $snippets_template->snippet->object, array( 'directory', 'group') ) )
			$permasnpt .= 'edit/';

		$class = 'edit-snippet';

		$link = '<a href="' . $permasnpt . '" class="button item-button bp-secondary-action ' . $class . '" rel="nofollow">' . __( 'Edit', 'bp-code-snippets' ) . '</a>';
		return apply_filters( 'bp_get_code_snippet_edit_link', $link );
	}



/* rss */
function bp_code_snippets_feed() {
	global $bp;
	
	$feed = false;
	
	if( bp_is_current_component( 'snippets' ) ) {
		
		if( !bp_current_action() ) {
			$feed = bp_get_code_snippets_directory_feed_link();
			$title = __('Public Snippets feed', 'bp-code-snippets');
		}
	
	}
	
	if( bp_is_current_component( 'groups' ) && bp_is_current_action( 'snippets' ) ){
		
		if( !bp_action_variable( 0 ) ) {
			$feed = bp_get_code_snippets_group_feed_link();
			$title = __('Group Snippets feed', 'bp-code-snippets');
		}
			
	}
	?>
	<?php if( !empty( $feed ) ) :?>
		<li class="feed"><a href="<?php echo $feed ;?>" title="<?php echo $title; ?>"><?php _e( 'RSS', 'bp-code-snippets' ); ?></a></li>
	<?php endif;?>
	<?php
}

function bp_code_snippets_directory_feed_link() {
	echo bp_get_code_snippets_directory_feed_link();
}

	function bp_get_code_snippets_directory_feed_link() {
		return apply_filters( 'bp_get_code_snippets_directory_feed_link', bp_get_root_domain() . '/' . bp_get_snippet_slug() . '/feed/' );
	}
	
function bp_code_snippets_group_feed_link() {
	echo bp_get_code_snippets_group_feed_link();
}

	function bp_get_code_snippets_group_feed_link() {
		global $bp;
		return apply_filters( '', bp_get_group_permalink( $bp->groups->current_group ) . bp_get_snippet_slug() . '/feed/');
	}

function bp_code_snippets_feed_item_date() {
	echo bp_get_code_snippets_feed_item_date();
}

	function bp_get_code_snippets_feed_item_date() {
		global $snippets_template;

		return apply_filters( 'bp_get_activity_feed_item_date', $snippets_template->snippet->date_cs );
	}
	
function bp_code_snippets_thickbox_button_href() {
	echo bp_get_code_snippets_thickbox_button_href();
}
	function bp_get_code_snippets_thickbox_button_href() {
		return apply_filters('bp_get_code_snippets_thickbox_button_href',  bp_get_root_domain() . '/' . bp_get_snippet_slug() . '/add/' );
	}
?>