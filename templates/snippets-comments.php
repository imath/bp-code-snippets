<?php

/**
 * BP Code Snippets Comments template
 *
 * This template is used by snippets-single.php and snippets-group-single.php templates to show
 * each activity.
 *
 * @package BP Code Snippets
 */

?>

<?php do_action( 'bp_before_snippet_comments' ); ?>

	<?php if( bp_snippets_have_comments() ):?>

	<div id="comments">

		<h3>
		<?php printf( _n( '1 response to %2$s', '%1$s responses to %2$s', bp_get_snippet_comment_count(), 'bp-code-snippets' ), number_format_i18n( bp_get_snippet_comment_count() ), '<em>' . bp_get_snippet_title() . '</em>' ) ?>
		</h3>

		<?php do_action( 'bp_before_blog_comment_list' ) ?>

		<ol class="commentlist">
			
			<?php bp_snippets_list_comments();?>

		</ol><!-- .comment-list -->

		<?php do_action( 'bp_after_blog_comment_list' ) ?>

	</div><!-- #comments -->

	<?php endif; ?>
	
	
<?php do_action( 'bp_after_snippet_comments' ); ?>

	<div id="s-reply">
		
		<?php do_action( 'bp_before_snippet_comment_comment_form' ); ?>

		<?php if( is_user_logged_in() ) :?>
			
			<form action="<?php bp_code_snippets_form_action();?>" method="post" id="snippets-comment-form" class="standard-form">
				
				
				<h4><?php _e( 'Add a reply:', 'bp-code-snippets' ); ?></h4>
				<textarea name="_comment_content" id="comment_content"></textarea>
				<input type="hidden" value="<?php bp_snippet_id();?>" name="_snippet_id">
				
				<div class="submit">
					
					<?php do_action( 'bp_code_snippets_before_submit_comment_button' );?>
					
					<input type="submit" value="<?php _e( 'Post Reply', 'bp-code-snippets' ); ?>" name="save_snippet_comment" id="submit">
				</div>
				
				<?php wp_nonce_field( 'bp_snippets_comment_snippet' ); ?>
				
			</form>
			
		<?php else:?>
				
			<div id="message" class="info"><p><?php _e('You must be logged in to comment.', 'bp-code-snippets');?></p></div>
			
		<?php endif;?>
		
		<?php do_action( 'bp_after_snippet_comment_comment_form' ); ?>
		
	</div>