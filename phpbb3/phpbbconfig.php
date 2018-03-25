<?php if (! defined('HARMONY_INCLUDE')) die("Harmony Library is not loaded !");
	/**
	 * This function return a phpbb config value.
	 */	 	 	 	
	/*function GetConfigValue($name)
	{
		global $mod_database;
		global $table_prefix;
		$query = sprintf("SELECT config_value FROM %sconfig WHERE config_name='%s';", $table_prefix, $name);

		$recordset_config = new CRecordset($mod_database);
		if (($recordset_config->OpenRecordset($query)) && (!$recordset_config->IsEof())) {
			return($recordset_config->GetFieldValue('config_value'));
		}
		
		return null;
	}*/
	
	/*function GetUserList($selected_id)
	{	
		global $mod_database;
		global $table_prefix;
		global $phpbb_version;
		
		$user_recordset = new CRecordset($mod_database);

		if ($phpbb_version == 2)
			$query = "SELECT user_id, username FROM `".$table_prefix."users` WHERE user_active='1' ORDER BY `username`";
		else if ($phpbb_version == 3)
			$query = "SELECT user_id, username FROM `forum_users` WHERE user_email<>'' and user_id <> '0' ORDER BY `username`";
				
		$UserList = '';
		
		if ($user_recordset->OpenRecordset($query)) {
			while (!$user_recordset->IsEOF()) {
				$UserList .= sprintf('<option%s value="%s">%s</option>', (
									($selected_id == $user_recordset->GetFieldValue("user_id"))?" selected":""),
									$user_recordset->GetFieldValue("user_id"),
									$user_recordset->GetFieldValue("username"));
				$user_recordset->MoveNext();
			}
		}
		
		return $UserList;
	}*/

	/***************************************************************************************
	 *
	 ***************************************************************************************/	 	 	 	
	/*function GetAvatar($avatar)
	{
		global $phpbb_version;
	
		if (strtolower(substr($avatar, 0, 7)) == 'http://') 
			return($avatar);
		else if ($phpbb_version == 2)
			return(PHPBB_PATH.'/images/avatars/'.$avatar);
		elseif ($phpbb_version == 3)
			return(PHPBB_PATH.'/download/file.php?avatar='.$avatar);
	}*/

	/***************************************************************************************
	 *
	 ***************************************************************************************/	 	 	 	
	/*function PrintTitle()
	{
		printf('<center><span style="font-size:16pt; font-weight: bold;">%s</span><br>%s</center>', GetConfigValue('sitename'), GetConfigValue('site_desc'));
		print("<br>");
		print("<hr>");
		print("<br>");
	}*/


	function ConvertMySQLDate($date)
	{
		if (substr(mysql_get_server_info(), 0, 1) >= 5) {
			$date_year = substr($date, 0, 4);
			$date_month = substr($date, 5, 2);			
			$date_day = substr($date, 8, 2);
			$date_hour = substr($date, 11, 2);			
			$date_minute = substr($date, 14, 2);
			$date_second = substr($date, 17, 2);		
		}
		else {
			$date_year = substr($date, 0, 4);
			$date_month = substr($date, 4, 2);			
			$date_day = substr($date, 6, 2);
			$date_hour = substr($date, 8, 2);			
			$date_minute = substr($date, 10, 2);
			$date_second = substr($date, 12, 2);
		}		

		return array(	"year" => $date_year,
						"month" => $date_month,
						"day" => $date_day,
						"hour" => $date_hour,
						"minute" => $date_minute,
						"second" => $date_second);
	}
?>
