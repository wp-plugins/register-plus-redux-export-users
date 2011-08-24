<?php
/*
Plugin Name: Register Plus redux export users
Plugin URI: http://www.mijnpress.nl
Plugin Description: Export users to CSV files, supports all WordPress profile data also Register Plus Redux plug-in
Version: 1.6
Author: Ramon Fincken
Author URI: http://www.mijnpress.nl
Based on: Cimy User Manager 0.9.2 by Marco Cimmino, cimmino.marco@gmail.com
Based on: http://www.mijnpress.nl/blog/plugin-framework/
*/

/*
Following comment has been un-altered from the original plugin:

Cimy User Manager - Import and export users from/to CSV files
Copyright (c) 2007-2010 Marco Cimmino

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.


The full copy of the GNU General Public License is available here: http://www.gnu.org/licenses/gpl.txt

*/

// pre 2.6 compatibility or if not defined
if (!defined("WP_CONTENT_URL"))
	define("WP_CONTENT_URL", get_option("siteurl")."/wp_content");
	
if (!defined("WP_CONTENT_DIR"))
	define("WP_CONTENT_DIR", ABSPATH."/wp_content");

$cum_plugin_name = basename(__FILE__);
$cum_plugin_path = plugin_basename(dirname(__FILE__))."/";
$cum_plugin_dir = WP_CONTENT_DIR."/plugins/".$cum_plugin_path;

$plugin_registerplusredux_eu_domain = 'plugin_registerplusredux_eu';
$plugin_registerplusredux_eu_i18n_is_setup = false;
plugin_registerplusredux_eu_i18n_setup();

$userid_code = '% USERID %';
$useremail_code = '% EMAIL %';
$username_code = '% USERNAME %';
$firstname_code = '% FIRSTNAME %';
$lastname_code = '% LASTNAME %';
$nickname_code = '% NICKNAME %';
$website_code = '% WEBSITE %';
$aim_code = '% AIM %';
$yahoo_code = '% YAHOO %';
$jabber_code = '% JABBER %';
$password_code = '% PASSWORD %';
$role_code = "% ROLE %";
$desc_code = "% DESCRIPTION %";

add_action('init', 'plugin_registerplusredux_eu_download_database'); // TODO : a better way?

if(!class_exists('mijnpress_plugin_framework'))
{
	include('mijnpress_plugin_framework.php');
}


class plugin_register_plus_redux_export_users extends mijnpress_plugin_framework
{
	function __construct()
	{
		$this->showcredits = true;
		$this->showcredits_fordevelopers = true;
		$this->plugin_title = 'Register Plus Redux Export users';
		$this->plugin_class = 'plugin_register_plus_redux_export_users';
		$this->plugin_filename = 'register-plus-redux-export-users/registerplusredux_user_manager.php';
		$this->plugin_config_url = 'users.php?page='.$this->plugin_filename;
		// TODO: get, set, or update config (options, transients) here
	}

	function plugin_register_plus_redux_export_users()
	{
		$args= func_get_args();
		call_user_func_array
		(
		    array(&$this, '__construct'),
		    $args
		);
	}

	function addPluginSubMenu()
	{
		$plugin = new plugin_register_plus_redux_export_users();
		parent::addPluginSubMenu($plugin->plugin_title,array($plugin->plugin_class, 'admin_menu'),__FILE__,10,"users.php");
	}

	/**
	 * Additional links on the plugin page
	 */
	function addPluginContent($links, $file) {
		$plugin = new plugin_register_plus_redux_export_users();
		$links = parent::addPluginContent($plugin->plugin_filename,$links,$file,$plugin->plugin_config_url);
		return $links;
	}

	/**
	 * Shows the admin plugin page
	 */
	public function admin_menu()
	{
		$plugin = new plugin_register_plus_redux_export_users();
		$plugin->content_start();		
		
		plugin_registerplusredux_eu_import_export_page();

		$plugin->content_end();
	}
}

// Admin only
if(mijnpress_plugin_framework::is_admin())
{
	add_action('admin_menu',  array('plugin_register_plus_redux_export_users', 'addPluginSubMenu'));
	add_filter('plugin_row_meta',array('plugin_register_plus_redux_export_users', 'addPluginContent'), 10, 2);
}

