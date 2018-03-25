<?php if (! defined('HARMONY_INCLUDE')) die("Harmony Library is not loaded !");
/**\defgroup usermgt User Management
 *
 * User Management module for Harmony PHP Library
 *  
 * Author: FredTGZ
 *  
 * Description:
 * 
 * Howto:  
 * 	 
 *<b>Example 1:</b>
 *	 
 *    $login = new CLogin("./auth/auth_", "Please login !");
 *    	 
 *    if ($login->Login(true)) {
 *    	 
 *&nbsp;&nbsp;&nbsp;&nbsp;<font color="green">// Add here your code, at this point there are no html header written.</font>
 *        	 
 *    }
 *    	 
 *<b>Example 2:</b>
 *	 
 *$login = new CLogin("./auth/auth_");
 *	 
 *if ($login->Login(false)) {
 *    	 
 *&nbsp;&nbsp;&nbsp;&nbsp;// Add here your code, at this point there are no html header written.
 *   
 *&nbsp;&nbsp;&nbsp;&nbsp;print $login->GetSmallLogin();
 *    	 
 *&nbsp;&nbsp;&nbsp;&nbsp;// Write your html footer.
 *         	 
 *    }	 	 	 
 */ 
	global $loadmodule;
	
	if ($loadmodule !== false) {
		CHarmony::IncludeModule('languages');
		require_once("user.php");
		require_once("login.php");

	}
	else require("module.version");
?>
