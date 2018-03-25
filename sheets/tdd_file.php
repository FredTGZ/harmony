<?php if (! defined('HARMONY_INCLUDE')) die("Harmony Library is not loaded !");
	//require_once("../inifile.inc");
	

	/**\brief ff
	 *\ingroup sheets
	 *
	 */ 
	class CTemplateDataDefinitions extends CIniFile
	{
		private $BaseTemplates= null;
		private $m_FieldNames=array();
		private $m_FieldCategories=array();
		private $m_FieldTypes=array();
		private $m_FieldParams=array();
		private $m_FieldRows=array();
		
		private $m_Formulas=array();
		private $m_Infos=array();
		private $Constants = array();
		//private $m_Data=array();
		
		
		public static function ReadTDDHeader($BaseTemplates, $DocumentTemplatesDirectory, $Filename)
		{
			$ret = array();
			if ($hFile = fopen($DocumentTemplatesDirectory.'/'.$Filename, "r")) {
				
				while(count($ret)<6) {
				
					$buffer = fgets($hFile);
					if ($buffer == '') break;
					else {
						$start = strtoupper(substr($buffer, 0, 3));
						switch($start)
						{
							case 'TIT':
								$ret['TITLE'] = substr($buffer, 6);
								break;
							case 'IMA':
								$ret['IMAGE'] = $DocumentTemplatesDirectory.'/'.substr($buffer, 6);
								break;
							case 'AUT':
								$ret['AUTHOR'] = substr($buffer, 7);
								break;
							case 'VER':
								$ret['VERSION'] = substr($buffer, 8);
								break;
							case 'TPL':
								$ret['TPL'] = substr($buffer, 4);
								break;
							case 'DES':
								$ret['DESCRIPTION'] = substr($buffer, 12);
								break;
						}
					}
				}
			}
			else print("Can't open file $Filename !");
			
			return $ret;
		}

		public function __construct($BaseTemplates, $templatedir, $Filename)
		{
			parent::__construct($templatedir, $Filename);
			$this->BaseTemplates = $BaseTemplates;
			
			$this->m_Infos = $this->GetSectionVars('INFOS');
			$this->m_Formulas = $this->GetSectionVars('FORMULAS');
			$this->Constants = $this->GetSectionVars('CONSTANTS');
			
			foreach($this->Constants as $key => $value) {
				if(substr($value,0,6)== 'array(')
					eval('$this->Constants[$key] = '.$value.';');
			}
			
			//print_r($this->Constants);
			
			foreach($this->GetSectionVars('FIELDS') as $key => $value) {
				
				$VarType = trim($value);
				$tmp = explode(":", $VarType);
				if(count($tmp) != 4) print("Invalid TDD file !");
				$VarCategory = $tmp[0];
				$VarRow = $tmp[1];
				$VarName = $tmp[2];
				$VarType = $tmp[3];
				$VarParams = array();
				
				$pos=strpos($VarType, "(");
	
				if ($pos >= 1) {
					$VarType2 = substr($VarType, 0, $pos);
					$buffer = substr($VarType, strlen($VarType2)+1, strlen($VarType)-strlen($VarType2)-2);
					$VarParams = explode(",", $buffer);
					$VarParams = array_map("trim", $VarParams);
					$VarType = $VarType2;
				}
				
				$this->m_FieldCategories[$key] = $VarCategory;
				$this->m_FieldNames[$key] = $VarName;
				$this->m_FieldTypes[$key] = $VarType;
				$this->m_FieldRows[$key] = $VarRow;
				$this->m_FieldParams[$key] = $VarParams;
			}
		}
		
		
		public function GetFieldNames()
		{
			return $this->m_FieldNames;			
		}
		
		public function GetFieldCategories()
		{
			return $this->m_FieldCategories;			
		}

		public function GetFieldRow($VarName)
		{
			if (array_key_exists($VarName, $this->m_FieldRows)) return $this->m_FieldRows[$VarName];
			else return null;
		}

		public function GetFieldName($VarName)
		{
			if (array_key_exists($VarName, $this->m_FieldNames)) {
				 return $this->m_FieldNames[$VarName];
			}
			else return null;
		}
		
		public function GetFormula($VarName)
		{
			if (array_key_exists($VarName, $this->m_Formulas)) {
				$formula = $this->m_Formulas[$VarName];
				if ($formula[0] == "'") $formula = substr($formula, 1, strlen($formula)-2);
				return $formula;
			}
			else die($VarName);//return null;
		}


		public function GetLogo()
		{
			//if (array_key_exists('IMAGE', $this->m_Infos)) return $this->m_Directory.'/'.$this->m_Infos['IMAGE'];
			if (array_key_exists('IMAGE', $this->m_Infos)) return $this->m_Infos['IMAGE'];
			else return null;
		}
	
		public function GetVersion()
		{
			if (array_key_exists('VERSION', $this->m_Infos)) return $this->m_Infos['VERSION'];
			else return null;
		}
	
		public function GetTitle()
		{
			if (array_key_exists('TITLE', $this->m_Infos)) return $this->m_Infos['TITLE'];
			else return null;
		}
	
		public function GetAuthor()
		{
			if (array_key_exists('AUTHOR', $this->m_Infos)) return $this->m_Infos['AUTHOR'];
			else return null;
		}

		public function GetDescription()
		{
			if (array_key_exists('DESCRIPTION', $this->m_Infos)) return $this->m_Infos['DESCRIPTION'];
			else return null;
		}

		public function GetFieldCategory($VarName)
		{
			if (array_key_exists($VarName, $this->m_FieldCategories)) return $this->m_FieldCategories[$VarName];
			else return null;
		}
	
		private function FormulaConcat()
		{
			$args = func_get_args();
			$ret = "";
			foreach($args as $arg) $ret .= $arg;
			return $ret;
		}
	
		private function FormulaRoundInf($value)
		{
			return floor($value);
		}
		
		private function FormulaRoundSup($value)
		{
			return ceil($value);
		}
	
		private function FormulaRound($value)
		{
			return round($value);
		}
		
		private function FormulaConst($value)
		{
			return ($value);
		}
	
		private function ReplaceDataInFormula($FormulaName, $Formula, &$fields)
		{
			$ret = "";
			$pos=-1;
			$pos2=0;
			$pos3=0;
			
			while ($pos !== False) {
				$pos3 = $pos2;
				$pos = strpos($Formula, "{", $pos+1);
	
				if ($pos !== False) {
					$pos2 = strpos($Formula, "}", $pos+1);
					if ($pos3 == 0) $text = substr($Formula, $pos3, $pos);
					elseif ($pos>$pos3) $text = substr($Formula, $pos3+1, $pos-$pos3-1);
	
					$field_name = substr($Formula, $pos+1, $pos2-$pos-1);
					$ret .= $text;
					$ret .= "'".$fields[$field_name]."'";
				}
				else {
					$ret .= ($pos3!=0?substr($Formula, $pos3+1):$Formula);
				}
			}
	
			$ret = str_replace('CONCAT(', '$this->FormulaConcat(', $ret);
			$ret = str_replace('CONST(', '$this->FormulaConst(', $ret);
			$ret = str_replace('ROUND.INF(', '$this->FormulaRoundInf(', $ret);
			$ret = str_replace('ROUND.SUP(', '$this->FormulaRoundSup(', $ret);
			$ret = str_replace('ROUND(', '$this->FormulaRound(', $ret);
			$ret = str_replace('GET(', '$this->GetConstantValue(', $ret);
			//die($ret);
			$value = "";
			if (FALSE === eval('$value=('.$ret.');')) return "ERREUR=>".$ret;
			$fields[$FormulaName] = $value;
			return $value;
			
		}
		
		public function GetConstantValue($value, $index=-1)
		{
			if ($index == -1) $ret = $this->Constants[$value];
			else
				$ret = $this->Constants[$value][$index];
				
			return $ret;
		}
		
		public function CalculateFormulas($fields)
		{
			$ret = array();
			$this->m_Data = $fields;
			foreach($this->m_Formulas as $FormulaName => $Formula) {
				
				$ret[$FormulaName] = $this->ReplaceDataInFormula($FormulaName, $Formula, $this->m_Data);
				
			}
			
			return $ret;
		}
		
		public function GetFieldFieldType($VarName)
		{
			if (array_key_exists($VarName, $this->m_FieldTypes))
				return $this->m_FieldTypes[$VarName];
			return null;
		}
		
		
		public function GetFieldDefaultValue($VarName)
		{
			if (array_key_exists($VarName, $this->m_FieldTypes) && array_key_exists($VarName, $this->m_FieldParams)) {
				$ControlName= 'FIELD_'.$VarName;
				$VarType = $this->m_FieldTypes[$VarName];
				$VarParams = $this->m_FieldParams[$VarName];		

				switch($VarType) {
					case 'NULL':
						return;
					case 'INT':
					case 'FLOAT':
						return 0;
					case 'BOOL':
						return 0;
					case 'TEXT':
					case 'PASSWORD':
						return '';
					case 'MAIL':
						return 'myname@mydomain.com';
					case 'URL':
						return 'http://';
					case 'LIST':
					case 'OPTION':
					case 'LISTINC':
					case 'OPTIONINC':
						return $VarParams[0];
					case 'HIDDEN':
						return '';
					case 'DATE':	// Format YYYY-MM-DD
						return date('Y-m-d');
					case 'DATETIME':// Format YYYY-MM-DD HH:MM:SS
						return date('Y-m-d H:m:s');
					case 'NONE':
						return '';
					default:
						break;
				}
			}
		}
	
		public function DisplayInputControl($VarName, $VarValue)
		{
			if (array_key_exists($VarName, $this->m_FieldTypes) && array_key_exists($VarName, $this->m_FieldParams)) {
				$ControlName= 'FIELD_'.$VarName;
				$VarType = $this->m_FieldTypes[$VarName];
				$VarParams = $this->m_FieldParams[$VarName];		
				$len=strlen($VarValue);
					//print("<BR>".$VarName.' => Type='.$VarType." Parameters=");
					//print_r($VarParams);

				switch($VarType)
				{
					case 'NULL':
						return '';
					case 'INT':
						return '<INPUT TYPE="TEXT" NAME="'.$ControlName.'" VALUE="'.$VarValue.'" MAXLEN="5" style="width: 40px;">';
					case 'FLOAT':
						return '<INPUT TYPE="TEXT" NAME="'.$ControlName.'" VALUE="'.$VarValue.'">';
					case 'BOOL':
						return sprintf('<input type="checkbox" name="%s" %s>', $ControlName, ($VarValue?'checked':''));
						break;
					case 'TEXT':
						if ($VarParams[1] <= 50) return sprintf('<INPUT TYPE="TEXT" NAME="%s" VALUE="%s" MAXLEN="%u" style="width: %upx;">',
							$ControlName, $VarValue, $VarParams[1], $VarParams[1] * 8); 
						else {
							$Cols = 50;
							$Rows = ceil($VarParams[1]/$Cols);
							return(sprintf('<TEXTAREA NAME="%s" COLS="%u" ROWS="%u">%s</TEXTAREA>',
								$ControlName, $Cols, $Rows, str_replace("<br>", "\n", $VarValue)));
						}
					case 'PASSWORD':
						return '<INPUT TYPE="PASSWORD" NAME="'.$ControlName.'" VALUE="'.$VarValue.'">';
					case 'MAIL':
						return '<INPUT TYPE="TEXT" NAME="'.$ControlName.'" VALUE="'.$VarValue.'">';
					case 'URL':
						return '<INPUT TYPE="TEXT" NAME="'.$ControlName.'" VALUE="'.$VarValue.'">';
					case 'LIST':
						$ret = '<SELECT NAME="'.$ControlName.'">';
						
						foreach($VarParams as $value) {
							if ($value == $VarValue) $ret .= '<OPTION VALUE="'.$value.'" SELECTED>'.$value.'</OPTION>';
							else $ret .= '<OPTION VALUE="'.$value.'">'.$value.'</OPTION>';
						}
	
						$ret .= '</SELECT>';
	
						return $ret;
					case 'OPTION':
						$ret = '';
						$i=0;					
						foreach($VarParams as $value) {
							$i++;
							if ($value == $VarValue) $ret .= '<INPUT TYPE="RADIO" NAME="'.$ControlName.'" VALUE="'.$value.'" CHECKED>'.$value;
							else $ret .= '<INPUT TYPE="RADIO" NAME="'.$ControlName.'" VALUE="'.$value.'">'.$value;
						}
	
						return $ret;
					case 'LISTINC':
						$ret = '<SELECT NAME="'.$ControlName.'">';
						
						for ($i=$VarParams[0]; $i<=$VarParams[1]; $i+=$VarParams[2]) {
							if ($i==$VarValue) $ret .= '<OPTION VALUE="'.$i.'" SELECTED>'.$i.'</OPTION>';
							else $ret .= '<OPTION VALUE="'.$i.'">'.$i.'</OPTION>';
						}
						$ret .= '</SELECT>';
	
						return $ret;
					case 'OPTIONINC':
						$ret = '';
						
						for ($i=$VarParams[0]; $i<=$VarParams[1]; $i+=$VarParams[2]) {
							if ($i == $VarValue) $ret .= '<INPUT TYPE="RADIO" NAME="'.$ControlName.'" VALUE="'.$i.'" CHECKED>'.$i.'&nbsp;';
							else $ret .= '<INPUT TYPE="RADIO" NAME="'.$ControlName.'" VALUE="'.$i.'">'.$i.'&nbsp;';
						}
						$ret .= '</SELECT>';
						return $ret;
					case 'NONE':
						return '';
					case 'HIDDEN':
						return '<INPUT TYPE="TEXT" NAME="'.$ControlName.'" VALUE="'.$VarValue.'">';
					case 'DATE':	// Format YYYY-MM-DD
						return '<INPUT TYPE="TEXT" NAME="'.$ControlName.'" VALUE="'.$VarValue.'">';
					case 'DATETIME':// Format YYYY-MM-DDTHH:MM:SS
						return '<INPUT TYPE="TEXT" NAME="'.$ControlName.'" VALUE="'.$VarValue.'">';
					default:
						break;
				}
				
				return true;
				
			}
			else print("Unknown variable &gt; $VarName &lt; !");
		}
		
		public function CheckVar($VarName, $VarValue)
		{
			if (array_key_exists($VarName, $this->m_FieldTypes) && array_key_exists($VarName, $this->m_FieldParams)) {
				$VarType = $this->m_FieldTypes[$VarName];
				$VarParams = $this->m_FieldParams[$VarName];		
				$len=strlen($VarValue);
				switch($VarType)
				{
					case 'NULL':
						return true;
					case 'INT':
					case 'FLOAT':
						if(! is_numeric($VarValue)) return false;
						break;
					case 'BOOL':
						if ($VarValue != "0" && $VarValue != "1") return "Not a boolean";
						break;
					case 'TEXT':
					case 'PASSWORD':
	//					if (!is_string($VarValue)) return false;
						if ($len < $VarParams[0]) return false;
						if ($len > $VarParams[1]) return false;
						break;
					case 'MAIL':
						if (!is_string($VarValue)) return false;
						if ($len>$VarParams[0]) return false;
						$pos = strpos($VarValue, "@");
						if ($pos === false) return false;
						$pos = strpos($VarValue, ".", $pos+1);
						if ($pos === false) return false;
						break;
					case 'URL':
						if ((strtolower(substr($VarValue, 0, 7)) != 'http://') && (strtolower(substr($VarValue, 0, 6)) != 'ftp://')) return false;
						if ($len>$VarParams[0]) return false;
						break;
					case 'LIST':
					case 'OPTION':
						foreach($VarParams as $comp)
							if ($comp == $VarValue) return true;
	
						return false;
						break;
					case 'LISTINC':
					case 'OPTIONINC':
						if (! is_numeric($VarValue)) return false;
						if (((string)((int) $VarValue)) !== $VarValue) return false;
						if ($VarValue<$VarParams[0] || $VarValue>$VarParams[1]) return false;
						if (($VarValue-$VarParams[0]) % $VarParams[2] != 0) return false;
						break;
					case 'NONE':
						return true;
					case 'HIDDEN':
						return true;
					case 'DATE':	// Format YYYY-MM-DD
						$DateParam = explode("-", $VarValue);
						if (count($DateParam) != 3) return false;
						if ((strlen($DateParam[0]) != 4) || (strlen($DateParam[1]) != 2) || (strlen($DateParam[2]) != 2)) return false;
						if (!is_numeric($DateParam[0]) || !is_numeric($DateParam[1]) || !is_numeric($DateParam[2])) return false;
						return checkdate ($DateParam[1], $DateParam[2], $DateParam[0]);
						break;
					case 'DATETIME':// Format YYYY-MM-DD HH:MM:SS
						$DateParam = explode("-", $VarValue);
						if ($len != 19) return false;
						if (count($DateParam) != 3) return false;
						$TimeParam = explode(":", substr($DateParam[2], 3));
						$DateParam[2] = substr($DateParam[2], 0, 2);
	
						if ((strlen($DateParam[0]) != 4) || (strlen($DateParam[1]) != 2) || (strlen($DateParam[2]) != 2)) return false;
						if (!is_numeric($DateParam[0]) || !is_numeric($DateParam[1]) || !is_numeric($DateParam[2])) return false;
						if (!checkdate ($DateParam[1], $DateParam[2], $DateParam[0])) return false;
	
						if (!is_numeric($TimeParam[0]) || !is_numeric($TimeParam[1]) || !is_numeric($TimeParam[2])) return false;				
						if ($TimeParam[0]<0 || $TimeParam[0]>24) return false;
						if ($TimeParam[1]<0 || $TimeParam[1]>59) return false;
						if ($TimeParam[2]<0 || $TimeParam[2]>59) return false;
						return true;
					default:
						break;
				}
				
				return true;
				
			}
			else print("Unknown variable &gt; $VarName &lt; !");
		}
		
		public function CreateBlankTDA($dir, $filename)
		{
			$myfile = new CIniFile($dir, $filename, true);

			$myfile->SetVar('FILES', 'TDD', $this->m_Filename);
			$myfile->SetVar('FILES', 'TPL', $this->GetVar('INFOS', 'TPL'));

			foreach($this->GetSectionVars('FIELDS') as $key => $value) {
				$myfile->SetVar('DATA', $key, $this->GetFieldDefaultValue($key));
			}
			
			$myfile->CreateFile();
			
		}
		
	}

?>
