<?php

/**
 * BuddyPress - Snippet Loop
 *
 * Querystring is set via AJAX in _inc/ajax.php - bp_dtheme_object_filter()
 *
 * @package BuddyPress
 * @subpackage bp-default
 */

?>

<?php do_action( 'bp_before_snippet_loop' ); ?>

<?php if ( bp_has_snippets( bp_code_snippets_what_component() ) ) : ?>

	<?php if( !bp_snippet_is_favorites_screen() ):?>
		<div class="snippets-pagination pagination no-ajax">
			<div class="pag-count"><?php bp_snippet_pagination_count(); ?></div>
			<div class="pagination-links"><?php bp_snippet_pagination_links(); ?></div>
		</div>
	<?php endif;?>

	<?php if ( empty( $_POST['acpage'] ) ) : ?>

		<ul id="snippet-stream" class="snippet-list item-list">

	<?php endif; ?>
	
	<?php while ( bp_snippets() ) : bp_the_snippet(); ?>

		<?php bp_code_snippets_locate_template('snippets-entry', false) ?>

	<?php endwhile; ?>
	
	<?php if ( bp_snippet_has_more_items() && bp_snippet_is_favorites_screen() ) : ?>

		<li class="<?php if( !empty( $_POST['acpage'] ) || !empty( $_POST['snptcat']) ) echo 'snippets-load-more-live'; else echo 'snippets-load-more';?>">
			<a href="#more"><?php _e( 'Load More', 'bp-code-snippets' ); ?></a>
		</li>

	<?php endif; ?>

	<?php if ( empty( $_POST['acpage'] ) ) : ?>

		</ul>

	<?php endif; ?>
		
	<?php if( !bp_snippet_is_favorites_screen() ):?>
		<div class="snippets-pagination bottom pagination no-ajax">
			<div class="pag-count"><?php bp_snippet_pagination_count(); ?></div>
			<div class="pagination-links"><?php bp_snippet_pagination_links(); ?></div>
		</div>
	<?php endif;?>

<?php else : ?>

	<div id="message" class="info">
		<p><?php _e( 'Sorry, there was no snippet found. Please try a different filter.', 'bp-code-snippets' ); ?></p>
	</div>

<?php endif; ?>

<?php do_action( 'bp_after_snippet_loop' ); ?>