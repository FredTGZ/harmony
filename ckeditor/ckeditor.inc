<?php namespace Harmony\ckeditor;


class CKeditorToolbar extends CBaseObject
{
	private $Plugins = null;
	
	public function AddButton($Plugin)
	{
		$this->AddPlugin($Plugin);
	}
	
	public function AddPlugin($Plugin)
 	{
 		if ($this->Plugins === null) $this->Plugins = array();
 		$this->Plugins[] = $Plugin;
 	}

	public function AddSeparator()
 	{
 		$this->AddPlugin("-");
 	}
 	
	public function GetConfig()
	{
		if ($this->Plugins === null) return "'/'";
		else {
			$ret = "[";
			$count = count($this->Plugins);
			
			for($i=0; $i<$count; $i++) {
			
				$ret .= "'".$this->Plugins[$i]."'";
				if ($i<($count-1)) $ret .= ', ';
			}
			 
			return $ret.']';
		}
	}
}

class CKEditor extends CBaseObject
{
	private $InstanceName;
	private $BasePath = './ckeditor';
	private $Config = array();
	
	private $Toolbars = null;
	
	public function __construct($RelativePath, $instance)
 	{
		$this->BasePath = $RelativePath;
		$this->InstanceName = $instance;
	}

	public function GetHeaderScript()
	{
		$ret = sprintf('<script type="text/javascript">var CKEDITOR_BASEPATH = \'%s/\';</script>', $this->BasePath);
		$ret .= sprintf('<script type="text/javascript" src="%s/ckeditor.js"></script>', $this->BasePath);
	
		return $ret;
	}
	
	public function CreateHtml($Value='')
	{
		$this->SetToolbars();
		$ret = sprintf('<textarea id="%s">%s</textarea>', $this->InstanceName, $Value);
		$count = count($this->Config);
		$number = 0;
		

		if ($count>0) {
			$ret .= sprintf("<script type=\"text/javascript\">\nCKEDITOR.replace( '%s', \n\t{\n", $this->InstanceName);
			
			foreach($this->Config as $varname => $value) {
					$number++;
			        $ret .= sprintf("\t\t%s : %s%s\n", $varname, $value, (($number==$count)?'':','));
			
			}
			        
			$ret .= "\t});\n</script>"; 
		}
		
		$ret .= sprintf('<script type="text/javascript">CKEDITOR.replace( \'%s\' );</script>', $this->InstanceName);
		
		return($ret);
	}
	
	public function SetConfiguration($varname, $value, $object=false)
	{
		if ($object)
			$this->Config[$varname] = $value;
		else
			$this->Config[$varname] = "'".$value."'";
	}
	
	
	public function AddToolbar(CKeditorToolbar $toolbar)
	{
		if ($this->Toolbars === null) $this->Toolbars = array();
 		$this->Toolbars[] = $toolbar;
	}
	
	public function AddToolbarBreak()
	{
		$this->AddToolbar(new CKeditorToolbar());
	}
	
	private function SetToolbars()
	{
		$count = count($this->Toolbars);
		if ($count == 0) return;
		
		$code = "[\n";
	
		for ($i=0; $i<$count; $i++)
		{
			$code .= "\t\t\t" . $this->Toolbars[$i]->GetConfig();
			
			if ($i<($count-1)) $code .= ",\n";
			else $code . "\n";
		}
		
		$code .= "\t]";
		
		$this->SetConfiguration('toolbar', $code, true);
	}
	
	
}



?>
