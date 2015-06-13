<?php

/**
 * BP Code Snippets - Template User
 *
 * @package BP Code Snippets
 */

?>
<?php if( bp_code_snippets_is_bp_default() ) :?>
<?php get_header( 'buddypress' ); ?>

	<div id="content">
		<div class="padder">

			<?php do_action( 'bp_before_member_snippets_content' ); ?>

			<div id="item-header" role="complementary">

				<?php locate_template( array( 'members/single/member-header.php' ), true ); ?>

			</div><!-- #item-header -->

			<div id="item-nav">
				<div class="item-list-tabs no-ajax" id="object-nav" role="navigation">
					<ul>

						<?php bp_get_displayed_user_nav(); ?>

						<?php do_action( 'bp_member_options_nav' ); ?>

					</ul>
				</div>
			</div><!-- #item-nav -->
			
<?php endif;?>

			<div id="item-body">
				
				<div class="item-list-tabs no-ajax" id="subnav">
					<form action="" method="get" id="snippet-form-filter">
					<ul>
						<?php bp_get_options_nav() ?>
						
						<?php do_action( 'bp_snippets_member_snippet_before_cats' ); ?>

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

				<?php do_action( 'bp_before_member_snippets_body' );?>

				<div class="snippet" role="main">

					<?php bp_code_snippets_locate_template( 'snippets-loop' ); ?>

				</div><!-- .snippet-->

				<?php do_action( 'bp_after_member_snippets_body' ); ?>

			</div><!-- #item-body -->

			<?php do_action( 'bp_after_member_snippets_content' ); ?>

<?php if( bp_code_snippets_is_bp_default() ):?>
		</div><!-- .padder -->
	</div><!-- #content -->

<?php get_sidebar( 'buddypress' ); ?>
<?php get_footer( 'buddypress' ); ?>

<?php endif;?>
