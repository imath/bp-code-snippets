<?php

/**
 * BP Code Snippets - Template Embed
 * We don't need BuddyPress Header, Sidebar and footer
 * @package BP Code Snippets
 */

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>
	<head profile="http://gmpg.org/xfn/11">
		<meta http-equiv="Content-Type" content="<?php bloginfo( 'html_type' ) ?>; charset=<?php bloginfo( 'charset' ) ?>" />
		<title><?php wp_title( '|', true, 'right' ); bloginfo( 'name' ); ?></title>
		
		<link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>" type="text/css" media="screen" />
		
		<?php wp_head();?>
		
		<?php do_action( 'bp_code_snippets_head_embed' ) ?>
		
	</head>

	<body <?php body_class() ?> id="bp-cs-embed">

	<?php do_action( 'bp_before_embed_snippets_page' ); ?>

			<?php do_action( 'bp_before_embed_snippets' ); ?>
				
					<?php if ( bp_code_snippets_is_oembed_enable() && bp_has_snippets( bp_code_snippets_what_component() ) ) : ?>

						<?php while ( bp_snippets() ) : bp_the_snippet(); ?>

						<div class="snippet-embed" role="main" id="snippet-<?php bp_snippet_id(); ?>">
							
							<div class="embed-action">
								<?php echo apply_filters('bp_code_snippets_add_brand', '', 'bp-cs-brand');?>
								<a href="#get-permalink" class="copy-link"><?php _e('Get Permalink', 'bp-code-snippets');?></a>
								<a href="#get-embed-code" class="copy-code"><?php _e('Get Embed Code', 'bp-code-snippets');?></a>
								<span>&nbsp;</span>
								<div style="display:none" class="copy-link-snippet">
									<input type="text" value="<?php bp_snippet_permasnpt();?>" class="snipt-perma" readonly/>
								</div>
								<div style="display:none" class="copy-embed-snippet">
									<textarea class="snipt-code" readonly>&lt;iframe src="<?php bp_snippet_oembed_link();?>" frameborder="0" width="100%" height="100px"&gt;&lt;/iframe&gt;</textarea>
								</div>
							</div>

							<div class="code_in_content">
									<pre class="brush: <?php bp_snippet_type();?>"><?php bp_snippet_the_content(); ?></pre>
							</div>

						</div>

						<?php endwhile; ?>

					<?php else : ?>
						
						<div id="message" class="info">
							<p><?php printf( __( 'Sorry, this snippet is no longer available on %s', 'bp-code-snippets' ), '<a href="'.site_url().'" target="top">'.get_bloginfo('sitename').'</a>'); ?></p>
						</div>
						
					<?php endif; ?>
					

				<?php do_action( 'bp_after_embed_snippets' ); ?>


	<?php do_action( 'bp_after_embed_snippets_page' ); ?>
	
<?php wp_footer()?>