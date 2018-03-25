<?php  namespace Harmony;
if (! defined('HARMONY_INCLUDE')) die("Harmony Library is not loaded !");
/**\brief HTTP Server informations
 *\ingroup base
 */
class CHTTPServer
{
	public static function GetName()			{ return CHTTPServer::GetServerVar('SERVER_NAME'); }
	public static function GetPort()			{ return CHTTPServer::GetServerVar('SERVER_PORT'); }
	public static function GetSoftware()		{ return CHTTPServer::GetServerVar('SERVER_SOFTWARE'); }
	public static function GetDocumentRoot()	{ return CHTTPServer::GetServerVar('DOCUMENT_ROOT'); }
	public static function GetScriptName()		{ return CHTTPServer::GetServerVar('SCRIPT_FILENAME'); }
	public static function GetRequestURI()		{ return CHTTPServer::GetServerVar('REQUEST_URI'); }
	
	public static function GetServerVar($name)
	{
		if (isset($_SERVER[$name])) return $_SERVER[$name];
		else return null;
	}
	
	
	public static function GetClientIP()
	{
		return CHTTPServer::GetServerVar('REMOTE_ADDR').':'.CHTTPServer::GetServerVar('REMOTE_PORT');
	}
	
	public static function GetClientLanguage()
	{
		$tmp = explode(";", CHTTPServer::GetServerVar('HTTP_ACCEPT_LANGUAGE'));
		
		return $tmp[0];
	}
	
	private static function GetPOSTGETVar($value)
	{
		ob_start();
		print_r($value);
		$ret = ob_get_contents();
		ob_end_clean();
		return $ret;
	}
	
	public static function PrintGETVar()
	{
		CHTTPServer::PrintArrayVar($_GET, "GET");
	}

	public static function PrintPOSTVar()
	{
		CHTTPServer::PrintArrayVar($_POST, "POST");
	}

	private static function PrintArrayVar($var, $name)
	{
		print('<br><table border="0" cellspacing="0" cellpadding="2" style="font-size: 10pt; text-align: left; border: none;">');
		print('<colgroup><col width="150"><col width="450"></colgroup>');
		printf('<caption>%s</caption>', $name);
		printf('<tr style="background-color: navy; color: white;"><th>%s</th><th>%s</th></tr>', "Key", "Value");

		$i=0;
		foreach($var as $key => $value) {
			$i++;
			printf('<tr style="background-color: %s;"><td>%s</td><td>%s</td></tr>', ($i%2==1?'silver':'white'), $key, CHTTPServer::GetPOSTGETVar($value));
		}

		print('</table><br>');
	
	}
	
	/***************************************************************************
	 *
	 *
	 **************************************************************************/	 	 	
	public static function GetVar($name)
	{
		if (isset($_POST[$name])) return $_POST[$name];
		elseif (isset($_GET[$name])) return $_GET[$name];
		else return null;
	}

	public static function GetScriptPath()
	{
		$ret = null;
		
		if (file_exists(CHTTPServer::GetServerVar('SCRIPT_FILENAME'))) {
			$pos = strrpos(CHTTPServer::GetServerVar('SCRIPT_FILENAME'), '/');
			if ($pos !== false) {
				$ret = substr(CHTTPServer::GetServerVar('SCRIPT_FILENAME'), 0, $pos+1);
			}
		}
		else $ret = CHTTPServer::GetServerVar('SCRIPT_FILENAME');
		
		return $ret;
	}

	public static function GetReferer()
	{
		return CHTTPServer::GetServerVar('HTTP_REFERER');
	}

	public static function GetScriptURL()
	{
		if (CHTTPServer::GetServerVar('SERVER_PORT') != '80')
			return ('http://'.CHTTPServer::GetServerVar('SERVER_NAME').':'.CHTTPServer::GetServerVar('SERVER_PORT').CHTTPServer::GetServerVar('SCRIPT_NAME').'?'.CHTTPServer::GetServerVar('QUERY_STRING'));
		else
			return ('http://'.CHTTPServer::GetServerVar('SERVER_NAME').CHTTPServer::GetServerVar('SCRIPT_NAME').'?'.CHTTPServer::GetServerVar('QUERY_STRING'));
	}

