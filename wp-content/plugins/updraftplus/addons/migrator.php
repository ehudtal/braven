<?php
/*
UpdraftPlus Addon: migrator:Migrate a WordPress site to a different location.
Description: Import a backup into a different site, including database search-and-replace. Ideal for development and testing and cloning of sites.
Version: 2.9
Shop: /shop/migrator/
Latest Change: 1.10.4
*/

if (!defined('UPDRAFTPLUS_DIR')) die('No direct access allowed');

// TODO: single-into-multisite migrations:
// TODO: http://iandunn.name/comprehensive-wordpress-multisite-migrations/
// So far: database prefix is switched; a new site is created. Next: selectively add plugins + themes (don't replace). And network-activate them.
// TODO: Then test path-based multisites too
// TODO: The siteurl at the top of the db dump should actually be what's in the DB, and not any over-rides ... ?
// TODO: Search for other TODOs in the code and in updraft-restorer.php
// TODO: Set all post/comment ownership to importing admin. Or import the users.
// TODO: Don't import extraneous tables (e.g. users)
// TODO: Rewriting of URLs like wp-content/uploads/2011/10/bant-logo.png
// TODO: Document it

// TODO: Use log_e

// See http://lists.automattic.com/pipermail/wp-hackers/2013-May/046089.html

// Search/replace code adapted in according with the licence from https://github.com/interconnectit/Search-Replace-DB

$updraftplus_addons_migrator = new UpdraftPlus_Addons_Migrator;

class UpdraftPlus_Addons_Migrator {

	private $is_migration;
	private $restored_blogs = false;
	private $restored_sites = false;
	private $wpdb_obj = false;

	public function __construct() {
		add_action('updraftplus_restore_form_db', array($this, 'updraftplus_restore_form_db'));
		add_action('updraftplus_restored_db', array($this, 'updraftplus_restored_db'), 10, 2);
		add_action('updraftplus_restored_db_table', array($this, 'updraftplus_restored_db_table'), 10, 3);
		add_action('updraftplus_restore_db_pre', array($this, 'updraftplus_restore_db_pre'));
		add_action('updraftplus_restore_db_record_old_siteurl', array($this, 'updraftplus_restore_db_record_old_siteurl'));
		add_action('updraftplus_restore_db_record_old_home', array($this, 'updraftplus_restore_db_record_old_home'));
		add_action('updraftplus_restore_db_record_old_content', array($this, 'updraftplus_restore_db_record_old_content'));
		add_action('updraftplus_restored_plugins_one', array($this, 'restored_plugins_one'));
		add_action('updraftplus_restored_themes_one', array($this, 'restored_themes_one'));
		add_action('updraftplus_debugtools_dashboard', array($this, 'debugtools_dashboard'));
		add_action('updraftplus_adminaction_searchreplace', array($this, 'adminaction_searchreplace'));
		add_action('updraftplus_migrate_modal_output', array($this, 'updraftplus_migrate_modal_output'));
		add_filter('updraftplus_restore_set_table_prefix', array($this, 'restore_set_table_prefix'), 10, 2);
		add_filter('updraftplus_dbscan_urlchange', array($this, 'dbscan_urlchange'), 10, 3);
		add_filter('updraftplus_restorecachefiles', array($this, 'restorecachefiles'), 10, 2);
		add_filter('updraftplus_restored_plugins', array($this, 'restored_plugins'));
	}

	public function updraftplus_migrate_modal_output() {

		echo '<div id="updraft_migrate_modal_main">';

		echo '<p>'.__('A "migration" is ultimately the same as a restoration - but using backup archives that you import from another site.', 'updraftplus').' '.__('The UpdraftPlus Migrator modifies the restoration operation appropriately, to fit the backup data to the new site.', 'updraftplus');

// 		echo __('To restore using any of the backup sets below, press the button.', 'updraftplus').' '.
		echo ' '.sprintf(__('<a href="%s">Read this article to see step-by-step how it\'s done.</a>', 'updraftplus'),'https://updraftplus.com/faqs/how-do-i-migrate-to-a-new-site-location/').'</p>';

		echo $this->migrate_widget();

		do_action('updraft_migrate_after_widget');

		echo '</div>';

		echo '<div id="updraft_migrate_modal_alt" style="display:none;"></div>';

	}

	private function migrate_widget($backup_history = false) {
		global $updraftplus, $updraftplus_admin;
 
		if (false === $backup_history) $backup_history = UpdraftPlus_Options::get_updraft_option('updraft_backup_history');
		if (!is_array($backup_history)) $backup_history=array();
		if (empty($backup_history)) return "<p><em>".__('This site has no backups to restore from yet.', 'updraftplus')."</em></p>";

		$updraft_dir = $updraftplus->backups_dir_location();
		$backupable_entities = $updraftplus->get_backupable_file_entities(true, true);

		$ret = '<div style="margin:8px 4px; padding: 0px 8px 8px; border: 1px dotted;">';

		$ret .= '<p style="margin-top: 4px; padding-top: 4px;"><strong>'.__('Restore an existing backup set onto this site', 'updraftplus').'</strong> <a href="#" onclick="jQuery(\'#updraft-migrate-modal\').dialog(\'close\'); updraft_openrestorepanel(); return false;">('.htmlspecialchars(__('To import a backup set, go to the "Existing Backups" tab', 'updraftplus')).")</a></p>";

		$ret .= '<div style="height:30px; clear:left;">
			<select id="updraft_migrate_select_backup" style="height:28px; margin-right: 6px; width:555px; float:left;">';

		krsort($backup_history);
		foreach ($backup_history as $key => $backup) {

			# https://core.trac.wordpress.org/ticket/25331 explains why the following line is wrong
			# $pretty_date = date_i18n('Y-m-d G:i',$key);
			// Convert to blog time zone
// 			$pretty_date = get_date_from_gmt(gmdate('Y-m-d H:i:s', (int)$key), 'Y-m-d G:i');
			$pretty_date = get_date_from_gmt(gmdate('Y-m-d H:i:s', (int)$key), 'M d, Y G:i');

			$non = $backup['nonce'];

			$jobdata = $updraftplus->jobdata_getarray($non);

			//$delete_button = $this->delete_button($key, $non, $backup);

			$date_label = $updraftplus_admin->date_label($pretty_date, $key, $backup, $jobdata, $non, true);

			$ret .= '<option value="'.esc_attr($key).'">'.htmlspecialchars($date_label).'</option>';

		}


		$ret .= '</select>';

		$ret .= '<button id="updraft_migrate_select_backup_go" title="'.__('After pressing this button, you will be given the option to choose which components you wish to migrate','updraftplus').'" type="button" class="button-primary" style="float: left; font-size:16px !important; height:28px; position:relative; top:1px;" onclick="var whichset=jQuery(\'#updraft_migrate_select_backup\').val();  updraft_initiate_restore(whichset);">'.__('Restore', 'updraftplus').'</button></div>';

		$ret .= '</div>';

// 		$ret .= '</tbody></table>';
		return $ret;
	}

	# Disable W3TC and WP Super Cache, etc.
	public function restored_plugins() {
		if (true !== $this->is_migration) return;
		global $updraftplus;
		$active_plugins = maybe_unserialize($updraftplus->option_filter_get('active_plugins'));
		if (!is_array($active_plugins)) return;
		$disable_plugins = array(
			'w3-total-cache/w3-total-cache.php' => 'W3 Total Cache',
			'wp-super-cache/wp-cache.php' => 'W3 Super Cache',
			'quick-cache/quick-cache.php' => 'Quick Cache',
			'wp-fastest-cache/wpFastestCache.php' => 'WP Fastest Cache'
		);
		foreach ($disable_plugins as $slug => $desc) {
			# in_array is case sensitive
			#if (in_array($slug, $active_plugins)) {
			if (preg_grep("#".$slug."#i" , $active_plugins)) {
				unset($active_plugins[$slug]);
				echo '<strong>';
				$updraftplus->log_e("Disabled this plugin: %s: re-activate it manually when you are ready.", $desc);
				echo '</strong>';
			}
		}
		update_option('active_plugins', $active_plugins);
	}

	public function restorecachefiles($val, $file) {
		# On a migration, we don't want to add cache files if they do not already exist (because usually they won't work until re-installed)
		if (true !== $this->is_migration || false == $val) return $val;
		$val = (is_file(WP_CONTENT_DIR.'/'.$file)) ? $val : false;
		if (false == $val) {
			global $updraftplus;
			$updraftplus->log_e("%s: Skipping cache file (does not already exist)", $file);
		}
		return $val;
	}

	public function adminaction_searchreplace() {
		echo '<h2>'.__('Search / replace database','updraftplus').'</h2>';
		echo '<strong>'.__('Search for', 'updraftplus').':</strong> '.htmlspecialchars($_POST['search'])."<br>";
		echo '<strong>'.__('Replace with', 'updraftplus').':</strong> '.htmlspecialchars($_POST['replace'])."<br>";
		$this->page_size = (empty($_POST['pagesize']) || !is_numeric($_POST['pagesize'])) ? 5000 : $_POST['pagesize'];
		$this->which_tables = (empty($_POST['whichtables'])) ? '' : explode(',', ($_POST['whichtables']));
		if (empty($_POST['search'])) {
			echo sprintf(__("Failure: No %s was given.",'updraftplus'), __('search term','updraftplus'))."<br>";
			echo '<a href="'.UpdraftPlus_Options::admin_page_url().'?page=updraftplus">'.__('Return to UpdraftPlus Configuration','updraftplus').'</a>';
			return;
		}
		global $updraftplus_restorer;
		if (empty($updraftplus_restorer) || !is_a($updraftplus_restorer, '')) {
			# Needed for the UpdraftPlus_WPDB class and Updraft_Restorer::sql_exec() method
			require_once(UPDRAFTPLUS_DIR.'/restorer.php');
			$updraftplus_restorer = new Updraft_Restorer(null, null, true);
		}
		$this->updraftplus_restore_db_pre();
		$this->tables_replaced = array();
		$this->updraftplus_restored_db_dosearchreplace($_POST['search'], $_POST['replace'], $this->base_prefix, false);
		echo '<a href="'.UpdraftPlus_Options::admin_page_url().'?page=updraftplus">'.__('Return to UpdraftPlus Configuration','updraftplus').'</a>';
	}

	public function debugtools_dashboard() {
		global $updraftplus_admin;
	?>
		<h3><?php echo __('Search / replace database','updraftplus'); ?></h3>
		<p><em><?php echo __('This can easily destroy your site; so, use it with care!', 'updraftplus');?></em></p>
		<form method="post" onsubmit="return(confirm('<?php echo esc_js(__('A search/replace cannot be undone - are you sure you want to do this?', 'updraftplus'));?>'))">
		<input type="hidden" name="nonce" value="<?php echo wp_create_nonce('updraftplus-credentialtest-nonce');?>">
		<input type="hidden" name="action" value="updraftplus_broadcastaction">
		<input type="hidden" name="subaction" value="updraftplus_adminaction_searchreplace">
		<table>
		<?php
			echo $updraftplus_admin->settings_debugrow(__('Search for', 'updraftplus').':', '<input type="text" name="search" value="" style="width:380px;">');
			echo $updraftplus_admin->settings_debugrow(__('Replace with', 'updraftplus').':', '<input type="text" name="replace" value="" style="width:380px;">');
			echo $updraftplus_admin->settings_debugrow(__('Rows per batch', 'updraftplus').':', '<input type="number" min="1" step="1" name="pagesize" value="5000" style="width:380px;">');
			echo $updraftplus_admin->settings_debugrow(__('These tables only', 'updraftplus').':', '<input type="text" name="whichtables" title="'.esc_attr(__('Enter a comma-separated list; otherwise, leave blank for all tables.', 'updraftplus')).'" value="" style="width:380px;">');
		?>
		<?php echo $updraftplus_admin->settings_debugrow('', '<input class="button-primary" type="submit" value="'.esc_attr(__('Go', 'updraftplus')).'">'); ?>
		</table>
		</form>
	<?php
	}

