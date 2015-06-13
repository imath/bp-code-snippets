<?php

/**
 * BP Code Snippets - Template Directory
 *
 * @package BP Code Snippets
 */

?>

<?php if( bp_code_snippets_is_bp_default() ): ?>

<?php get_header( 'buddypress' ); ?>

	<?php do_action( 'bp_before_directory_snippets_page' ); ?>

	<div id="content">
		<div class="padder">

		<?php do_action( 'bp_before_directory_snippets' ); ?>


			<h3><?php _e( 'Snippets Directory', 'bp-code-snippets' ); ?><?php if ( is_user_logged_in() ) : ?> &nbsp;<a class="button show-hide-new-snippet" href="#new-snippet" id="new-topic-button"><?php _e( 'New Snippet', 'bp-code-snippets' ); ?></a><?php endif; ?></h3>
				
<?php else:?>
	
	<div id="buddypress">
	
<?php endif;?>

			<?php do_action( 'bp_before_directory_snippets_content' ); ?>

			<div id="snippets-dir-search" class="dir-search no-ajax" role="search">

				<?php bp_code_snippets_directory_search_form(); ?>

			</div><!-- #snippets-dir-search -->
			
			<?php do_action( 'template_notices' ); ?>

			<div class="item-list-tabs no-ajax" role="navigation">
				<ul>
					<li <?php bp_dir_snippets_selected_item();?> id="snippets-all"><a href="<?php bp_root_domain(); ?>/<?php bp_snippet_slug() ?>"><?php printf( __( 'All Snippets <span>%s</span>', 'bp-code-snippets' ), bp_code_snippets_get_all_count( array('filter' => array( 'object' => apply_filters( 'bp_snippets_directory_objects','directory,group,group_forum_topic,blog_post') ) ) ) ); ?></a></li>

					<?php if ( is_user_logged_in() ) : ?>

						<li <?php bp_dir_snippets_selected_item( 'mine' );?> id="snippets-personal"><a href="<?php bp_root_domain(); ?>/<?php bp_snippet_slug() ?>/mine/"><?php printf( __( 'My Snippets <span>%s</span>', 'bp-code-snippets' ), bp_code_snippets_get_all_count_for( array('filter' => array('user_id' => bp_loggedin_user_id(), 'object' => apply_filters('bp_snippets_mine_objects', 'directory,group,group_forum_topic,blog_post') ), 'show_hidden' => true, 'is_draft' => true ) ) ); ?></a></li>
						
						<li <?php bp_dir_snippets_selected_item( 'favs' );?> id="snippets-favs"><a href="<?php bp_root_domain(); ?>/<?php bp_snippet_slug() ?>/favs/"><?php printf( __( 'My Favorite Snippets <span>%s</span>', 'bp-code-snippets' ), bp_code_snippets_get_favorite_count( bp_loggedin_user_id() ) ); ?></a></li>

					<?php endif; ?>

					<?php do_action( 'bp_snippets_directory_snippet_types' ); ?>

				</ul>
			</div><!-- .item-list-tabs -->

			<div class="item-list-tabs" id="subnav" role="navigation">
				<form action="" method="get" id="snippet-form-filter">
				<ul>
					
					<?php bp_code_snippets_feed();?>

					<?php do_action( 'bp_snippets_directory_snippet_before_cats' ); ?>

					<li id="snippets-cat-select" class="last s-filter">

						<label for="snippets-filter-by"><?php _e( 'Category:', 'bp-code-snippets' ); ?></label>
						<select id="snippets-filter-by" name="snptcat">
							<option value="all"><?php _e('All', 'bp-code-snippets') ?></option>
							<?php bp_code_snippets_fill_dropdown_lg() ?>
							
						</select>
					</li>
				</ul>
				</form>
			</div>

			<div id="snippets-dir-list" class="snippets dir-list">
				
				<?php bp_code_snippets_locate_template( 'snippets-loop' ); ?>
				
			</div><!-- #snippets-dir-list -->


			<?php do_action( 'bp_after_directory_snippets_content' ); ?>
		
		<?php bp_code_snippets_locate_template( 'snippets-create' ); ?>
		
<?php if( bp_code_snippets_is_bp_default() ) :?>

		<?php do_action( 'bp_after_directory_snippets' ); ?>

		</div><!-- .padder -->
	</div><!-- #content -->

	<?php do_action( 'bp_after_directory_snippets_page' ); ?>

<?php get_sidebar( 'buddypress' ); ?>
<?php get_footer( 'buddypress' ); ?>

<?php else :?>
	
	</div>
	
<?php endif;?>
