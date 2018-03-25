<?php  namespace Harmony;
if (! defined('HARMONY_INCLUDE')) die("Harmony Library is not loaded !");

class CFilePermissions
{
	protected $UnixFormat = '';

	public function __construct($Name)
	{
	/*	$perms = fileperms($Name);
		
		// Owner
		$this->UnixFormat .= (($perms & self::$OwnerRead) ? 'r' : '-');
		$this->UnixFormat .= (($perms & self::$OwnerWrite) ? 'w' : '-');
		$this->UnixFormat .= (($perms & self::$OwnerExecute) ?
		            (($perms & self::$OwnerSocket) ? 's' : 'x' ) :
		            (($perms & self::$OwnerSocket) ? 'S' : '-'));
		$this->UnixFormat .= '-';
		// Group
		$this->UnixFormat .= (($perms & self::$GroupRead) ? 'r' : '-');
		$this->UnixFormat .= (($perms & self::$GroupWrite) ? 'w' : '-');
		$this->UnixFormat .= (($perms & self::$GroupExecute) ?
		            (($perms & self::$GroupSocket) ? 's' : 'x' ) :
		            (($perms & self::$GroupSocket) ? 'S' : '-'));
		
		$this->UnixFormat .= '-';
		// All
		$this->UnixFormat .= (($perms & self::$AllRead) ? 'r' : '-');
		$this->UnixFormat .= (($perms & self::$AllWrite) ? 'w' : '-');
		$this->UnixFormat .= (($perms & self::$AllExecute) ?
		            (($perms & self::$AllSocket) ? 't' : 'x' ) :
		            (($perms & self::$AllSocket) ? 'T' : '-'));*/
	}

	public function GetUnixPermissionString()
	{
		return $this->UnixFormat;
	}


}

class CFileProperties
{
	public $AccessTime = null;
	public $ModificationTime = null;
	public $OwnerID = null;
	public $Permissions = null;
}

class CFileSystemEntry
{
	protected $Type = null; //File, Directory, Drive
	protected $Name = "";
	
	public function __construct($Name="")
	{
		$this->Type = new CEnum('File', 'Directory', 'Drive');
		$this->Name = $Name;
		
		if (file_exists($this->Name))
		{
			if (is_file($this->Name)) $this->Type->SetValue('File');
			elseif (is_dir($this->Name)) $this->Type->SetValue('Directory');
		}
	}
	
	public function GetProperties()
	{
		$Properties = new CFileProperties();
 		$Properties->AccessTime = fileatime($this->Name);
 		$Properties->ModificationTime = filemtime($this->Name);
 		$Properties->OwnerID = fileowner($this->Name);
 		$Properties->Permissions = new CFilePermissions($this->Name);

		print ($this->Name.' ('.$this->Type->GetValue().')'.$Properties->Permissions->UnixFormat.'<br>');

	
	}
}

class CDirectory extends CFileSystemEntry
{
	private $Path = "";
	private $hDir = null;
	private $Entries;
	
	public function __construct($path)
	{
		parent::__construct($path);
		
		$this->Type->SetValue('Directory');
		$this->Open($path);
	}
	
	public function __destruct()
	{
		$this->Close();
	}
	
	public function Open($path=null)
	{
		if ($path !== null) $this->Path = $path;
		$this->hDir = opendir($this->Path);
	}

	public function Close()
	{
		closedir($this->hDir);
	}
	
	public function GetEntries($recursive=false)
	{
	    while (false !== ($object = readdir($this->hDir))) {
	        if ($object != "." && $object != "..") {
	        	$obj = new CFileSystemEntry($this->Path.'/'.$object);
	        	
	        	if ($obj->Type->GetValue() == 'Directory')
					$obj = new CDirectory($this->Path.'/'.$object);
		        	if ($recursive) $obj->GetEntries(true);

	        	$Entries[] = $obj;
	        }
	    }
	}
}
?>