	public function dbscan_urlchange($output, $old_siteurl, $res) {
		if (isset($res['updraft_restorer_replacesiteurl']) && $res['updraft_restorer_replacesiteurl']) return '';
		return '<strong>'.__('Warning:', 'updraftplus').'</strong>'.' '.__('This looks like a migration (the backup is from a site with a different address/URL), but you did not check the option to search-and-replace the database. That is usually a mistake.', 'updraftplus');
	}
	
	public function restored_plugins_one($plugin) {
		echo '<strong>'.__('Processed plugin:', 'updraftplus').'</strong> '.$plugin."<br>";
		global $updraftplus;
		$updraftplus->log("Processed plugin: $plugin");
	}

	public function restored_themes_one($theme) {
		// Network-activate
		$allowed_themes = get_site_option('allowedthemes');
		$allowed_themes[$theme] = true;
		update_site_option('allowedthemes', $allowed_themes);
		global $updraftplus;
		echo '<strong>'.__('Network activating theme:', 'updraftplus').'</strong> '.$theme."<br>";
		$updraftplus->log('Network activating theme: '.$theme);
	}

	public function restore_set_table_prefix($import_table_prefix, $backup_is_multisite) {
		if (!is_multisite() || $backup_is_multisite !== 0) return $import_table_prefix;
		
		$new_blogid = $this->generate_new_blogid();

		if (!is_integer($new_blogid)) return $new_blogid;

		$this->new_blogid = $new_blogid;

		return (string)$import_table_prefix.$new_blogid.'_';
	}

	function dump_form() {
		$form = '';
		foreach ($_POST as $key =>$val) {
			if (is_array($val)) {
				foreach ($val as $ktwo => $valtwo) {
					$form .= '<input type="hidden" name="'.$key.'['.$ktwo.']" value="'.htmlspecialchars($valtwo).'">';
				}
			} else {
				$form .= '<input type="hidden" name="'.$key.'" value="'.htmlspecialchars($val).'">';
			}
		}
		print $form;
	}

	function getinfo_form($msg = '', $blogname = '') {

		global $current_site;

		echo '<h3>'.__('Information needed to continue:','updraftplus').'</h3>';
		echo '<p><em>'.__('Please supply the following information:', 'updraftplus').'</em></p>';

		echo '<p>'.__('Enter details for where this new site is to live within your multisite install:', 'updraftplus').'</p>';

		if ($msg) {
			echo '<p>'.$msg.'</p>';
		}

		echo '<form method="POST">';
		// These strings are part of WordPress
		if ( !is_subdomain_install() ) {
			echo '<label for="blogname">' . __('Site Name:') . '</label>';
		} else {
			echo '<label for="blogname">' . __('Site Domain:') . '</label>';
		}
		$this->dump_form();

		if ( !is_subdomain_install() )
			echo '<span class="prefix_address">' . $current_site->domain . $current_site->path . '</span><input name="updraftplus_migrate_blogname" type="text" id="blogname" value="'. esc_attr($blogname) .'" maxlength="60" /><br />';
		else
			echo '<input name="updraftplus_migrate_blogname" type="text" id="blogname" value="'.esc_attr($blogname).'" maxlength="60" /><span class="suffix_address">.' . ( $site_domain = preg_replace( '|^www\.|', '', $current_site->domain ) ) . '</span><br />';


		?><p><input type="submit" name="submit" class="button button-primary" value="<?php esc_attr_e('Continue') ?>" /></p><?php

		echo '</form>';
	}

	private function generate_new_blogid() {

		$blog_title = __('Migrated site (from UpdraftPlus)', 'updraftplus');

		if (empty($_POST['updraftplus_migrate_blogname'])) {
			$this->getinfo_form();
			return false;
		}

		// Verify value given
		$result = wpmu_validate_blog_signup($_POST['updraftplus_migrate_blogname'], $blog_title);

		if (count($result['errors']) >0 && $result['errors']->get_error_code()) {
			if (is_wp_error($result['errors'])) {
				$err_msg = '<ul style="list-style: disc inside;">';
				foreach ($result['errors']->get_error_messages() as $key => $msg) {
					$err_msg .= '<li><strong>'.__('Error:', 'updraftplus').'</strong> '.htmlspecialchars($msg).'</li>';
				}
				$err_msg .= '</ul>';
			}
			if (isset($err_msg)) {
				$this->getinfo_form($err_msg, $_POST['updraftplus_migrate_blogname']);
				return false;
			}
		}

		$blogname = $_POST['updraftplus_migrate_blogname'];

		global $wpdb;
		if ( domain_exists($result['domain'], $result['path'], $wpdb->siteid) ) {
			// A WordPress-native string
			$this->getinfo_form(__( '<strong>ERROR</strong>: Site URL already taken.'), $_POST['updraftplus_migrate_blogname']);
			return false;
		}

		$create = create_empty_blog($result['domain'], $result['path'], $blog_title, $wpdb->siteid);
		if (is_integer($create)) {
			$url = untrailingslashit($result['domain'].$result['path']);
			echo '<strong>'.__('New site:','updraftplus').'</strong> '.$url.'<br>';

			// Update record of what we want to rewrite the URLs to in the search/replace operation
			// TODO: How to detect whether http or https???
			$this->siteurl = 'http://'.$url;
			// ???
			$this->home = 'http://'.$url;

			return $create;
		} else {
			$this->getinfo_form(print_r($create,true), $_POST['updraftplus_migrate_blogname']);
			return false;
		}
	}

	public function updraftplus_restore_form_db() {

		echo '<input name="updraft_restorer_replacesiteurl" id="updraft_restorer_replacesiteurl" type="checkbox" value="1"><label for="updraft_restorer_replacesiteurl" title="'.sprintf(__('All references to the site location in the database will be replaced with your current site URL, which is: %s', 'updraftplus'), htmlspecialchars(untrailingslashit(site_url()))).'"> '.__('Search and replace site location in the database (migrate)','updraftplus').'</label> <a href="https://updraftplus.com/faqs/tell-me-more-about-the-search-and-replace-site-location-in-the-database-option/">'.__('(learn more)','updraftplus').'</a>';

	}

	public function updraftplus_restore_db_record_old_siteurl($old_siteurl) {
		// Only record once
		if (!empty($this->old_siteurl)) return;
		$this->old_siteurl = $old_siteurl;
	}

	public function updraftplus_restore_db_record_old_home($old_home) {
		// Only record once
		if (!empty($this->old_home)) return;
		$this->old_home = $old_home;
	}

	public function updraftplus_restore_db_record_old_content($old_content) {
		// Only record once
		if (!empty($this->old_content)) return;
		$this->old_content = $old_content;
	}

	public function updraftplus_restore_db_pre() {

		global $wpdb, $updraftplus;

		$this->siteurl = untrailingslashit(site_url());
		$this->home = untrailingslashit(home_url());
		$this->content = untrailingslashit(content_url());
		$this->use_wpdb = ((!function_exists('mysql_query') && !function_exists('mysqli_query')) || !$wpdb->is_mysql || !$wpdb->ready) ? true : false;

		$this->base_prefix = $updraftplus->get_table_prefix(false);

		$mysql_dbh = false;

		if (false == $this->use_wpdb) {
			// We have our own extension which drops lots of the overhead on the query
			// This class is defined in updraft-restorer.php, which has been included if we get here
			$wpdb_obj = new UpdraftPlus_WPDB( DB_USER, DB_PASSWORD, DB_NAME, DB_HOST);
			// Was that successful?
			if (!$wpdb_obj->is_mysql || !$wpdb_obj->ready) {
				$this->use_wpdb = true;
			} else {
				$this->wpdb_obj = $wpdb_obj;
				$mysql_dbh = $wpdb_obj->updraftplus_getdbh();
				$use_mysqli = $wpdb_obj->updraftplus_use_mysqli();
			}
		}

		$this->mysql_dbh = $mysql_dbh;
		$this->use_mysqli = $use_mysqli;

		if (true == $this->use_wpdb) $updraftplus->log_e('Database access: Direct MySQL access is not available, so we are falling back to wpdb (this will be considerably slower)');

		if (is_multisite()) {
			$sites = $wpdb->get_results('SELECT id, domain, path FROM '.esc_sql($this->base_prefix).'site', ARRAY_N);
			if (is_array($sites)) {
				$nsites = array();
				foreach ($sites as $site) $nsites[$site[0]] = array($site[1], $site[2]);
				$this->original_sites = $nsites;
			}
		}

		$this->report = array(
			'tables' => 0,
			'rows' => 0,
			'change' => 0,
			'updates' => 0,
			'timetaken' => 0,
			'errors' => array(),
		);

	}

