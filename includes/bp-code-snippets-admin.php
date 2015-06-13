<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;


/**
 * bp_snippets_manager_admin()
 *
 * Checks for form submission, saves component settings and outputs admin screen HTML.
 */
function bp_snippets_manager_admin() {
	global $bp;
	if ( isset( $_POST['save'] ) && check_admin_referer('cs-settings') ) {
		
		$bp_cs_settings = $_POST['bp-cs-settings'];
		
		if( is_array( $bp_cs_settings ) && count( $bp_cs_settings ) >= 1 ) {
			
			foreach( $bp_cs_settings as $key => $bp_cs_option ) {
				
				if( $key == 'cs-admin-selected-languages' )
					$bp_cs_option = array_map( 'esc_attr', $bp_cs_option );
					
				else if( $key == 'cs-setting-theme' )
					$bp_cs_option = esc_attr( $bp_cs_option );
					
				else
					$bp_cs_option = intval( $bp_cs_option );
				
				update_option( $key, $bp_cs_option );
				
			}
			
			
			$updated = true;
		}
		
	}

	$setting_theme = get_option( 'cs-setting-theme' );
	$cs_enable = get_option( 'cs-enable' );
	$cs_ep_enable = get_option( 'cs-ep-enable' );
	$cs_ef_enable = get_option( 'cs-ef-enable' );
	$cs_oembed = get_option( 'cs-oembed' );
	$cs_iframe_activity = get_option( 'cs-iframe-activity-enable' );
	$cs_bkmklet_enable = get_option( 'cs-bkmklet-enable' );
	$list_cs_theme = array();
	$cs_theme_directory = BP_CS_PLUGIN_DIR .'/css/theme/';
	$dir_handle = @opendir($cs_theme_directory) or die("Unable to open $path");
	while ($file = readdir($dir_handle)) {
		$name = explode(".",$file);
		if($name[1]=="css") {
				$list_cs_theme[]=array('value'=>$file, 'label'=>$name[0]);
		}
	}
?>
	<div class="wrap">
		<h2><?php _e( 'Code Snippets Manager', 'bp-code-snippets' ) ?></h2>
		<br />

		<?php if ( isset($updated) ) : ?><?php echo "<div id='message' class='updated fade'><p>" . __( 'Settings Updated.', 'bp-code-snippets' ) . "</p></div>" ?><?php endif; ?>

		<form action="" name="bp-cs-settings-form" id="bp-cs-settings-form" method="post">

			<table class="widefat">
				<thead>
					<tr><th><?php _e('Snippet languages', 'bp-code-snippets')?></th><th><?php _e('Snippet settings', 'bp-code-snippets')?></th></tr>
				</thead>
				<tbody>
				<tr><td id="bpcs-languages">
					<table>
						<tr valign="top">
							<th scope="row"><label for="cs-source-code"><?php _e( 'Select the available source code languages', 'bp-code-snippets' ) ?></label></th>
							<td>
								<?php
								$bp_cs_languages = get_option('bp_code_snippets_available_language');
								foreach($bp_cs_languages as $label => $val){
								?>
								<input type="checkbox" name="bp-cs-settings[cs-admin-selected-languages][]" id="cs-source-code-<?php echo $val;?>" value="<?php echo $val;?>" <?php bp_code_snippets_is_language_ok($val);?>>&nbsp;<?php echo $label;?><br/>
								<?php
								}
								?>
							</td>
						</tr>
					</table>
					</td>
					<td>
						<table id="snippet_settings">
							<?php if( bp_is_active( 'groups' ) ):?>
							<tr valign="top">
								<th scope="row"><label for="cs-enable"><a href="#tab-panel-bp-cs-groups" class="bpcs-help"><?php _e( 'Enable Code Snippets for Groups', 'bp-code-snippets' ) ?></a></label></th>
								<td>
									<input type="radio" name="bp-cs-settings[cs-enable]" id="cs-enable-yes" value="1" <?php if($cs_enable) echo "checked";?>/><?php _e( 'Yes', 'bp-code-snippets' ) ?>&nbsp;
									<input type="radio" name="bp-cs-settings[cs-enable]" id="cs-enable-no" value="0" <?php if(!$cs_enable) echo "checked";?>/><?php _e( 'No', 'bp-code-snippets' ) ?>
								</td>
							</tr>
							<?php endif?>
							<?php if( bp_is_active( 'forums' ) ):?>
							<tr valign="top">
								<th scope="row"><label for="cs-ef-enable"><a href="#tab-panel-bp-cs-forums" class="bpcs-help"><?php _e( 'Enable Snippets in group forum posts', 'bp-code-snippets' ) ?></a></label></th>
								<td>
									<input type="radio" name="bp-cs-settings[cs-ef-enable]" id="cs-ef-enable-yes" value="1" <?php if($cs_ef_enable) echo "checked";?>/><?php _e( 'Yes', 'bp-code-snippets' ) ?>&nbsp;
									<input type="radio" name="bp-cs-settings[cs-ef-enable]" id="cs-ef-enable-no" value="0" <?php if(!$cs_ef_enable) echo "checked";?>/><?php _e( 'No', 'bp-code-snippets' ) ?>
								</td>
							</tr>
							<?php endif;?>
							<tr valign="top">
								<th scope="row"><label for="cs-ep-enable"><a href="#tab-panel-bp-cs-blogs" class="bpcs-help"><?php _e( 'Enable Snippets in blogs posts', 'bp-code-snippets' ) ?></a></label></th>
								<td>
									<input type="radio" name="bp-cs-settings[cs-ep-enable]" id="cs-ep-enable-yes" value="1" <?php if($cs_ep_enable) echo "checked";?>/><?php _e( 'Yes', 'bp-code-snippets' ) ?>&nbsp;
									<input type="radio" name="bp-cs-settings[cs-ep-enable]" id="cs-ep-enable-no" value="0" <?php if(!$cs_ep_enable) echo "checked";?>/><?php _e( 'No', 'bp-code-snippets' ) ?>
								</td>
							</tr>
							<tr valign="top">
								<th scope="row"><label for="cs-theme"><?php _e( 'Highlighter Theme', 'bp-code-snippets' ) ?></label></th>
								<td>
									<select name="bp-cs-settings[cs-setting-theme]" id="cs-theme">
									<?php foreach($list_cs_theme as $theme):?>
										<?php if($setting_theme == $theme['value']):?>
											<option value="<?php echo $theme['value'];?>" selected><?php echo $theme['label'];?></option>
										<?php else:?>
											<option value="<?php echo $theme['value'];?>"><?php echo $theme['label'];?></option>
										<?php endif;?>
									<?php endforeach;?>
									</select>
								</td>
							</tr>
							<tr valign="top">
								<th scope="row"><label for="cs-oembed"><a href="#tab-panel-bp-cs-iframe" class="bpcs-help"><?php _e( 'Enable users to embed the code using an iframe', 'bp-code-snippets' ) ?></a></label></th>
								<td>
									<input type="radio" name="bp-cs-settings[cs-oembed]" id="cs-oembed-yes" value="1" <?php if($cs_oembed) echo "checked";?>/><?php _e( 'Yes', 'bp-code-snippets' ) ?>&nbsp;
									<input type="radio" name="bp-cs-settings[cs-oembed]" id="cs-oembed-no" value="0" <?php if(!$cs_oembed) echo "checked";?>/><?php _e( 'No', 'bp-code-snippets' ) ?>
								</td>
							</tr>
							<tr valign="top">
								<th scope="row"><label for="cs-iframe-activity"><a href="#tab-panel-bp-cs-shortcode" class="bpcs-help"><?php _e( 'Enable Snippets shortcode button in embedding toolbar', 'bp-code-snippets' ) ?></a></label></th>
								<td>
									<input type="radio" name="bp-cs-settings[cs-iframe-activity-enable]" id="cs-iframe-activity-yes" value="1" <?php if($cs_iframe_activity) echo "checked";?>/><?php _e( 'Yes', 'bp-code-snippets' ) ?>&nbsp;
									<input type="radio" name="bp-cs-settings[cs-iframe-activity-enable]" id="cs-iframe-activity-no" value="0" <?php if(!$cs_iframe_activity) echo "checked";?>/><?php _e( 'No', 'bp-code-snippets' ) ?>
								</td>
							</tr>
							<tr valign="top">
								<th scope="row"><label for="cs-bkmlet-enable"><a href="#tab-panel-bp-cs-bkmklet" class="bpcs-help"><?php _e( 'Enable Snippets tab in BP Bookmarklet plugin', 'bp-code-snippets' ) ?></a></label></th>
								<td>
									<?php if( is_plugin_active('bp-bookmarklet/bp-bookmarklet.php') ):?>
										
										<?php if( bp_code_snippets_is_bp_bookmarklet_version_not_ok() ):?>
											
										<?php _e('The current version is out of date<br/> please upgrade BP Bookmarklet', 'bp-code-snippets');?>
										
										<?php else:?>
											<input type="radio" name="bp-cs-settings[cs-bkmklet-enable]" id="cs-bkmlet-enable-yes" value="1" <?php if($cs_bkmklet_enable) echo "checked";?>/><?php _e( 'Yes', 'bp-code-snippets' ) ?>&nbsp;
											<input type="radio" name="bp-cs-settings[cs-bkmklet-enable]" id="cs-bkmlet-enable-no" value="0" <?php if(!$cs_bkmklet_enable) echo "checked";?>/><?php _e( 'No', 'bp-code-snippets' ) ?>
										<?php endif;?>
									<?php else:?>
										<?php _e('Not available', 'bp-code-snippets')?>
									<?php endif;?>
								</td>
							</tr>
						</table>
					</td></tr>
					</tbody>
				</table>
			<p class="submit">
				<input type="submit" name="save" value="<?php _e( 'Save Settings', 'bp-code-snippets' ) ?>"/>
			</p>

			<?php
			/* This is very important, don't leave it out. */
			wp_nonce_field( 'cs-settings' );
			?>
		</form>
	</div>
<?php
}

