<?php

/**
 * BP Code Snippets - Template Create in Thickbox
 * We don't need BuddyPress Header, Sidebar and footer
 * @package BP Code Snippets
 */
global $default_purpose_data;
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>
	<head profile="http://gmpg.org/xfn/11">
		<meta http-equiv="Content-Type" content="<?php bloginfo( 'html_type' ) ?>; charset=<?php bloginfo( 'charset' ) ?>" />
		<title><?php wp_title( '|', true, 'right' ); bloginfo( 'name' ); ?></title>
		
		<link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>" type="text/css" media="screen" />
		
		<?php wp_head();?>
		
		<?php do_action( 'bp_code_snippets_head_create_thickbox' ) ?>
		
	</head>

	<body <?php body_class() ?> id="bp-create-thickbox">
		
		<?php if( bp_code_snippets_is_bp_default() ) : ?>
		
		<div id="content">
			<div class="padder">
				
		<?php else:?>
			
			<div style="margin:2em">
			
			<div id="buddypress">
			
		<?php endif;?>
				
				<?php do_action( 'template_notices' ); ?>
				
				<?php if( is_numeric( bp_action_variable( 0 ) ) ):?>
					
					<div class="snippet" role="main">


						<?php if ( bp_has_snippets( array( 'in' => bp_action_variable( 0 ) ) ) ) : ?>

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

									<div class="snippet-detail">
										
										<?php do_action( 'bp_before_purpose_snippets' ); ?>

										<p><?php bp_snippet_excerpt();?></p>
										
										<?php do_action( 'bp_after_purpose_snippets' ); ?>

										<div class="code_in_content">	
											<pre class="brush: <?php bp_snippet_type();?>"><?php bp_snippet_the_content(); ?></pre>
										</div>
										
										<?php do_action( 'bp_after_content_snippets' ); ?>

									</div>
									
									<?php if ( is_user_logged_in() ) : ?>
									
									<a href="#insert-snippet" class="insertSnippet button" id="snipt-<?php bp_snippet_id(); ?>"><?php _e('Insert this Snippet', 'bp-code-snippets');?></a> <?php if ( bp_code_snippets_can_delete() ) bp_code_snippet_delete_link(); ?>
									
									<?php endif; ?>

								</div>

							</div>

							<?php endwhile; ?>

						<?php else : ?>

							<div id="message" class="info">
								
								<p><?php _e( 'Sorry, there was no snippet found. Please try a different filter.', 'bp-code-snippets' ); ?></p>
							</div>

						<?php endif; ?>

					</div><!-- .snippet-->
					
				<?php else:?>

				<div id="new-snippet">

				<?php if ( is_user_logged_in() ) : ?>

					<?php if ( bp_is_active( 'snippets' ) ) : ?>

						<form action="<?php bp_code_snippets_form_action();?>" method="post" id="snippets-form" class="standard-form">

							<?php do_action( 'snippets_new_snippet_before' ) ?>

							<a name="post-new"></a>
							<h4><?php _e( 'Create New Snippet:', 'bp-code-snippets' ); ?></h4>
							
							<?php do_action( 'snippets_new_snippet_thickbox_specific', $_REQUEST); ?>
							
							<label><?php _e( 'Title:', 'bp-code-snippets' ); ?></label>
							<input type="text" name="snippet_title" id="snippet_title" value="<?php if( !empty( $default_purpose_data['title'] ) ) echo $default_purpose_data['title'];?>" />
				
							<label><?php _e( 'Categories', 'bp-code-snippets' ); ?></label>
							<?php bp_code_snippets_dropdown_lg();?>

							<label><?php _e( 'Description:', 'bp-code-snippets' ); ?> <?php if( !empty( $default_purpose_data['action'] ) ) echo $default_purpose_data['action'];?></label>
							<textarea name="snippet_purpose" id="snippet_purpose" <?php if( !empty( $default_purpose_data['class'] ) ) echo $default_purpose_data['class'];?>><?php if( !empty( $default_purpose_data['ta_value'] ) ) echo $default_purpose_data['ta_value'];?></textarea>
				
							<label><?php _e( 'Content:', 'bp-code-snippets' ); ?></label>
							<textarea name="snippet_content" id="snippet_content"></textarea>
				

							<?php do_action( 'snippets_new_thickbox_snippet_after' ); ?>

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
				
				<?php endif;?>
		
		<?php if( bp_code_snippets_is_bp_default() ): ?>
		
			</div><!-- .padder -->
		</div><!-- #content -->
		
		<?php else:?>
			
			</div>
			
			</div>
			
		<?php endif;?>

	<?php wp_footer()?>