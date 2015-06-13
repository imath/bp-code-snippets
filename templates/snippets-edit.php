<?php

/**
 * BP Code Snippets - Template Create
 *
 * @package BP Code Snippets
 */

?>

<div id="edit-snippet">

	<?php if ( is_user_logged_in() ) : ?>

		<?php if ( bp_is_active( 'snippets' ) ) : ?>

			<form action="<?php bp_code_snippets_form_action();?>" method="post" id="snippets-form" class="standard-form">

				<?php do_action( 'snippets_edit_snippet_before' ) ?>
				
				
				<?php if ( bp_has_snippets( array( 'in' => bp_action_variable( 0 ) ) ) ) : ?>

					<?php while ( bp_snippets() ) : bp_the_snippet(); ?>
						
						<a name="post-edit"></a>
						<h4><?php printf( __('Editing Snippet: %s', 'bp-code-snippets' ), '<a href="'. bp_get_snippet_permasnpt() .'">' . bp_get_snippet_title() .'</a>'); ?></h4>
						
						<label><?php _e( 'Title:', 'bp-code-snippets' ); ?></label>
						<input type="text" name="snippet_title" id="snippet_title" value="<?php bp_snippet_title();?>" />

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

						<label><?php _e( 'Categories', 'bp-code-snippets' ); ?></label>
						<?php bp_code_snippets_dropdown_lg( bp_get_snippet_type() );?>

						<label><?php _e( 'Decription:', 'bp-code-snippets' ); ?></label>
						<textarea name="snippet_purpose" id="snippet_purpose"><?php bp_snippet_the_purpose();?></textarea>

						<label><?php _e( 'Content:', 'bp-code-snippets' ); ?></label>
						<textarea name="snippet_content" id="snippet_content"><?php bp_snippet_the_content();?></textarea>
						
						<input type="hidden" name="snippet_edit_id" value="<?php bp_snippet_id();?>">

						<?php do_action( 'snippets_edit_snippet_after' ); ?>

						<div class="submit">
							<input type="submit" name="submit_snippet" id="submit" value="<?php _e( 'Save Changes', 'bp-code-snippets' ); ?>" />
							<a href="<?php echo wp_get_referer();?>" class="button" id="submit_snippet_cancel"><?php _e( 'Cancel', 'bp-code-snippets' ); ?></a>
						</div>

						<?php wp_nonce_field( 'bp_snippets_new_snippet' ); ?>
						
					<?php endwhile; ?>

				<?php else : ?>

					<div id="message" class="info">
						<p><?php _e( 'Sorry, there was no snippet found. Please try a different filter.', 'bp-code-snippets' ); ?></p>
					</div>

				<?php endif; ?>


			</form><!-- #snippets-form -->

		<?php endif; ?>

	<?php endif; ?>
</div><!-- #edit-snippet -->

<?php do_action( 'bp_after_edit_snippet_form' ); ?>