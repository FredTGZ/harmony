<?php  namespace Harmony;
if (! defined('HARMONY_INCLUDE')) die("Harmony Library is not loaded !");
/**\defgroup base Base objects
 * \author FredTGZ
 * \brief Base objects
 * \details This module implements base objects
 */
	global $loadmodule;
	
	if ($loadmodule !== false) {
		require_once("basetypes.php");
		require_once("baseobject.php");
		require_once("error_mgt.php");
		require_once("exception.php");
		require_once("timer.php");
		require_once("html.php");
		require_once("popup.php");
		require_once("template_exception.php");
		require_once("template.php");
		require_once("document.php");
		require_once("basefile.php");
		require_once("bbfile.php");
		require_once("htmlfile.php");
		require_once("inifile.php");
		require_once("phpfile.php");
		require_once("xmlexception.php");
		require_once("xmlfile.php");
		require_once("configfile.php");
		require_once("sysinfos.php");
		require_once("bbcode2.php");
		require_once("enum.php");
		require_once("filesystem.php");
		require_once("htmldocument.php");
		require_once("upload.php");
	}
	else require("module.version");
?>
