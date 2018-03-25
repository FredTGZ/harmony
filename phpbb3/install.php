<?php if (! defined('HARMONY_INCLUDE')) die("Harmony Library is not loaded !");
	global $module_tables;
	global $table_prefix;
	global $database;
			global $install_status;
	
	function print_install($message)
	{
		printf("<br>%s", $message);
	}
	
	function check_file($filename, &$message, &$critical_errors)
	{
			$message .= "<br>Checking file ".$filename."... ";

		if (! file_exists(PHPBB_THIS_MODULE_PATH.$filename)) {
			$message .= "<font color=red>not present</font>";
			$critical_errors++;
		}
		else $message .= "present";
	}
?>
