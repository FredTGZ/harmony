<?php if (! defined('HARMONY_INCLUDE')) die("Harmony Library is not loaded !");

class CPHPBBUser
{
	private $Nickname = "";
	private $ID = 0;
	private $ISAdmin = false;
	private $Avatar = "";
	private $ISOK = false;
	private $Language = "english";
	private $DateFormat;
	private $SessionSID = null;
	public $User = null;
	private $Forum;
	
	function __construct(&$Forum, &$user)
	{
		$this->Forum = $Forum;
		
		$this->ISOK = false;
		$this->TablePrefix = $table_prefix;
		$this->Error = true;
		$this->User = $user;
		
		if($this->User->data['is_registered']) {
			$this->ID = $this->User->data['user_id'];
			$this->SessionSID = $this->User->data['session_id'];	
			$this->Nickname = $this->User->data['username'];
			
			//22/01/2014 : correction majeure sur la détermination du groupe admin.
			//$this->ISAdmin = $this->User->data['group_id'];
			$query = sprintf("SELECT 1 FROM `forum_user_group` UG WHERE user_id=%u AND group_id IN (SELECT group_id FROM forum_groups WHERE group_founder_manage=1)", $this->ID);
			if ($this->Forum->Database->GetFirst($query) == 1)
				$this->ISAdmin = 1;
			else
				$this->ISAdmin = 0;
			
			$this->Language = $this->User->data['user_lang'];
			$this->DateFormat = $this->User->data['user_dateformat'];
			$this->Avatar = $this->User->data['user_avatar'];
			$this->ISOK = ($this->User->data["user_permissions"]<>'') && ($this->ID!=1) && ($this->ID!="");
		}
	}
	
	public function GetSessionSID() { return $this->SessionSID; }
  	public function GetLanguage()  	{ return $this->Language; }
  	public function GetUserID() 	{ return $this->ID; }
  	public function GetNickname()  	{ return $this->Nickname; }
	public function GetAvatar()		{ return $this->Avatar; }
  	public function IsAdmin()	  	{ return($this->ISAdmin); }
  	public function IsConnected()	{ return($this->User->data['is_registered']/*$this->ISOK*/); }
	public function GetUserLevel()  { die(($this->ISAdmin?1:0)); }

	public static function IsMemberOf(&$Database, $TablePrefix, $UserID, $groups)
	{
		$list = 'null';
		
		foreach($groups as $group)
			$list .= ", '$group'";

		$query = sprintf("SELECT count(*) FROM `%suser_group` UG
			INNER JOIN `%sgroups` G ON UG.group_id=G.group_id
			INNER JOIN `%susers` U ON U.user_id=UG.user_id
			WHERE U.user_id=%u and G.group_name IN (%s)",
			$TablePrefix, $TablePrefix, $TablePrefix, $UserID, $list);
		
		return ($Database->GetFirst($query) > 0); 
	}
	
	public function GetMPLink()
	{
		return ("ucp.php?i=pm&mode=compose&u=" . $this->ID);
	}
}
?>