function plugin_registerplusredux_eu_download_database() {
	if (isset($_POST["plugin_registerplusredux_eu_filename"]) && mijnpress_plugin_framework::is_admin()) {
		if(
		strpos(
		strtolower($_SERVER['HTTP_REFERER']), 
		strtolower(admin_url('users.php?page=register-plus-redux-export-users/registerplusredux_user_manager.php'))) !== false) {
			$plugin_registerplusredux_eu_filename = $_POST["plugin_registerplusredux_eu_filename"];

			header("Pragma: "); // Leave blank for issues with IE
			header("Expires: 0");
			header('Vary: User-Agent');
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Content-Type: text/csv");
			header("Content-Type: application/force-download");
			header("Content-Type: application/download");
			if(file_exists($plugin_registerplusredux_eu_filename))
			{
				header("Content-Disposition: attachment; filename=\"".basename($plugin_registerplusredux_eu_filename)."\";");
				header("Content-Transfer-Encoding: binary");
				header("Content-Length: ".filesize($plugin_registerplusredux_eu_filename));
				readfile($plugin_registerplusredux_eu_filename);
				@unlink($plugin_registerplusredux_eu_filename);
			}
			else
			{
				// TODO: show message
			}
			exit();
		}
		else
		{
			echo "Refer check fail. Please enable browser referrer headers.<br>";
			echo "Origin: ".strtolower($_SERVER['HTTP_REFERER']);
			echo "<br>";
			echo "Match fail: ".strtolower(admin_url('users.php?page=register-plus-redux-export-users/registerplusredux_user_manager.php'));
			die();
		}
	}
}

function plugin_registerplusredux_eu_i18n_setup() {
	global $plugin_registerplusredux_eu_domain, $plugin_registerplusredux_eu_i18n_is_setup, $cum_plugin_path;

	if ($plugin_registerplusredux_eu_i18n_is_setup)
		return;

	load_plugin_textdomain($plugin_registerplusredux_eu_domain, PLUGINDIR.'/'.$cum_plugin_path.'langs', $cum_plugin_path.'langs');
	
	$plugin_registerplusredux_eu_i18n_is_setup = true;
}