function bp_code_snippets_is_language_ok($language){
	$bp_cs_admin_languages = get_option( 'cs-admin-selected-languages' );
	
	if( !$bp_cs_admin_languages ) echo "checked";
	
	else{
		
		if( in_array( $language, $bp_cs_admin_languages )) echo "checked";
		
	}
}

function bp_code_snippets_administration_menu(){
	global $bp, $code_snippets_manager_admin_page;

	if ( !$bp->loggedin_user->is_site_admin )
		return false;
		
	$bp_cs_installed_version = get_option('bp-code-snippets-version');
	
	if( version_compare( $bp_cs_installed_version, BP_CS_PLUGIN_VERSION, '<' ) )
		update_option( 'bp-code-snippets-version', $bp_cs_installed_version );
	
	$code_snippets_manager_admin_page = add_submenu_page( 'bp-general-settings', __( 'Code Snippets Manager', 'bp-code-snippets' ), __( 'Code Snippets Manager', 'bp-code-snippets' ), 'manage_options', 'bp-cs-admin', 'bp_snippets_manager_admin' );
		
	add_action("load-$code_snippets_manager_admin_page", 'bp_code_snippets_csm_help');
}
add_action( is_multisite() ? 'network_admin_menu' : 'admin_menu', 'bp_code_snippets_administration_menu');


