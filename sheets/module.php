<?php if (! defined('HARMONY_INCLUDE')) die("Harmony Library is not loaded !");
/**\defgroup sheets Web Sheets
 *
 * Web Sheets module for Harmony PHP Library
 *  
 * Author: FredTGZ
 *  
 * Description:
 * This module implements cache management of document.
 *  
 * Howto:  
 */

	global $loadmodule;
	
	if ($loadmodule !== false) {
		CHarmony::IncludeModule('usermgt');
		CHarmony::IncludeModule('javascript');
		require_once(HARMONY_INCLUDE."/sheets/tda_file.php");
		require_once(HARMONY_INCLUDE."/sheets/tdd_file.php");
		require_once(HARMONY_INCLUDE."/sheets/manager.php");
		require_once(HARMONY_INCLUDE."/sheets/tddeditor.php");
	}
	else require("module.nfo");
?>