function plugin_registerplusredux_eu_import_export_page() {
	global $plugin_registerplusredux_eu_domain, $cimy_uef_name;
	
	if (!mijnpress_plugin_framework::is_admin())
		return;

	$results_import = array();
	$results_export = array();
	
	if (isset($_POST['plugin_registerplusredux_eu_import']))
		$results_import = plugin_registerplusredux_eu_import_data();
	
	if (isset($_POST['plugin_registerplusredux_eu_export']))
		$results_export = plugin_registerplusredux_eu_export_data();
	
	if (isset($_POST['db_add_users']))
		$db_add_users_sel = ' checked="checked"';
	else
		$db_add_users_sel = "";
	
	if (isset($_POST['db_extra_fields']))
		$db_extra_fields_sel = ' checked="checked"';
	else
		$db_extra_fields_sel = "";
	
	if (isset($_POST['db_date_format']))
		$db_date_format = attribute_escape($_POST['db_date_format']);
	else
		$db_date_format = "%d %B %Y @%H:%M";
	
	
	//if (!isset($cimy_uef_name)) {
	if(!class_exists("RegisterPlusReduxPlugin"))
	{
		$db_extra_fields_sel = ' disabled="disabled"';
		$db_extra_fields_warning = "<br /><strong>".__("You must activate RegisterPlusReduxPlugin to export data from that plug-in", $plugin_registerplusredux_eu_domain)."</strong>";
	}
	else
		$db_extra_fields_warning = "";
	
	if (isset($_POST["db_field_separator"]))
		$field_separator = attribute_escape(stripslashes($_POST["db_field_separator"]));
	else
		$field_separator = attribute_escape(",");

	if (isset($_POST["db_text_separator"]))
		$text_separator = attribute_escape(stripslashes($_POST["db_text_separator"]));
	else
		$text_separator = attribute_escape("\"");
	?>
	
	<div class="wrap" id="options">
	<!--  Ramon Fincken, removed complete import title and error msgs 
	 -->

	<script type="text/javascript" language="javascript">
		function changeEnc(form_id) {
			var browser = navigator.appName;
		
			if (browser == "Microsoft Internet Explorer")
				document.plugin_registerplusredux_eu_import.encoding = "multipart/form-data";
			else
				document.plugin_registerplusredux_eu_import.enctype = "multipart/form-data";
		}
	</script>
	
	<!--  Ramon Fincken, removed complete import form 
		<form name="plugin_registerplusredux_eu_import" id="plugin_registerplusredux_eu_import" method="post" enctype="multipart/form-data">
	 -->
	<br />
	
	<?php
		if (function_exists("screen_icon"))
			screen_icon("users");
	?>
	<h2><?php _e("Export Users", $plugin_registerplusredux_eu_domain); ?></h2><?php
	
	// print successes/errors if there are some
	if (count($results_export) > 0) {
	?>
		<br /><div class="updated">
	<?php
		if (isset($results_export["tmp_file"])) {
			echo "<h3>".__("FILE GENERATED", $plugin_registerplusredux_eu_domain)." (".count($results_export["tmp_file"]).")</h3>";
			echo $results_export["tmp_file"];
			echo "<form name=\"plugin_registerplusredux_eu_download\" id=\"plugin_registerplusredux_eu_download\" method=\"post\"><input type=\"hidden\" name=\"plugin_registerplusredux_eu_filename\" value=\"".$results_export["tmp_file"]."\" /><input type=\"submit\" value=\"".__("Download Export File")."\" /></form>";
			echo "<br />";
		}
		
		if (isset($results_export["error"])) {
			echo "<h3>".__("ERRORS", $plugin_registerplusredux_eu_domain)." (".count($results_export["error"]).")</h3>";
	
			foreach ($results_export["error"] as $result)
				echo $result."<br />";
		}
		
		if (isset($results_export["exported"])) {
			echo "<h3>".__("USERS SUCCESSFULLY EXPORTED", $plugin_registerplusredux_eu_domain)." (".count($results_export["exported"]).")</h3>";
	
			foreach ($results_export["exported"] as $result)
				echo $result."<br />";
		}
		?><br /></div><?php
	}
	?>
	
	<p>
	</p>
	
	<form name="plugin_registerplusredux_eu_export" id="plugin_registerplusredux_eu_export" method="post">
	
	<table class="form-table">
		<tr>
			<th scope="row" width="40%"><?php _e("Select field delimiter", $plugin_registerplusredux_eu_domain); ?></th>
			<td width="60%">
				<input type="text" name="db_field_separator" value="<?php echo $field_separator; ?>" />  <?php _e('If your CSV file is like: "field1","field2" then you need to use comma', $plugin_registerplusredux_eu_domain); ?>
			</td>
		</tr>
		<tr>
			<th scope="row"><?php _e("Select text delimiter", $plugin_registerplusredux_eu_domain); ?></th>
			<td>
				<input type="text" name="db_text_separator" value="<?php echo $text_separator; ?>" />  <?php _e('If your CSV file is like: "field1","field2" then you need to use double quote', $plugin_registerplusredux_eu_domain); ?>
			</td>
		</tr>
		<tr>
			<th scope="row"><?php _e("Add also Register Redux Extra Fields data", $plugin_registerplusredux_eu_domain); ?></th>
			<td>
				<input type="checkbox" name="db_extra_fields" value="1"<?php echo $db_extra_fields_sel; ?> />  <?php _e("Select this option to let the plug-in export also data present into RegisterPlusReduxPlugin", $plugin_registerplusredux_eu_domain); echo $db_extra_fields_warning; ?>
			</td>
		</tr>
		<tr>
			<th><?php _e("Select date format", $plugin_registerplusredux_eu_domain); ?></th>
			<td>
				<input type="text" name="db_date_format" value="<?php echo $db_date_format; ?>" />  <?php _e("Select the date/time format to represent registration dates (if any)", $plugin_registerplusredux_eu_domain); ?><br />
				<?php _e("More info about date/time format are on this", $plugin_registerplusredux_eu_domain); ?> <a href="http://www.php.net/manual/en/function.strftime.php"><?php _e("LINK", $plugin_registerplusredux_eu_domain); ?></a>
			</td>
		</tr>
	</table>
	
	<input type="hidden" name="plugin_registerplusredux_eu_export" value="1" />
	<p class="submit"><input class="button-primary" type="submit" name="Export" value="<?php _e('Export') ?>" /></p>
	</form>
	</div>
	<br />
	<?php
}

