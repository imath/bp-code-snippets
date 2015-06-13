<?php

/**
 * BP Code Snippets - Group single template
 *
 * @package BP Code Snippets
 */

?>


<?php do_action( 'bp_before_group_snippets' ); ?>


	<div class="snippet" role="main">

		<?php if ( bp_has_snippets( bp_code_snippets_what_component() ) ) : ?>

			<?php while ( bp_snippets() ) : bp_the_snippet(); ?>
				
			<div class="snippet-entry" role="main" id="snippet-<?php bp_snippet_id(); ?>">

				<div class="snippet-avatar">

						<?php bp_snippet_type_avatar();?>

				</div>

				<div class="snippet-content">

					<div class="snippet-header">

						<?php bp_snippet_action(); ?>

					</div>

					<div class="snippet-inner">

						<div class="snippet-desc">

							<h2><?php bp_snippet_title();?></h2>

						</div>

					</div>

					<?php do_action( 'bp_snippet_entry_content' ); ?>

					<?php if ( is_user_logged_in() ) : ?>

						<div class="snippet-meta">

							<?php if ( bp_code_snippet_can_comment() ) : ?>

								<a href="<?php bp_snippet_comment_link(); ?>" class="button snippet-comment-reply bp-primary-action" id="snippet-comment-<?php bp_snippet_id(); ?>"><?php printf( __( 'Comment <span>%s</span>', 'bp-code-snippets' ), bp_get_snippet_comment_count() ); ?></a>

							<?php endif; ?>

							<?php if ( bp_activity_can_favorite() ) : ?>

								<?php if ( !bp_get_code_snippets_is_favorite() ) : ?>

									<a href="<?php bp_code_snippets_favorite_link(); ?>" class="button snippet-fav bp-secondary-action sniptf" title="<?php esc_attr_e( 'Mark as Favorite', 'bp-code-snippets' ); ?>"><?php _e( 'Favorite', 'bp-code-snippets' ) ?></a>  <span class="fav_counter" id="fav_counter-<?php bp_snippet_id(); ?>"><?php bp_code_snippets_favorite_count_for_snippet();?></span>

								<?php else : ?>

									<a href="<?php bp_code_snippets_unfavorite_link(); ?>" class="button snippet-unfav bp-secondary-action sniptf" title="<?php esc_attr_e( 'Remove Favorite', 'bp-code-snippets' ); ?>"><?php _e( 'Remove Favorite', 'bp-code-snippets' ) ?></a>  <span class="fav_counter" id="fav_counter-<?php bp_snippet_id(); ?>"><?php bp_code_snippets_favorite_count_for_snippet();?></span>

								<?php endif; ?>

							<?php endif; ?>
							
							<?php do_action('bp_code_snippets_other_meta_buttons');?>
							
							<?php if ( bp_code_snippets_can_edit() ) bp_code_snippet_edit_link(); ?>

							<?php if ( bp_code_snippets_can_delete() ) bp_code_snippet_delete_link(); ?>

						</div>

					<?php endif; ?>
					
					<div class="snippet-detail">
						
						<?php do_action( 'bp_before_purpose_snippets' ); ?>
						
						<p><?php bp_snippet_the_purpose();?></p>
						
						<?php do_action( 'bp_after_purpose_snippets' ); ?>
						
						<?php bp_snippet_the_snippet_content(); ?>
						
						<?php do_action( 'bp_after_content_snippets' ); ?>
						
					</div>

				</div>
				
			</div>
			
			<?php bp_code_snippets_load_template('snippets-comments.php') ;?>

			<?php endwhile; ?>

		<?php else : ?>

			<div id="message" class="info">
				<p><?php _e( 'Sorry, there was no snippet found. Please try a different filter.', 'bp-code-snippets' ); ?></p>
			</div>

		<?php endif; ?>
		
		<?php do_action( 'bp_after_snippets_single_content' ); ?>

	</div><!-- .snippet-->

	<?php do_action( 'bp_after_group_snippets_content' ); ?>

	
<?php do_action( 'bp_after_group_snippets' ); ?>