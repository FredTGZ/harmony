<?php
/**\defgroup javascript
 * \author FredTGZ
 * \brief Javascript support
 * \details Javascript support
 */
	global $loadmodule;
	
	if ($loadmodule !== false) {
		require_once("javascript.php");
	}
	else require("module.version");
?>