	public function updraftplus_restored_db_table($table, $import_table_prefix) {

		global $updraftplus, $wpdb;

		// Anything to do?
		if (!isset($_POST['updraft_restorer_replacesiteurl']) || $_POST['updraft_restorer_replacesiteurl'] != 1) return;

		// Can only do something if the old siteurl is known
		$old_siteurl = isset($this->old_siteurl) ? $this->old_siteurl : '';
		$old_home = isset($this->old_home) ? $this->old_home : '';
		$old_content = isset($this->old_content) ? $this->old_content : $old_siteurl.'/wp-content';

		if (!$old_home && !$old_siteurl) return;

		if (empty($this->tables_replaced)) $this->tables_replaced = array();

		// Already done?
		if (!empty($this->tables_replaced[$table])) return;

		# If not done already, then search & replace this table, + record that it is done
		@set_time_limit(1800);

		$stripped_table = substr($table, strlen($import_table_prefix));
		# Remove multisite site number prefix, if relevant
		if (is_multisite() && preg_match('/^(\d+)_(.*)$/', $stripped_table, $matches)) $stripped_table = $matches[2];

		# This array is for tables that a) we know don't need URL search/replacing and b) are likely to be sufficiently big that they could significantly delay the progress of the migrate (and increase the risk of timeouts on hosts that enforce them)
		# The term_relationships table contains 3 columns, all integers. Therefore, we can skip it. It can easily get big, so this is a good time-saver.
		$skip_tables = array('slim_stats', 'statpress', 'term_relationships', 'icl_languages_translations', 'icl_string_positions', 'icl_string_translations', 'icl_strings', 'redirection_logs', 'Counterize', 'Counterize_UserAgents', 'Counterize_Referers', 'adrotate_stats', 'login_security_solution_fail', 'wfHits', 'wbz404_logs', 'wbz404_redirects', 'wp_wfFileMods', 'tts_trafficstats', 'tts_referrer_stats', 'dmsguestbook', 'relevanssi', 'wponlinebackup_generations', 'svisitor_stat', 'simple_feed_stats', 'itsec_log', 'wp_rp_tags', 'woocommerce_order_items', 'relevanssi_log', 'blc_instances');

		if (in_array($stripped_table, $skip_tables)) {
			$this->tables_replaced[$table] = true;
			$updraftplus->log_e("Skipping this table: data in this table (%s) should not be search/replaced", $table);
			return;
		}

		# Blogs table on multisite doesn't contain the full URL
		if (is_multisite() && ($table == $this->base_prefix.'blogs' || $table == $this->base_prefix.'site') && (preg_match('#^https?://([^/]+)#i', $this->home, $matches) || preg_match('#^https?://([^/]+)#i', $this->siteurl, $matches)) && (preg_match('#^https?://([^/]+)#i', $old_home, $omatches) || preg_match('#^https?://([^/]+)#i', $old_siteurl, $omatches))) {
			$from_array = strtolower($omatches[1]);
			$to_array = strtolower($matches[1]);
			$updraftplus->log_e("Replacing in blogs/site table: from: %s to: %s", htmlspecialchars($from_array),htmlspecialchars( $to_array));
			$try_site_blog_replace = true;
		} else {

			list($from_array, $to_array) = $this->build_searchreplace_array($old_siteurl, $old_home, $old_content);

			# This block is for multisite installs, to do the search/replace of each site's URL individually. We want to try to do it here for efficiency - i.e. so that we don't have to double-pass tables
			if (!empty($this->restored_blogs) && preg_match('/^(\d+)_(.*)$/', substr($table, strlen($import_table_prefix)), $tmatches) && (preg_match('#^((https?://)([^/]+))#i', $this->home, $matches) || preg_match('#^((https?://)([^/]+))#i', $this->siteurl, $matches)) && (preg_match('#^((https?://)([^/]+))#i', $old_home, $omatches) || preg_match('#^((https?://)([^/]+))#i', $old_siteurl, $omatches))) {
				$nfrom = strtolower($omatches[3]);
				$nto = strtolower($matches[3]);
				$blognum = $tmatches[1];
				if ($nfrom == $this->restored_blogs[1][0] && isset($this->restored_blogs[$blognum])) {
					$bdom = $this->restored_blogs[$blognum][0];
					$bpath = $this->restored_blogs[$blognum][1];
					$sblog = $omatches[2].$bdom.(('/' != $bpath) ? untrailingslashit($bpath) : '');
					$rblog = $omatches[2].str_replace($nfrom, $nto, $bdom).(('/' != $bpath) ? untrailingslashit($bpath) : '');
					if (!in_array($sblog, $from_array)) {
						$from_array[] = $sblog;
						$to_array[] = $rblog;
					}
				}
			}
		}

		// The search/replace parameters are allowed to be either strings or arrays
		$report = $this->_migrator_icit_srdb_replacer($from_array, $to_array, array($table => $stripped_table));

		if (!empty($try_site_blog_replace)) {
			if ($table == $this->base_prefix.'blogs') {
				$blogs = $wpdb->get_results('SELECT blog_id, domain, path, site_id FROM '.esc_sql($this->base_prefix).'blogs', ARRAY_N);
				if (is_array($blogs)) {
					$nblogs = array();
					foreach ($blogs as $blog) {
						$nblogs[$blog[0]] = array($blog[1], $blog[2], $blog[3]);
					}
					$this->restored_blogs = $nblogs;
				}
			} elseif ($table == $this->base_prefix.'site') {
				$sites = $wpdb->get_results('SELECT id, domain, path FROM '.esc_sql($this->base_prefix).'site', ARRAY_N);
				if (is_array($sites)) {
					$nsites = array();
					foreach ($sites as $site) {
						$nsites[$site[0]] = array($site[1], $site[2]);
					}
					$this->restored_sites = $nsites;
				}
			}
			if (!empty($this->restored_sites) && !empty($this->restored_blogs) && !empty($this->original_sites)) {
				# Adjust paths
				# Domain, path
				$any_site_changes = false;
				foreach ($this->original_sites as $oid => $osite) {
					if (empty($this->restored_sites[$oid])) continue;
					$rsite = $this->restored_sites[$oid];
					# Task: 1) Replace the site path with the previous site path 2) Replace all the blog path prefixes from the same blog
					if ($rsite[1] != $osite[1]) {
						$any_site_changes = true;
						$sitepath = $osite[1];
						$this->restored_sites[$oid][1] = $sitepath;
						foreach ($this->restored_blogs as $bid => $blog) {
							# From this site?
							if ($blog[2] != $oid) continue;
							# Replace the prefix according to the change in prefix for the site
							$this->restored_blogs[$bid] = array($blog[0], $sitepath.substr($blog[1], strlen($rsite[1])), $oid);
						}
					}
				}
				if ($any_site_changes) {
					$updraftplus->log_e('Adjusting multisite paths');
					foreach ($this->restored_sites as $oid => $osite) {
						$wpdb->query("UPDATE ".esc_sql($this->base_prefix)."site SET path='".esc_sql($osite[1])."' WHERE id=$oid");
					}
					foreach ($this->restored_blogs as $bid => $blog) {
						$wpdb->query("UPDATE ".esc_sql($this->base_prefix)."blogs SET path='".esc_sql($blog[1])."' WHERE blog_id=$bid");
					}
				}
			}
		}

		// Output any errors encountered during the db work.
		if ( !empty($report['errors'] ) && is_array( $report['errors'] ) ) {
			echo '<p><h3>'.__('Error:','updraftplus').'</h3> <ul style="list-style: disc inside;">';
			$processed_errors = array();
			foreach( $report['errors'] as $error ) {
				if (in_array($error, $processed_errors)) continue;
				$processed_errors[] = $error;
				$num = count(array_keys($report['errors'], $error));
				echo "<li>".htmlspecialchars($error);
				if ($num > 1) echo ' (x'.$num.')';
				echo "</li>";
			}
			echo '</ul></p>';
		}

		if ($report == false) {
			echo sprintf(__('Failed: the %s operation was not able to start.', 'updraftplus'), __('search and replace', 'updraftplus'));
		} elseif (!is_array($report)) {
			echo sprintf(__('Failed: we did not understand the result returned by the %s operation.', 'updraftplus'), __('search and replace', 'updraftplus'));
		} else {

			$this->tables_replaced[$table] = true;

			// Calc the time taken.
			foreach (array('tables', 'rows', 'change', 'updates') as $key) {
				$this->report[$key] += $report[$key];
			}
			$this->report['timetaken'] += $report['end'] - $report['start'];
		}

	}

	# Builds from supplied parameters and $this->(siteurl,home,content)
	private function build_searchreplace_array($old_siteurl, $old_home, $old_content = false) {
		if (false === $old_content) $old_content = $old_siteurl.'/wp-content';
		$from_array = array();
		$to_array = array();
		if (!empty($old_siteurl) && $old_siteurl == $old_home) {
			$from_array[] = $old_siteurl;
			$to_array[] = $this->siteurl;
		} elseif (!empty($old_home) && strpos($old_siteurl, $old_home) === 0) {
			# strpos: haystack, needle - i.e. old_home is a (proper, since they were not ==) substring of old_siteurl
			$from_array[] = $old_siteurl;
			$to_array[] = $this->siteurl;
			$from_array[] = $old_home;
			$to_array[] = $this->home;
			# If the source home URL is also a proper substring of the destination site URL, then this should be skipped
			if ($old_home != $this->siteurl && strpos($this->siteurl, $old_home) === 0) {
				# Not pretty, but the only solution that can cope with content in posts that contains references to both site and home URLs in this case. This extra search URL un-does the adding of an unnecessary duplicate portion to site URLs in the case that is detected here.
				$from_array[] = $this->home.substr($this->home, strlen($old_home));
				$to_array[] = $this->home;
			}
		} elseif (!empty($old_siteurl) && strpos($old_home, $old_siteurl) === 0) {
			# old_siteurl is a substring of old_home (weird!)
			$from_array[] = $old_home;
			$to_array[] = $this->home;
			$from_array[] = $old_siteurl;
			$to_array[] = $this->siteurl;
		} else {
			# neither contains the other
			if (!empty($old_siteurl)) { $from_array[] = $old_siteurl; $to_array[] = $this->siteurl; }
			if (!empty($old_home)) { $from_array[] = $old_home; $to_array[] = $this->home; }
		}
		# We now have a minimal array based on the site_url and home settings
		# The case we need to detect is: (site_url is a prefix of content_url and new_site_url is a prefix of new_content_url and the remains are the same.
		# We do [0] of the existing array, to handle the weird case where old_siteurl is a substring of old_home (i.e. we get the shortest possible match)
		# We will want to do the content URLs first, since they are likely to be longest
		if (!empty($from_array) && 0 === strpos($old_content, $from_array[0]) && 0 === strpos($this->content, $to_array[0]) && substr($old_content, strlen($from_array[0])) === substr($this->content, strlen($to_array[0]))) {
			# OK - nothing to do - is already covered
		} else {
			# Search/replace needed
			array_unshift($from_array, $old_content);
			array_unshift($to_array, $this->content);
		}
		return array($from_array, $to_array);
	}

