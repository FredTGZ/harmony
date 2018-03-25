<?php if (! defined('HARMONY_INCLUDE')) die("Harmony Library is not loaded !");
	if (! defined("PHPBB_MODULE_MAIN")) {
		global $mod_database;
		global $user;
		print('<br><hr>');
		if ($user->IsAdmin()) {
			print('<center><br><a href="admin.php">Administration</a></center><br>');
		}
	
		global $module_name;
		global $module_author;
		global $module_version;
		global $module_author_email;
	
		print('<i>'.$module_name.' version '.$module_version).' by <a href="mailto:'.$module_author_email.'">'.$module_author.'</a></i>';
	}	

	$mod_database->Close();
?>
