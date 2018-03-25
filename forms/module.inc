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
		require_once("formcontrol.inc");
		require_once("input.inc");
		require_once("form.inc");
		require_once("radio.inc");
		require_once("select.inc");
	}
	else require("module.nfo");
?>
