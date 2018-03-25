<?php
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
		require_once(HARMONY_INCLUDE."/backup/directory.inc");
	}
	else require("module.nfo");
?>
