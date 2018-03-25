<?php
/**\defgroup cache Cache management
 *
 * Cache management module for Harmony PHP Library
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
		require_once("cache.php");
		require_once("cachefile.php");
	}
	else require("module.version");
?>
