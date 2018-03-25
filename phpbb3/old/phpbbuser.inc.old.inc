<?php if (! defined('HARMONY_INCLUDE')) die("Harmony Library is not loaded !");

class CPHPBBUser
{
	/// Name 
	/*private */var $Nickname = "";
	/// Database User Identifier
	/*private */var $ID = 0;
	/// Set to true if this user is an administrator.
	private $ISAdmin = false;
	/// User avatar
	private $Avatar = "";
	/// Set to true if this user is valid.
	private $ISOK = false;
	/// User language
	private $Language = "english";
	/// User date format
	private $DateFormat;
	/// User Theme name
	private $ThemeName = "";
	/// Theme parameters
	private $Theme = null;
	private $SessionSID = null;
	private $Database = null;
	private $TablePrefix = null;
	private $Error = false;
  
	function __construct(&$database, $cookie_name, $path, $table_prefix='phpbb_', $SessionID='')
	{
		$this->ISOK = false;
		$this->Database = &$database;
		$this->TablePrefix = $table_prefix;
		$this->Error = true;
		
		//if (isset($_COOKIE[$cookie_name.'_sid'])) {
		if ($SessionID != '') {
			try {
				//$this->SessionSID = $_COOKIE[$cookie_name.'_sid'];
				$this->SessionSID = $SessionID;

				$query = sprintf("SELECT session_user_id userid FROM `".$table_prefix."sessions` WHERE session_id='%s' ORDER BY session_start DESC LIMIT 0,1", $this->SessionSID);
				
				/*$tmp_recordset = new CRecordset($this->Database);
		
				if ($tmp_recordset->OpenRecordset($query)) {
					$this->ID = $tmp_recordset->GetFieldValue('userid');
				}*/
				
				$this->ID = $database->GetFirst($query);
				$user_recordset = new CRecordset($this->Database);
			
				$query = "SELECT user.username username, user.user_dateformat, user.user_lang,user.user_avatar user_avatar, session.session_id session_id, user.user_rank user_level, user_permissions, user.user_style ";
				$query .= " FROM `".$table_prefix."users` user ";
				$query .= " LEFT JOIN `".$table_prefix."sessions` session ON user.user_id=session.session_user_id";
				$query .= " WHERE user_id='".$this->ID."' ORDER BY session_start DESC LIMIT 0, 1";
	
				if ($user_recordset->OpenRecordset($query)) {
					$this->Nickname = $user_recordset->GetFieldValue("username");
					
					
					// Change FHE 2009-06-03 - Don't use user_rank but instead search for one or more manager groups
					//$this->ISAdmin = ($user_recordset->GetFieldValue("user_level") == 1);
					$this->ISAdmin = ("1" == $database->GetFirst("SELECT 1 FROM `".$table_prefix."user_group` ugrp INNER JOIN `".$table_prefix."groups` grp ON ugrp.group_id=grp.group_id WHERE grp.group_founder_manage=1 AND user_id='".$this->ID."'"));
					
					$this->Avatar = $user_recordset->GetFieldValue("user_avatar");
					$this->Language = $user_recordset->GetFieldValue("user_lang");
					$this->DateFormat = $user_recordset->GetFieldValue("user_dateformat");
					$this->ISOK = ($user_recordset->GetFieldValue("user_permissions")<>'') && ($this->ID!=1) && ($this->ID!="") /*&& ($this->SessionSID == $user_recordset->GetFieldValue("session_id"))*/;

					$theme_id = $user_recordset->GetFieldValue("user_style");

					if ($theme_id == '') $theme_id = $this->GetConfigValue("default_style");
					if ($this->Language == '') $this->Language = $this->GetConfigValue("default_lang");
					
					$query2 = "SELECT * FROM `".$table_prefix."styles_theme` user WHERE theme_id='" . $theme_id . "'";
					
					$this->Theme = new CRecordset($this->Database);
					
					if ($this->Theme->OpenRecordset($query2)) {
						if (! $this->Theme->IsEOF()) {
							$this->ThemeName = $this->Theme->GetFieldValue("theme_name");
						}
					}
					else throw new CException($user_recordset->GetLastError());
					
					if (strtolower(substr($this->Avatar, 0, 7)) != 'http://')
						$this->Avatar = CHTTPServer::GetScriptDomain().$path.'/download/file.php?avatar='.$this->Avatar;
						
					$this->Error = false;
				}
				else throw new CException($user_recordset->GetLastError());
			}
			catch (CException $e) {
			    print($e->DisplayExceptionAndDie());
			    return false;
			}
		}
		else {
		//print_r($_COOKIE);
		//die("pas de cookie !!!");
		}
	}
	
	public function GetSessionSID() { return $this->SessionSID; }
  	public function GetLanguage()  	{ return $this->Language; }
  	public function GetUserID() 	{ return $this->ID; }
  	public function GetNickname()  	{ return $this->Nickname; }
	public function GetAvatar()		{ return $this->Avatar; }
  	public function IsAdmin()	  	{ return($this->ISAdmin); }
  	public function IsConnected()	{ return($this->ISOK); }
	public function GetUserLevel()  { return(($this->ISAdmin?1:0)); }

	public function IsMemberOf($groups)
	{
		$list = 'null';
		
		foreach($groups as $group)
			$list .= ", '$group'";

		$query = sprintf("SELECT count(*) FROM `%suser_group` UG
			INNER JOIN `%sgroups` G ON UG.group_id=G.group_id
			INNER JOIN `%susers` U ON U.user_id=UG.user_id
			WHERE U.user_id=%u and G.group_name IN (%s)",
			$this->TablePrefix, $this->TablePrefix, $this->TablePrefix, $this->ID, $list);
		
		return ($this->Database->GetFirst($query) > 0); 
	}
	
	public function GetMPLink()
	{
		return ("ucp.php?i=pm&mode=compose&u=" . $this->ID);
	}
	
	public function GetConfigValue($name)
	{
		$query = sprintf("SELECT config_value FROM %sconfig WHERE config_name='%s';", $this->TablePrefix, $name);

		$recordset_config = new CRecordset($this->Database);
		
		if (($recordset_config->OpenRecordset($query)) && (!$recordset_config->IsEof())) {
			return($recordset_config->GetFieldValue('config_value'));
		}
		
		return null;
	}

}
?>
