<?php

/**
 * BP Code Snippets - snippets-single Template
 *
 * @package BP Code Snippets
 */

?>

<?php if( bp_code_snippets_is_bp_default() ): ?>

<?php get_header( 'buddypress' ); ?>

	<?php do_action( 'bp_before_snippets_single_page' ); ?>

	<div id="content">
		<div class="padder">
			
		<?php do_action( 'bp_before_directory_snippets' ); ?>
		
		<h3><?php _e( 'Snippets Directory', 'bp-code-snippets' ); ?></h3>
		
<?php else:?>
	
	<div id="buddypress">
	
<?php endif;?>
		
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
			<ul>
				
				<?php bp_code_snippets_feed();?>

			</ul>
		</div>
		
		<?php do_action( 'bp_before_snippets_single' ); ?>

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

									<?php if ( bp_is_active('activity') && bp_activity_can_favorite() ) : ?>

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
					
					<?php bp_code_snippets_locate_template('snippets-comments') ;?>

					<?php endwhile; ?>

				<?php else : ?>

					<div id="message" class="info">
						<p><?php _e( 'Sorry, there was no snippet found. Please try a different filter.', 'bp-code-snippets' ); ?></p>
					</div>

				<?php endif; ?>
				
				<?php do_action( 'bp_after_snippets_single_content' ); ?>

			</div><!-- .snippet-->
			
<?php if( bp_code_snippets_is_bp_default() ): ?>

		<?php do_action( 'bp_after_snippets_single' ); ?>

		</div><!-- .padder -->
	</div><!-- #content -->

	<?php do_action( 'bp_after_snippets_single_page' ); ?>

<?php get_sidebar( 'buddypress' ); ?>
<?php get_footer( 'buddypress' ); ?>

<?php else:?>

	</div>
	
<?php endif;?>