function bp_code_snippets_csm_help() {
	global $code_snippets_manager_admin_page;
	
	add_thickbox();
	wp_enqueue_script( 'bp-cs-admin', BP_CS_PLUGIN_URL_JS . '/bp-cs-admin.js');
	wp_enqueue_style( 'bp-cs-admin-style', BP_CS_PLUGIN_URL_CSS . '/bp-cs-admin.css');
	
	add_action('admin_head', 'bp_code_snippets_admin_messages');
	
	$screen = get_current_screen();
	
	if( is_multisite() )
		$page_id = $code_snippets_manager_admin_page . '-network';
		
	else
		$page_id = $code_snippets_manager_admin_page;
	
	if ( $screen->id != $page_id )
		return;
		
	$screen->add_help_tab( array(
		'id'      => 'bp-cs-about',
		'title'   => __('About', 'bp-code-snippets'),
		'content' => '<p>' . sprintf( __("BP Code Snippets is a <b>BuddyPress 1.6+</b> component to share snippets in your community. Version 2.0 comes with some improvements such as <ul><li>Better integration with group forums and blog posts</li><li><a href='%s' class='thickbox'>The Snippets directory</a> now supports language category filtering (and searching)</li><li>Snippets can now be favorited by members</li><li>A widget can display the most favorited snippets</li><li>You can allow people to embed snippets in an iframe</li><li>You can ease the process of sharing snippets thanks to BP Bookmarklet plugin.</li></ul>", 'bp-code-snippets'), BP_CS_PLUGIN_URL . '/screenshot-1.png') . '</p>',
	));

	$screen->add_help_tab( array(
		'id'      => 'bp-cs-groups',
		'title'   => __('Group Snippets', 'bp-code-snippets'),
		'content' => '<p>' .sprintf( __("When this option is enabled, Group Admins will have the choice to activate Snippets sharing by <a href='%s' class='thickbox'>setting the available languages for their group</a> (based on the ones the blog/network admin activated on the <a href='#bpcs-languages' class='bpcs-visu'>first column</a> of this page) during group creation process or at anytime from the admin tab (Snippets submenu) of the group. The snippets (except those attached to comments) of <b>public</b> groups will also be displayed in the main Snippets directory.", 'bp-code-snippets'), BP_CS_PLUGIN_URL . '/screenshot-2.png') . '</p>',
	));
	
	$screen->add_help_tab( array(
		'id'      => 'bp-cs-forums',
		'title'   => __('Forum Snippets', 'bp-code-snippets'),
		'content' => '<p>' . sprintf( __("If Group Snippets option and this one are enabled, then <b>Group</b> Forum topics and replies can include snippets in their content. At the left of the post topic / reply button, a button with the caption <em>Add Snippet</em>  will display and on click a <a href='%s' class='thickbox'>modal window will allow the member to attach a new snippet</a> to his topic/reply. The snippets attached to <b>topics</b> of <b>public</b> groups will also be listed in the main Snippets directory.", 'bp-code-snippets'), BP_CS_PLUGIN_URL . '/screenshot-3.png') . '</p>',
	));
	
	$screen->add_help_tab( array(
		'id'      => 'bp-cs-blogs',
		'title'   => __('Blog Snippets', 'bp-code-snippets'),
		'content' => '<p>' . __("When this option is enabled, members with the capacity of <b>publishing</b> posts will be able to add new snippets to their posts. A new button with the caption <em>Add Snippet</em>  will show in the media bar. On click, a modal window will allow the member to create a new snippet and attach it to his post. Snippets are only displayed on page or single templates, on other templates a link to the post is shown. If the privacy settings of the blog allow the search engines to index it, then the snippets will also be available in the main Snippets directory.", 'bp-code-snippets') . '</p>',
	));
	
	$screen->add_help_tab( array(
		'id'      => 'bp-cs-iframe',
		'title'   => __('Iframe Embedding', 'bp-code-snippets'),
		'content' => '<p>' . sprintf( __("When this option is enabled, a <a href='%s' class='thickbox'>toolbar shows over the content</a> of the snippets. It contains two buttons, one to display the permalink of the snippets and one other to display the iframe embedding code. After copying this code, members or visitors will be able to paste it in their website.", 'bp-code-snippets'), BP_CS_PLUGIN_URL . '/screenshot-4.png') . '</p>',
	));
	
	$screen->add_help_tab( array(
		'id'      => 'bp-cs-shortcode',
		'title'   => __('Shortcode', 'bp-code-snippets'),
		'content' => '<p>' . __("When Iframe Embedding option and this option are enabled, it's possible for <b>logged in members</b> to copy the shortcode of a snippet. The Embedding toolbar will show a third button to display the snippet shortcode. After copying it, the member can paste it into a blog post, a forum topic / reply, a snippet comment. In case people paste shortcode in activity, the <em>default</em> link to the snippet showing when [Read more] is clicked will automatically be replaced by an iframe containing the code.", 'bp-code-snippets') . '</p>',
	));
	
	$screen->add_help_tab( array(
		'id'      => 'bp-cs-bkmklet',
		'title'   => __('BP Bookmarklet', 'bp-code-snippets'),
		'content' => '<p>' . __("<a href='http://wordpress.org/extend/plugins/bp-bookmarklet/' target='_blank'>BP Bookmarklet</a> is a BuddyPress plugin that adds a bookmarklet to the user's browser so that he can easily share links in his activity or group updates. If this plugin is installed and activated, then you can choose to check this option so that BP Code Snippets adds a new tab to BP Bookmarklet to also share snippets in the main directory or one of the user's group.", 'bp-code-snippets') . '</p>',
	));	
}

