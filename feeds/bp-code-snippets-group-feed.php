<?php

/**
 * RSS2 Feed Template for displaying a group activity stream
 *
 * @package BuddyPress
 * @subpackage ActivityFeeds
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

header('Content-Type: text/xml; charset=' . get_option('blog_charset'), true);
header('Status: 200 OK');
?>
<?php echo '<?xml version="1.0" encoding="'.get_option('blog_charset').'"?'.'>'; ?>

<rss version="2.0"
	xmlns:content="http://purl.org/rss/1.0/modules/content/"
	xmlns:wfw="http://wellformedweb.org/CommentAPI/"
	xmlns:dc="http://purl.org/dc/elements/1.1/"
	xmlns:atom="http://www.w3.org/2005/Atom"
	<?php do_action('bp_code_snippets_group_feed'); ?>
>

<channel>
	<title><?php bp_site_name() ?> | <?php echo $bp->groups->current_group->name ?> | <?php _e( 'Snippets', 'bp-code-snippets' ) ?></title>
	<atom:link href="<?php self_link(); ?>" rel="self" type="application/rss+xml" />
	<link><?php echo bp_get_group_permalink( $bp->groups->current_group ) . bp_get_snippet_slug() . '/feed' ?></link>
	<description><?php printf( __( '%s - Group Snippets Feed', 'bp-code-snippets' ), $bp->groups->current_group->name  ) ?></description>
	<pubDate><?php echo mysql2date('D, d M Y H:i:s O', bp_code_snippets_get_last_updated(), false); ?></pubDate>
	<generator>http://buddypress.org/?v=<?php echo BP_VERSION ?></generator>
	<language><?php echo get_option('rss_language'); ?></language>
	<?php do_action('bp_code_snippets_group_feed_head'); ?>

	<?php if ( bp_has_snippets( 'object=group&primary_id=' . $bp->groups->current_group->id . '&per_page=10&max=20' ) ) : ?>
		<?php while ( bp_snippets() ) : bp_the_snippet(); ?>
			<item>
				<guid><?php bp_snippet_permasnpt() ?></guid>
				<title><?php bp_snippet_title() ?></title>
				<link><?php bp_snippet_permasnpt() ?></link>
				<pubDate><?php echo mysql2date('D, d M Y H:i:s O', bp_get_code_snippets_feed_item_date(), false); ?></pubDate>

				<description>
					<![CDATA[
					<?php bp_snippet_excerpt() ?>
					]]>
				</description>
				<?php do_action('bp_code_snippets_group_feed_item'); ?>
			</item>
		<?php endwhile; ?>

	<?php endif; ?>
</channel>
</rss>
