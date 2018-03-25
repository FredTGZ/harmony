<?php if (! defined('HARMONY_INCLUDE')) die("Harmony Library is not loaded !");
/**\defgroup database Database objects
 * \author FredTGZ
 * \brief Database module for Harmony PHP Library
 * \details This module implements database access and querying.
 *
 *\n 
 *<b>Sample #1:</b>\n\n
 *global $database;\n
 *$database = new CDatabase(CDatabase::$mySQL, CDatabase::$localhost, "my_database", "username", "password");\n\n
 *if ($database->Open()) {\n
 *&nbsp;&nbsp;&nbsp;&nbsp;$recordset = new CRecordset($database);\n
 *\n
 *&nbsp;&nbsp;&nbsp;&nbsp;if ($recordset->OpenRecordset("SELECT field2 FROM my table where field1='AV8'")) {\n
 *&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;while (!$recordset->IsEOF()) {\n
 *&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;print('&lt;br&gt;'.$recordset->GetFieldValue('field2'));\n
 *&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$recordset->MoveNext();\n
 *&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;}\n
 *&nbsp;&nbsp;&nbsp;&nbsp;}\n
 *&nbsp;&nbsp;&nbsp;&nbsp;else print $recordset->GetLastError();\n   
 *\n
 *&nbsp;&nbsp;&nbsp;&nbsp;$database->Close();\n
 *}\n   
 *else print $database->GetLastError();\n   
 */
 	global $loadmodule;
	
	if ($loadmodule !== false) {
		require_once("dbexception.php");
		require_once("database.php");
		require_once("recordset_exception.php");
		require_once("recordset.php");
		require_once("base_recordset.php");
		require_once("dbview.php");
		require_once("dbform.php");
		require_once("sqlfile.php");
	}
	else require("module.version");
?>
