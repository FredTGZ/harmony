<?php  namespace Harmony;
if (! defined('HARMONY_INCLUDE')) die("Harmony Library is not loaded !");

/**\brief Base file access
 *\ingroup base
 */ 
	class CBaseFile extends CBaseObject
	{
		protected $m_Filename = "";
		public static $TAB= "\t";
		public static $TAB2= "\t\t";
		public static $TAB3= "\t\t\t";
		public static $TAB4= "\t\t\t\t";
		public static $TAB5= "\t\t\t\t\t";
		public static $TAB6= "\t\t\t\t\t\t";
		public static $EOL= "\n";
		public static $RETURN= "\r";
		public static $SPACE= "\r";
		public static $HTMLSPACE= "&nbsp;";
		protected $m_FilePath = '';
		public $m_Header;
		public $hFile;
		
		protected $LastmodDate = null;
		protected $CreateDate = null;
		protected $LastAccessDate = null;
		protected $Size = null;

		protected $UnixPermissions = null;
	

		public static $OwnerRead = 0x0100;
		public static $OwnerWrite = 0x0080;
		public static $OwnerExecute = 0x0040;
		public static $OwnerSocket = 0x0800;
		public static $GroupRead = 0x0020;
		public static $GroupWrite = 0x0010;
		public static $GroupExecute = 0x0400;
		public static $GroupSocket = 0x0400;
		public static $AllRead = 0x0004;
		public static $AllWrite = 0x0002;
		public static $AllExecute = 0x0001;
		public static $AllSocket = 0x0200;

		public static $FilesizeFormatB = 1;
		public static $FilesizeFormatKB = 2;
		public static $FilesizeFormatMB = 3;
		public static $FilesizeFormatGB = 4;
		public static $FilesizeFormatTB = 5;
		
		
		public function __construct($Filename /**Filename*/)
		{
			$this->m_FilePath = \Harmony\CHTTPServer::GetScriptPath();
			$this->m_Filename = $this->m_FilePath . $Filename;
			
			if (file_exists($this->m_Filename)) {
				if ($this->hFile = fopen($this->m_Filename, "r")) {
					$header = fgets ($this->hFile);
					$this->m_Header = new CDocumentHeader($header);	
				}
			}
		}
		
		public static function GetExtension($filename)
		{
			$pos = strrpos($filename, '.');
			if (!$pos) return '';
			else return strtolower(substr($filename,$pos+1));
		}
		
		/**Close the file*/
		public function Close()
		{
			//assert($this->hFile != null);
		    if ($this->hFile != null)
			fclose($this->hFile);
		}
		
		/**Return the header (the first "line")*/
		public function GetHeader()
		{
			return $this->m_Header;
		}
		
		/**Return HTML version of you file. This function must be overloaded. */
		public function GetHTML()
		{
			die("This function must be renewed !");	
		}
		
		/**Return file content. */
		public function GetContent()
		{
			return file_get_contents ($this->m_Filename);
		}
		
		/**Set file content. */
		public function SetContent($content/**File content*/)
		{
			if ($hFile = fopen($this->m_Filename, "w+")) {
				fwrite($hFile, $content);
				fclose($hFile);
			}
		}
		
		/**Append to file content. */
		public function Append($content/**File content to append*/)
		{
			if ($hFile = fopen($this->m_Filename, "a+")) {
				fwrite($hFile, $content);
				fclose($hFile);
			}
		}

		/**Test if the file exist. */
		public function Exists()
		{
			return file_exists($this->m_Filename);
		}
		
		/**Return the file size. */
		public function GetSize($Format=1)
		{
			if ($this->Size === null)
				$this->Size = filesize($this->m_Filename);
			
			return round($this->Size / pow(1024, $Format-1), 2);
		}
		
		public function GetInformations()
		{
		
		}
		
		public function GetUnixPermissions()
		{
			if ($this->UnixPermissions === null) {
				$perms = fileperms($Name);
				
				// Owner
				$this->UnixPermissions = (($perms & self::$OwnerRead) ? 'r' : '-');
				$this->UnixPermissions .= (($perms & self::$OwnerWrite) ? 'w' : '-');
				$this->UnixPermissions .= (($perms & self::$OwnerExecute) ?
				            (($perms & self::$OwnerSocket) ? 's' : 'x' ) :
				            (($perms & self::$OwnerSocket) ? 'S' : '-'));
				$this->UnixPermissions .= '-';
				// Group
				$this->UnixPermissions .= (($perms & self::$GroupRead) ? 'r' : '-');
				$this->UnixPermissions .= (($perms & self::$GroupWrite) ? 'w' : '-');
				$this->UnixPermissions .= (($perms & self::$GroupExecute) ?
				            (($perms & self::$GroupSocket) ? 's' : 'x' ) :
				            (($perms & self::$GroupSocket) ? 'S' : '-'));
				
				$this->UnixPermissions .= '-';
				// All
				$this->UnixPermissions .= (($perms & self::$AllRead) ? 'r' : '-');
				$this->UnixPermissions .= (($perms & self::$AllWrite) ? 'w' : '-');
				$this->UnixPermissions .= (($perms & self::$AllExecute) ?
				            (($perms & self::$AllSocket) ? 't' : 'x' ) :
				            (($this->UnixPermissions & self::$AllSocket) ? 'T' : '-'));
			}
			
			return $this->UnixPermissions;
		}

		public function GetLastModificationDate($Format=null)
		{
			if ($this->LastmodDate === null) {
				$this->LastmodDate = filemtime($this->m_Filename);
			}
			
			if ($Format === null) return $this->LastmodDate;
			else return date($Format, $this->LastmodDate);
		}

		public function GetLastAccessDate($Format=null)
		{
			if ($this->LastAccessDate === null) {
				$this->LastAccessDate = fileatime($this->m_Filename);
			}
			
			if ($Format === null) return $this->LastAccessDate;
			else return date($Format, $this->LastAccessDate);
		}
	}

?>
