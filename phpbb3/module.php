<?php if (! defined('HARMONY_INCLUDE')) die("Harmony Library is not loaded !");
/**\defgroup phpbb
 *
 * phpBB3 support module for Harmony PHP Library
 *  
 * Author: FredTGZ
 *  
 * Description:
 *  
 * Howto:  
 */

	global $loadmodule;
	
	if ($loadmodule !== false) {
		CHarmony::LoadModule("database");
		CHarmony::LoadModule("javascript");
		require_once("phpbbforum.php");
	}
	else require("module.version");
?>
