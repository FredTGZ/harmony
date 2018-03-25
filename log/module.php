<?php
/*******************************************************************************
 * Cache management module for Harmony PHP Library
 *
 * Author: FredTGZ
 * Description:
 * This module implements log file management.
 *
 ******************************************************************************/       
	global $loadmodule;
	
	if ($loadmodule !== false) {
		require_once("logfile.php");
	}
	else require("module.version");
?>