	public function updraftplus_restored_db($info, $import_table_prefix) {

		global $wpdb, $updraftplus;

		$updraftplus->log('Begin search and replace (updraftplus_restored_db)');
		echo "<h3>".__('Database: search and replace site URL', 'updraftplus')."</h3>";

		if (!isset($_POST['updraft_restorer_replacesiteurl']) || $_POST['updraft_restorer_replacesiteurl'] != 1) {
			echo '<p>';
			$updraftplus->log_e('This option was not selected.');
			echo '</p>';
			return;
		}

		$replace_this_siteurl = isset($this->old_siteurl) ? $this->old_siteurl : '';

		// Don't call site_url() - the result may/will have been cached
// 		if (isset($this->new_blogid)) switch_to_blog($this->new_blogid);
// 		$db_siteurl = $wpdb->get_row("SELECT option_value FROM $wpdb->options WHERE option_name='siteurl'")->option_value;
// 		$db_home = $wpdb->get_row("SELECT option_value FROM $wpdb->options WHERE option_name='home'")->option_value;
// 		if (isset($this->new_blogid)) restore_current_blog();

		$db_siteurl = $wpdb->get_row("SELECT option_value FROM ".esc_sql($this->base_prefix)."options WHERE option_name='siteurl'")->option_value;
		$db_home = $wpdb->get_row("SELECT option_value FROM ".esc_sql($this->base_prefix)."options WHERE option_name='home'")->option_value;

		if (!$replace_this_siteurl) {
			$replace_this_siteurl = $db_siteurl;
		}

		$replace_this_home = isset($this->old_home) ? $this->old_home : '';
		if (!$replace_this_home) {
			$replace_this_home = $db_home;
		}

		$replace_this_content = isset($this->old_content) ? $this->old_content : '';
		if (!$replace_this_content) {
			$replace_this_content = $replace_this_siteurl.'/wp-content';
		}

		// Sanity checks
		if (empty($replace_this_siteurl)) {
			echo '<p>'.sprintf(__('Error: unexpected empty parameter (%s, %s)', 'updraftplus'), 'backup_siteurl', $this->siteurl).'</p>';
			return;
		}
		if (empty($replace_this_home)) {
			echo '<p>'.sprintf(__('Error: unexpected empty parameter (%s, %s)', 'updraftplus'), 'backup_home', $this->home).'</p>';
			return;
		}
		if (empty($replace_this_content)) {
			echo '<p>'.sprintf(__('Error: unexpected empty parameter (%s, %s)', 'updraftplus'), 'backup_content_url', $this->content).'</p>';
			return;
		}

		if (empty($this->siteurl)) {
			echo '<p>'.sprintf(__('Error: unexpected empty parameter (%s, %s)', 'updraftplus'), 'new_siteurl', $replace_this_siteurl).'</p>';
			return;
		}
		if (empty($this->home)) {
			echo '<p>'.sprintf(__('Error: unexpected empty parameter (%s, %s)', 'updraftplus'), 'new_home', $replace_this_home).'</p>';
			return;
		}
		if (empty($this->content)) {
			echo '<p>'.sprintf(__('Error: unexpected empty parameter (%s, %s)', 'updraftplus'), 'new_contenturl', $replace_this_content).'</p>';
			return;
		}

		if ($replace_this_siteurl == $this->siteurl && $replace_this_home == $this->home && $replace_this_content == $this->content) {
			$this->is_migration = false;
			echo '<p>'.sprintf(__('Nothing to do: the site URL is already: %s','updraftplus'), $this->siteurl).'</p>';
			return;
		}

		$this->is_migration = true;

		do_action('updraftplus_restored_db_is_migration');

		# Detect situation where the database's siteurl in the header differs from that actual row data in the options table. This can occur if the options table was being over-ridden by a constant. In that case, the search/replace will have failed to set the option table's siteurl; and the result will be that that siteurl is hence wrong, leading to site breakage. The solution is to re-set it.
		# $info['expected_oldsiteurl'] is from the db.gz file header
		if (isset($info['expected_oldsiteurl']) && $info['expected_oldsiteurl'] != $db_siteurl && $db_siteurl != $this->siteurl) {
			$updraftplus->log_e(sprintf(__('Warning: the database\'s site URL (%s) is different to what we expected (%s)', 'updraftplus'), $db_siteurl, $info['expected_oldsiteurl']));
			# Here, we change only the site URL entry; we don't run a full search/replace based on it. In theory, if someone developed using two different URLs, then this might be needed.
			if (!empty($this->base_prefix) && !empty($this->siteurl)) $wpdb->query("UPDATE ".esc_sql($this->base_prefix)."options SET option_value='".esc_sql($this->siteurl)."' WHERE option_name='siteurl'");
		}
		if (isset($info['expected_oldhome']) && $info['expected_oldhome'] != $db_home && $db_home != $this->home) {
			$updraftplus->log_e(sprintf(__('Warning: the database\'s home URL (%s) is different to what we expected (%s)', 'updraftplus'), $db_home, $info['expected_oldhome']));
			if (!empty($this->base_prefix) && !empty($this->home)) $wpdb->query("UPDATE ".esc_sql($this->base_prefix)."options SET option_value='".esc_sql($this->home)."' WHERE option_name='home'");
		}

		@set_time_limit(1800);

		echo '<p>';

		list($from_array, $to_array) = $this->build_searchreplace_array($replace_this_siteurl, $replace_this_home, $replace_this_content);

		foreach ($from_array as $ind => $from_url) {
			$updraftplus->log_e('Database search and replace: replace %s in backup dump with %s', $from_url, $to_array[$ind]);
		}

		echo '</p>';

		return $this->updraftplus_restored_db_dosearchreplace($from_array, $to_array, $import_table_prefix);
	}

	private function updraftplus_restored_db_dosearchreplace($from_array, $to_array, $import_table_prefix, $examine_siteurls = true) {

		echo '<p>';

		global $updraftplus, $wpdb;

		// Now, get an array of tables and then send it off to _migrator_icit_srdb_replacer()
		// Code modified from searchreplacedb2.php version 2.1.0 from http://www.davidcoveney.com

		// Do we have any tables and if so build the all tables array
		$tables = array();

		// We use $wpdb for non-performance-sensitive operations (e.g. one-time calls)
		$tables_mysql = $wpdb->get_results('SHOW TABLES', ARRAY_N);

		$is_multisite = is_multisite();
		if ($examine_siteurls && $is_multisite) {
			$blogs = $wpdb->get_results('SELECT blog_id, domain, path FROM '.esc_sql($import_table_prefix).'blogs', ARRAY_N);
			$nblogs = array();
			foreach ($blogs as $blog) $nblogs[$blog[0]] = array($blog[1], $blog[2]);
		}

		if (!$tables_mysql) {
			echo __('Error:','updraftplus').' '.__('Could not get list of tables','updraftplus');
			echo '</p>';
			$updraftplus->log('Could not get list of tables');
			$this->_migrator_print_error('SHOW TABLES');
			return false;
		} else {
			// Run through the array - each element a numerically-indexed array
			foreach ($tables_mysql as $table) {

				// Type equality is necessary, as we don't want to match false
				// "Warning: strpos(): Empty delimiter" means that the second parameter is a zero-length string
				if (strpos($table[0], $import_table_prefix) === 0) {
					$tablename = $table[0];

					$stripped_table = substr($tablename, strlen($import_table_prefix));
					# Remove multisite site number prefix, if relevant
					if (is_multisite() && preg_match('/^(\d+)_(.*)$/', $stripped_table, $matches)) $stripped_table = $matches[2];

					if (!empty($this->which_tables) && is_array($this->which_tables)) {
						if (!in_array($tablename, $this->which_tables)) {
							echo '<strong>'.sprintf(__('Search and replacing table:', 'updraftplus')).'</strong> '.htmlspecialchars($tablename).': '.__('skipped (not in list)', 'updraftplus').'<br>';
							continue;
						}
					}

					$still_needs_doing = empty($this->tables_replaced[$tablename]);

					# Looking for site tables on multisite
					if ($examine_siteurls && $is_multisite && !empty($this->restored_blogs) && preg_match('/^(\d+)_(.*)$/', substr($tablename, strlen($import_table_prefix)), $tmatches) && is_numeric($tmatches[1]) && !empty($this->restored_blogs[$tmatches[1]]) && !empty($nblogs[$tmatches[1]]) && (preg_match('#^((https?://)([^/]+))#i', $this->home, $matches) || preg_match('#^((https?://)([^/]+))#i', $this->siteurl, $matches))) {
						# If the database file was not created by UD, then it may be out of order. Specifically, the 'blogs' table might have come *after* the tables for the individual sites. As a result, the tables for those sites may not have been fully searched + replaced... so we need to check that.
						# What are we expecting the site_url to be?
						$blognum = $tmatches[1];
						$site_url_current = $wpdb->get_var("SELECT option_value FROM ".esc_sql($import_table_prefix.$blognum)."_options WHERE option_name='siteurl'");
						if (is_string($site_url_current)) {
							$bpathroot = $this->restored_blogs[1][1];
							$bpath = $this->restored_blogs[$blognum][1];
							if (substr($bpath, 0, strlen($bpathroot)) == $bpathroot) $bpath = substr($bpath, strlen($bpathroot)-1);
							$site_url_target = $matches[2].$nblogs[$blognum][0].(('/' != $bpath) ? untrailingslashit($bpath) : '');
							if ($site_url_target != $site_url_current) {
								$still_needs_doing = true;
								$from_array[] = $site_url_current;
								$to_array[] = $site_url_target;
							}
						}
					}

					if ($still_needs_doing) {
						$tables[$tablename] = $stripped_table;
					} else {
						echo '<strong>'.sprintf(__('Search and replacing table:', 'updraftplus')).'</strong> '.htmlspecialchars($tablename).': '.__('already done', 'updraftplus').'<br>';
						$updraftplus->log('Search and replacing table: '.$tablename.': already done');
					}
				}
			}
		}

		$final_report = $this->report;

		if (!empty($tables)) {

			$report = $this->_migrator_icit_srdb_replacer($from_array, $to_array, $tables);

			// Output any errors encountered during the db work.
			if ( ! empty( $report['errors'] ) && is_array( $report['errors'] ) ) {
				echo '<h3>'.__('Error:','updraftplus').'</h3> <ul style="list-style: disc inside;">';

				$processed_errors = array();
				foreach( $report['errors'] as $error ) {
					if (in_array($error, $processed_errors)) continue;
					$processed_errors[] = $error;
					$num = count(array_keys($report['errors'], $error));
					echo "<li>".htmlspecialchars($error);
					if ($num > 1) echo ' (x'.$num.')';
					echo "</li>";
				}

				echo '</ul>';
			}

			if ($report == false) {
				echo sprintf(__('Failed: the %s operation was not able to start.', 'updraftplus'),'search and replace');
			} elseif (!is_array($report)) {
				echo sprintf(__('Failed: we did not understand the result returned by the %s operation.', 'updraftplus'),'search and replace');
			}

			// Calc the time taken.
			foreach (array('tables', 'rows', 'change', 'updates') as $key) {
				$final_report[$key] += $report[$key];
			}
			$final_report['timetaken'] += $report['end'] - $report['start'];
			foreach ($report['errors'] as $error) {
				$final_report['errors'][] = $error;
			}

		}

		echo '</p><p>';

		echo '<strong>'.__('Tables examined:', 'updraftplus').'</strong> '.$final_report['tables'].'<br>';
		echo '<strong>'.__('Rows examined:', 'updraftplus').'</strong> '.$final_report['rows'].'<br>';
		echo '<strong>'.__('Changes made:', 'updraftplus').'</strong> '.$final_report['change'].'<br>';
		echo '<strong>'.__('SQL update commands run:', 'updraftplus').'</strong> '.$final_report['updates'].'<br>';
		echo '<strong>'.__('Errors:', 'updraftplus').'</strong> '. count($final_report['errors']).'<br>';
		echo '<strong>'.__('Time taken (seconds):', 'updraftplus').'</strong> '.round($final_report['timetaken'], 3).'<br>';

		echo '</p>';

	}

// Returns either an array of results, or false - we abstract away what the wpdb class does compared to plain mysql_query
// 	private function query($sql_line, $sql_type = 5) {
// 		global $wpdb, $updraftplus;
// 		if ($this->use_wpdb) {
// 			$res = $wpdb->get_results($sql_line, ARRAY_A);
// 			if ($wpdb->last_error) return false;
// 			return $res;
// 		} else {
// 			$res = mysql_query($sql_line, $this->mysql_dbh);
// 			if (is_bool($res)) return $res;
// 			$nres = array();
// 			while ($row = mysql_fetch_array($res)) {
// 				$nres[] = $row;
// 			}
// 			return $nres;
// 		}
// 	}

