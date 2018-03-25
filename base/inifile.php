<?php  namespace Harmony;
if (! defined('HARMONY_INCLUDE')) die("Harmony Library is not loaded !");
/**\brief INI File management (configuration file)
 *\ingroup base
 */
	class CIniFile
	{
		var $m_Filename = "";
		var $m_Data = array();
		var $m_Directory;
	
		public function __construct($Directory, $Filename, $CreateIt=false)
		{
			$this->m_Filename = $Filename;
			$this->m_Directory = $Directory;
			
			if (!$CreateIt) {
				$this->ReadFile();
			}
			else {
				if ($hFile = fopen($Directory.'/'.$Filename, 'w+')) fclose($hFile);
			}
		}
		
		public function CreateFile()
		{
			if (! file_exists($this->m_Filename)) {
				if ($FILE=fopen($this->m_Directory.'/'.$this->m_Filename, "w+")) {

					foreach($this->m_Data as $SectionName => $SectionVars) {
						fwrite($FILE, '['.$SectionName.']'."\r\n");

						foreach($this->m_Data[$SectionName] as $key => $value)
							fwrite($FILE, $key.'='.$value."\r\n");

						fwrite($FILE, "\r\n");
					}

					fclose($FILE);
				}
				else die("Can't create file ".$this->m_Filename." !");
			}
			else die("File &gt; $this->m_Filename &lt; exists !");
		}
		
		public function ReadFile()
		{
			try
			{
				if (file_exists($this->m_Directory.'/'.$this->m_Filename)) {
					$temp = file_get_contents($this->m_Directory.'/'.$this->m_Filename);

					$templines = explode("\n", $temp);
					$CurrentSection = "UNKNOWN";
					
					foreach($templines as $value) {
						$buffer = ltrim(str_replace("\r", "", $value));
						
						if ((substr($buffer, 0, 1) == '[') && (substr($buffer, strlen($buffer)-1, 1) == ']')) {
							$CurrentSection = substr($buffer, 1, strlen($buffer)-2);
							$this->m_Data[$CurrentSection] = array();
						}
						else {
							if (substr($buffer, 0, 1) != ";") {
								$pos = strpos($buffer, "=");
								if ($pos != false) {
									$var_name = substr($buffer, 0, $pos);
									$var_value = substr($buffer, $pos+1);
									$this->m_Data[$CurrentSection][$var_name] = $var_value;
								}
							}
						}
					}
				}
				else throw new CException("File [$this->m_Directory/$this->m_Filename] doesn't exist !");
			}
			catch (CException $e)
			{
				$e->DisplayException();
			}
		}
		
		function GetSectionVars($SectionName)
		{
			if (array_key_exists($SectionName, $this->m_Data)) return $this->m_Data[$SectionName];
			else return array();
		}
	
		public function GetVar($SectionName, $VarName)
		{
			try
			{
				if (array_key_exists($SectionName, $this->m_Data)) {
					if (array_key_exists($VarName, $this->m_Data[$SectionName])) return $this->m_Data[$SectionName][$VarName];
					else throw new CException("Variable [$SectionName/$VarName] does not exist in [$this->m_Filename] !");
				}
				else throw new CException("Section [$SectionName] does not exist in [$this->m_Filename] !");
			}
			catch (CException $e)
			{
				$e->DisplayException();
				return null;
			}
		}
		
		public function SetVar($SectionName, $VarName, $Value)
		{
			$this->m_Data[$SectionName][$VarName] = str_replace("\n", "<br>", $Value);
		}
	}
?>
