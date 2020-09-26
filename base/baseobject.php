<?php  namespace Harmony;
if (! defined('HARMONY_INCLUDE')) die("Harmony Library is not loaded !");
/**\brief Base object
 *\ingroup base
 */ 
class CBaseObject
{
	/**Dump object*/
	public function DumpObject()
	{
		$classname = get_class($this);
		$parent_classname = get_parent_class ($classname);
		$ret = sprintf('<class id="%s" parent="%s">'."\n", $classname, $parent_classname);

		$methods = get_class_methods($this);

		$ret .= sprintf("\t<methods>\n");

		foreach($methods as $method) {
			$ret .= sprintf("\t\t".'<method name="%s" />'."\n", $method);
		}
		
		$ret .= sprintf("\t</methods>\n");

		$ret .= sprintf("\t<variables>\n");
				ob_start();
				print_r($this);
				$code = ob_get_contents();
				ob_end_clean();
		$ret .= $this->__ParsePrintR($code);
		$ret .= sprintf("\t</variables>\n");

		
		$ret .= "</class>";
		
		printf('<textarea cols="80" rows="20">%s</textarea>', $ret);
		
		
		print '<br><br><br>';
		//print_r($this);		
		die();
	}

	/**
	 * 
	 * @param string $Filename
	 */
	public function SerializeInFile($Filename)
	{
		$File = new CBaseFile($Filename);
		$Content = serialize($this);
		$File->SetContent($Content);	
	}
	
	/**
	 * 
	 * @param string $Filename
	 * @return mixed
	 */
	public function UnSerializeFromFile($Filename)
	{
		$File = new CBaseFile($Filename);
		return unserialize($File->GetContent());
	}
	
	/**
	 * 
	 * @param string $text
	 * @return string
	 */
	private function __ParsePrintR($text)
	{
		$ret = "";
		$index = 0;
		$strpos = strpos($text, "\n(", 0);

		//$vars_string = substr($text, $strpos, strlen($text) - $strpos - 7);
		
		$vars_string = $text;

		$strpos = strpos($vars_string, "\n    [", 0);
		$strpos = 1;

		while ($strpos>0) {
			$strpos = strpos($vars_string, "\n    [", $strpos+1);
		
			if ($strpos>0) {
				$strpos2 = strpos($vars_string, "\n", $strpos+1);
				
				if ($strpos2 > 0) {
					$var_string = substr($vars_string, $strpos, $strpos2 - $strpos);
					//
					$declaration = substr($var_string, 6, strpos($var_string, ']') - 6);
						$strpos3 = strpos($declaration, ':');
						$var_name = substr($declaration, 0, $strpos3);
						$var_right = substr($declaration, $strpos3+1);
					
					if (substr($vars_string, $strpos2, 10) == "\n        (") {
						$var_value = "object";
					}
					else {
						$var_value = substr($var_string, strlen($declaration)+11);
					}

					$ret .= sprintf("\t\t".'<variable name="%s" visibility="%s" value="%s" />'."\n", $var_name, $var_right, $var_value);
				}
			}
		}

		return $ret;
	}
	
	/**
	 * 
	 * @return string
	 */
	function __toString()
	{
		return get_class($this);
	}
}

?>