	public static function GetScriptDomain()
	{
		$file_url = CHTTPServer::GetServerVar('REQUEST_URI');

		if (substr($file_url, strlen($file_url)-1, 1) == '/') {
			$ret = CHTTPServer::GetServerVar('REQUEST_URI');
		}
		else {
			$pos = strrpos(CHTTPServer::GetServerVar('REQUEST_URI'), "/");
			$ret = substr(CHTTPServer::GetServerVar('REQUEST_URI'), 0, $pos+1);
		}
		
		if (CHTTPServer::GetServerVar('SERVER_PORT') != '80') $ret = ':'.CHTTPServer::GetServerVar('SERVER_PORT').$ret;
		
		return 'http://'.CHTTPServer::GetServerVar('SERVER_NAME').$ret;
	}
}


class CSystemInfo
{
	public static function GetPhpVersion()
	{
		return (phpversion());
	}

	public static function GetPhpConfiguration()
	{
		ob_start();
		phpinfo(INFO_CONFIGURATION);
		$info = ob_get_contents();
		ob_end_clean();
		return ($info);
	}

	public static function GetPhpLogo()
	{
		return sprintf('<img src="%s?=%s" alt="Logo PHP !" />', CHTTPServer::GetServerVar('PHP_SELF'), php_logo_guid());
	}

	public static function GetPhpModules()
	{
		ob_start();
		phpinfo(INFO_MODULES);
		$info = ob_get_contents();
		ob_end_clean();
		return ($info);
	}

	public static function GetPhpExtensionVersion($extension)
	{
		//assert($extension!='');
		return (phpversion($extension));
	}


}
/*
Mozilla/5.0 (Windows; U; Windows NT 5.1; fr; rv:1.8.1.8) Gecko/20071008 Firefox/2.0.0.8 Array ( [browser_name_regex] => ^mozilla/5\.0 (windows; .; windows nt 5\.1; .*; rv:1\.8.*) gecko/.*$ [browser_name_pattern] => Mozilla/5.0 (Windows; ?; Windows NT 5.1; *; rv:1.8*) Gecko/* [parent] => Mozilla 1.8 [platform] => WinXP [browser] => Mozilla [version] => 1.8 [majorver] => 1 [minorver] => 8 [css] => 2 [frames] => 1 [iframes] => 1 [tables] => 1 [cookies] => 1 [backgroundsounds] => [vbscript] => [javascript] => 1 [javaapplets] => 1 [activexcontrols] => [cdf] => [aol] => [beta] => 1 [win16] => [crawler] => [stripper] => [wap] => [netclr] => )



Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; .NET CLR 1.1.4322; .NET CLR 2.0.50727) Array ( [browser_name_regex] => ^.*$ [browser_name_pattern] => * [browser] => Default Browser [css] => 0 [frames] => [iframes] => [tables] => 1 [cookies] => [backgroundsounds] => [vbscript] => [javascript] => [javaapplets] => [activexcontrols] => [cdf] => [aol] => [beta] => [win16] => [crawler] => [stripper] => [wap] => [netclr] => )

Mozilla/5.0 (Macintosh; U; *Mac OS X; *) AppleWebKit/* (*) Pandora/2.*]

Win16=false
Win32=false
Win64=false



Browser="DefaultProperties"
Version=0
MajorVer=0
MinorVer=0
Platform=unknown
Alpha=false
Beta=false
Win16=false
Win32=false
Win64=false
Frames=false
IFrames=false
Tables=false
Cookies=false
BackgroundSounds=false
AuthenticodeUpdate=
CDF=false
VBScript=false
JavaApplets=false
JavaScript=false
ActiveXControls=false
Stripper=false
isBanned=false
WAP=false
isMobileDevice=false
isSyndicationReader=false
Crawler=false
CSS=0
CssVersion=0
supportsCSS=false
AOL=false
aolVersion=0
netCLR=false
ClrVersion=0

*/
class CClientBrowser
{
	public $FullName = null;
	public $Name = null;
	public $Version = "unknown";
	public $OperatingSystem = null;
	protected $BrowserInfos = "";
	
	public static $WINDOWS='Microsoft Windows';
	public static $WINDOWSNT='Microsoft Windows NT';
	public static $LINUX='Linux';
	public static $UNIX='Unix';
	public static $MACINTOSH='Mac OS';
	
