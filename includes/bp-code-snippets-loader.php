<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'BP_Component' ) ){
	require_once( WP_PLUGIN_DIR . '/buddypress/bp-loader.php' );
}

class BP_Code_Snippets_Component extends BP_Component {
	
	function __construct() {
		global $bp;
		
		parent::start(
			'snippets',
			__( 'Snippets', 'bp-code-snippets' ),
			BP_CS_PLUGIN_DIR
		);
		
		$this->includes();
		
		$bp->active_components[$this->id] = '1';
	}
	
	function includes() {
		$includes = array(
			'includes/bp-code-snippets-actions.php',
			'includes/bp-code-snippets-screens.php',
			'includes/bp-code-snippets-functions.php',
			'includes/bp-code-snippets-filters.php',
			'includes/bp-code-snippets-class.php',
			'includes/bp-code-snippets-templatetags.php',
			'includes/bp-code-snippets-ajax.php',
			'includes/bp-code-snippets-notifications.php'
		);
		
		if( bp_is_active( 'groups' ) )
			$includes[] = 'includes/bp-code-snippets-groups.php';
		
		parent::includes( $includes );
		
		// handling older version
		if ( bp_code_snippets_had_previous_version() ) {
			include( BP_CS_PLUGIN_DIR . '/includes/bp-code-snippets-deprecated.php' );
		}
	}
	
	function setup_globals() {
		global $bp;

		// Define a slug, if necessary
		if ( !defined( 'BP_CS_SLUG' ) )
			define( 'BP_CS_SLUG', $this->id );

		/* Global tables for snippets component */
		$global_tables = array(
			'table_cs'      => $bp->table_prefix . 'code_snippets',
			'table_cs_comment' => $bp->table_prefix . 'code_snippets_comments',
			'table_cs_meta' => $bp->table_prefix . 'code_snippets_meta'
		);

		// All globals for activity component.
		// Note that global_tables is included in this array.
		$globals = array(
			'path'                  => BP_CS_PLUGIN_DIR,
			'slug'                  => BP_CS_SLUG,
			'root_slug'             => isset( $bp->pages->snippets->slug ) ? $bp->pages->snippets->slug : BP_CS_SLUG,
			'has_directory'         => true,
			'search_string'         => __( 'Search Snippets...', 'bp-code-snippets' ),
			'global_tables'         => $global_tables,
			'notification_callback' => 'bp_code_snippets_format_notifications',
		);

		parent::setup_globals( $globals );
	}
	
	function setup_nav() {
		global $bp;
		
		$main_nav = array(
			'name'                => __( 'Snippets', 'bp-code-snippets' ),
			'slug'                => $this->slug,
			//'show_for_displayed_user' => false,
			'screen_function'     => 'bp_code_snippets_screen_member',
			'default_subnav_slug' => 'mine',
			'item_css_id'         => $this->id
		);
		
		$user_domain = ( !empty( $bp->displayed_user->id ) ) ? $bp->displayed_user->domain : $bp->loggedin_user->domain;
		
		$bp_code_snippets_home_link = trailingslashit( $user_domain . $this->slug );
		
		// Add the My Widgets and My Home nav item
		$sub_nav[] = array(
			'name'            => __( 'Snippets', 'bp-code-snippets' ),
			'slug'            => 'mine',
			'parent_url'      => $bp_code_snippets_home_link,
			'parent_slug'     => $this->slug,
			'screen_function' => 'bp_code_snippets_screen_member',
			'position'        => 10,
			'item_css_id'     => $this->id.'-mine'
		);
		$sub_nav[] = array(
			'name'            => __( 'Favorites', 'bp-code-snippets' ),
			'slug'            => 'favs',
			'parent_url'      => $bp_code_snippets_home_link,
			'parent_slug'     => $this->slug,
			'screen_function' => 'bp_code_snippets_screen_member',
			'position'        => 20,
			'item_css_id'     => $this->id.'-favs'
		);
		
		parent::setup_nav( $main_nav, $sub_nav );
	}
	
	function setup_admin_bar() {
		global $bp;

		// Prevent debug notices
		$wp_admin_nav = array();

		// Menus for logged in user
		if ( is_user_logged_in() ) {

			// Setup the logged in user variables
			$bp_code_snippets_link = trailingslashit( $bp->loggedin_user->domain . $this->slug );

			// Add main Shop menu
			$wp_admin_nav[] = array(
				'parent' => 'my-account-buddypress',
				'id'     => 'my-account-' . $this->id,
				'title'  => __( 'Snippets', 'bp-code-snippets' ),
				'href'   => trailingslashit( $bp_code_snippets_link )
			);
			
			$wp_admin_nav[] = array(
				'parent' => 'my-account-' . $this->id,
				'id'     => 'my-account-' . $this->id . '-mine',
				'title'  => __( 'My Snippets', 'bp-code-snippets' ),
				'href'   => trailingslashit( $bp_code_snippets_link . 'mine' )
			);

			// Goods
			$wp_admin_nav[] = array(
				'parent' => 'my-account-' . $this->id,
				'id'     => 'my-account-' . $this->id . '-favs',
				'title'  => __( 'My favorites', 'bp-code-snippets' ),
				'href'   => trailingslashit( $bp_code_snippets_link . 'favs' )
			);

		}

		parent::setup_admin_bar( $wp_admin_nav );
	}
}

//$bp->snippets = new BP_Code_Snippets_Component();

function bp_code_snippets_load_core_component() {
	global $bp;

	$bp->snippets = new BP_Code_Snippets_Component();
}
add_action( 'bp_loaded', 'bp_code_snippets_load_core_component' );
