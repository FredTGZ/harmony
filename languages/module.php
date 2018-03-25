<?php if (! defined('HARMONY_INCLUDE')) die("Harmony Library is not loaded !");
/**\defgroup language Language support
 *
 * Language support module for Harmony PHP Library
 *  
 * Author: FredTGZ
 *  
 * Description:
 * This module implements language support.
 *  
 * Howto:  
 */

	global $loadmodule;
	
	if ($loadmodule !== false) {
		require_once("dictionary.inc");
	}
	else require("module.nfo");
?>