	private function ConvertOSName()
	{
		switch ($this->OperatingSystem)
		{
			case CClientBrowser::$WINDOWS.' 4.0':
				$this->OperatingSystem = CClientBrowser::$WINDOWS. ' 95';
				break;
			case CClientBrowser::$WINDOWS.' 4.1':
				$this->OperatingSystem = CClientBrowser::$WINDOWS. ' 98';
				break;
			case CClientBrowser::$WINDOWSNT.' 5.0':
				$this->OperatingSystem = CClientBrowser::$WINDOWS. ' 2000';
				break;
			case CClientBrowser::$WINDOWSNT.' 5.1':
				$this->OperatingSystem = CClientBrowser::$WINDOWS. ' XP';
				break;
			case CClientBrowser::$WINDOWSNT.' 6.0':
				$this->OperatingSystem = CClientBrowser::$WINDOWS. ' Vista';
				break;
			case CClientBrowser::$WINDOWSNT.' 7.0':
				$this->OperatingSystem = CClientBrowser::$WINDOWS. ' Seven';
				break;
			default:
				$this->OperatingSystem = $this->OperatingSystem;
				break;
		}
	}
	
	private function ConvertBrowserName()
	{
		switch($this->Name)
		{
			case "msie":
				$this->FullName = "Microsoft Internet Explorer ".$this->Version;
				break;
			case "firefox":
				$this->FullName = "Mozilla Firefox ".$this->Version;
				break;
			case "chrome":
				$this->FullName = "Google Chrome ".$this->Version;
				break;
			default:				
				$this->FullName = $this->Name.' '.$this->Version;
				break;
		}		
	}


	private function SetInfo(&$array, $varname, $default=null)
	{
		$this->BrowserInfos[$varname] = $default;
		
		if (isset($array[$varname]))
			if ($array[$varname] != null)
				$this->m_BrowserInfos[$varname] = $array[$varname];
	}
	
	public function __construct()
	{
		if (isset($_SERVER['HTTP_USER_AGENT'])) {
			$tmp = strtolower(CHTTPServer::GetServerVar('HTTP_USER_AGENT'));
		    $tmp = str_replace(";", " ", $tmp);
		    $tmp = str_replace("(", " ", $tmp);
		    $tmp = str_replace(")", " ", $tmp);
		    $tmp = str_replace("/", " ", $tmp);
			$UserAgent = explode(' ', $tmp);
			$this->OperatingSystem = CHTTPServer::GetServerVar('HTTP_USER_AGENT');
			
			foreach($UserAgent as $key => $value)
			{
				if ($this->Name == null)
				{
					if(strpos($value, 'chrome') !== false) {
					 	$this->Name = "chrome";
						$this->Version = $UserAgent[$key+1];
					}
					elseif(strpos($value, 'opera') !== false) {
						$this->Name = "opera";
						$this->Version = $UserAgent[$key+1];
					}
					elseif(strpos($value, 'safari') !== false) {
						$this->Name = "safari";
						$this->Version = substr($value, 7);
					}
					elseif(strpos($value, 'msie') !== false) {
						$this->Name = "msie";
						$this->Version = str_replace(";", "", $UserAgent[$key+1]);
					}
					elseif(strpos($value, 'firefox') !== false) {
						$this->Name = "firefox";
						$this->Version = $UserAgent[$key+1];
					}
				}
	
				if($value == 'windows') {
					$this->OperatingSystem = CClientBrowser::$WINDOWS;
					if ($UserAgent[$key+1] == "nt") {
						$this->OperatingSystem = str_replace(";", "", CClientBrowser::$WINDOWSNT.' '.$UserAgent[$key+2]);
					}
				}
				elseif(strpos($value, 'linux') !== false)
					$this->OperatingSystem = CClientBrowser::$LINUX;
				elseif(strpos($value, 'unix') !== false)
					$this->OperatingSystem = CClientBrowser::$UNIX;
			}
			
			$this->ConvertOSName();
			$this->ConvertBrowserName();
		}
		else {
			// Pas de User agent, on suppose du coup qu'il s'agit d'une exécution serveur (tâche CRON par exemple)
			if (isset($_SERVER['SERVER_SOFTWARE'])) {
				$value = $_SERVER['SERVER_SOFTWARE'];
			
				if(strpos($value, 'apache') !== false) {
					$this->Name = "Apache";
					$this->Version = '';
				}
				if(strpos($value, 'iis') !== false) {
					$this->Name = "IIS";
					$this->Version = '';
				}
				else {
					$this->Name = "unknown";
					$this->Version = 0;
				}
			}
			else {
				$this->Name = "unknow";
				$this->Version = 'unknow';
			}
				
			$this->OperatingSystem = "unknow";
		}
	}
}


?>
