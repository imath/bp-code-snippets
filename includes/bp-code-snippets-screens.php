<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;


/** 1.7 bp theme compat **/
function bp_code_snippets_add_template_stack( $templates ) {
    // if we're on a page of our plugin and the theme is not BP Default, then we
    // add our path to the template path array
    if ( ( bp_is_current_component( 'snippets' ) && !bp_code_snippets_is_bp_default() ) || ( bp_is_current_component( 'groups' ) && bp_is_current_action( 'snippets' ) ) ) {
 
        $templates[] = BP_CS_PLUGIN_DIR . '/templates/';

    }
 
    return $templates;
}
 
add_filter( 'bp_get_template_stack', 'bp_code_snippets_add_template_stack', 10, 1 );

/**
* bp_code_snippets_load_template_filter
* loads template filter
*/
function bp_code_snippets_load_template_filter( $found_template, $templates ) {
	global $bp,$bp_deactivated;
	

	//Only filter the template location when we're on the example component pages.
	if ( !bp_is_current_component( 'snippets' ) )
		return $found_template;
		
	// theme compat in 1.7
	if( !bp_code_snippets_is_bp_default() && !in_array( bp_current_action(), array( 'add', 'embed' ) ) )
		return $found_template;

	foreach ( (array) $templates as $template ) {
		if ( file_exists( STYLESHEETPATH . '/' . $template ) )
			$filtered_templates[] = STYLESHEETPATH . '/' . $template;
		else
			$filtered_templates[] = BP_CS_PLUGIN_DIR . '/templates/' . $template;
	}

	$found_template = $filtered_templates[0];

	return apply_filters( 'bp_code_snippets_load_template_filter', $found_template );
}

add_filter( 'bp_located_template', 'bp_code_snippets_load_template_filter', 10, 2 );


function bp_code_snippets_screen_index() {
	if ( !bp_displayed_user_id() && bp_is_current_component( 'snippets' ) && !bp_current_action() ) {
		bp_update_is_directory( true, 'snippets' );

		do_action( 'bp_code_snippets_screen_index' );

		bp_core_load_template( apply_filters( 'bp_code_snippets_template_dir', 'snippets-dir' ) );
	}
}
add_action( 'bp_screens', 'bp_code_snippets_screen_index', 2 );


function bp_code_snippets_screen_catched() {
	global $bp;
	
	if ( !bp_displayed_user_id() && bp_is_current_component( 'snippets' ) && bp_is_current_action( 'permasnipt' ) ) {
		
		if ( isset( $_GET['n'] ) ) {
			bp_core_delete_notifications_by_type( $bp->loggedin_user->id, 'snippets', 'comment' );
		}
		
		if( bp_action_variable( 1 ) == 'edit' ) {
			
			do_action( 'bp_code_snippets_screen_snippet_edit' );
			
			$template = 'snippets-dir-edit';
			
		} else {
			
			do_action( 'bp_code_snippets_screen_permasnipt' );

			$template = 'snippets-single';
			
		}
		
	} 
	elseif ( !bp_displayed_user_id() && bp_is_current_component( 'snippets' ) && ( bp_is_current_action( 'mine' ) || bp_is_current_action( 'favs' ) ) ) {
		
		do_action( 'bp_code_snippets_screen_mine' );

		$template = 'snippets-dir';
	}
	
	elseif ( !bp_displayed_user_id() && bp_is_current_component( 'snippets' ) && bp_is_current_action( 'embed' ) ) {
		
		do_action( 'bp_code_snippets_screen_embed' );
		
		// we don't need scripts and css from bp default !
		remove_action( 'wp_enqueue_scripts', 'bp_dtheme_enqueue_scripts' );
		remove_action( 'wp_enqueue_scripts', 'bp_dtheme_enqueue_styles');
		if( has_action( 'wp_footer', 'bp_core_admin_bar') ){
			remove_action( 'wp_footer', 'bp_core_admin_bar', 8);
			wp_deregister_style('bp-admin-bar');
		}
		
		// we don't need wp admin bar !
		remove_action( 'wp_head', '_admin_bar_bump_cb');
		add_filter( 'show_admin_bar', 'bp_code_snippets_hide_for_embed', 1, 1  );

		$template = 'snippets-embed';
	}
	
	elseif ( !bp_displayed_user_id() && bp_is_current_component( 'snippets' ) && bp_is_current_action( 'add' ) ) {
		
		do_action( 'bp_code_snippets_screen_add' );
		
		// we don't need scripts from bp default !
		remove_action( 'wp_enqueue_scripts', 'bp_dtheme_enqueue_scripts' );
		if( has_action( 'wp_footer', 'bp_core_admin_bar') ){
			remove_action( 'wp_footer', 'bp_core_admin_bar', 8);
			wp_deregister_style('bp-admin-bar');
		}
		
		// we don't need wp admin bar !
		remove_action( 'wp_head', '_admin_bar_bump_cb');
		add_filter( 'show_admin_bar', 'bp_code_snippets_hide_for_embed', 1, 1  );

		$template = 'snippets-create-thickbox';
	}
	
	if( !empty($template) ) 
		bp_core_load_template( apply_filters( 'bp_code_snippets_template_catched', $template ) );
}
add_action( 'bp_screens', 'bp_code_snippets_screen_catched', 3 );