function bp_code_snippets_admin_messages(){
	?>
	<script type="text/javascript">
	var nogroup = "<?php _e('If snippets are not available for groups, then snippets are not available for group forums :(','bp-code-snippets');?>";
	var noembed = "<?php _e('If snippets cannot be embed in an iframe, then embeding snippets in activity is not allowed :(','bp-code-snippets');?>";
	</script>
	<?php
}


/* wp-pointer */

add_action( 'admin_enqueue_scripts', 'bp_code_snippets_enqueue_pointer' );

function bp_code_snippets_enqueue_pointer() {
	
	$bpcs_welcome = get_user_setting( '_bp_code_snippets_user_settings', 0 );
	if ( ! $bpcs_welcome ) {
		wp_enqueue_style( 'wp-pointer' ); 
		wp_enqueue_script( 'wp-pointer' ); 
		wp_enqueue_script( 'utils' );
		add_action( 'admin_print_footer_scripts', 'bp_code_snippets_pointer_print_footer_scripts' );
	}
}

function bp_code_snippets_pointer_print_footer_scripts() {
	$pointer_content = '<h3>'. __('Thanks for choosing BP Code Snippets !', 'bp-code-snippets') . '</h3>';
	$pointer_content .= '<p>'. __("Please take a few seconds to store the settings of this plugin in BuddyPress submenu Code Snippets Manager.", 'bp-code-snippets') . '</p>';
?>
<script type="text/javascript"> 
//<![CDATA[
jQuery(document).ready( function($) { 
	$('#toplevel_page_bp-general-settings').pointer({ 
		content: '<?php echo $pointer_content; ?>', 
		position: {
			edge:'left',
			my: 'left middle', 
			at: 'right top', 
			offset: '0 10',
		},
		close: function() { 
			setUserSetting( '_bp_code_snippets_user_settings', '1' ); 
		} 
	}).pointer('open'); 
}); 
//]]> 
</script>
<?php
}