function plugin_registerplusredux_eu_import_data() {
	// Disabled
	return;
	
	global $wpdb, $wpdb_data_table, $wpdb_fields_table, $plugin_registerplusredux_eu_domain;
	global $userid_code, $useremail_code, $username_code, $firstname_code, $lastname_code, $nickname_code, $website_code, $aim_code, $yahoo_code, $jabber_code, $password_code, $role_code, $desc_code;
	
	$results = array();
	
	if (!current_user_can('edit_users'))
		return;

	// try to not timeout
	set_time_limit(0);

	// needed for silly Windows files
	ini_set('auto_detect_line_endings', true);

	$file_type = $_FILES["db_import"]['type'];
	$file_tmp_name = $_FILES["db_import"]['tmp_name'];
	$file_error = $_FILES["db_import"]['error'];

	if (!is_readable($file_tmp_name)) {
		$results["error"][] = __("Cannot open the file", $plugin_registerplusredux_eu_domain);
		return $results;
	}
	else if (($fh = fopen($file_tmp_name, 'r')) === false) {
		$results["error"][] = __("Cannot open the file", $plugin_registerplusredux_eu_domain);
		return $results;
	}

	$field_separator = stripslashes($_POST["db_field_separator"]);
	$text_separator = stripslashes($_POST["db_text_separator"]);

	$separator = $text_separator.$field_separator.$text_separator;

	// name of the fields in the file imported
	$fields = explode($separator, fgets($fh));

	// position of special things in $all_data array
	$specials = array();
	
	// position of extra_fields data in $all_data array
	$extra_fields = array();
	
	// ID of the fields in the DB
	$db_extra_fields = array();
	
	$missing_cimy_uef_error = false;
	
	$i = 0;
	$next_field = reset($fields);

	while ($next_field != false) {
		$field = $next_field;
		$next_field = next($fields);

		$field = strtoupper(trim($field, "\n\r"));

		if ($i == 0) {
			$field = substr($field, strlen($text_separator));
			$fields[$i] = $field;
		}

		if ($next_field == false) {
			$last_field = $i;
			$field = substr($field, 0, (-1)*strlen($text_separator));
			$fields[$i] = $field;
		}
		
		switch ($field) {
			case $userid_code:
				$specials["ID"] = $i;
				break;
				
			case $useremail_code:
				$specials["email"] = $i;
				break;
			
			case $username_code:
				$specials["username"] = $i;
				break;
				
			case $firstname_code:
				$specials["firstname"] = $i;
				break;
				
			case $lastname_code:
				$specials["lastname"] = $i;
				break;
				
			case $nickname_code:
				$specials["nickname"] = $i;
				break;
				
			case $website_code:
				$specials["website"] = $i;
				break;
				
			case $aim_code:
				$specials["aim"] = $i;
				break;
				
			case $yahoo_code:
				$specials["yahoo"] = $i;
				break;
				
			case $jabber_code:
				$specials["jabber"] = $i;
				break;
				
			case $password_code:
				$specials["password"] = $i;
				break;
				
			case $role_code:
				$specials["role"] = $i;
				break;
				
			case $desc_code:
				$specials["description"] = $i;
				break;
				
			default:
				$extra_fields[strtoupper($field)] = $i;
				break;
		}
		
		$i++;
	}

	// first line already read, so will be immediately increased to 2
	$line = 1;

	// looping through rows
	while (!feof($fh)) {
		$row = fgets($fh);
		$line++;

		$line_tr = " ".sprintf(__("(line %s)", $plugin_registerplusredux_eu_domain), $line);

		// remove definitly all new lines and carriage returns
		$row = trim($row, "\n\r");
		
		// remove also space, but not definitly from the row
		if (trim($row) == "")
			continue;

		$all_data = explode($separator, $row);

		$all_data[0] = substr($all_data[0], strlen($text_separator));
		$all_data[$last_field] = substr($all_data[$last_field], 0, (-1)*strlen($text_separator));

		$email = "";
		$username = "";
		$passw = "";
		$wp_user = false;
		$wp_new_user = array();
		$wp_userid = false;
		
		if ((isset($specials["ID"])) && (trim($all_data[$specials["ID"]]) != "")) {
			$wp_userid = intval(trim($all_data[$specials["ID"]]));
			$wp_user = new WP_User($wp_userid);
			
			if ($wp_user->ID != 0) {
				$username = $wp_user->user_login;
				$results["modified"][] = "'".attribute_escape($username)."'".$line_tr;
			}
		}
		
		if ((isset($specials["username"])) && ($username == "")) {
			$username = sanitize_user($all_data[$specials["username"]], true);

			if ($username != $all_data[$specials["username"]])
				$results["error"][] = sprintf(__("username '%s' has some invalid characters, used this username instead: '%s'", $plugin_registerplusredux_eu_domain), attribute_escape($all_data[$specials["username"]]), attribute_escape($username)).$line_tr;

			if (!is_object($wp_user)) {
				$wp_user = new WP_User($username);
				$wp_userid = intval($wp_user->ID);
		
				if ($wp_user->ID != 0)
					$results["modified"][] = "'".attribute_escape($username)."'".$line_tr;
			}
		}

		// check if user doesn't exist, if not insert!
		if (($wp_user->ID != $wp_userid) || ($wp_user->ID == 0)) {
			// just check what was the error and drop the row as we are not allowed to create new users by the admin
			if (!isset($_POST["db_add_users"])) {
				$new_user_error = "";
				
				if (isset($specials["ID"])) {
					if ($wp_userid == 0)
						$new_user_error = __("userid is missing", $plugin_registerplusredux_eu_domain);
					else
						$new_user_error = sprintf(__("userid '%s' is not present in the DB", $plugin_registerplusredux_eu_domain), attribute_escape($wp_userid));
				}
				
				if (isset($specials["username"])) {
					if ($new_user_error != "")
						$new_user_error .= " ".__("and", $plugin_registerplusredux_eu_domain)." ";
				
					if ($username == "")
						$new_user_error .= __("username is missing", $plugin_registerplusredux_eu_domain);
					else
						$new_user_error .= sprintf(__("the username '%s' is not present in the DB", $plugin_registerplusredux_eu_domain), attribute_escape($username));
				}
				
				if ($new_user_error != "")
					$results["error"][] = $new_user_error.$line_tr;
				
				// drop as user is not valid and we cannot create it
				continue;
			}
		
			// check for username: if missing cannot add a new user
			if ($username == "") {
				$results["error"][] = __("username missing cannot add an user without it", $plugin_registerplusredux_eu_domain).$line_tr;
				continue;
			}
			
			// check for username: if already existing cannot add a new user
			if (username_exists($username) != null) {
				$results["error"][] = sprintf(__("username '%s' already present in the DB (line %s)", $plugin_registerplusredux_eu_domain), attribute_escape($username)).$line_tr;
				continue;
			}
			
			// check for e-mail: if missing or already present cannot add a new user
			if (isset($specials["email"])) {
				$email = sanitize_email($all_data[$specials["email"]]);

				if ($email != $all_data[$specials["email"]])
					$results["error"][] = sprintf(__("e-mail '%s' has some invalid characters, used this address instead: '%s'", $plugin_registerplusredux_eu_domain), attribute_escape($all_data[$specials["email"]]), attribute_escape($email)).$line_tr;
			
				if (!email_exists($email)) {
					$wp_new_user["user_email"] = $email;
				}
				else {
					$results["error"][] = sprintf(__("e-mail '%s' already present in the DB, dropped this new user: '%s'", $plugin_registerplusredux_eu_domain), attribute_escape($email), attribute_escape($username)).$line_tr;
					continue;
				}
			}
			else {
				$results["error"][] = sprintf(__("e-mail field empty, dropped this new user: '%s'", $plugin_registerplusredux_eu_domain), attribute_escape($username)).$line_tr;
				continue;
			}
			
			// check for e-mail: if empty cannot add a new user
			if ($email == "") {
				$results["error"][] = sprintf(__("e-mail field empty, dropped this new user: '%s'", $plugin_registerplusredux_eu_domain), attribute_escape($username)).$line_tr;
				continue;
			}
			
			$passw = $all_data[$specials["password"]];
			
			if ($passw == "")
				$passw = $username;
			
			$wp_new_user["user_pass"] = $passw;
			$wp_new_user["user_login"] = $username;
			
			// dropping ID as WordPress/MySQL will assign a correct/new one
			//unset($wp_new_user["ID"]);
			//$results["error"][] = $wp_new_user["ID"];
			
			$wp_userid = wp_insert_user($wp_new_user);
			$wp_user = new WP_User($wp_userid);
			
			$results["added"][] = "'".attribute_escape($username)."'".$line_tr;
		}
		else {
			if (isset($specials["password"])) {
				$value = $all_data[$specials["password"]];

				//$wp_new_user["ID"] = $wp_userid;
				$wp_new_user["user_pass"] = $value;
			}
		}

		if (isset($specials["role"])) {
			switch ($all_data[$specials["role"]]) {
				case "subscriber":
				case "administrator":
				case "editor":
				case "author":
				case "contributor":
					$wp_user->set_role($all_data[$specials["role"]]);
			}
		}

		if (isset($specials["firstname"])) {
			$value = $all_data[$specials["firstname"]];

			//$wp_new_user["ID"] = $wp_userid;
			$wp_new_user["first_name"] = $value;
		}
		
		if (isset($specials["lastname"])) {
			$value = $all_data[$specials["lastname"]];

			//$wp_new_user["ID"] = $wp_userid;
			$wp_new_user["last_name"] = $value;
		}
		
		if (isset($specials["nickname"])) {
			$value = $all_data[$specials["nickname"]];

			//$wp_new_user["ID"] = $wp_userid;
			$wp_new_user["nickname"] = $value;
		}

		// $email == "" means is not a new user
		if ((isset($specials["email"])) && ($email == "")) {
			$value = sanitize_email($all_data[$specials["email"]]);

			if ($value != $all_data[$specials["email"]])
				$results["error"][] = sprintf(__("e-mail '%s' has some invalid characters, used this address instead: '%s'", $plugin_registerplusredux_eu_domain), attribute_escape($all_data[$specials["email"]]), attribute_escape($value)).$line_tr;

			
			if (!email_exists($value)) {
				//$wp_new_user["ID"] = $wp_userid;
				$wp_new_user["user_email"] = $value;
			}
			else {
				$results["error"][] = sprintf(__("e-mail '%s' already present in the DB, dropped this modification", $plugin_registerplusredux_eu_domain), attribute_escape($value)).$line_tr;
			}
		}
		
		if (isset($specials["website"])) {
			$value = $all_data[$specials["website"]];

			//$wp_new_user["ID"] = $wp_userid;
			$wp_new_user["user_url"] = $value;
		}
		
		if (isset($specials["aim"])) {
			$value = $all_data[$specials["aim"]];

			//$wp_new_user["ID"] = $wp_userid;
			$wp_new_user["aim"] = $value;
		}
		
		if (isset($specials["yahoo"])) {
			$value = $all_data[$specials["yahoo"]];

			//$wp_new_user["ID"] = $wp_userid;
			$wp_new_user["yim"] = $value;
		}
		
		if (isset($specials["jabber"])) {
			$value = $all_data[$specials["jabber"]];

			//$wp_new_user["ID"] = $wp_userid;
			$wp_new_user["jabber"] = $value;
		}
		
		if (isset($specials["description"])) {
			$value = $all_data[$specials["description"]];

			//$wp_new_user["ID"] = $wp_userid;
			$wp_new_user["description"] = $value;
		}

		if (!empty($wp_new_user)) {
			$wp_new_user["ID"] = $wp_userid;
			wp_update_user($wp_new_user);
		}

		// looping through array that contains extra_fields position in CSV rows
		
		// $fields is the first row
		// every $e_field is the column position in the first row
		// $fields[$e_field] are extra fields' names
		// $db_extra_fields stores all DB ids, key of the array is fields' names
		foreach ($extra_fields as $e_field) {
			if (!isset($db_extra_fields[$fields[$e_field]])) {
				$field_name = trim($fields[$e_field]);
				
				$sql = "SELECT ID,TYPE,LABEL FROM $wpdb_fields_table WHERE NAME=\"".$wpdb->escape(strtoupper($field_name))."\"";
				$result = $wpdb->get_results($sql, ARRAY_A);
				
				if (count($result) > 0)
					$db_extra_fields[$fields[$e_field]] = $result;
				else {
					$db_extra_fields[$fields[$e_field]] = -1;
					
					if (!isset($wpdb_fields_table)) {
						if (!$missing_cimy_uef_error)
							$results["error"][] = __("Cimy User Extra Fields is not active, impossible to import any extra fields data", $plugin_registerplusredux_eu_domain).$line_tr;
						
						$missing_cimy_uef_error = true;
					} else
						$results["error"][] = sprintf(__("'%s' field doesn't exist", $plugin_registerplusredux_eu_domain), attribute_escape($field_name)).$line_tr;
				}
			}
			
			if ($db_extra_fields[$fields[$e_field]] != -1) {
				foreach ($db_extra_fields[$fields[$e_field]] as $ef_details) {
					$sql = "SELECT ID FROM $wpdb_data_table WHERE FIELD_ID=".$ef_details["ID"]." AND USER_ID=$wp_userid";
				
					unset($present);
					$present = $wpdb->get_var($sql);
		
					$all_data[$e_field] = $wpdb->escape($all_data[$e_field]);
					$value_to_store = trim($all_data[$e_field]);
					
					if ($ef_details["TYPE"] == "radio") {
						if ($ef_details["LABEL"] == $value_to_store)
							$value_to_store = "selected";
						else
							$value_to_store = "";
					}
					
					if ($ef_details["TYPE"] == "checkbox") {
						if ((strtoupper($value_to_store) == "YES") || ($value_to_store == "1"))
							$value_to_store = "YES";
						else
							$value_to_store = "NO";
					}
				
					$value_to_store = "\"".$value_to_store."\"";
		
					if (isset($present))
						$sql = "UPDATE $wpdb_data_table SET VALUE=".$value_to_store." WHERE USER_ID=$wp_userid AND FIELD_ID=".$ef_details["ID"];
					else
						$sql = "INSERT INTO $wpdb_data_table (USER_ID, FIELD_ID, VALUE) VALUES ($wp_userid, ".$ef_details["ID"].", ".$value_to_store.")";
	
					$wpdb->query($sql);
				}
			}
		}
	}
	
	fclose($fh);
	
	return $results;
}

