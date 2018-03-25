<?php
/**\defgroup ajax Ajax implementation
 * \author FredTGZ
 * \brief Web Sheets module for Harmony PHP Library
 * \details This module implements ajax services wich means that you can simply
 * call an ajax functionality on your web pages.
 */

	global $loadmodule;
	
	if ($loadmodule !== false) {
		require_once(HARMONY_INCLUDE."/ajax/client.php");
		require_once(HARMONY_INCLUDE."/ajax/service.php");
	}
	else require("module.nfo");
?>