<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

function bp_code_snippets_db_install(){
	global $wpdb;
	
	$sql = false;
	
	$bp_cs_installed_version = get_option('bp-code-snippets-version');

	if ( !empty($wpdb->charset) )
		$charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
		
	if( !empty( $bp_cs_installed_version ) && version_compare( $bp_cs_installed_version, '2.0', '<' ) ) {
		
		$wpdb->query("ALTER TABLE {$wpdb->base_prefix}code_snippets ADD secondary_id int(11) NOT NULL DEFAULT 0 AFTER id_group;");
		$wpdb->query("ALTER TABLE {$wpdb->base_prefix}code_snippets ADD object varchar(20) NOT NULL DEFAULT 'directory' AFTER id_group;");
		$wpdb->query("ALTER TABLE {$wpdb->base_prefix}code_snippets ADD is_draft int(1) NOT NULL DEFAULT 0 AFTER id_user;");
		$wpdb->query("ALTER TABLE {$wpdb->base_prefix}code_snippets ADD hide int(1) NOT NULL DEFAULT 0 AFTER id_user;");
		$wpdb->query("ALTER TABLE {$wpdb->base_prefix}code_snippets CHANGE id_group item_id int(11);");
		
		/* only group in table */
		$wpdb->query("UPDATE {$wpdb->base_prefix}code_snippets SET object = 'group' WHERE item_id IS NOT NULL;");
		$wpdb->query("UPDATE {$wpdb->base_prefix}code_snippets SET hide = 1 WHERE item_id IN( SELECT id FROM {$wpdb->base_prefix}bp_groups WHERE status != 'public' );");
		
		/* we need to change meta for each group :( */
		$groups = $wpdb->get_results("SELECT group_id, meta_value FROM {$wpdb->base_prefix}bp_groups_groupmeta WHERE meta_key = 'snippets_code_ok';");
		
		if( $groups && count($groups) > 0 ) {
			foreach($groups as $group) {
				$serialgroup = maybe_unserialize($group->meta_value);
				if( $serialgroup && !is_array( $serialgroup ) ){
					$migre_snippets_group_code = explode("|", $group->meta_value);
					for($i = 0 ; $i < count($migre_snippets_group_code) ; $i++){
						$code = explode(":", $migre_snippets_group_code[$i]);
						$array_snippets_group_code[] = $code[1];
					}
					if(count($array_snippets_group_code) >=1 ) {
						groups_update_groupmeta( $group->group_id, 'snippets_code_ok', $array_snippets_group_code);
					}

				}
			}
		}

		
		update_option('bp-code-snippets-old-version', 1);
		
		$sql[] = "CREATE TABLE {$wpdb->base_prefix}code_snippets_meta (
			  	  id int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
			  	  snippet_id int(11) NOT NULL,
			  	  meta_key varchar(255) NOT NULL,
			  	  meta_value text NOT NULL
				  ){$charset_collate};";
		
	} elseif( !empty( $bp_cs_installed_version) && version_compare( $bp_cs_installed_version, BP_CS_PLUGIN_VERSION, '<' ) ) {
		$zclip = get_option( 'cs-zclip-enable' );
		
		if( !empty( $zclip) )
			delete_option( 'cs-zclip-enable' );
			
	} else {
		
		$sql[] = "CREATE TABLE {$wpdb->base_prefix}code_snippets (
		          id_cs int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
		  		  item_id int(11) NOT NULL DEFAULT 0,
				  object varchar(20) NOT NULL DEFAULT 'directory',
				  secondary_id int(11) NOT NULL DEFAULT 0,
		  		  id_user int(11) NOT NULL,
				  hide int(1) NOT NULL DEFAULT 0,
				  is_draft int(1) NOT NULL DEFAULT 0,
		  		  date_cs datetime NOT NULL,
		  		  snippet_title varchar(200) NOT NULL,
		  	      snippet_type varchar(20) NOT NULL,
		  		  snippet_purpose text NOT NULL,
		  		  snippet_content text NOT NULL,
		  		  cs_comment_count int(11) NOT NULL
		          ){$charset_collate};";

		$sql[] = "CREATE TABLE {$wpdb->base_prefix}code_snippets_comments (
			   	  id_cs_comment int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
			  	  cs_id int(11) NOT NULL,
			   	  user_id int(11) NOT NULL,
			  	  date_comment datetime NOT NULL,
			  	  comment_content text NOT NULL
				  ){$charset_collate};";
				
		$sql[] = "CREATE TABLE {$wpdb->base_prefix}code_snippets_meta (
			  	  id int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
			  	  snippet_id int(11) NOT NULL,
			  	  meta_key varchar(255) NOT NULL,
			  	  meta_value text NOT NULL
				  ){$charset_collate};";
		
	}
	

	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	
	if( !empty( $sql ) )
		dbDelta($sql);
}

function bp_code_snippets_set_available_lg(){
	if(!get_option('bp_code_snippets_available_language')){
		$snippets_languages = array( "Php" => "php",
									"Javascript" => "js",
									"Css" => "css",
									"Xml-Html" => "xml",
									"Sql" => "sql",
									"Actionscript" => "as3",
									"Java" => "java",
									"JavaFX" => "jfx",
									"Perl" => "pl",
									"Python" => "py",
									"Ruby" => "ruby",
									"ColdFusion" => "cf",
									"AppleScript" => "applescript",
									"Cpp" => "cpp",
									"Csharp" => "csharp",
									"VB"  => "vb",
									"Bash" => "bash");
		update_option( 'bp_code_snippets_available_language', $snippets_languages );
	}
}
?>