function plugin_registerplusredux_eu_export_data() {
	global $wpdb, $wpdb_data_table, $wpdb_fields_table, $plugin_registerplusredux_eu_domain;
	global $userid_code, $useremail_code, $username_code, $firstname_code, $lastname_code, $nickname_code, $website_code, $aim_code, $yahoo_code, $jabber_code, $password_code, $role_code, $desc_code, $cimy_uef_name;
	
	$results = array();
	
	if (!current_user_can('edit_users'))
		return;
	
	@set_time_limit(0);

	$field_separator = stripslashes($_POST["db_field_separator"]);
	$text_separator = stripslashes($_POST["db_text_separator"]);
	
	if (isset($_POST["db_extra_fields"]) && class_exists("RegisterPlusReduxPlugin")) {
		global $wpdb_data_table;
		$RegisterPlusReduxPlugin = new RegisterPlusReduxPlugin();
		//$extra_fields = get_cimyFields();
		$extra_fields = get_option("register_plus_redux_custom_fields");
						
		$all_radio_fields = array();
	}
	else {
		$extra_fields = false;
	}
	
	if (isset($_POST['db_date_format']))
		$db_date_format = $_POST['db_date_format'];
	else
		$db_date_format = "";
	
	$all_users = get_users_of_blog();
	$tmpdir = plugin_registerplusredux_eu_get_temp_dir();
	$tmpfile = $tmpdir."/plugin_registerplusredux_eu_exported_users-".date("Ymd-His").".csv";
	
	$fd_tmp_file = fopen($tmpfile, "w");
	
	$line = $text_separator.$userid_code.$text_separator.
		$field_separator.$text_separator.$username_code.$text_separator.
		$field_separator.$text_separator.$role_code.$text_separator.
		$field_separator.$text_separator.$firstname_code.$text_separator.
		$field_separator.$text_separator.$lastname_code.$text_separator.
		$field_separator.$text_separator.$nickname_code.$text_separator.
		$field_separator.$text_separator.$useremail_code.$text_separator.
		$field_separator.$text_separator.$website_code.$text_separator.
		$field_separator.$text_separator.$aim_code.$text_separator.
		$field_separator.$text_separator.$yahoo_code.$text_separator.
		$field_separator.$text_separator.$jabber_code.$text_separator.
		$field_separator.$text_separator.$desc_code.$text_separator;

						
	if ($extra_fields) {

				// Hack Ramon Fincken, show all fields
				if ( is_array($extra_fields) ) 
				{
					foreach ( $extra_fields as $k => $v ) 
					{
						$key = $RegisterPlusReduxPlugin->sanitizeText($v["custom_field_name"]);
						$all_radio_fields[] = $key;
						$line .= $field_separator.$text_separator.$key.$text_separator;
					}
				}				
	}
	
	$line .= "\r";
	
	fwrite($fd_tmp_file, $line);
	
	$results["exported"] = array();
	
	foreach ($all_users as $user) {
		$current_user = new WP_User($user->user_id);
		
		$results["exported"][] = $current_user->user_login;

		$line = $text_separator.$current_user->ID.$text_separator.
				$field_separator.$text_separator.$current_user->user_login.$text_separator.
				$field_separator.$text_separator.$current_user->roles[0].$text_separator.
				$field_separator.$text_separator.$current_user->first_name.$text_separator.
				$field_separator.$text_separator.$current_user->last_name.$text_separator.
				$field_separator.$text_separator.$current_user->nickname.$text_separator.
				$field_separator.$text_separator.$current_user->user_email.$text_separator.
				$field_separator.$text_separator.$current_user->user_url.$text_separator.
				$field_separator.$text_separator.$current_user->aim.$text_separator.
				$field_separator.$text_separator.$current_user->yim.$text_separator.
				$field_separator.$text_separator.$current_user->jabber.$text_separator.
				$field_separator.$text_separator.$current_user->user_description.$text_separator;
		
				
				if ($extra_fields) {
					if ( is_array($extra_fields) ) {
						foreach ( $extra_fields as $k => $v ) {
							$key = $RegisterPlusReduxPlugin->sanitizeText($v["custom_field_name"]);
							$value = get_user_meta($current_user->ID, $key, true);
							$ef_db = $value;
							$line .= $field_separator.$text_separator.$ef_db.$text_separator;
						}
					}
				}
			
		$line .= "\r";
		
		fwrite($fd_tmp_file, $line);
	}
	
	fclose($fd_tmp_file);

	$results["tmp_file"] = $tmpfile;

	return $results;
}