	private function _migrator_print_error($sql_line) {
		global $wpdb;
		if ($this->use_wpdb) {
			$last_error = $wpdb->last_error;
		} else {
			$last_error = ($this->use_mysqli) ? mysqli_error($this->mysql_dbh) : mysql_error($this->mysql_dbh);
		}
		echo __('Error:', 'updraftplus')." ".htmlspecialchars($last_error)." - ".__('the database query being run was:','updraftplus').' '.htmlspecialchars($sql_line).'<br>';
		return $last_error;
	}

	private function fetch_sql_result($table, $on_row, $page_size, $where = '') {

		$sql_line = sprintf('SELECT * FROM %s%s LIMIT %d, %d', $table, $where, $on_row, $page_size);

		global $updraftplus;
		$updraftplus->check_db_connection($this->wpdb_obj, true);

		if ($this->use_wpdb) {
			global $wpdb;
			$data = $wpdb->get_results($sql_line, ARRAY_A);
			if (!$wpdb->last_error) return array($data, $page_size);
		} else {
			if ($this->use_mysqli) {
				$data = mysqli_query($this->mysql_dbh, $sql_line);
			} else {
				$data = mysql_query($sql_line, $this->mysql_dbh);
			}
			if (false !== $data) return array($data, $page_size);
		}
		
		if (5000 <= $page_size) return $this->fetch_sql_result($table, $on_row, 2000, $where);
		if (2000 <= $page_size) return $this->fetch_sql_result($table, $on_row, 500, $where);

		# At this point, $page_size should be 500; and that failed
		return array(false, $page_size);

	}

	// The engine
	private function _migrator_icit_srdb_replacer($search, $replace, $tables) {

		if (!is_array($tables)) return false;

		global $wpdb, $updraftplus;

		$report = array(
			'tables' => 0,
			'rows' => 0,
			'change' => 0,
			'updates' => 0,
			'start' => microtime(true),
			'end' => microtime(true),
			'errors' => array(),
		);

		$page_size = (empty($this->page_size) || !is_numeric($this->page_size)) ? 5000 : $this->page_size;

		foreach ($tables as $table => $stripped_table) {

			$report['tables']++;

			if ($search === $replace) {
				$updraftplus->log("No search/replace required: would-be search and replacement are identical");
				continue;
			}

			$this->columns = array( );

			echo '<strong>'.__('Search and replacing table:', 'updraftplus').'</strong> '.htmlspecialchars($table);

			$updraftplus->check_db_connection($this->wpdb_obj, true);

			// Get a list of columns in this table
			$fields = $wpdb->get_results('DESCRIBE '.$updraftplus->backquote($table), ARRAY_A);

			$indexkey_field = "";

			$prikey_field = false;
			foreach ($fields as $column) {
				$primary_key = ($column['Key'] == 'PRI') ? true : false;
				if ($primary_key) $prikey_field = $column['Field'];
				if ('posts' == $stripped_table && 'guid' == $column['Field']) {
					$updraftplus->log("Skipping search/replace on GUID column in posts table");
					continue;
				}
				$this->columns[$column['Field']] = $primary_key;
			}

			// Count the number of rows we have in the table if large we'll split into blocks, This is a mod from Simon Wheatley

			# InnoDB does not do count(*) quickly. You can use an index for more speed - see: http://www.cloudspace.com/blog/2009/08/06/fast-mysql-innodb-count-really-fast/

			$where = '';
			# Opportunity to use internal knowledge on tables which may be huge
			if ('postmeta' == $stripped_table && ((is_array($search) && strpos($search[0], 'http') === 0) || strpos($search, 'http') === 0)) {
				$where = " WHERE meta_value LIKE '%http%'";
			}

			$count_rows_sql = 'SELECT COUNT(*) FROM '.$table;
			if ($prikey_field) $count_rows_sql .= " USE INDEX (PRIMARY)";
			$count_rows_sql .= $where;

			$row_countr = $wpdb->get_results($count_rows_sql, ARRAY_N);

			// If that failed, try this
			if (false !== $prikey_field && $wpdb->last_error) {
				$row_countr = $wpdb->get_results("SELECT COUNT(*) FROM $table USE INDEX ($prikey_field)".$where, ARRAY_N) ;
				if ($wpdb->last_error) $row_countr = $wpdb->get_results("SELECT COUNT(*) FROM $table", ARRAY_N) ;
			}

			$row_count = $row_countr[0][0];
			echo ': '.sprintf(__('rows: %d', 'updraftplus'),$row_count).'<br>';
			$updraftplus->log('Search and replacing table: '.$table.": rows: ".$row_count);
			if (0 == $row_count) continue;

			for ($on_row = 0; $on_row <= $row_count; $on_row = $on_row+$page_size) {

				$this->current_row = 0;

				if ($on_row>0) $updraftplus->log_e("Searching and replacing reached row: %d", $on_row);

				// Grab the contents of the table
				list($data, $page_size) = $this->fetch_sql_result($table, $on_row, $page_size, $where);
				# $sql_line is calculated here only for the purpose of logging errors
				# $where might contain a %, so don't place it inside the main parameter

				$sql_line = sprintf('SELECT * FROM %s LIMIT %d, %d', $table.$where, $on_row, $on_row+$page_size);

				# Our strategy here is to minimise memory usage if possible; to process one row at a time if we can, rather than reading everything into memory
				if ($this->use_wpdb) {

					if ($wpdb->last_error) {
						$report['errors'][] = $this->_migrator_print_error($sql_line);
					} else {
						foreach ($data as $row) {
							$rowrep = $this->process_row($table, $row, $search, $replace);
							$report['rows']++;
							$report['updates'] += $rowrep['updates'];
							$report['change'] += $rowrep['change'];
							foreach ($rowrep['errors'] as $err) $report['errors'][] = $err;
						}
					}
				} else {
					if (false === $data) {
						$report['errors'][] = $this->_migrator_print_error($sql_line);
					} elseif ($data !== true && $data !== null) {
						if ($this->use_mysqli) {
							while ($row = mysqli_fetch_array($data)) {
								$rowrep = $this->process_row($table, $row, $search, $replace);
								$report['rows']++;
								$report['updates'] += $rowrep['updates'];
								$report['change'] += $rowrep['change'];
								foreach ($rowrep['errors'] as $err) $report['errors'][] = $err;
							}
							@mysqli_free_result($data);
						} else {
							while ($row = mysql_fetch_array($data)) {
								$rowrep = $this->process_row($table, $row, $search, $replace);
								$report['rows']++;
								$report['updates'] += $rowrep['updates'];
								$report['change'] += $rowrep['change'];
								foreach ($rowrep['errors'] as $err) $report['errors'][] = $err;
							}
							@mysql_free_result($data);
						}
					}
				}

			}

		}

		$report['end'] = microtime(true);

		return $report;
	}

	private function process_row($table, $row, $search, $replace) {

		global $updraftplus, $wpdb, $updraftplus_restorer;

		$report = array('change' => 0, 'errors' => array(), 'updates' => 0);

		$this->current_row++;
		
		$update_sql = array( );
		$where_sql = array( );
		$upd = false;

		foreach ($this->columns as $column => $primary_key) {

			$edited_data = $data_to_fix = $row[ $column ];

			// Run a search replace on the data that'll respect the serialisation.
			$edited_data = $this->_migrator_recursive_unserialize_replace($search, $replace, $data_to_fix);

			// Something was changed
			if ( $edited_data != $data_to_fix ) {
				$report['change']++;
				$ed = $edited_data;
				$wpdb->escape_by_ref($ed);
				$update_sql[] = $updraftplus->backquote($column) . ' = "' . $ed . '"';
				$upd = true;
			}

			if ($primary_key) {
				$df = $data_to_fix;
				$wpdb->escape_by_ref($df);
				$where_sql[] = $updraftplus->backquote($column) . ' = "' . $df . '"';
			}
		}

		if ( $upd && ! empty( $where_sql ) ) {
			$sql = 'UPDATE '.$updraftplus->backquote($table).' SET '.implode(', ', $update_sql).' WHERE '.implode(' AND ', array_filter($where_sql));
			
			$result = $updraftplus_restorer->sql_exec($sql, 5);
			if ( false === $result || is_wp_error($result) ) {
				$last_error = $this->_migrator_print_error($sql);
				$report['errors'][] = $last_error;
			} else { 
				$report['updates']++;
			}

		} elseif ( $upd ) {
			$report['errors'][] = sprintf( '"%s" has no primary key, manual change needed on row %s.', $table, $this->current_row );
			echo __('Error:','updraftplus').' '.sprintf( __('"%s" has no primary key, manual change needed on row %s.', 'updraftplus'),$table, $this->current_row );
		}

		return $report;

	}

	/**
	* Take a serialised array and unserialise it replacing elements as needed and
	* unserialising any subordinate arrays and performing the replace on those too.
	*
	* @param string $from       String we're looking to replace.
	* @param string $to         What we want it to be replaced with
	* @param array  $data       Used to pass any subordinate arrays back to in.
	* @param bool   $serialised Does the array passed via $data need serialising.
	*
	* @return array	The original array with all elements replaced as needed.
	*/
	// N.B. $from and $to can be arrays - they get passed only to str_replace(), which can take an array
	private function _migrator_recursive_unserialize_replace($from = '', $to = '', $data = '', $serialised = false) {

		// some unserialised data cannot be re-serialised eg. SimpleXMLElements
		try {

			// O:8:"DateTime":0:{} : see https://bugs.php.net/bug.php?id=62852
			if ( is_string( $data ) && false === strpos($data, 'O:8:"DateTime":0:{}') && ( $unserialized = @unserialize( $data ) ) !== false ) {
				$data = $this->_migrator_recursive_unserialize_replace( $from, $to, $unserialized, true );
			}

			elseif ( is_array( $data ) ) {
				$_tmp = array( );
				foreach ( $data as $key => $value ) {
					$_tmp[ $key ] = $this->_migrator_recursive_unserialize_replace( $from, $to, $value, false );
				}

				$data = $_tmp;
				unset( $_tmp );
			}

			elseif ( is_object( $data ) ) {
				// $data_class = get_class( $data );
				$_tmp = $data; // new $data_class( );
				$props = get_object_vars( $data );
				foreach ( $props as $key => $value ) {
					$_tmp->$key = $this->_migrator_recursive_unserialize_replace( $from, $to, $value, false );
				}

				$data = $_tmp;
				unset( $_tmp );
			}
			elseif ( is_string($data) && (null !== ($_tmp = json_decode($data, true)) )) {

				if (is_array($_tmp)) {
					foreach ( $_tmp as $key => $value ) {
						$_tmp[ $key ] = $this->_migrator_recursive_unserialize_replace( $from, $to, $value, false );
					}

					$data = json_encode($_tmp);
					unset( $_tmp );
				}

			}

			else {
				if ( is_string( $data ) ) {
					$data = str_replace( $from, $to, $data );
# Below is the wrong approach. In fact, in the problematic case, the resolution is an extra search/replace to undo unnecessary ones
// 					if (is_string($from)) {
// 						$data = str_replace( $from, $to, $data );
// 					} else {
// 						# Array. We only want a maximum of one replacement to take place. This is only an issue in non-default setups, but in those situations, carrying out all the search/replaces can be wrong. This is also why the most specific URL should be done first.
// 						foreach ($from as $i => $f) {
// 							$ndata = str_replace($f, $to[$i], $data);
// 							if ($ndata != $data) {
// 								$data = $ndata;
// 								break;
// 							}
// 						}
// 					}
				}
			}

			if ( $serialised )
				return serialize( $data );

		} catch( Exception $error ) {
		}

		return $data;
	}

}