function bp_code_snippets_child_blog_deactivate_option() {
	global $blog_id;
	
	if(is_multisite() && $blog_id != bp_get_root_blog_id() && bp_code_snippets_is_blog_snippets_enable() ) {
		
		add_options_page( __('Snippets Option','bp-code-snippets'), __('Snippets Option','bp-code-snippets'), 'manage_options', 'snippets-option', 'bp_code_snippets_child_option');
		
	}
}
add_action( 'admin_menu', 'bp_code_snippets_child_blog_deactivate_option' );


function bp_code_snippets_child_option() {
	
	if ( isset( $_POST['save'] ) && check_admin_referer('child-cs-settings') ) {
		update_option( 'cs-child-enable', $_POST['cs-child-enable'] );
		$updated = true;
	}
	
	$cs_child_enable = get_option('cs-child-enable');
	?>
	<div class='wrap'>
		<h2>Snippets Option</h2>
		
		<?php if ( isset($updated) ) : ?><?php echo "<div id='message' class='updated fade'><p>" . __( 'Settings Updated.', 'bp-code-snippets' ) . "</p></div>" ?><?php endif; ?>

			<p>&nbsp;</p>
			<p class="description"><?php printf(__('BP Code Snippets is a BuddyPress plugin to highlight syntax/share snippets of code. The Super Admin of your network activated it for blogs. If you wish to highlight and share your snippets in your posts, you can enable this feature by checking the option below. Currently the Super Admin activated syntax highlighting for these languages : %s.', 'bp-code-snippets'), implode(', ', bp_group_snippets_get_available_languages() ) );?></p>
			<p>&nbsp;</p>
			
			<form action="" name="child-cs-settings-form" id="child-cs-settings-form" method="post">
		
			<table id="snippet_settings">
				<tr valign="top">
					<td><label for="cs-child-enable"><?php _e( 'Enable Code Snippets for your blog ? For more infos, please contact super admin', 'bp-code-snippets' ) ?></label></td>
					<td>
						<input type="radio" name="cs-child-enable" id="cs-child-enable-yes" value="1" <?php if($cs_child_enable) echo "checked";?>/><?php _e( 'Yes', 'bp-code-snippets' ) ?>&nbsp;
						<input type="radio" name="cs-child-enable" id="cs-child-enable-no" value="0" <?php if(!$cs_child_enable) echo "checked";?>/><?php _e( 'No', 'bp-code-snippets' ) ?>
					</td>
				</tr>
			</table>
		
			<p class="submit">
				<input type="submit" name="save" value="<?php _e( 'Save Settings', 'bp-code-snippets' ) ?>"/>
			</p>

			<?php
			/* This is very important, don't leave it out. */
			wp_nonce_field( 'child-cs-settings' );
			?>
		</form>
		
	</div>
	<?php
}

function bp_code_snippets_is_bp_bookmarklet_version_not_ok() {
	$bpbkmkversion = get_option('bp-bookmarklet-version');
	
	if( $bpbkmkversion && version_compare( $bpbkmkversion, '1.1', '<' ) ){
		return true;
	}
	else return false;
}
?>