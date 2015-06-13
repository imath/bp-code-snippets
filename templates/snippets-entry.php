<?php

/**
 * BP Code Snippets Stream (Entry Item)
 *
 */

?>

<?php do_action( 'bp_before_snippet_entry' ); ?>

<li id="snippet-<?php bp_snippet_id(); ?>">
	<div class="snippet-avatar">
		
		<?php bp_snippet_type_avatar();?>
		
	</div>

	<div class="snippet-content">

		<div class="snippet-header">

			<?php bp_snippet_action(); ?>

		</div>

		<div class="snippet-inner">
				
			<div class="snippet-desc">
					
				<h4><a href="<?php bp_snippet_permasnpt();?>" title="<?php bp_snippet_title();?>"><?php bp_snippet_title();?></a></h4>
				
				<p><?php bp_snippet_excerpt();?></p>
					
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

						<a href="<?php bp_code_snippets_favorite_link(); ?>" class="button snippet-fav bp-secondary-action sniptf" title="<?php esc_attr_e( 'Mark as Favorite', 'bp-code-snippets' ); ?>"><?php _e( 'Favorite', 'bp-code-snippets' ) ?></a> <span class="fav_counter" id="fav_counter-<?php bp_snippet_id(); ?>"><?php bp_code_snippets_favorite_count_for_snippet();?></span>

					<?php else : ?>

						<a href="<?php bp_code_snippets_unfavorite_link(); ?>" class="button snippet-unfav bp-secondary-action sniptf" title="<?php esc_attr_e( 'Remove Favorite', 'bp-code-snippets' ); ?>"><?php _e( 'Remove Favorite', 'bp-code-snippets' ) ?></a> <span class="fav_counter" id="fav_counter-<?php bp_snippet_id(); ?>"><?php bp_code_snippets_favorite_count_for_snippet();?></span>

					<?php endif; ?>

				<?php endif; ?>
				
				<?php if ( bp_code_snippets_can_edit() ) bp_code_snippet_edit_link(); ?>
				
				<?php if ( bp_code_snippets_can_delete() ) bp_code_snippet_delete_link(); ?>

			</div>

		<?php endif; ?>

	</div>

	

</li>

<?php do_action( 'bp_after_snippet_entry' ); ?>
