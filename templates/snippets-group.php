<?php

/**
 * BP Code Snippets - Group loop template
 *
 * @package BP Code Snippets
 */

?>


<?php do_action( 'bp_before_group_snippets' ); ?>


	<div class="snippet" role="main">

		<?php bp_code_snippets_locate_template( 'snippets-loop' ); ?>

	</div><!-- .snippet-->

	<?php do_action( 'bp_after_group_snippets_content' ); ?>
	
	<?php bp_code_snippets_locate_template( 'snippets-create' ); ?>

	
<?php do_action( 'bp_after_group_snippets' ); ?>