function plugin_registerplusredux_eu_get_temp_dir() {
	$temp_dir = "";
	if(defined('WP_TEMP_DIR') && is_writable(WP_TEMP_DIR))
	{
		return WP_TEMP_DIR;
	}
	
	if (function_exists("sys_get_temp_dir"))
		$temp_dir = sys_get_temp_dir();

	if (!empty($temp_dir) && (is_writable($temp_dir)))
		return $temp_dir;

	// Try to get from environment variable
	if ((!empty($_ENV['TMP'])) && (is_writable(realpath($_ENV['TMP']))))
	{
		return realpath($_ENV['TMP']);
	}
	else if ((!empty($_ENV['TMPDIR'])) && (is_writable(realpath($_ENV['TMPDIR']))))
	{
		return realpath($_ENV['TMPDIR']);
	}
	else if ((!empty($_ENV['TEMP'])) && (is_writable(realpath($_ENV['TEMP']))))
	{
		return realpath($_ENV['TEMP']);
	}

        // Detect by creating a temporary file
	else
	{
		// Try to use system's temporary directory
		// as random name shouldn't exist
		$temp_file = tempnam(md5(uniqid(rand(), true)), '');
		if ($temp_file)
		{
			$temp_dir = realpath(dirname($temp_file));
			unlink($temp_file);
			return $temp_dir;
		}
		else
		{
			return false;
		}
	}
}
?>