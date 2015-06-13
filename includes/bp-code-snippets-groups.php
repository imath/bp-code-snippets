<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

class Bp_Code_Snippets_Group extends BP_Group_Extension {	

	var $visibility  = 'public';
	var $enable_create_step  = true;
	var $enable_nav_item  = true;
	var $enable_edit_item = true;
		
	function bp_code_snippets_group() {
		global $bp;
		$this->name = __( 'Snippets', 'bp-code-snippets' );
		$this->slug = 'snippets';
		$this->create_step_position = 21;
		$this->nav_item_position = 31;
		$this->enable_nav_item = $this->enable_nav_item();
	}

	function create_screen() {
		if ( !bp_is_group_creation_step( $this->slug ) )
			return false;
		?>

		<h2>Snippets <?php _e('settings for this group.','bp-code-snippets');?></h2>

		<p><?php _e('Activate the codes category for your group (leave all unchecked if you do not want to use Snippets)','bp-code-snippets');?> :</p>
			<ul>
				<?php bp_code_snippets_group_lg_li();?>
			</ul>

		<?php
		wp_nonce_field( 'groups_create_save_' . $this->slug );
	}

	function create_screen_save() {
		global $bp;

		check_admin_referer( 'groups_create_save_' . $this->slug );

		$cs_languages = $_POST['_snippets_code_ok'];
		
		if( !is_array($cs_languages) || count($cs_languages) < 1 ){
			$success = groups_update_groupmeta( $bp->groups->current_group->id, 'snippets_code_ok', 0 );
		}
		else $success = groups_update_groupmeta( $bp->groups->current_group->id, 'snippets_code_ok', $cs_languages );
	}

	function edit_screen() {
		if ( !bp_is_group_admin_screen( $this->slug ) )
					return false; ?>

				<h2><?php echo esc_attr( $this->name ) ?> <?php _e('settings for this group.','bp-code-snippets');?></h2>

				<p><?php _e('Activate the codes category for your group (leave all unchecked if you do not want to use Snippets)','bp-code-snippets');?> :</p>
				<ul>
						<?php bp_code_snippets_group_lg_li();?>
				</ul>
				<input type="submit" name="save" value="<?php _e('Save','bp-code-snippets');?>" />

				<?php
				wp_nonce_field( 'groups_edit_save_' . $this->slug );
	}

	function edit_screen_save() {
		global $bp;

		if ( !isset( $_POST['save'] ) )
			return false;

		check_admin_referer( 'groups_edit_save_' . $this->slug );

		/* Insert your edit screen save code here */
		$cs_languages = $_POST['_snippets_code_ok'];
		
		if( !is_array($cs_languages) || count($cs_languages) < 1 ){
			$success = groups_update_groupmeta( $bp->groups->current_group->id, 'snippets_code_ok', 0 );
		}
		else $success = groups_update_groupmeta( $bp->groups->current_group->id, 'snippets_code_ok', $cs_languages );
			

		/* To post an error/success message to the screen, use the following */
		if ( !$success )
			bp_core_add_message( __( 'There was an error saving, please try again', 'bp-code-snippets' ), 'error' );
		else
			bp_core_add_message( __( 'Settings saved successfully', 'bp-code-snippets' ) );

		bp_core_redirect( bp_get_group_permalink( $bp->groups->current_group ) . '/admin/' . $this->slug );
		}

	function display() {
		global $bp;
		$current_url = $bp->root_domain."/".$bp->current_component."/".$bp->current_item."/".$bp->current_action."/";
		$snp_group_class = false;
		?>
		<div class="item-list-tabs no-ajax" id="subnav">
			<form action="" method="get" id="snippet-form-filter">
			<ul>
				<?php bp_code_snippets_feed();?>
				
				<?php if( !bp_action_variable( 0 ) ):?>
					<?php $snp_group_class = 'class="current"'; ?>
					
					<li><a class="show-hide-new-snippet" href="#new-snippet" id="new-snippet-button"><?php _e( 'New Snippet', 'bp-code-snippets' ); ?></a></li>
					
				<?php endif;?>
				
				<li id="li_list_code" <?php echo $snp_group_class;?>><a href="<?php echo $current_url;?>"><?php _e( 'List of Snippets' , 'bp-code-snippets');?></a></li>
				
				<?php do_action( 'bp_snippets_goup_snippet_before_cats' ); ?>
				
				<?php if( !is_numeric( bp_action_variable( 0 ) ) ):?>

				<li id="snippets-cat-select" class="last s-filter">

					<label for="snippets-filter-by"><?php _e( 'Category:', 'bp-code-snippets' ); ?></label>
					<select id="snippets-filter-by" name="snptcat">
						<option value="all"><?php _e('All', 'bp-code-snippets') ?></option>
						<?php bp_code_snippets_fill_dropdown_lg() ?>
						
					</select>
				</li>
				<?php endif;?>
			</ul>
			</form>
		</div>
		
		<?php bp_code_snippets_screen_group_display();
	}

	function widget_display() {
		return false;
	}
	
	function enable_nav_item() {
		global $bp;
		
		if( empty( $bp->groups->current_group ) )
			return false;
		
		if ( 'public' != $bp->groups->current_group->status && ( !is_user_logged_in() || !groups_is_user_member( $bp->loggedin_user->id, $bp->groups->current_group->id ) ) )
			return false;
			
		/* enable the nav item if code category has been selected */
		
		if ( groups_get_groupmeta( $bp->groups->current_group->id, 'snippets_code_ok' ) )
			return true;
		else
			return false;
	}
}

if( bp_code_snippets_is_group_snippets_enable() ){
		bp_register_group_extension( 'Bp_Code_Snippets_Group' );
}
?>