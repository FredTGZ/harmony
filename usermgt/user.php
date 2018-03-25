<?php namespace Harmony\usermgt;
 if (! defined('HARMONY_INCLUDE')) die("Harmony Library is not loaded !");
/*******************************************************************************
 * User Management module for Harmony PHP Library
 *
 * Author: FredTGZ
 * Description:
 * 
 ******************************************************************************/       
	/**\brief User informations
	*\ingroup usermgt
 	*
 	*
 	* 	
 	*/
	class CUser extends \Harmony\CBaseObject
	{
		private $Filename = null;
		private $FilePosition = -1;
		private $FilePosition2 = -1;
		private $Nickname = "";
		private $PasswordMD5 = "";
		private $Level = "";
		private $Email = "";
		private $Name = "";
		private $Surname = "";
		private $Data = array();
		private $Valid = false;
		private $InfosRetrieved = false;
		
		private static $IDX_PASSWORD = 1;
		
		
		
		public function GetNickname()	{ if (!$this->InfosRetrieved) $this->ReadInfos(); return $this->Nickname; }
		public function GetName()		{ if (!$this->InfosRetrieved) $this->ReadInfos(); return $this->Name; }
		public function GetSurname()	{ if (!$this->InfosRetrieved) $this->ReadInfos(); return $this->Surname; }
		public function GetEmail()		{ if (!$this->InfosRetrieved) $this->ReadInfos(); return $this->Email; }
		public function GetUserLevel()	{ if (!$this->InfosRetrieved) $this->ReadInfos(); return $this->Level; }
		public function IsValid()		{ if (!$this->InfosRetrieved) $this->ReadInfos(); return $this->Valid; }
		
		/**Constructor
		 *
		 *\param[in]	$data	User string stored in Auth file.
		 *\return		Nothing
		 */		 		 		 		
		public function __construct($data, $filename, $position, $position2)
		{
			try {
				$this->Filename = $filename;
				$this->FilePosition = $position;
				$this->FilePosition2 = $position2;
				
				if ($data == "")
					throw new CException("No data provided.", "1");
					
				$this->Data = explode("|", $data);
			}
			catch (CException $e) {
			    print($e->DisplayExceptionAndDie());
			}		
		}

		/**Check user password
		 *
		 *\param[in]	$password		User password to check
		 *\return		true if the password has the same md5 value as the stored password.
		 */		 		 		 		
		public function CheckPassword($password)
		{
			if($this->Data[1] == md5($password)) {
				$this->ReadInfos();
				return true;
			}
			else return false;
		}
		
		public function ReadInfos()
		{
			$i = 0;
			$this->Nickname = $this->Data[$i++];
			$this->PasswordMD5 = $this->Data[$i++];
			$this->Level = $this->Data[$i++];
			$this->Email = $this->Data[$i++];
			$this->Surname = $this->Data[$i++];
			$this->Name = $this->Data[$i++];
			$this->Valid = ($this->Data[$i++]=='1'?true:false);
			$this->InfosRetrieved = true;
		}
			 		 				
		public function SetPassword($password)
		{
			$this->PasswordMD5 = md5($password);
				
		}
		
		public function SetPasswordMD5($PasswordMD5)
		{
			$this->PasswordMD5 = $PasswordMD5;
		}
		
		/**\brief Retrieve user string to be stored in auth file.
		 *
		 *Format: user|MD5password|level|email|surname|name		 		
		 *
		 *\return	User string to be stored in auth file.	
		 */		 		 		
		public function GetUserString()
		{
			if (!$this->InfosRetrieved) $this->ReadInfos();
			$ret = sprintf("%s|%s|%s|%s|%s|%s|%s\n", $this->Nickname, $this->PasswordMD5, $this->Level, $this->Email, $this->Surname, $this->Name, ($this->Valid?1:0));
			return $ret;
		}

		/**\brief Retrieve user string to be stored in auth file.
		 *
		 *Format: user|MD5password|level|email|surname|name		 		
		 *
		 *\return	User string to be stored in auth file.	
		 */		 		 		
		public static function GetNewUserString($Nickname, $Password, $Level, $Email, $Surname, $Name, $Valid=true)
		{
			$ret = sprintf("%s|%s|%s|%s|%s|%s|%s\n", stripslashes($Nickname), md5($Password), $Level, $Email, stripslashes($Surname), stripslashes($Name), ($Valid?1:0));
			return $ret;
		}
		
		
		public function SaveUser($Nickname, $Password, $Level, $Email, $Surname, $Name, $Valid=true)
		{
			if ($this->Nickname == $Nickname) {
				$UserString = $this->GetUserString();
				
				$Content = file($this->Filename);
				
				if ($key = array_search($UserString, $Content)) {
					try {
						unset($Content[$key]);
						if ($Name != '') $this->Name = $Name;
						if ($Surname != '') $this->Surname = $Surname;
						if ($Password != '') $this->SetPassword($Password);
						$this->Level = $Level;
						$this->Email = $Email;
						$this->Valid = $Valid;
						$this->InfosRetrieved = true;
						$UserString = $this->GetUserString();
						$Content[] = $UserString;
						file_put_contents($this->Filename, $Content);
						return true;
					}
					catch (CException $e) {
					    print($e->DisplayExceptionAndDie());
					    return false;
					}		
				}
					
			}
		}
		
	}
?>
