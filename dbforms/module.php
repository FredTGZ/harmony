<?php
/*******************************************************************************
 * Forms module for Harmony PHP Library
 *
 * Author: FredTGZ
 * Description:
 *
 *
 *
 ******************************************************************************/       
	global $loadmodule;
	
	if ($loadmodule !== false) {
		CHarmony::IncludeModule('forms');
		CHarmony::IncludeModule('database');
		require_once("dbselect.inc");
	}
	else require("module.nfo");
?>
