<?php

/**
 * BP Code Snippets - Template Create
 *
 * @package BP Code Snippets
 */

?>

<div id="new-snippet">

	<?php if ( is_user_logged_in() ) : ?>

		<?php if ( bp_is_active( 'snippets' ) ) : ?>

			<form action="<?php bp_code_snippets_form_action();?>" method="post" id="snippets-form" class="standard-form">

				<?php do_action( 'snippets_new_snippet_before' ) ?>

				<a name="post-new"></a>
				<h4><?php _e( 'Create New Snippet:', 'bp-code-snippets' ); ?></h4>

				<label><?php _e( 'Title:', 'bp-code-snippets' ); ?></label>
				<input type="text" name="snippet_title" id="snippet_title" value="" />
				
				<?php if( bp_is_active( 'groups' )  ):?>
				
					<?php if( ( bp_is_current_component( 'snippets' ) || bp_is_current_component( 'bookmarklet' ) ) && bp_code_snippets_is_group_snippets_enable() && bp_has_groups( 'user_id=' . bp_loggedin_user_id() . '&type=alphabetical&max=100&per_page=100' ) ):?>
					
						<label><?php _e( 'Post In Group Snippets:', 'bp-code-snippets' ); ?></label>
				
						<select id="snippet_group_id" name="snippet_group_id">

							<option value="0"><?php /* translators: no option picked in select box */ _e( '----', 'bp-code-snippets' ); ?></option>

							<?php while ( bp_groups() ) : bp_the_group(); ?>

								<?php if ( bp_group_snippets_is_snippet_enabled() && ( is_super_admin() || 'public' == bp_get_group_status() || bp_group_is_member() ) ) : ?>

									<option value="<?php bp_group_id(); ?>"><?php bp_group_name(); ?></option>

								<?php endif; ?>
							
							<?php endwhile; ?>

						</select><!-- #snippet_group_id -->
				
					<?php endif;?>
					
				<?php endif;?>
				
				<label><?php _e( 'Categories', 'bp-code-snippets' ); ?></label>
				<?php bp_code_snippets_dropdown_lg();?>

				<label><?php _e( 'Decription:', 'bp-code-snippets' ); ?></label>
				<textarea name="snippet_purpose" id="snippet_purpose"></textarea>
				
				<label><?php _e( 'Content:', 'bp-code-snippets' ); ?></label>
				<textarea name="snippet_content" id="snippet_content"></textarea>
				

				<?php do_action( 'snippets_new_snippet_after' ); ?>

				<div class="submit">
					<input type="submit" name="submit_snippet" id="submit" value="<?php _e( 'Post Snippet', 'bp-code-snippets' ); ?>" />
					<input type="button" name="submit_snippet_cancel" id="submit_snippet_cancel" value="<?php _e( 'Cancel', 'bp-code-snippets' ); ?>" />
				</div>

				<?php wp_nonce_field( 'bp_snippets_new_snippet' ); ?>

			</form><!-- #snippets-form -->

		<?php endif; ?>

	<?php endif; ?>
</div><!-- #new-snippet -->

<?php do_action( 'bp_after_new_snippet_form' ); ?>