$updraftplus_addons_migrator_remotesend = new UpdraftPlus_Addons_Migrator_RemoteSend();

class UpdraftPlus_Addons_Migrator_RemoteSend {

	private $receivers = array();
	private $php_events = array();

	public function __construct() {
		add_action('updraft_migrate_after_widget', array($this, 'updraft_migrate_after_widget'));
		add_action('admin_footer', array($this, 'admin_footer'));
		add_action('updraft_migrate_newdestination', array($this, 'updraft_migrate_newdestination'));
		add_action('updraft_remote_ping_test', array($this, 'updraft_remote_ping_test'));
		add_action('updraft_migrate_key_create', array($this, 'updraft_migrate_key_create'));
		add_action('updraft_migrate_key_delete', array($this, 'updraft_migrate_key_delete'));
		add_filter('updraftplus_initial_jobdata', array($this, 'updraftplus_initial_jobdata'), 10, 3);
		add_filter('updraft_printjob_beforewarnings', array($this, 'updraft_printjob_beforewarnings'), 10, 2);
		add_action('plugins_loaded', array($this, 'plugins_loaded'));
	}

	public function plugins_loaded() {

		global $updraftplus;

		// Create a receiver for each key
		if (!class_exists('UpdraftPlus_Options')) {
			error_log("UpdraftPlus_Options class not found: is UpdraftPlus properly installed?");
			return;
		}
		$our_keys = UpdraftPlus_Options::get_updraft_option('updraft_migrator_localkeys');
		if (is_array($our_keys) && !empty($our_keys)) {
			foreach ($our_keys as $name_hash => $key) {
				if (!is_array($key)) return;
				$ud_rpc = $updraftplus->get_udrpc($name_hash.'.migrator.updraftplus.com');
				$this->receivers[$name_hash] = $ud_rpc;
				$ud_rpc->set_key_local($key['key']);
				// Create listener (which causes WP actions to be fired when messages are received)
				$ud_rpc->activate_replay_protection();
				$ud_rpc->create_listener();
			}
			add_filter('udrpc_command_send_chunk', array($this, 'udrpc_command_send_chunk'), 10, 3);
			add_filter('udrpc_command_get_file_status', array($this, 'udrpc_command_get_file_status'), 10, 3);
		}
	}

	private function initialise_listener_error_handling($hash) {
		global $updraftplus;
		$updraftplus->error_reporting_stop_when_logged = true;
		set_error_handler(array($updraftplus, 'php_error'), E_ALL & ~E_STRICT);
		$this->php_events = array();
		add_action('updraftplus_logline', array($this, 'updraftplus_logline'), 10, 4);
		if (!UpdraftPlus_Options::get_updraft_option('updraft_debug_mode')) return;
		$updraftplus->nonce = $hash;
		$updraftplus->logfile_open($hash);
	}

	private function return_rpc_message($msg) {
		if (is_array($msg) && isset($msg['response']) && 'error' == $msg['response']) {
			global $updraftplus;
			$updraftplus->log('Unexpected response code in remote communications: '.serialize($msg));
		}
		if (!empty($this->php_events)) {
			if (!isset($msg['data'])) $msg['data'] = null;
			$msg['data'] = array('php_events' => array(), 'previous_data' => $msg['data']);
			foreach ($this->php_events as $logline) {
				$msg['data']['php_events'][] = $logline;
			}
		}
		restore_error_handler();

		return $msg;
	}

	public function updraftplus_logline($line, $nonce, $level, $uniq_id) {
		if ('notice' === $level && 'php_event' === $uniq_id) {
			$this->php_events[] = $line;
		}
	}

	public function udrpc_command_send_chunk($response, $data, $name_indicator) {

		if (!preg_match('/^([a-f0-9]+)\.migrator.updraftplus.com$/', $name_indicator, $matches)) return $response;
		$name_hash = $matches[1];

		$this->initialise_listener_error_handling($name_hash);

		global $updraftplus;

		// send_message('send_chunk', array('file' => $file, 'data' => $chunk, 'start' => $upload_start))

		if (!is_array($data)) return $this->return_rpc_message(array('response' => 'error', 'data' => 'invalid_input_expected_array'));

		if (!isset($data['file'])) return $this->return_rpc_message(array('response' => 'error', 'data' => 'invalid_input_no_file'));

		if (!isset($data['data'])) return $this->return_rpc_message(array('response' => 'error', 'data' => 'invalid_input_no_data'));

		if (!isset($data['start'])) return $this->return_rpc_message(array('response' => 'error', 'data' => 'invalid_input_no_start'));

		// Make sure the parameters are valid
		if (!is_numeric($data['start']) || $data['start'] != absint($data['start'])) return $this->return_rpc_message(array('response' => 'error', 'data' => 'invalid_start'));

		// Sanity-check the file name
		$file = $data['file'];
		if (!preg_match('/(-db\.gz|-db\.gz\.crypt|-db|\.(sql|sql\.gz|sql\.bz2|zip|tar|tar\.bz2|tar\.gz|txt))/i', $file)) return array('response' => 'error', 'data' => 'illegal_file_name1');
		if ($file != basename($file)) return $this->return_rpc_message(array('response' => 'error', 'data' => 'invalid_input_illegal_character'));

		$start = $data['start'];

		$is_last_chunk = empty($data['last_chunk']) ? 0 : 1;
		if (!$is_last_chunk) {
		} else {
			$orig_file = $file;
			if (!empty($data['label'])) $label = $data['label'];
		}
		$file .= '.tmp';

		// Intentionally over-write the variable, in case memory is short and in case PHP's garbage collector is this clever
		$data = base64_decode($data['data']);

		$updraft_dir = $updraftplus->backups_dir_location();
		$fullpath = $updraft_dir.'/'.$file;

		$existing_size = file_exists($fullpath) ? filesize($fullpath) : 0;

		if ($start > $existing_size) {
			return $this->return_rpc_message(array('response' => 'error', 'data' => 'invalid_start_too_big'));
		}

		if (false == ($fhandle = fopen($fullpath, 'ab'))) {
			return $this->return_rpc_message(array('response' => 'error', 'data' => 'file_open_failure'));
		}

		// fseek() returns 0 for success, or -1 for failure
		if ($start != $existing_size && -1 == fseek($fhandle, $start))  return $this->return_rpc_message(array('response' => 'error', 'data' => 'fseek_failure'));

		if (false === fwrite($fhandle, $data)) return $this->return_rpc_message(array('response' => 'error', 'data' => 'fwrite_failure'));

		@fclose($fhandle);

		$our_keys = UpdraftPlus_Options::get_updraft_option('updraft_migrator_localkeys');
		if (is_array($our_keys) && isset($our_keys[$name_hash]) && !empty($our_keys[$name_hash]['name'])) $updraftplus->log("Received data chunk on key ".$our_keys[$name_hash]['name']. " ($file, ".$start.", is_last=$is_last_chunk)");

		// Remove: this is faking it
// 		return array(
// 			'response' => 'file_status',
// 			'data' => array(
// 				'size' => $start + strlen($data),
// 				'status' => 0
// 			)
// 		);

		if ($is_last_chunk) {
			if (!rename($fullpath, $updraft_dir.'/'.$orig_file)) return $this->return_rpc_message(array('response' => 'error', 'data' => 'rename_failure'));
			$only_add_this_file = array('file' => $orig_file);
			if (isset($label)) $only_add_this_file['label'] = $label;
			$updraftplus->rebuild_backup_history(false, $only_add_this_file);
		}

		return $this->return_rpc_message(array(
			'response' => 'file_status',
			'data' => $this->get_file_status($file)
		));
	}

	private function get_file_status($file) {

		global $updraftplus;
		$fullpath = $updraftplus->backups_dir_location().'/'.basename($file);

		if (file_exists($fullpath)) {
			$size = filesize($fullpath);
			$status = 1;
		} elseif (file_exists($fullpath.'.tmp')) {
			$size = filesize($fullpath.'.tmp');
			$status = 0;
		} else {
			$size = 0;
			$status = 0;
		}

		return array(
			'size' => $size,
			'status' => $status,
		);
	}

	public function udrpc_command_get_file_status($response, $data, $name_indicator) {
		if (!preg_match('/^([a-f0-9]+)\.migrator.updraftplus.com$/', $name_indicator, $matches)) return $response;
		$name_hash = $matches[1];

		$this->initialise_listener_error_handling($name_hash);

		if (!is_string($data)) return $this->return_rpc_message(array('response' => 'error', 'data' => 'invalid_input_expected_string'));

		if ($data != basename($data)) return $this->return_rpc_message(array('response' => 'error', 'data' => 'invalid_input_illegal_character'));

		return $this->return_rpc_message(array(
			'response' => 'file_status',
			'data' => $this->get_file_status($data)
		));
	}

	public function updraftplus_initial_jobdata($initial_jobdata, $options, $split_every) {

		if (is_array($options) && !empty($options['extradata']) && preg_match('#services=remotesend/(\d+)#', $options['extradata'], $matches)) {

			// Load the option now - don't wait until send time
			$site_id = $matches[1];
			$remotesites = UpdraftPlus_Options::get_updraft_option('updraft_remotesites');
			if (!is_array($remotesites)) $remotesites = array();

			if (empty($remotesites[$site_id]) || empty($remotesites[$site_id]['url']) || empty($remotesites[$site_id]['key']) || empty($remotesites[$site_id]['name_indicator'])) {
				throw new Exception("Remote site id ($site_id) not found - send aborted");
			}

			array_push($initial_jobdata, 'remotesend_info', $remotesites[$site_id]);

			// Reduce to 100Mb if it was above. Since the user isn't expected to directly manipulate these zip files, the potentially higher number of zip files doesn't matter.
			if ($split_every > 100) array_push($initial_jobdata, 'split_every', 100);

		}

		return $initial_jobdata;
	}

	public function updraft_printjob_beforewarnings($ret, $jobdata) {
		if (!empty($jobdata['remotesend_info']) && !empty($jobdata['remotesend_info']['url'])) {
			$ret .= '<p style="padding:0px; margin:2px 0;">'.__('Backup data will be sent to:', 'updraftplus').' '.htmlspecialchars($jobdata['remotesend_info']['url']).'</p>';
		}
		return $ret;
	}

