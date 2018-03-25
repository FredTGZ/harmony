<?php if (! defined('HARMONY_INCLUDE')) die("Harmony Library is not loaded !");
	require_once("phpbbuser.inc");
	require_once("configfile.inc");
	require_once("install.inc");
	require_once("language.inc");
	require_once("phpbbconfig.inc");
	require_once("phpbbfunctions.inc");
	
	CHarmony::LoadModule("javascript");
	
	class CPHPBBForum
	{
		private $RootDirectory;
		private $ForumRelativeDirectory;
		private $ForumPath;
		private $User;
		public $Database;
		private $TablePrefix;
		public $CurrentUser;
		public $CookieName;
		public $CookiePath;
		public $CookieDomain;
		public $CookieSecure;
		private $CookieExpire;
		public $SessionID;
		
		public function __construct($Root, $ForumDirectory, $Relative=null)
		{
			$this->RootDirectory = $Root;
			$this->ForumRelativeDirectory = $Relative.'/'.$ForumDirectory;
			$this->ForumDirectory = $Root.'/'.$ForumDirectory;
			

			global $dbms;
			global $dbhost;
			global $dbport;
			global $dbname;
			global $dbuser;
			global $dbpasswd;
			global $table_prefix;
			global $acm_type;
			global $load_extensions;

			require_once($this->ForumDirectory."/config.php");
			
			if ($dbms == 'mysql') $ServerType = CDatabase::$MYSQL;
			elseif ($dbms == 'mssql') $ServerType = CDatabase::$MSSQL;
			else die("Can't connect to $dbms");

			$this->TablePrefix = $table_prefix;
			$this->Database = new CDatabase(CDatabase::$MYSQL, $dbhost, $dbname, $dbuser, $dbpasswd, $dbport);

			if ($this->Database->Open()) {
				$domain = $this->GetConfigValue('cookie_domain');
				$this->CookieName = (string) $this->GetConfigValue('cookie_name');
				$this->CookiePath = $this->GetConfigValue('cookie_path');
				$this->CookieSecure = $this->GetConfigValue('cookie_secure');
				$this->CookieExpire = (int) $this->GetConfigValue('max_autologin_time');
				$this->CookieDomain = (($domain == '' || $domain == 'localhost' || $domain == '127.0.0.1') ? '' : $domain);
				$this->GetSessionID();			
				
				$this->CurrentUser = new CPHPBBUser($this->Database, $this->CookieName, $this->ForumDirectory, $this->TablePrefix, $this->SessionID);

			}
			else die($this->Database->GetLastError());
		}

		private function SetConfigValue($var, $value, $dynamic=false)
		{
			$query = sprintf("UPDATE %sconfig SET config_value ='%s', is_dynamic=%u WHERE config_name='%s'",
					$this->TablePrefix, $value, ($dynamic?1:0), $var);
					
			return $this->Database->ExecuteSQLQuery($query);
		}
		
		
		private function GetUniqueID($extra = 'c')
		{
			static $dss_seeded = false;
			global $config;
			$rand_seed = $this->GetConfigValue('rand_seed');
			$rand_seed_last_update = $this->GetConfigValue('rand_seed_last_update');
			$val = $rand_seed . microtime();
			$val = md5($val);
			$rand_seed = md5($rand_seed . $val . $extra);
		
			if ($dss_seeded !== true && ($rand_seed_last_update < time() - rand(1,10)))
			{
				$this->SetConfigValue('rand_seed', $config['rand_seed']);
				$this->SetConfigValue('rand_seed_last_update', time());
				$dss_seeded = true;
			}
		
			return substr($val, 4, 16);
		}
		
		public function GetForumUrl()
		{
			return /*CHTTPServer::GetScriptDomain().'/'.*/$this->ForumRelativeDirectory;
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
	
		private function GetUserArrayWithQuery($query)
		{
			$user_recordset = new CRecordset($this->Database);

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
	
		public function GetUserFieldOKArray($field_name, $field_value=1)
		{
			$query = "SELECT usr.user_id, usr.username, usr.user_email
						FROM `".$this->TablePrefix."users` usr
						LEFT JOIN `".$this->TablePrefix."profile_fields_data` fld ON usr.user_id=fld.user_id
						INNER JOIN `".$this->TablePrefix."profile_fields` fl ON field_name='".$field_name."'
						WHERE usr.group_id<>6 AND IFNULL(fld.pf_".$field_name.", fl.field_default_value) = '".$field_value."' AND usr.user_email <> '' 
						ORDER BY usr.username ASC;";

			return $this->GetUserArrayWithQuery($query);
		}
	
	
	
		public function GetUserInGroupArray($groupname)
		{	
			$query = "SELECT usr.user_id, usr.username, usr.user_email
						FROM `".$this->TablePrefix."groups` grp
						INNER JOIN `".$this->TablePrefix."user_group` usrgrp ON grp.group_id=usrgrp.group_id
						INNER JOIN `".$this->TablePrefix."users` usr ON usr.user_id=usrgrp.user_id
						where grp.group_name='".$groupname."'";

			return $this->GetUserArrayWithQuery($query);
		}
	
	
		public function GetUserArray()
		{	
			$query = "SELECT user_id, username, user_email 
						FROM `".$this->TablePrefix."users` 
						WHERE user_email<>'' and user_id <> '0' 
						ORDER BY `username` ASC";
					
			return $this->GetUserArrayWithQuery($query);
		}

		public function GetUserList($selected_id)
		{	
			$users = $this->GetUserArray();
			$UserList = '';
			foreach($users as $user) {
					$UserList .= sprintf('<option%s value="%s">%s</option>',
										($selected_id == $user['ID']?" selected":""),
										$user['ID'],
										$user['NICKNAME']);
			
			
			}
			
			return $UserList;
		}

		public function CheckPassword($username, $password)
		{
			if($username == null) return false;
			$query = sprintf("SELECT user_password FROM %susers WHERE username='%s'", $this->TablePrefix, $username);
			$db_password = $this->Database->GetFirst($query);  
  			$hasher = new PasswordHash();

			return ($hasher->CheckPassword($password, $db_password));
		} 
		
		/***********************************************************************
		 *
		 **********************************************************************/		 		 		 		 		
		public function GetConfigValue($name)
		{
			$query = sprintf("SELECT config_value FROM %sconfig WHERE config_name='%s';", $this->TablePrefix, $name);
	
			$recordset_config = new CRecordset($this->Database);
			
			if (($recordset_config->OpenRecordset((string) $query)) && (!$recordset_config->IsEof())) {
				return($recordset_config->GetFieldValue('config_value'));
			}
			
			return null;
		}
		
		public function Logout()
		{
			$query = sprintf("DELETE FROM `%ssessions` WHERE session_id='%s'",
				$this->TablePrefix, $this->SessionID);
			
			//session_unset();

			/*
		$sql = 'DELETE FROM ' . SESSIONS_TABLE . "
			WHERE session_id = '" . $db->sql_escape($this->session_id) . "'
				AND session_user_id = " . (int) $this->data['user_id'];
		$db->sql_query($sql);*/
			
			
			if (!$this->Database->ExecuteSQLQuery($query)) return false;

			if (!$this->SetCookie('sid', '')) return false;
			if (!$this->SetCookie('u', '1')) return false;
			
			return true;
		}

		private function DeleteDBSession($Username)
		{
			$query = sprintf("DELETE FROM `%ssessions` WHERE session_user_id IN(SELECT user_id FROM `%susers` WHERE username='%s')",
				$this->TablePrefix, $this->TablePrefix, $Username);

			return $this->Database->ExecuteSQLQuery($query);
		}

		
		private function CreateDBSession($Username, $ViewOnline, $AutoLogin)
		{
			try {
				if ($Username=='') throw new CException('CreateDBSession, Username empty');
				if(false == $this->DeleteDBSession($Username)) throw new CException('CreateDBSession, can\' delete old sessions');
				
				
				$query = sprintf("INSERT INTO `%ssessions` (session_user_id, session_id, session_ip, session_browser, session_forwarded_for, session_page, session_viewonline, session_autologin, session_last_visit, session_start, session_time)
				SELECT U.user_id, '%s', '%s', '%s', '', 'index.php', %u, %u, %u, %u, %u
				FROM `%susers` U
				LEFT JOIN `%ssessions` S ON U.user_id=S.session_user_id
				WHERE U.username='%s'
				GROUP BY U.user_id",
				$this->TablePrefix,
				$this->SessionID,
				 CHTTPServer::GetClientIP(), $_SERVER['HTTP_USER_AGENT'], $ViewOnline, $AutoLogin, 
				time(), time(), time(), $this->TablePrefix, $this->TablePrefix, $Username);

				return $this->Database->ExecuteSQLQuery($query);
			}
			catch (CException $e) {
				$e->DisplayExceptionAndDie();
			}
		}

		private function UpdateDBSession($Username, $ViewOnline, $AutoLogin)
		{
			$query = sprintf("UPDATE %ssessions
			SET session_last_visit=NOW(), session_viewonline=%u, session_autologin=%u,
			session_user_id=(SELECT user_id FROM `%susers` WHERE username='%s')
			WHERE session_id='%s'" ,
				$this->TablePrefix, $ViewOnline, $AutoLogin,
				$this->TablePrefix, $Username, $this->SessionID);
			return $this->Database->ExecuteSQLQuery($query);
		}

		private function DBSessionIsRegistered()
		{
			return $this->Database->RowExist($this->TablePrefix."sessions", "session_id='".$this->SessionID."'");	
		}

		
		/**
		 * 
		 * Set a cookie for domain and path
		 * @param string $name Cookie name
		 * @param string $value Cookie value
		 */
		private function SetCookie($name, $value)
		{
			// Si la configuration
			if ($this->CookieExpire > 0)
				$expire = time() + 86400 * $this->CookieExpire;
			else
				$expire = time() + 31536000;	// Expiration = 1 an

			//DEBUG A SUPPRIMER !!!
			//$expire = time() + 100600;

			$expire = gmdate('D, d-M-Y H:i:s \\G\\M\\T', $expire);
			$name_data = rawurlencode($this->CookieName . '_' . $name) . '=' . rawurlencode($value);
			
			if (!headers_sent()) {
				header('Set-Cookie: '.$name_data .'; expires=' . $expire . '; path='.$this->CookiePath.'; domain='.$this->CookieDomain.((!$this->CookieSecure) ? '' : '; secure') . '; HttpOnly', false);
				return true;
			}
			else return false;
			
			//return setcookie($this->CookieName.'_'.$name, $value, $expire, $this->CookiePath, $this->CookieDomain, $this->CookieSecure);
		}
		
		public function DeleteAllSessions()
		{
			$query = sprintf("DELETE FROM %ssessions" , $this->TablePrefix);
			if (!$this->Database->ExecuteSQLQuery($query)) return false;
			
			$query = sprintf("DELETE FROM %ssessions_keys" , $this->TablePrefix);
			if (!$this->Database->ExecuteSQLQuery($query)) return false;
			
			return true;
		}
		
		public function GetSessionID()
		{
			if ($this->SessionID === null || $this->SessionID === '') {
				if (array_key_exists($this->CookieName.'_sid', $_COOKIE))
					$this->SessionID = $_COOKIE[$this->CookieName.'_sid'];
				else {
					$this->SessionID = md5($this->GetUniqueID());
				}
			}

			return $this->SessionID;
		}

		public function Login($Username, $Password, $ViewOnline, $AutoLogin)
		{
			// Check if the password is correct.
		    if ($this->CheckPassword($Username, $Password)) {
				$this->GetSessionID();			
				
				// Session is already registered...
				if ($this->SessionID != '' && $this->DBSessionIsRegistered()) {
					if (!$this->UpdateDBSession($Username, $ViewOnline, $AutoLogin)) {
						return false;				
					}
				}
				elseif(!$this->CreateDBSession($Username, $ViewOnline, $AutoLogin))
					return false;				

				if (!$this->SetCookie('sid', $this->SessionID)) {
					die("tptp");
					return false;
				}
				
				unset($this->CurrentUser);
				
				$this->CurrentUser = new CPHPBBUser($this->Database, $this->CookieName, $this->ForumDirectory, $this->TablePrefix, $this->SessionID);

				if (!$this->SetCookie('u', $this->CurrentUser->GetUserID())) return false;

				return true;
				
			}
			else {
				return false;
			}
		}		
	}
?>