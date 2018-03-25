<?php if (! defined('HARMONY_INCLUDE')) die("Harmony Library is not loaded !");
    require_once("phpbbuser.php");
	require_once("configfile.php");
	require_once("install.php");
	require_once("language.php");
	require_once("phpbbconfig.php");
	//require_once("phpbbfunctions.inc");
	
	class CPHPBBForum
	{
		private $RootDirectory;
		private $ForumRelativeDirectory;
		private $ForumPath;
		private $User;
		//public $Database;
		private $TablePrefix;
		public $CurrentUser;
		/*public $CurrentUser;
		public $CookieName;
		public $CookiePath;
		public $CookieDomain;
		public $CookieSecure;
		private $CookieExpire;*/
		public $SessionID;
		public $Database=null;
		
		public function __construct(&$Database, $Root, $ForumDirectory, $Relative=null)
		{
			// 2011-02-01 - FredTGZ - R�vision compl�te pour meilleure int�gration phpBB - DEBUT
			$this->RootDirectory = $Root;
			$this->ForumRelativeDirectory = $Relative.'/.'.$ForumDirectory.'/';
			$this->ForumDirectory = $Root.'/'.$this->ForumRelativeDirectory;
			$this->Database = $Database;
			global $phpbb_root_path; $phpbb_root_path = $this->ForumDirectory;
			global $phpEx; $phpEx = 'php';
			global $db;
			global $table_prefix;
			global $user;
			global $auth;
			global $cache;
			global $config;
			global $template;
			define('IN_PHPBB', true);
			global $dbhost;
			global $dbname;
			global $dbuser;
			global $dbpasswd;
			global $dbport;
			
			if (file_exists($this->ForumDirectory. '/common.'.$phpEx))
				require_once($this->ForumDirectory . '/common.'.$phpEx);
			else 
				die("Le fichier ".$phpbb_root_path. 'common.'.$phpEx.' est introuvable !');

		    // Start session management
		    $user->session_begin();
		    $auth->acl($user->data);
		    $user->setup();
		    
			$this->TablePrefix = $table_prefix;
		    
		    $this->CurrentUser = new CPHPBBUser($this, $user);
		}
		
		private function SetConfigValue($var, $value, $dynamic=false)
		{
			set_config($var, $value, $dynamic);
			return true;
		}

		public function GetForumUrl()
		{
			return $this->ForumRelativeDirectory;
		}

		public function GetTablePrefix()
		{
			return $this->TablePrefix;
		}
		
		public static function ConvertMySQLDate($date)
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

		public function GetUserAvatar($avatar)
		{
			if (strtolower(substr($avatar, 0, 7)) == 'http://') 
				return($avatar);
			else
				return($this->GetForumUrl().'/download/file.php?avatar='.$avatar);
		}
	
		private function GetUserArrayWithQuery(&$database, $query)
		{
			$user_recordset = new CRecordset($database);

			$UserArray = array();
			
			if ($user_recordset->OpenRecordset($query)) {
				while (!$user_recordset->IsEOF()) {
					$UserArray[] = array(	"ID" => $user_recordset->GetFieldValue("user_id"),
											"NICKNAME" => $user_recordset->GetFieldValue("username"),
											"EMAIL" => $user_recordset->GetFieldValue("user_email"));
					$user_recordset->MoveNext();
				}
			}

			return $UserArray;		
		}
	
		public function GetUserFieldOKArray(&$database, $field_name, $field_value=1)
		{
			$query = "SELECT usr.user_id, usr.username, usr.user_email
						FROM `".$this->TablePrefix."users` usr
						LEFT JOIN `".$this->TablePrefix."profile_fields_data` fld ON usr.user_id=fld.user_id
						INNER JOIN `".$this->TablePrefix."profile_fields` fl ON field_name='".$field_name."'
						WHERE usr.group_id<>6 AND IFNULL(fld.pf_".$field_name.", fl.field_default_value) = '".$field_value."' AND usr.user_email <> '' 
						ORDER BY usr.username ASC;";

			return $this->GetUserArrayWithQuery($database, $query);
		}
	
	
	
		public function GetUserInGroupArray(&$database, $groupname)
		{	
			$query = "SELECT usr.user_id, usr.username, usr.user_email
						FROM `".$this->TablePrefix."groups` grp
						INNER JOIN `".$this->TablePrefix."user_group` usrgrp ON grp.group_id=usrgrp.group_id
						INNER JOIN `".$this->TablePrefix."users` usr ON usr.user_id=usrgrp.user_id
						where grp.group_name='".$groupname."'";

			return $this->GetUserArrayWithQuery($database, $query);
		}
	
	
		public function GetUserArray(&$database)
		{	
			$query = "SELECT user_id, username, user_email 
						FROM `".$this->TablePrefix."users` 
						WHERE user_email<>'' and user_id <> '0' 
						ORDER BY `username` ASC";
					
			return $this->GetUserArrayWithQuery($database, $query);
		}

		public function GetUserList(&$database, $selected_id)
		{	
			$users = $this->GetUserArray($database);
			$UserList = '';
			
			foreach($users as $user) {
					$UserList .= sprintf('<option%s value="%s">%s</option>',
										($selected_id == $user['ID']?" selected":""),
										$user['ID'],
										$user['NICKNAME']);
			}
			
			return $UserList;
		}

		/***********************************************************************
		 *
		 **********************************************************************/		 		 		 		 		
		public function GetConfigValue($name)
		{
			if (isset($this->CurrentUser->data[$name]))
				return $this->CurrentUser->data[$name];
			else
				return null;
		}
		
		public function Login($Username, $Password, $ViewOnline, $AutoLogin)
		{
			global $auth;
			
			if (!$this->CurrentUser->IsConnected()) {
				if ($Username != null && $Username != $Password) {
					$result = $auth->login($Username, $Password, $AutoLogin, $ViewOnline, $admin);
					return true;
				}
				else return true;
			}
			else return true;
		}		
	}
?>