	public function updraft_remote_ping_test() {

		global $updraftplus;

		if (!isset($_POST['id']) || !is_numeric($_POST['id']) || empty($_POST['url'])) die;

		$remotesites = UpdraftPlus_Options::get_updraft_option('updraft_remotesites');
		if (!is_array($remotesites)) $remotesites = array();

		if (empty($remotesites[$_POST['id']]) || $_POST['url'] != $remotesites[$_POST['id']]['url'] || empty($remotesites[$_POST['id']]['key']) || empty($remotesites[$_POST['id']]['name_indicator'])) {
			echo json_encode(array('e' => 1, 'r' => __('Error:', 'updraftplus').' '.__('site not found', 'updraftplus')));
			die();
		}

		try {
			$ud_rpc = $updraftplus->get_udrpc($remotesites[$_POST['id']]['name_indicator']);
			$ud_rpc->set_key_local($remotesites[$_POST['id']]['key']);
			$ud_rpc->set_destination_url($_POST['url']);
			$ud_rpc->activate_replay_protection();
			$response = $ud_rpc->send_message('ping');

			if (is_wp_error($response)) {

				$err_msg = __('Error:', 'updraftplus').' '.$response->get_error_message();
				$err_data = $response->get_error_data();
				$err_code = $response->get_error_code();

			} elseif (!is_array($response) || empty($response['response']) || 'pong' != $response['response']) {

				$err_msg =  __('Error:', 'updraftplus').' '.sprintf(__('You should check that the remote site is online, not firewalled, does not have security modules that may be blocking access, has UpdraftPlus version %s or later active and that the keys have been entered correctly.', 'updraftplus'), '2.10.3');
				$err_data = $response;
				$err_code = 'no_pong';

			}

			if (isset($err_msg)) {

				$res = array('e' =>1, 'r' => $err_msg);

				if ($this->url_looks_internal($_POST['url'])) {
					$res['moreinfo'] = '<p>'.sprintf(__('The site URL you are sending to (%s) looks like a local development website. If you are sending from an external network, it is likely that a firewall will be blocking this.', 'updraftplus'), htmlspecialchars($_POST['url'])).'</p>';
				}

				// We got several support requests from people who didn't seem to be aware of other methods
				$msg_try_other_method = '<p>'.__('If sending directly from site to site does not work for you, then there are three other methods - please try one of these instead.', 'updraftplus').'<a href="https://updraftplus.com/faqs/how-do-i-migrate-to-a-new-site-location/#importing">'.__('For longer help, including screenshots, follow this link.', 'updraftplus').'</a></p>';

				$res['moreinfo'] = isset($res['moreinfo']) ? $res['moreinfo'].$msg_try_other_method : $msg_try_other_method;

				if (isset($err_data)) $res['data'] = $err_data;
				if (isset($err_code)) $res['code'] = $err_code;
				echo json_encode($res);
				die;
			}

			$ret = '<p>'.__('Testing connection...', 'updraftplus').' '.__('OK', 'updraftplus').'</p>';

			global $updraftplus, $updraftplus_admin;
// 			$ret .= $updraftplus_admin->backup_now_widgetry(false, 'remotesend_');

			$ret .= '<input type="checkbox" checked="checked" id="remotesend_backupnow_db"> <label for="remotesend_backupnow_db">'.__("Database", 'updraftplus').'</label><br>';
			$ret .= $updraftplus_admin->files_selector_widgetry('remotesend_');

			$service = $updraftplus->just_one(UpdraftPlus_Options::get_updraft_option('updraft_service'));
			if (is_string($service)) $service = array($service);

			if (is_array($service) && !empty($service) && array('none') !== $service) {
				$first_one = true;
				foreach ($service as $s) {
					if (!$s) continue;
					if (isset($updraftplus->backup_methods[$s])) {
						if ($first_one) {
							$first_one = false;
							$ret .= '<p>';
							$ret .= '<input type="checkbox" id="remotesend_backupnow_cloud"> <label for="remotesend_backupnow_cloud">'.__("Also send this backup to the active remote storage locations", 'updraftplus');
							$ret .= ' (';
						} else {
							$ret .= ', ';
						}
						$ret .= $updraftplus->backup_methods[$s];
					}
				}
				if (!$first_one) $ret .= ')';
				$ret .= '</label></p>';
			}

			$ret .= apply_filters('updraft_backupnow_modal_afteroptions', '', 'remotesend_');

			echo json_encode(array('success' => 1, 'r' => $ret));
		} catch (Exception $e) {
			echo json_encode(array('e' => 1, 'r' => __('Error:', 'updraftplus').' '.$e->getMessage().' (line: '.$e->getLine().', file: '.$e->getFile().')'));
		}
		die;
	}

	// This is used only for an advisory warning - does not have to be able to always detect
	private function url_looks_internal($url) {
		$url_host = strtolower(parse_url($url, PHP_URL_HOST));
		if ('localhost' == $url_host || strpos($url_host, '127.') === 0 || strpos($url_host, '10.') === 0  || '::1' == $url_host || strpos($url_host, 'localhost') !== false || substr($url_host, -4, 4) == '.dev') return true;
		return false;
	}

	public function updraft_migrate_key_delete() {
		if (empty($_POST['keyid'])) die;
		$our_keys = UpdraftPlus_Options::get_updraft_option('updraft_migrator_localkeys');
		if (!is_array($our_keys)) $our_keys = array();
		unset($our_keys[$_POST['keyid']]);
		UpdraftPlus_Options::update_updraft_option('updraft_migrator_localkeys', $our_keys);
		echo json_encode(array('ourkeys' => $this->list_our_keys($our_keys)));
		die;
	}

	public function updraft_migrate_key_create() {

		if (empty($_POST['name'])) die;

		$name_hash = md5($_POST['name']); // 32 characters
		$indicator_name = $name_hash.'.migrator.updraftplus.com';

		$our_keys = UpdraftPlus_Options::get_updraft_option('updraft_migrator_localkeys');
		if (!is_array($our_keys)) $our_keys = array();
		
		if (isset($our_keys[$name_hash])) {
			echo json_encode(array('e' => 1, 'r' => __('Error:', 'updraftplus').' '.__('A key with this name already exists; you must use a unique name.', 'updraftplus')));
			die;
		}

		global $updraftplus;
		$ud_rpc = $updraftplus->get_udrpc($indicator_name);

		if (is_object($ud_rpc) && $ud_rpc->generate_new_keypair()) {
			$local_bundle = $ud_rpc->get_portable_bundle('base64_with_count');

			$our_keys[$name_hash] = array('name' => $_POST['name'], 'key' => $ud_rpc->get_key_local());
			UpdraftPlus_Options::update_updraft_option('updraft_migrator_localkeys', $our_keys);

			echo json_encode(array(
				'bundle' => $local_bundle,
				'r' => __('Key created successfully.', 'updraftplus').' '.__('You must copy and paste this key now - it cannot be shown again.', 'updraftplus'),
				'selector' => $this->get_remotesites_selector(array()),
				'ourkeys' => $this->list_our_keys($our_keys),
			));
			die;
		}
		echo json_encode(array('e' => 1));
		die;
	}

	public function updraft_migrate_newdestination() {

		global $updraftplus;
		$ret = array();

		if (empty($_POST['key'])) {
			$ret['e'] = sprintf(__("Failure: No %s was given.",'updraftplus'), __('key','updraftplus'));
		} else {
			$ud_rpc = $updraftplus->get_udrpc();

			// A bundle has these keys: key, name_indicator, url
			$decode_bundle = $ud_rpc->decode_portable_bundle($_POST['key'], 'base64_with_count');

			if (!is_array($decode_bundle) || !empty($decode_bundle['code'])) {
				$ret['e'] = __('Error:','updraftplus');
				if (!empty($decode_bundle['code']) && $decode_bundle['code'] == 'invalid_wrong_length') {
					$ret['e'] .= ' '.__('The entered key was the wrong length - please try again.', 'updraftplus');
				} elseif (!empty($decode_bundle['code']) && $decode_bundle['code'] == 'invalid_corrupt') {
					$ret['e'] .= ' '.__('The entered key was corrupt - please try again.', 'updraftplus').' ('.$decode_bundle['data'].')';
				} elseif (empty($decode_bundle['key']) || empty($decode_bundle['url'])) {
					$ret['e'] .= ' '.__('The entered key was corrupt - please try again.', 'updraftplus');
					$ret['data'] = $decode_bundle;
				}
			} elseif (empty($decode_bundle['key']) || empty($decode_bundle['url'])) {
					$ret['e'] = __('Error:','updraftplus').' '.__('The entered key was corrupt - please try again.', 'updraftplus');
					$ret['data'] = $decode_bundle;
			} else {
				
				if ($decode_bundle['url'] == trailingslashit(network_site_url())) {
					$ret['e'] = __('Error:','updraftplus').' '.__('The entered key does not belong to a remote site (it belongs to this one).', 'updraftplus');
				} else {

					// Store the information
					$remotesites = UpdraftPlus_Options::get_updraft_option('updraft_remotesites');
					if (!is_array($remotesites)) $remotesites = array();
					foreach ($remotesites as $k => $rsite) {
						if (!is_array($rsite)) continue;
						if ($rsite['url'] == $decode_bundle['url']) unset($remotesites[$k]);
					}
					$remotesites[] = $decode_bundle;
					UpdraftPlus_Options::update_updraft_option('updraft_remotesites', $remotesites);

					$ret['selector'] = $this->get_remotesites_selector($remotesites);

					// Return the new HTML widget to the front end
					$ret['r'] = __('The key was successfully added.', 'updraftplus').' '.__('It is for sending backups to the following site: ', 'updraftplus').htmlspecialchars($decode_bundle['url']);

				}
			}

		}

		echo json_encode($ret);
		die;
	}

	private function get_remotesites_selector($remotesites = false) {

		if (false === $remotesites) {
			$remotesites = UpdraftPlus_Options::get_updraft_option('updraft_remotesites');
			if (!is_array($remotesites)) $remotesites = array();
		}

		if (empty($remotesites)) {
			return '<span id="updraft_migrate_receivingsites_nonemsg"><em>'.__('No receiving sites have yet been added.', 'updraftplus').'</em></span>';
		} else {
			$ret = '<div style="height:34px;"><div style="width:100px; float:left; padding-top:5px;"><strong>'.__('Send to site:', 'updraftplus').'</strong></div><select id="updraft_remotesites_selector" style="width:455px;float:left;">';
			foreach ($remotesites as $k => $rsite) {
				if (!is_array($rsite) || empty($rsite['url'])) continue;
				$ret .= '<option value="'.esc_attr($k).'">'.htmlspecialchars($rsite['url']).'</option>';
			}
			$ret .= '</select>';
			$ret .= '<div style="float:left;"><button class="button-primary" style="height:30px; font-size:16px; margin-left: 3px; width:85px;" id="updraft_migrate_send_button">'.__('Send', 'updraftplus').'</button></div></div>';
		}

		return $ret;
	}