function bp_code_snippets_screen_group_display() {
	global $bp;
	
	if ( !bp_displayed_user_id() && bp_is_current_component( 'groups' ) && bp_is_current_action( 'snippets' ) ) {
		
		$template = 'snippets-group';
		
		if( bp_action_variable( 0 ) && is_numeric( bp_action_variable( 0 ) ) ) {
			
			if ( isset( $_GET['n'] ) ) {
				bp_core_delete_notifications_by_type( $bp->loggedin_user->id, 'snippets', 'comment' );
			}
			
			if( bp_action_variable( 1 ) == 'edit' )
				$template = 'snippets-edit';
			else
				$template = 'snippets-group-single';
		}

		do_action( 'bp_code_snippets_screen_group_display' );

		bp_code_snippets_locate_template( apply_filters( 'bp_code_snippets_template_group', $template ) );
	}
}

function bp_code_snippets_screen_member(){
	global $bp;
	
	if ( isset( $_GET['n'] ) ) {
		bp_core_delete_notifications_by_type( $bp->loggedin_user->id, 'snippets', 'comment' );
	}
	bp_core_load_template( apply_filters( 'bp_code_snippets_template_member', 'snippets-member' ) );
	
	if( !bp_code_snippets_is_bp_default() )
		add_filter('bp_get_template_part','bp_code_snippets_user_template_part', 10, 3 );
}
 
function bp_code_snippets_user_template_part( $templates, $slug, $name ) {
    if( $slug != 'members/single/plugins' )
        return $templates;
 
    return array( 'snippets-member.php' );
}

function bp_code_snippets_load_template( $template, $require_once = true ){
	if ( file_exists( STYLESHEETPATH . '/' . $template ) )
		$filtered_templates = STYLESHEETPATH . '/' . $template;
	else
		$filtered_templates = BP_CS_PLUGIN_DIR . '/templates/' . $template;
		
	load_template( apply_filters( 'bp_code_snippets_load_template', $filtered_templates ),  $require_once);
}

function bp_code_snippets_locate_template( $template = false, $require_once = true ) {
    if( empty( $template ) )
        return false;
 
    if( bp_code_snippets_is_bp_default() ) {
	
        bp_code_snippets_load_template( $template . '.php', $require_once );

    } else {
        bp_get_template_part( $template );
    }
}

/**
 * Renders the snippet settings field on the Notification Settings page
 */
function bp_code_snippets_screen_notification_settings() {
	global $bp;

	if ( !$snippet_mail_comment = bp_get_user_meta( $bp->displayed_user->id, 'snippet_mail_comment', true ) )
		$snippet_mail_comment  = 'yes';

?>

	<table class="notification-settings" id="snippets-notification-settings">
		<thead>
			<tr>
				<th class="icon"></th>
				<th class="title"><?php _e( 'Snippets', 'bp-code-snippets' ) ?></th>
				<th class="yes"><?php _e( 'Yes', 'bp-code-snippets' ) ?></th>
				<th class="no"><?php _e( 'No', 'bp-code-snippets' )?></th>
			</tr>
		</thead>

		<tbody>
			<tr id="snippets-notification-settings-mail-comment">
				<td></td>
				<td><?php _e( 'A member added a comment to one of your snippets', 'bp-code-snippets' ) ?></td>
				<td class="yes"><input type="radio" name="notifications[snippet_mail_comment]" value="yes" <?php checked( $snippet_mail_comment, 'yes', true ) ?>/></td>
				<td class="no"><input type="radio" name="notifications[snippet_mail_comment]" value="no" <?php checked( $snippet_mail_comment, 'no', true ) ?>/></td>
			</tr>

			<?php do_action( 'bp_code_snippets_screen_notification_settings' ); ?>

		</tbody>
	</table>

<?php
}
add_action( 'bp_notification_settings', 'bp_code_snippets_screen_notification_settings' );



