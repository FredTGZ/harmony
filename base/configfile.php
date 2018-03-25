<?php  namespace Harmony;
if (! defined('HARMONY_INCLUDE')) die("Harmony Library is not loaded !");
/**\brief XML Config File access
 *\ingroup base
 */ 
class CConfigFile extends CXMLFile
{
	protected $Variables = array();
	
	public function __construct($filename = "", $codepage='ISO-8859-1')
	{
		parent::__construct($filename, $codepage);
		if ($filename != "") $this->GetVars();
	}
	
	protected function GetVars()
	{
		foreach($this->XMLData as $key => $value) {
			switch($value['tag']) {
				case 'CONFIGURATION':
					break;
				case 'VARIABLE':
					$VarName = $value['attributes']['NAME'];
					$VarValue = $value['attributes']['VALUE'];
					$this->Variables[$VarName] = $VarValue;
					break;
				default:
					break;		
			}
		}
	}
	
	public function GetVarArray()
	{
		return $this->Variables;
	}
	
	public function GetConfigValue($varname, $default=null)
	{
		try {
			if (array_key_exists($varname, $this->Variables))
				return $this->Variables[$varname];
			else if ($default === null) throw new CException($varname. " is not defined !");
			else return null;
		}
		catch (CException $e) {
		    print($e->DisplayException());
		    return false;
		}
	}
	
	public function SetConfigValue($varname, $value)
	{
		try {
			if (array_key_exists($varname, $this->Variables))
				$this->Variables[$varname] = $value;
			else throw new CException($varname. " is not defined !");
		}
		catch (CException $e) {
		    print($e->DisplayException());
		    return false;
		}
	}
	
	public function Write()
	{
		$this->DeleteData();
		$this->AddTag('configuration', null, null, "open");
		foreach($this->Variables as $key => $value)
			$this->AddTag('variable', '', array('name' => $key, 'value' => $value));
		$this->AddTag('configuration', null, null, "close");
		return parent::Write();
	}
}
?>
