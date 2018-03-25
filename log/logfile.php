<?php namespace Harmony\log;

date_default_timezone_set("Europe/Paris");

global $_LOG;

class CLogFile extends \Harmony\CBaseFile
{
	public static $FatalError=0;
	public static $Error=1;
	public static $Warning=2;
	public static $Notice=3;
	private $LogCount = 0;
	protected $FileCreated=false;
	protected $FullFilename = "";
	
	public function __construct($filename, $DontCreate=false)
	{
		$this->FullFilename = $filename.'_'.date("Ymd_His").'.xml';

		if (! $DontCreate) {
			parent::__construct($this->FullFilename);
			$this->FileCreated = true;
		}
		else $this->FileCreated = false;	
	}

	public function AddLog($message, $level=3)
	{
		if (!$this->FileCreated) {
			parent::__construct($this->FullFilename);
			$this->FileCreated = true;
		}
		
		$this->LogCount++;
		
		if ($this->LogCount == 1)
			$this->SetContent("<?xml version=\"1.0\" encoding=\"utf-8\"?>\r\n<log>\r\n");
		
		$message = str_replace(array("<", ">", '"', '&nbsp;'), array("&gt;", "&lt;", "'", ' '), $message);
		$content = sprintf("\t<message id=\"%s\" level=\"%s\" message=\"%s\" />\r\n", time(), $level, $message);
		
		$this->Append($content);
	}
	
	public function __destruct()
	{
		if ($this->LogCount != 0)
			$this->Append("</log>");
	}
}
?>
