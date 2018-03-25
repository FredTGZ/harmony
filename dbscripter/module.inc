<?php
/**\defgroup database Database access
 *
 * Database module for Harmony PHP Library
 *  
 * Author: FredTGZ
 *  
 * Description:
 * This module implements database access and querying.
 *  
 * Howto:  
 */
 	global $loadmodule;
	
	if ($loadmodule !== false) {
		require_once("dbscripter.inc");
	}
	else require("module.nfo");
?>
