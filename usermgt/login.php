<?php namespace Harmony\usermgt;
 if (! defined('HARMONY_INCLUDE')) die("Harmony Library is not loaded !");
/*******************************************************************************
 * User Management module for Harmony PHP Library
 *
 * Author: FredTGZ
 * Description:
 *
 ******************************************************************************/       

	/**\brief Login manager class.
	 *\ingroup usermgt
	 *
	 * Manage exception, format exception message.
	 */	 	 	
	class CLogin extends \Harmony\CBaseObject
	{
		/// CTemplate instance.
		protected $Templates = null;
		///Auth file prefix, default is "./auth_".
//		private $PrefixAuthFiles = "";
		private $AuthFileDirectory = "";
		///User session idenfier.
		private $SessionID = null;
		///CUser instance, you can use GetUser() to retrieve the user connected.
		private $User = null;
		/// Expiration time in minutes.
		private $ExpirationTime = 20;
		///CSS
		private $CSS = null;
		private $CSSStyle = null;
		///Logo to display
		private $Logo = "";
		///
		private $CanRegister = true;
		///
		private $Dictionary = null;
		///
		private $NeedValidation = true;
		///
		
		private $HomeDirectory = "";
		
		private $BaseScript = "index.php";
		private $RootScript = "index.php";
		
		///
		private $IsConnected = false;
		
		private $UsePHPBB = false;
		
		private $PHPBBPath=null;
		private $PHPBBForum=null;
		
		private $NewConnection = false;
		private $DBPassword;
		
		public function IsNewConnection()
		{
			return $this->NewConnection;
		}
		
		public function IsConnected()
		{
			return $this->IsConnected;
		}
		
		public function SetLanguage($language)
		{
			if ($language === 'auto') {
				$language = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
				$pos = strpos($language, ',');
				if ($pos>0) $language = substr($language, 0, $pos);
				$pos = strpos($language, '-');
				if ($pos>0) $language = substr($language, 0, $pos);
			}
		
			$this->Dictionary = new  \Harmony\languages\CDictionnaryFile($language);
		}
		
		public function CanRegister($can)
		{
			$this->CanRegister = $can;
		}
		
		/**
		 * 
		 * @param string $filename
		 */
		public function SetLogo($filename)
		{
			if ($filename!= "")
				$this->Logo = sprintf('<center><img id="LoginLogo" src="%s" border="0" height="100"></center>', $filename);
			else $this->Logo = "";
		}

		/**
		 * 
		 * @param string $filename
		 * @param string $charset
		 */
		public function SetCSS($filename, $charset="ISO-8859-1")
		{
			if ($filename != "") {
				$this->CSS = sprintf('<link rel="stylesheet" href="%s" charset="%s">',  \Harmony\CHTTPServer::GetScriptDomain().$filename, $charset);
				$this->CSSStyle = "";
			}
			else {
				
				$LoginCSSContent = file_get_contents(HARMONY_INCLUDE."/usermgt/templates/login.css");
				$this->CSS = sprintf('<style>%s</style>', $LoginCSSContent);
				$this->CSSStyle = $this->CSS;
			}
		}
		
		/**
		 * 
		 * @param unknown $config_file
		 * @param unknown $RelativePath
		 */
		private function UseConfigurationFile($config_file, $RelativePath, $codepage='ISO-8859-1')
		{
			if ($config_file[0] != '/')
				$RelativePath =  \Harmony\CHTTPServer::GetDocumentRoot().'/'.$RelativePath;
			
			$filename = $RelativePath.'/'.$config_file;
			
			
			$configfile = new  \Harmony\CConfigFile($filename, $codepage);
			
			$this->SetTitle1($configfile->GetConfigValue('Title'));
            $this->SetTitle2('');
            
			$this->SetAuthFileDirectory($RelativePath.$configfile->GetConfigValue('AuthFileDirectory'));
			$this->SetSessionExpiration($configfile->GetConfigValue('ExpirationTime'));
			$this->SetCSS($configfile->GetConfigValue('CSS'), $configfile->GetConfigValue('Charset'));
			$this->SetLogo($configfile->GetConfigValue('Logo'));
			$this->CanRegister(($configfile->GetConfigValue('CanRegister')==1));
			$this->NeedValidation = ($configfile->GetConfigValue('NeedValidation') == 1);
			$this->SetLanguage($configfile->GetConfigValue('Language'));
			$this->HomeDirectory = $RelativePath.$configfile->GetConfigValue('HomeDirectory');
			$this->BaseScript = $configfile->GetConfigValue('BaseScript');
			$this->RootScript = $this->BaseScript;
			$this->UsePHPBB = ($configfile->GetConfigValue('UsePHPBB') == "1");
			$this->PHPBBPath = $configfile->GetConfigValue('PHPBBPath2', ($this->UsePHPBB?null:false));
			$this->Templates->AssignVar("UserSearch", $this->Dictionary->Translate('login_usersearch', true));
			$this->Templates->AssignVar("Search", $this->Dictionary->Translate('find', true));
							
			if ($this->UsePHPBB) {
				CHarmony::LoadModule("phpbb3");
				global $db;
				$ServerType = ((strpos($db->sql_layer, 'mysql')>=0)?CDatabase::$MYSQL:CDatabase::$MSSQL);
				$Database = new CDatabase($ServerType, $db->server, $db->dbname, $db->user, $configfile->GetConfigValue('DBPassword'));
				$Database->Open();
				$this->PHPBBForum = new CPHPBBForum($Database, \Harmony\CHTTPServer::GetDocumentRoot(), $RelativePath.'/'.$this->PHPBBPath);
			}
		}
		
		
		/*public function CheckHomeDirectory()
		{
			if ($this->HomeDirectory != '') {
			
			}
		} */
		
		public function GetClientBrowserInfos()
		{
			return $this->ClientBrowser;
		}

		public function GetTopToolbar($toolbar="")
		{
			$this->Templates->AssignVar("TOPTOOLBAR", $toolbar);
			
			if(isset($_SESSION['LoginNickname']))
				$this->Templates->AssignVar("LoginNickname", $_SESSION['LoginNickname']);
				
			return $this->Templates->GetHtml('toptoolbar');
		}

		private function GetLoginNickname()
		{
			if ($this->UsePHPBB) $LoginNickname = $this->User->GetNickname();
			elseif (isset($_SESSION['LoginNickname'])) $LoginNickname = $_SESSION['LoginNickname'];

			return $LoginNickname;
		}
		
		public function GetSmallToolbar()
		{
			$this->Templates->AssignVar('LoginBaseScript', $this->BaseScript);

			if($this->UsePHPBB || isset($_SESSION['LoginNickname'])) {
				global $module_infos;
				require_once("module.version");
                $this->Templates->AssignVar("ModuleName", $module_infos['name']);
				$this->Templates->AssignVar("ModuleVersion", $module_infos['version']);
				$this->Templates->AssignVar("ModuleDescription", $module_infos['description']);
                
                
                
				$BrowserImage = "browser";
				if ($this->ClientBrowser->Name != '') $BrowserImage = $this->ClientBrowser->Name; 

				$this->Templates->AssignVars(array(
						"ClientIP" => \Harmony\CHTTPServer::GetClientIP(),
						"ClientLanguage" => \Harmony\CHTTPServer::GetClientLanguage(),
						"ClientBrowser" => $this->ClientBrowser->FullName,
						"ClientOS" => $this->ClientBrowser->OperatingSystem,
						"ClientBrowserImage" => $BrowserImage
				));

				$this->Templates->AssignVar('LoginTimeSeconds', $this->Dictionary->Translate('time_seconds', false));
				$this->Templates->AssignVar('LoginTimeMinutes', $this->Dictionary->Translate('time_minutes', false));
				$this->Templates->AssignVar('LoginSessionExpirationText', $this->Dictionary->Translate('login_session_exp', false));
				$this->Templates->AssignVar('LoginSessionExpired', $this->Dictionary->Translate('LoginSessionExpired', false));
				$this->Templates->AssignVar('LoginSessionExpiration', $this->ExpirationTime * 60);
			
				$this->Templates->AssignVar("LoginYouAreConnected", sprintf($this->Dictionary->Translate("you_are_connected"), $this->GetLoginNickname()));		
				
				if (!$this->UsePHPBB && $this->User->GetUserLevel()>0) {
					$this->Templates->AssignVar("LoginAdmin", "Admin");
					$this->Templates->AssignVar("LoginAdminButton", $this->Templates->GetHtml('adminbtn'));
				}
				
				return $this->Templates->GetHtml('toolbar');
			}
		}
		
		/**Set session cookie expiration time.
		 *
		 *\param[in]	$time		Session lifetime in minutes (not seconds). Default is 20 minutes.
		 *\return		Nothing		 
		 */
		public function SetSessionExpiration($time=20)
		{
			$this->ExpirationTime = $time;
			session_set_cookie_params($time * 60);
		}
			
		/**Define HTML page title
		 *
		 *\param[in]	$title		HTML page title
		 *\return		Nothing	 
		 */
		public function SetTitle1($title)
		{
			$this->Templates->AssignVar("LoginTitle1", $title);
		}
		
		private function SetTitle2($title)
		{
			$this->Templates->AssignVar("LoginTitle2", $title);
		}
		
		/**Define authentification file prefix (including a sub directory)
		 *
		 *\param[in]	$AuthFileDirectory		Authentification file directory (i.e. "auth")
		 *\return		Nothing	 
		 */
		public function SetAuthFileDirectory($AuthFileDirectory)
		{
			$this->AuthFileDirectory = $AuthFileDirectory;
		}

		/**Retrieve current user CUser instance.
		 *
		 *\return		Current CUser instance (null if not connected).	 
		 */
		public function GetUser()
		{
			return $this->User;
		}
		
		/**Constructor
		 *
		 *\param[in]	AuthFileDirectory		Authentification file directory (i.e. "./auth_", "./auth/users_")
		 *\param[in]	$title		HTML page title
		 *\return		Nothing		 
		 */
		public function __construct($AuthFileDirectory = "auth", $title="Login")
		{
			$this->ClientBrowser = new \Harmony\CClientBrowser();
			$this->PrefixAuthFiles = $AuthFileDirectory;
			$this->Templates = new  \Harmony\CTemplate(HARMONY_INCLUDE.'/usermgt/templates/');
			$this->Templates->AddTemplate('edit', 'edit.tpl');
			$this->Templates->AddTemplate('toolbar', 'toolbar.tpl');
			$this->Templates->AddTemplate('register_link', 'register_link.tpl');
			$this->Templates->AddTemplate('register', 'register.tpl');
			//$this->Templates->AddTemplate('navigator', 'navigator.tpl');
			$this->Templates->AddTemplate('header', 'header.tpl');
			$this->Templates->AddTemplate('footer', 'footer.tpl');
			$this->Templates->AddTemplate('toptoolbar', 'toptoolbar.tpl');
			$this->Templates->AddTemplate('adminbtn', 'adminbtn.tpl');
			$this->Templates->AddTemplate('admin', 'admin.tpl');
			$this->Templates->AddTemplate('admin_menu', 'admin_menu.tpl');
			$this->Templates->AddTemplate('admin_edituser', 'admin_edituser.tpl');
			
			$LoginJS = file_get_contents(HARMONY_INCLUDE."/usermgt/templates/login.js");
			$this->Templates->AssignVar("TITLE", "Login");
			$this->Templates->AssignVar("LoginJS", $LoginJS);
			$this->SetSessionExpiration(20);
			$this->SetTitle1($title);
		}
		
		public function __destruct()
		{

		}
				
		public function AddUser($nickname, $user_data)
		{
			$first_letter = strtolower(substr($nickname, 0, 1));
			$filename = sprintf($this->AuthFileDirectory.'/auth_'.$first_letter.".php");

			$hFile = null;
			
			try {
				if (! file_exists($filename)) {
					$this->CreateAuthFiles($first_letter);
				}

				if ($hFile = fopen($filename, "a")) {
					fwrite($hFile, $user_data);
					fclose($hFile);
				}
			}
			catch (CException $e) {
			    print($e->DisplayExceptionAndDie());
			}		
		}
		
		private function GetAuthFile($first_letter) { return sprintf($this->AuthFileDirectory.'/auth_'.$first_letter.".php"); }
		
		public function CreateAuthFiles($first_letter)
		{
			$hFile = null;
			$filename = $this->GetAuthFile($first_letter);
			
			try {

				if (!file_exists($this->AuthFileDirectory))
					mkdir($this->AuthFileDirectory, '0777', true);
				
				if (! file_exists($filename)) {
					if ($hFile = fopen($filename, "w")) {
						fwrite($hFile, '# <?php exit()?>'."\n");
						fwrite($hFile, '# Format:'."\n");
						fwrite($hFile, '# user|MD5password|level|email|surname|name|valid'."\n\n");
						fclose($hFile);
					}
				}
			}
			catch (CException $e) {
			    print($e->DisplayExceptionAndDie());
			}
		}
		
		private function FindUser($nickname)
		{
			$first_letter = strtolower(substr($nickname, 0, 1));
			$filename = $this->GetAuthFile($first_letter);
			$hFile = null;
			
			try {
				if (! file_exists($filename)) {
					//throw new CException("File ".$filename." does not exist.", "1");
					return false;
				}
				else {
						$users = file_get_contents($filename);
						
						$pos = strpos ($users, "\n".$nickname);

						if (false !== $pos) {
							$pos2 = strpos ($users, "\n", $pos+1);
							$data = substr($users, $pos + 1, $pos2 - $pos - 1);
							$user = new CUser($data, $filename, $pos + 1, $pos2);
							return $user;
						}
						else return false;
				}
			}
			catch (CException $e) {
			    print($e->DisplayExceptionAndDie());
			}		
		}

		private function WriteUser($Password, $PasswordConfirm, $Email, $Surname, $Name)
		{
			if ($Password == $PasswordConfirm) {
				$old_string = $this->User->GetUserString();
				$new_string = CUser::GetNewUserString($this->User->GetNickname(), $Password, $this->User->GetUserLevel(), $Email, $Surname, $Name, $this->User->IsValid());
				
				$first_letter = strtolower(substr($this->User->GetNickname(), 0, 1));
				$filename = sprintf($this->PrefixAuthFiles.'/auth_'.$first_letter.".php");
				$users = file_get_contents($filename);
				$users = str_replace($old_string, $new_string, $users);
				file_put_contents($filename, $users);

                // Reload it !
				$this->FindUser($this->User->GetNickname());

			}
			//else die("Mots de passe diff�rents [$Password] vs [$PasswordConfirm]");
		}

		
		public function DisplayLogin($mandatory)
		{
			$this->Templates->Display('header');
			$this->Templates->Display('login');
			$this->Templates->Display('footer');
			if ($mandatory) die();
			return false;
		}
		
		public function DisplayRegister($mandatory)
		{
			$this->Templates->Display('header');
			$this->Templates->Display('register');
			$this->Templates->Display('footer');
			if ($mandatory) die();
			return false;
		}
		
		public function Logout()
		{
			if(session_id() != '')
				session_destroy();
		}

		public function DisplayEdit($mandatory)
		{
			if (isset($_SESSION["LoginNickname"])) {
				$this->IsConnected = true;
				$this->User = $this->FindUser($_SESSION["LoginNickname"]);
				$this->User->ReadInfos();
				$this->Templates->AssignVar("LoginNickname", $this->User->GetNickname());
				$this->Templates->AssignVar("LoginName", $this->User->GetName());
				$this->Templates->AssignVar("LoginSurname", $this->User->GetSurname());
				$this->Templates->AssignVar("LoginEmail", $this->User->GetEmail());
			
				$this->Templates->Display('header');
				$this->Templates->Display('edit');
				$this->Templates->Display('footer');
				die();
			}
			else return $this->DisplayLogin($mandatory);
		}

		/**Main function
		 *
		 *\param[in]	$mandatory		true if login is mandatory
		 *\return		true if successfully log in, false if user is not authentified.		 
		 **/		 		 		 		 		
		public function Login($mandatory=false, $Path='/', $ConfigurationFile='login_config.xml', $CodePage='ISO-8859-1')
		{
			require_once("module.version");
				
			$this->UseConfigurationFile($ConfigurationFile, $Path, $CodePage);
			$this->IsConnected = false;
				
			if (session_id() == '') session_start();

			$this->BaseScript .= '?'. $_SERVER['QUERY_STRING'];

			if ($this->Dictionary === null) $this->SetLanguage("auto");
			
			if ($this->CSS !== null)
				$this->Templates->AssignVar("LoginCSS", $this->CSS);
			else {
				//Loading default style...
				$file = new CHTMLFile(HARMONY_INCLUDE."/usermgt/templates/login.css");
				$this->Templates->AssignVar("LoginCSS", '<style>'.$file->GetHtml().'</style>');
			}
					
			global $module_infos;
			$this->Templates->AssignVar("LoginBoxLogo",			$this->Logo);
			$this->Templates->AssignVar("lblPrevious",	 		$this->Dictionary->Translate("previous"));
			$this->Templates->AssignVar("lblNext", 				$this->Dictionary->Translate("next"));
			$this->Templates->AssignVar("lblRegister",			str_replace(" ", "&nbsp;", $this->Dictionary->Translate("register")));
			$this->Templates->AssignVar("lblNickname",			str_replace(" ", "&nbsp;", $this->Dictionary->Translate("username")));
			$this->Templates->AssignVar("lblName",				str_replace(" ", "&nbsp;", $this->Dictionary->Translate("name")));
			$this->Templates->AssignVar("lblSurname",			str_replace(" ", "&nbsp;", $this->Dictionary->Translate("surname")));
			$this->Templates->AssignVar("lblPassword",			str_replace(" ", "&nbsp;", $this->Dictionary->Translate("password")));
			$this->Templates->AssignVar("lblSubmit",			str_replace(" ", "&nbsp;", $this->Dictionary->Translate("submit")));
			$this->Templates->AssignVar("LoginCharset",			$this->Dictionary->Translate("#charset"));
			$this->Templates->AssignVar("LoginLanguage",		$this->Dictionary->GetLanguage());
			$this->Templates->AssignVar("lblConfirmPassword",	str_replace(" ", "&nbsp;", $this->Dictionary->Translate("confirm_password")));
			$this->Templates->AssignVar("lblEmail",				$this->Dictionary->Translate("email"));
			$this->Templates->AssignVar("lblLoginEditProfile",	str_replace(" ", "&nbsp;", $this->Dictionary->Translate("edit_profile")));
			$this->Templates->AssignVar("lblActivate",	$this->Dictionary->Translate("active"));
			$this->Templates->AssignVar("lblAdmin",	$this->Dictionary->Translate("administrator"));
				
			
			
			
			
			$this->Templates->AssignVar("LoginDisconnect",		str_replace(" ", "&nbsp;", $this->Dictionary->Translate("disconnect")));
			$this->Templates->AssignVar("lblIndex", 			str_replace(" ", "&nbsp;", $this->Dictionary->Translate("index")));
			$this->Templates->AssignVar("lblCancel", 			str_replace(" ", "&nbsp;", $this->Dictionary->Translate("cancel")));
			$this->Templates->AssignVar("LoginBaseScript", 		$this->BaseScript);
			$this->Templates->AssignVar("LoginRootScript", 		$this->RootScript);
			$this->Templates->AssignVar("ModuleName", 			$module_infos['name']);
			$this->Templates->AssignVar("ModuleVersion", 		$module_infos['version']);
			$this->Templates->AssignVar("CSSStyle", 			$this->CSSStyle);
			$this->Templates->AssignVar("HarmonyPath", 			HARMONY_INCLUDE);
			$this->Templates->AssignVar("lblCurrentPassword",		$this->Dictionary->Translate("current_password"));
			$this->Templates->AssignVar("lblNewPassword",		$this->Dictionary->Translate("new_password"));
				
			
			$action =  \Harmony\CHTTPServer::GetVar('LoginAction');
			
			if ($action == '') $action = "connect";
			if ($this->UsePHPBB) {
				$this->IsConnected = $this->PHPBBForum->CurrentUser->IsConnected();

				if ($this->IsConnected && $action != "reset" && $action != "edit" && $action != "edit_valid" && $action != "admin") {

					$_SESSION['LoginDate'] = time();
					$this->User = $this->PHPBBForum->CurrentUser;
					return $this->IsConnected;
				}
				else {
				    $this->Templates->AddTemplate("login", "pbpbb_login.tpl");
					$this->SessionID = $this->PHPBBForum->CurrentUser->GetSessionSID();
				    $this->Templates->AssignVar("UserSID", $this->SessionID);
  				}

			}
			else {
							$this->Templates->AddTemplate('login', 'login.tpl');
				if($this->CanRegister) $this->Templates->AssignVar("LoginSubscribe", $this->Templates->GetHtml('register_link'));
	
				$this->SessionID = session_id();
	
				if (isset($_SESSION["LoginNickname"]) && $action != "reset" && $action != "edit" && $action != "edit_valid" && $action != "admin") {
					$_SESSION['LoginDate'] = time();
					$this->User = $this->FindUser($_SESSION["LoginNickname"]);
					$this->User->ReadInfos();
					$this->IsConnected = true;
					return true;
				}
				elseif ($mandatory == false) {// Si pas obligatoire de se loguer,
					$this->IsConnected = false;
					if ($action != 'login') 
						return false;
				}
			}
			
			switch ($action) {
				case "login":
					$message = "";
                    $user = \Harmony\CHTTPServer::Getvar('LoginNickname');

                    $password = \Harmony\CHTTPServer::Getvar('LoginPassword');
					$this->NewConnection = true;

					if ($this->UsePHPBB) {
	                    $viewonline = (\Harmony\CHTTPServer::Getvar('viewonline')==0?'1':'0');
	                    $autologin = \Harmony\CHTTPServer::Getvar('autologin');

				    	if (!$this->PHPBBForum->Login($user, $password, $viewonline, $autologin, $this->SessionID)) {
							$message .= $this->Dictionary->Translate("login_invalid");
							return false;
						}
						else {
							$this->User = $this->PHPBBForum->CurrentUser;
							$_SESSION['LoginNickname'] = $this->User->GetNickname();
							$this->IsConnected = true;
							return true;
						}
					}
					else {
						$user = $this->FindUser($user);
						
						if ($user !== false) {
							$user->ReadInfos();
							
							if ($user->IsValid()) {
								if ($user->CheckPassword($_POST["LoginPassword"])) {
									$message .= $this->Dictionary->Translate("login_ok");
									
									//save user nickname in cookie
									try {
										$_SESSION['LoginDate'] = time();
										$_SESSION['LoginNickname'] = stripslashes($_POST["LoginNickname"]);
									}
									catch(CException $e) {
									    print($e->DisplayExceptionAndDie());
									}
										
									session_write_close();
									$this->User = $user;
									$this->IsConnected = true;
									return true;
								}
								else {
									$message .= $this->Dictionary->Translate("login_invalid");
								}
							}
							else {
								$message .= $this->Dictionary->Translate("login_disableduser");
							}
						}
						else {
							$message .= $this->Dictionary->Translate("login_invalid");
						}
					}				
				
					$this->Templates->AssignVar("LoginMessage", htmlentities($message));
					$this->DisplayLogin($mandatory);				
					return false;
				case 'reset':
					if ($this->UsePHPBB) $this->PHPBBForum->Logout();
					else $this->Logout();
					//break;	// need to continue
				case "connect":
                    $this->SetTitle2($this->Dictionary->Translate("connection"));
                    
					if ($this->UsePHPBB) {
						$this->DisplayLogin($mandatory);
					}
					else {
						$this->IsConnected = false;
						//print_r ($_COOKIE);

						if (isset($_COOKIE['LoginNickname'])) {
							$this->Templates->AssignVar("LoginNickname", $_COOKIE['LoginNickname']);
							return $this->DisplayLogin($mandatory);				
						}
						else $this->DisplayLogin($mandatory);
					}
					break;
				case  "create":
					break;
				case  "edit":
					$this->DisplayEdit($mandatory);
					break;
				case "edit_valid":
					if (isset($_SESSION["LoginNickname"])) {
						$this->IsConnected = true;
						$_SESSION['LoginDate'] = time();
						$this->User = $this->FindUser($_SESSION["LoginNickname"]);
						$this->User->ReadInfos();
						$Password = \Harmony\CHTTPServer::GetVar('LoginPassword');
						$PasswordConfirm = \Harmony\CHTTPServer::GetVar('LoginPassword2');
						$LoginOldPassword = \Harmony\CHTTPServer::GetVar('LoginOldPassword'); 
						$Email = \Harmony\CHTTPServer::GetVar('LoginEmail');
						$Surname = \Harmony\CHTTPServer::GetVar('LoginSurname');
						$Name = \Harmony\CHTTPServer::GetVar('LoginName');

						if ($Password == $PasswordConfirm) {
							$this->WriteUser($Password, $PasswordConfirm, $Email, $Surname, $Name);
							$this->Templates->AssignVar("LoginMessage", htmlentities($this->Dictionary->Translate("profile_saved")));
							return(true);
						}
						else {
							$this->Templates->AssignVar("LoginMessage", $this->Dictionary->Translate('password_doesnt_match'));
							
							$this->DisplayEdit($mandatory);
							return false;
						}
					}
					else return $this->DisplayLogin($mandatory);

					break;
				case "admin":
					if (isset($_SESSION["LoginNickname"])) {
						$this->IsConnected = true;
						$this->User = $this->FindUser($_SESSION["LoginNickname"]);
						
						$this->User->ReadInfos();
						if ($this->User->GetUserLevel()>0) {
							$this->Templates->AssignVar("LoginNavigator", $this->GetSmallToolbar());
							$Action2 = \Harmony\CHTTPServer::GetVar('LoginAction2');
							
							if ($Action2 !== null) {
								
								$message = '';
									
								switch($Action2) {
									case 'saveuser':
										$Nickname = \Harmony\CHTTPServer::GetVar('LoginNickname');

										$User = $this->FindUser($Nickname);
										if ($User !== false) {
											$User->ReadInfos();
											$Name = \Harmony\CHTTPServer::GetVar('LoginName');
											$Surname = \Harmony\CHTTPServer::GetVar('LoginSurname');
											$Password = \Harmony\CHTTPServer::GetVar('LoginPassword');
											$Email = \Harmony\CHTTPServer::GetVar('LoginEmail');
											$Level = (\Harmony\CHTTPServer::GetVar('LoginAdmin') == 'on'?1:0);
											$Valid = (\Harmony\CHTTPServer::GetVar('LoginActive') == 'on'?1:0);
																					
											if ($User->SaveUser($Nickname, $Password, $Level, $Email, $Surname, $Name, $Valid)) {
												$this->Templates->AssignVar("LoginMessage", $this->Dictionary->Translate("saveok"));
												
											}
										}
										else
											$this->Templates->AssignVar("LoginMessage", $this->Dictionary->Translate('login_usernotfound'));

										$this->Templates->AssignVar("LoginAdminContent", $this->Templates->GetHtml('admin_menu'));
										break;											
									case 'finduser':
										$user = $this->FindUser(\Harmony\CHTTPServer::GetVar('user_name'));
											
										if ($user === false) {
											$this->Templates->AssignVar("LoginMessage", $this->Dictionary->Translate("login_usernotfound", true));
											$this->Templates->AssignVar("LoginAdminContent", $this->Templates->GetHtml('admin_menu'));
										}
										else {	// Edit user informations
											$this->Templates->AssignVar("LoginNickname", $user->GetNickName());
											$this->Templates->AssignVar("LoginSurname", $user->GetSurName());
											$this->Templates->AssignVar("LoginName", $user->GetName());
											$this->Templates->AssignVar("LoginEmail", $user->GetEmail());
							
											$this->Templates->AssignVar("LoginEmail", $user->GetEmail());
											$this->Templates->AssignVar("LoginAdmin", ($user->GetUserLevel()==0?"":"checked"));
											$this->Templates->AssignVar("LoginActive", ($user->IsValid()?"checked":""));
											//
							
							
											$message .= $this->Templates->GetHtml('admin_edituser');
											$this->Templates->AssignVar("LoginAdminContent", $message);
										}
										break;
								}
							
							}
							else
								$this->Templates->AssignVar("LoginAdminContent", $this->Templates->GetHtml('admin_menu'));
							
							$this->Templates->Display('admin');
							die();
							}
							else $this->DisplayLogin($mandatory);
						}
					break;
				case 'logout':
						$this->IsConnected = false;
						if ($this->UsePHPBB) $this->PHPBBForum->Logout();
						else $this->Logout();
					break;
				case 'register':
						$this->IsConnected = false;
						$this->DisplayRegister($mandatory);
						return false;
					break;
				case 'register_valid':
					$Nickname = stripslashes($_POST["LoginNickname"]);
					if (false === $this->FindUser(stripslashes($Nickname))) {
						// The user does not exist, create it.
						$user_data = CUser::GetNewUserString($Nickname, $_POST["LoginPassword"], 0, $_POST["LoginEmail"], $_POST["LoginSurname"], $_POST["LoginName"], !$this->NeedValidation);
						
						$this->AddUser($Nickname, $user_data);

						if (! $this->NeedValidation) {
							$this->User = $this->FindUser($Nickname);
							if ($this->User !== false) {
								$this->User->ReadInfos();
								$_SESSION['LoginNickname'] = $Nickname;
								$this->IsConnected = true;
								return true;
							}
							else { die($Nickname);
								return false; }
						}
						else {
						    // an administrator must validate this account
							$this->Templates->AssignVar('LoginMessage', htmlentities($this->Dictionary->Translate('login_inactive')));
						    $this->DisplayLogin($mandatory);
							return false;
						}
					}
					else {
						$message = sprintf($this->Dictionary->Translate("user_exist"), stripslashes($_POST["LoginNickname"]));
						$this->Templates->AssignVar("LoginMessage", htmlentities($message));
						$this->Templates->Display('register');
						if ($mandatory) die();
						return false;
					}
						//if ($mandatory) die();
					break;
				default:
					if ($mandatory) die();
					return false;
					break;
			}

			
			return true;
		}
		
		/**Get login date.
		 *
		 *\return		login date or null if no date is registered.		 
		 **/		 		 		 		 		
		public function GetLoginDate()
		{
			if (isset($_SESSION["LoginDate"])) return '+'.$_SESSION['LoginDate'];
			else return null;
		}
	}
?>