class BP_Plugin_Theme_Compat {
 
    /**
     * Setup the bp plugin component theme compatibility
     */
    public function __construct() {
        /* this is where we hook bp_setup_theme_compat !! */
        add_action( 'bp_setup_theme_compat', array( $this, 'is_code_snippets' ) );
    }
 
    /**
     * Are we looking at something that needs theme compatability?
     */
    public function is_code_snippets() {
 
        if ( !bp_displayed_user_id() && bp_is_current_component( 'snippets' ) && ( !bp_current_action() || in_array( bp_current_action(), array( 'mine', 'favs' ) ) ) ) {
            // first we reset the post
            add_action( 'bp_template_include_reset_dummy_post_data', array( $this, 'directory_dummy_post' ) );
            // then we filter 'the_content' thanks to bp_replace_the_content
            add_filter( 'bp_replace_the_content', array( $this, 'directory_content'    ) );
 
        }

		if ( !bp_displayed_user_id() && bp_is_current_component( 'snippets' ) && bp_is_current_action( 'permasnipt' ) ) {

			if( bp_action_variable( 1 ) == 'edit' ) {
				
				add_action( 'bp_template_include_reset_dummy_post_data', array( $this, 'permasnipt_edit_dummy_post' ) );
	            // then we filter 'the_content' thanks to bp_replace_the_content
	            add_filter( 'bp_replace_the_content', array( $this, 'permasnipt_edit_content'    ) );


			} else {

				add_action( 'bp_template_include_reset_dummy_post_data', array( $this, 'permasnipt_dummy_post' ) );
	            // then we filter 'the_content' thanks to bp_replace_the_content
	            add_filter( 'bp_replace_the_content', array( $this, 'permasnipt_content'    ) );

			}

		}
    }
 
    /**
     * Update the global $post with directory data
     */
    public function directory_dummy_post() {
	
		$title = __( 'Snippets', 'bp-code-snippets' );
		
		if ( is_user_logged_in() )
			$title .= '&nbsp;<a class="button show-hide-new-snippet bp-title-button" href="#new-snippet" id="new-topic-button">'.__( 'New Snippet', 'bp-code-snippets' ).'</a>';
 
        bp_theme_compat_reset_post( array(
            'ID'             => 0,
            'post_title'     => $title,
            'post_author'    => 0,
            'post_date'      => 0,
            'post_content'   => '',
            'post_type'      => 'bp_snippets',
            'post_status'    => 'publish',
            'is_archive'     => true,
            'comment_status' => 'closed'
        ) );
    }
    /**
     * Filter the_content with bp-plugin index template part
     */
    public function directory_content() {
        bp_buffer_template_part( 'snippets-dir' );
    }


	/**
     * Update the global $post with permasnipt edit data
     */
    public function permasnipt_edit_dummy_post() {
	
		$title = __( 'Snippets', 'bp-code-snippets' );
		
 
        bp_theme_compat_reset_post( array(
            'ID'             => 0,
            'post_title'     => $title,
            'post_author'    => 0,
            'post_date'      => 0,
            'post_content'   => '',
            'post_type'      => 'bp_snippets_edit',
            'post_status'    => 'publish',
            'is_archive'     => true,
            'comment_status' => 'closed'
        ) );
    }
    /**
     * Filter the_content with permasnipt edit template part
     */
    public function permasnipt_edit_content() {
        bp_buffer_template_part( 'snippets-dir-edit' );
    }

	/**
     * Update the global $post with permasnipt edit data
     */
    public function permasnipt_dummy_post() {
	
		$title = __( 'Snippets', 'bp-code-snippets' );
		
 
        bp_theme_compat_reset_post( array(
            'ID'             => 0,
            'post_title'     => $title,
            'post_author'    => 0,
            'post_date'      => 0,
            'post_content'   => '',
            'post_type'      => 'bp_snippets_single',
            'post_status'    => 'publish',
            'is_archive'     => true,
            'comment_status' => 'closed'
        ) );
    }
    /**
     * Filter the_content with permasnipt edit template part
     */
    public function permasnipt_content() {
        bp_buffer_template_part( 'snippets-single' );
    }
}
 
new BP_Plugin_Theme_Compat ();