	public function admin_footer() {
		global $updraftplus, $pagenow;

		// Next, the actions that only come on the UpdraftPlus page
		if ($pagenow != UpdraftPlus_Options::admin_page() || empty($_REQUEST['page']) || 'updraftplus' != $_REQUEST['page']) return;

		?>
		<script>
			jQuery(document).ready(function($) {

				$('#updraft_migrate_modal_main').on('click', '.updraft_migrate_local_key_delete', function() {
					var $keylink = $(this);
					var keyid = $keylink.data('keyid');
					var data = {
						action: 'updraft_ajax',
						subaction: 'doaction',
						subsubaction: 'updraft_migrate_key_delete',
						nonce: '<?php echo wp_create_nonce('updraftplus-credentialtest-nonce'); ?>',
						keyid: keyid,
					}
					$keylink.html(updraftlion.deleting);
					jQuery.post(ajaxurl, data, function(response) {
						try {
							resp = jQuery.parseJSON(response);
							if (resp.hasOwnProperty('ourkeys')) {
								$('#updraft_migrate_our_keys_container').html(resp.ourkeys);
							} else {
								alert(updraftlion.unexpectedresponse+' '+response);
								console.log(resp);
								console.log(response);
							}
						} catch(err) {
							alert(updraftlion.unexpectedresponse+' '+response);
							console.log(err);
							console.log(response);
							return;
						}
					});
				});

				$('#updraft_migrate_modal_main').on('click', '#updraft_migrate_send_button', function() {
					$('#updraft_migrate_modal_main').hide();
					var site_id = $('#updraft_remotesites_selector').val();
					var site_url = $('#updraft_remotesites_selector option:selected').text();
					$('#updraft_migrate_modal_alt').html('<p><strong>'+updraftlion.sendtosite+'</strong> '+site_url+'</p><p id="updraft_migrate_testinginprogress">'+updraftlion.testingconnection+'</p>').slideDown('fast');

					var data = {
						action: 'updraft_ajax',
						subaction: 'doaction',
						subsubaction: 'updraft_remote_ping_test',
						nonce: '<?php echo wp_create_nonce('updraftplus-credentialtest-nonce'); ?>',
						id: site_id,
						url: site_url
					}
					$.post(ajaxurl, data, function(response) {
						try {
							resp = $.parseJSON(response);
							if (resp.hasOwnProperty('e')) {
								console.log(resp);
								$('#updraft_migrate_modal_alt').append('<p style="color:red;">'+updraftlion.unexpectedresponse+' '+resp.r+' ('+resp.code+'). '+updraftlion.checkrpcsetup+'</p>');
								if (resp.hasOwnProperty('moreinfo')) {
									$('#updraft_migrate_modal_alt').append(resp.moreinfo);
								}
// 								alert(updraftlion.unexpectedresponse+' '+resp.r+' ('+resp.code+'). '+updraftlion.checkrpcsetup);
							} else if (resp.hasOwnProperty('success')) {
								if (resp.hasOwnProperty('r')) {
									$('#updraft_migrate_testinginprogress').replaceWith('<p style="">'+resp.r+'</p>');
								}
								var entities = [ '<?php
									$entities = $updraftplus->get_backupable_file_entities();
									echo implode("', '", array_keys($entities));
								?>' ];
								var $mmodal = $("#updraft-migrate-modal");
								var buttons = {};
								buttons[updraftlion.send] = function () {

									var onlythisfileentity = '';
									var arrayLength = entities.length;
									for (var i = 0; i < arrayLength; i++) {
										if ($('#remotesend_updraft_include_'+entities[i]).is(':checked')) {
											if (onlythisfileentity != '') { onlythisfileentity += ','; }
											onlythisfileentity += entities[i];
										}
										//Do something
									}

									var backupnow_nodb = $('#remotesend_backupnow_db').is(':checked') ? 0 : 1;

									var backupnow_nofiles = 0;
									if ('' == onlythisfileentity) { backupnow_nofiles = 1; }

									var backupnow_nocloud = 1;
									var extradata = 'services=remotesend/'+site_id;

									if ($('#remotesend_backupnow_cloud').is(':checked')) {
// 										extradata = extradata+',cloudalso';
										backupnow_nocloud = 0;
									}

									if (backupnow_nodb && backupnow_nofiles) {
										alert(updraftlion.excludedeverything);
										return;
									}
									
									$(this).dialog("close");

									setTimeout(function() {
										$('#updraft_lastlogmessagerow').fadeOut('slow', function() {
											$(this).fadeIn('slow');
										});
									}, 1700);
									
									updraft_backupnow_go(backupnow_nodb, backupnow_nofiles, backupnow_nocloud, onlythisfileentity, extradata, $('#remotesend_backupnow_label').val());
								}
								buttons[updraftlion.close] = function() { $(this).dialog("close"); };
								$mmodal.dialog("option", "buttons", buttons);

							}
						} catch(err) {
							$('#updraft_migrate_modal_alt').append('<p style="color:red;">'+updraftlion.unexpectedresponse+' '+response+'</p>');
							console.log(err);
							console.log(response);
							return;
						}
					});

				});
				$('#updraft_migrate_receivingsites_createkey').click(function() {
					// Remember to tell them that this key will never be shown again
					
					var currentdate = new Date(); 
					var default_key_name = updraftlion.key+" - " + currentdate.getFullYear() + "/"
								+ (currentdate.getMonth()+1)  + "/" 
								+ currentdate.getDate();
// 								+ " @ " + currentdate.getHours() + ":" + currentdate.getMinutes() + ":" + currentdate.getSeconds();

					var key_name = prompt(updraftlion.pleasenamekey, default_key_name);

					if ('' == key_name || false == key_name || null == key_name) { alert(updraftlion.nokeynamegiven); return false; }

					$('#updraft_migrate_new_key_container').show();
					$('#updraft_migrate_new_key').html(updraftlion.creating);

					var data = {
						action: 'updraft_ajax',
						subaction: 'doaction',
						subsubaction: 'updraft_migrate_key_create',
						nonce: '<?php echo wp_create_nonce('updraftplus-credentialtest-nonce'); ?>',
						name: key_name
					}
					jQuery.post(ajaxurl, data, function(response) {
						try {
							resp = jQuery.parseJSON(response);
							if (resp.hasOwnProperty('bundle')) {
								$('#updraft_migrate_new_key').html(resp.bundle);
								if (resp.hasOwnProperty('selector')) {
									$('#updraft_migrate_receivingsites').html(resp.selector);
								}
								if (resp.hasOwnProperty('r')) {
									alert(resp.r);
								}
								if (resp.hasOwnProperty('ourkeys')) {
									$('#updraft_migrate_our_keys_container').html(resp.ourkeys);
								}
							} else if (resp.hasOwnProperty('e')) {
								$('#updraft_migrate_new_key').html(resp.r);
								console.log(resp);
							} else {
								alert(updraftlion.servererrorcode);
								console.log(resp);
								console.log(response);
								$('#updraft_migrate_new_key_container').hide();
							}
						} catch(err) {
							console.log(err);
							console.log(response);
							alert(updraftlion.unexpectedresponse+' '+response);
							return;
						}
					});

					// Update (via AJAX) the list of existing keys
					// AJAX command to delete an existing key
				});

				$('#updraft_migrate_receiving_makenew').click(function() {
					var data = {
						action: 'updraft_ajax',
						subaction: 'doaction',
						subsubaction: 'updraft_migrate_newdestination',
						nonce: '<?php echo wp_create_nonce('updraftplus-credentialtest-nonce'); ?>',
						key: $('#updraft_migrate_receiving_new').val()
					}
					$('#updraft_migrate_receiving_makenew').html(updraftlion.addingsite);
					jQuery.post(ajaxurl, data, function(response) {
						$('#updraft_migrate_receiving_makenew').html(updraftlion.addsite);
						try {
							resp = jQuery.parseJSON(response);
							if (resp.hasOwnProperty('e')) {
								console.log(resp);
								alert(resp.e);
							} else if (resp.hasOwnProperty('r')) {
								if (resp.hasOwnProperty('selector')) {
									$('#updraft_migrate_receivingsites').html(resp.selector);
								}
								$('#updraft_migrate_receiving_new').val('');
								alert(resp.r);
							} else {
								alert(updraftlion.unexpectedresponse+' '+response);
								console.log(resp);
								console.log(response);
							}
						} catch(err) {
							console.log(err);
							alert(updraftlion.unexpectedresponse+' '+response);
							return;
						}
					});
				});
			});
		</script>
		<?php
	}

	public function updraft_migrate_after_widget() {

		echo '<div style="margin:8px 4px; padding: 0px 8px 8px; border: 1px dotted;">';

		echo '<p style="clear:left; margin-top: 4px; padding-top: 4px;"><strong>'.__('Or, send a backup to another site', 'updraftplus').'</strong><br>';
		echo __("To add a site as a destination for sending to, enter that site's key below.", 'updraftplus').' <a href="#" onclick="alert(\''.esc_js(__('Keys for this site are created in the section below the one you just pressed in.', 'updraftplus').' '.__("So, to get the key for the remote site, open the 'Migrate' window on that site, scroll down, and you can create one there.", 'updraftplus')).'\')">'.__("How do I get a site's key?", 'updraftplus').'</a></p>';

		echo '<div style="clear:both;">';
			echo '<input type="text" id="updraft_migrate_receiving_new" style="width:555px; height:30px;" placeholder="'.esc_attr(__('Paste key here', 'updraftplus')).'"> <button class="button-primary" style="height:30px; font-size:16px; width:85px;" id="updraft_migrate_receiving_makenew">'.__('Add site', 'updraftplus').'</button>';
		echo '</div>';

		echo '<div id="updraft_migrate_receivingsites" style="clear:both; margin-top:10px;">';
			echo $this->get_remotesites_selector();
		echo '</div>';

		echo '</div>';

		echo '<div style="margin:8px 4px; padding: 0px 8px 8px; border: 1px dotted;">';

		echo '<p style="margin-top: 4px; padding-top: 4px;"><strong>'.__('Or, receive a backup from a remote site', 'updraftplus').'</strong><br>';
		echo htmlspecialchars(__("To allow another site to send a backup to this site, create a key, and then press the 'Migrate' button on the sending site, and copy-and-paste the key there.", 'updraftplus')).' <a href="#" id="updraft_migrate_receivingsites_createkey">'.__("Create a key...", 'updraftplus').'</a></p>';

		?>
		<div id="updraft_migrate_new_key_container" style="display:none;">
			<?php _e('Your new key:', 'updraftplus'); ?><br>
			<textarea id="updraft_migrate_new_key" onclick="this.select();" style="width:625px; height:235px; word-wrap:break-word; border: 1px solid #aaa; border-radius: 3px; padding:4px;"></textarea>
		</div>

		<div id="updraft_migrate_our_keys_container">
			<?php echo $this->list_our_keys(); ?>
		</div>

		<?php
		echo '</div>';

	}

	private function list_our_keys($our_keys = false) {
		if (false === $our_keys) {
			$our_keys = UpdraftPlus_Options::get_updraft_option('updraft_migrator_localkeys');
		}

		if (empty($our_keys)) return '<em>'.__('No keys to allow remote sites to connect have yet been created.', 'updraftplus').'</em>';

		$ret = '';
		$first_one = true;

		foreach ($our_keys as $k => $key) {
			if (!is_array($key)) continue;
			if ($first_one) {
				$first_one = false;
				$ret .= '<p><strong>'.__('Existing keys', 'updraftplus').'</strong><br>';
			}
			$ret .= htmlspecialchars($key['name']);
			$ret .=  ' - <a href="#" class="updraft_migrate_local_key_delete" data-keyid="'.esc_attr($k).'">'.__('Delete', 'updraftplus').'</a>';
			$ret .=  '<br>';
		}

		if ($ret) $ret .= '</p>';

		return $ret;

	}
	
}
