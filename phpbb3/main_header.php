<?php if (! defined('HARMONY_INCLUDE')) die("Harmony Library is not loaded !");
	global $page;
	global $path;
	global $selected_menu;

	if (! defined('HARMONY_INCLUDE')) {
		define('HARMONY_INCLUDE', $_SERVER['DOCUMENT_ROOT']."/include/harmony");
		require_once(HARMONY_INCLUDE."/harmony_lib.inc");
	}

	CHarmony::LoadModule("database");


	if (! defined("IN_PHPBB")) define("IN_PHPBB", "");
	if (! defined("PHPBB_BASE_PATH")) define("PHPBB_BASE_PATH", "");

	if (defined("PHPBB_MODULE_MAIN")) {
		define("PHPBB_PATH",					PHPBB_BASE_PATH."..");
		define("PHPBB_MODULES_PATH",			PHPBB_BASE_PATH.".");
	}
	else {
		define("PHPBB_PATH",					PHPBB_BASE_PATH."../..");
		define("PHPBB_MODULES_PATH",			PHPBB_BASE_PATH."..");
	}
	

	define("PHPBB_MODULES_INCLUDE",			PHPBB_MODULES_PATH."/common_include");
	define("PHPBB_MODULES_TEMPLATES",		PHPBB_MODULES_PATH."/common_templates");
	define("PHPBB_MODULES_IMAGES",			PHPBB_MODULES_PATH."/common_images");
	define("PHPBB_THIS_MODULE_PATH",		PHPBB_BASE_PATH.".");
	define("PHPBB_THIS_MODULE_TEMPLATES",	PHPBB_THIS_MODULE_PATH.'/templates');
	define("PHPBB_THIS_MODULE_INCLUDE",		PHPBB_THIS_MODULE_PATH.'/include');
	define("PHPBB_THIS_MODULE_IMAGES",		PHPBB_THIS_MODULE_PATH.'/images');
	define("SPACE",							'&nbsp;');

	global $module_name;
	global $module_author;
	global $module_version;
	global $template;
	global $user;
	global $global_template;
	global $mod_database;
	global $install_status;
	global $lang;
	$install_status=0;
	
	global $phpbb_version;
	global $table_prefix;
	global $locale;
	global $timezone;

	require_once(PHPBB_MODULES_PATH."/config.inc");
	require_once(PHPBB_MODULES_INCLUDE."/config_file.inc");	// Loading CTemplate definition
	require_once(PHPBB_MODULES_INCLUDE."/lists.inc");		// Loading CTemplate definition
	
	require_once(PHPBB_PATH."/config.php");					// Loading phpBB database Configuration
	

	setlocale (LC_ALL, $locale);
	date_default_timezone_set($timezone);
	
	
	$mod_database = new CDatabase(CDatabase::$MYSQL, $dbhost, $dbname, $dbuser, $dbpasswd);
	$global_template = new CTemplate(PHPBB_MODULES_TEMPLATES);
	
	if (! $mod_database->Open()) {
		$global_template->AssignVar("DB_ERROR", $mod_database->GetLastError());
		$global_template->DisplayTemplate("dberror");
		die();
	}
	else {
		require_once(PHPBB_MODULES_INCLUDE."/user.inc");		// Loading CUser definition (phpBB user)
		require_once(PHPBB_MODULES_INCLUDE."/phpbb_config.inc");// Loading Config tools
	
		if (! defined("PHPBB_MODULE_MAIN")) {
			require_once(PHPBB_THIS_MODULE_PATH."/module.def");		// Loading phpBB database Configuration
			require_once(PHPBB_MODULES_INCLUDE."/install.inc");		// Used to check module installation
			require_once(PHPBB_MODULES_INCLUDE."/language.inc");	// Loading language particular strings
			require_once(PHPBB_THIS_MODULE_PATH."/config.inc");		// Loading module Configuration
		}

		$user = new CUser($mod_database, GetConfigValue('cookie_name'), $table_prefix);
		
		$template = new CTemplate(PHPBB_THIS_MODULE_TEMPLATES);
		LoadCommonResources($user->GetLanguage());
	}
?>
