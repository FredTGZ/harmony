<?php namespace Harmony\database;
 if (! defined('HARMONY_INCLUDE')) die("Harmony Library is not loaded !");
 use Harmony\CEnum;
 /** \brief Data edition formular
 * 
 * \ingroup database
 */    

class CDBForm extends CBaseRecordset
{
	private $FieldInfos = array();	
	
	private function GetIntegerMinMax(&$Field, $Unsigned, $Size)
	{
		$Field['JSType']->SetValue('integer');
		
		if ($Unsigned) {
			$Field['JSMin'] = 0;
			$Field['JSMax'] = pow(256, $Size);
		}
		else {
			$Field['JSMin'] = 1 - pow(128, $Size);
			$Field['JSMax'] = pow(128, $Size);
		}
	}
	
	private function GetDecimalMinMax(&$Field, $Unsigned, $Size)
	{
		$Sizes = explode(',', $Size);
		$Field['JSType']->SetValue('decimal');
		
		$Field['JSMax'] = pow(10, $Sizes[0]) - pow(0.1, $Sizes[1]);
		
		if ($Unsigned)
			$Field['JSMin'] = 0;
		else
			$Field['JSMin'] = - $Field['JSMax'];
	}
	
	public function __construct(&$Database, $Tablename)
	{
		parent::__construct($Database, $Tablename);
	
		foreach($this->Fields as $key => $Field) {
			$this->Fields[$key]['Unsigned'] = (count(explode(' ', $Field['Type']))>1);
			$pos = strpos($Field['Type'], '(');
			
			if ($pos>0) {
				$SQLType = substr($Field['Type'], 0, $pos);
				$SQLSize = substr($Field['Type'], $pos+1, strlen($Field['Type'])-$pos-2-($this->Fields[$key]['Unsigned']?9:0));
			}
			else {
				$SQLType = $Field['Type'];
				$SQLSize = 0;
			}
			
			$this->Fields[$key]['JSLabel'] = $Field['Field'];
			$this->Fields[$key]['JSType'] = new CEnum('date', 'datetime', 'string', 'text', 'integer', 'boolean', 'decimal');
			$unsigned = $this->Fields[$key]['Unsigned'];
			$this->Fields[$key]['JSSelect'] = null;
			
			switch($SQLType)
			{
				case 'numeric':
				case 'decimal':
					$this->GetDecimalMinMax($this->Fields[$key], $unsigned, $SQLSize);
					$this->Fields[$key]['JSType']->SetValue('decimal');
					break;
				case 'float':
					$this->GetIntegerMinMax($this->Fields[$key], false, 4);
					$this->Fields[$key]['JSType']->SetValue('decimal');
					break;
				case 'double':
				case 'real':
					$this->GetIntegerMinMax($this->Fields[$key], false, 8);
					$this->Fields[$key]['JSType']->SetValue('decimal');
					break;
				case 'tinyint':
					if ($SQLSize == 1) {
						$this->Fields[$key]['JSMin'] = 0;
						$this->Fields[$key]['JSMax'] = 1;
						$this->Fields[$key]['JSType']->SetValue('boolean');
					}
					else
						$this->GetIntegerMinMax($this->Fields[$key], $unsigned, 1);
					break;
				case 'smallint':
					$this->GetIntegerMinMax($this->Fields[$key], $unsigned, 2);
					break;
				case 'mediumint':
					$this->GetIntegerMinMax($this->Fields[$key], $unsigned, 3);
					break;
				case 'int':
					$this->GetIntegerMinMax($this->Fields[$key], $unsigned, 4);
					break;
				case 'bigint':
					$this->GetIntegerMinMax($this->Fields[$key], $unsigned, 8);
					break;
				case 'char':
				case 'varchar':
					$this->Fields[$key]['JSType']->SetValue('string');
					$this->Fields[$key]['JSMin'] = 0;
					$this->Fields[$key]['JSMax'] = $SQLSize;
					break;
				case 'text':
					$this->Fields[$key]['JSType']->SetValue('text');
					$this->Fields[$key]['JSMin'] = null;
					$this->Fields[$key]['JSMax'] = null;
				default:
					$this->Fields[$key]['JSMin'] = null;
					$this->Fields[$key]['JSMax'] = null;
					break;
			}
			
			$this->Fields[$key]['JSMin2'] = null;
			$this->Fields[$key]['JSMax2'] = null;
			$this->Fields[$key]['Value'] = null;
			$this->Fields[$key]['Hidden'] = false;
		}
	}
	
	public function SetFormFieldInfos($Name, $Label, $Min=null, $Max=null)
	{
		foreach($this->Fields as $key => $Field)
		{
			if (strtolower($Field['Field']) == strtolower($Name)) {
				$this->Fields[$key]['JSLabel'] = $Label;
				$this->Fields[$key]['JSMin2'] = $Min;
				$this->Fields[$key]['JSMax2'] = $Max;
				return true;
			}
		}
		
		return false;
	}
	
	public function HideFormField($Name, $Value)
	{
		foreach($this->Fields as $key => $Field)
			if (strtolower($Field['Field']) == strtolower($Name)) {
				$this->Fields[$key]['Hidden'] = true;
				$this->Fields[$key]['Value'] = $Value;
			}
	}
	public function SetFormFieldForeignKey(&$Database, $Name, $TableName, $FieldID, $FieldName, $Criteria="1=1", $Selected=null)
	{
		foreach($this->Fields as $key => $Field)
		{
			if (strtolower($Field['Field']) == strtolower($Name)) {
				$Recordset = new CRecordset($Database);
				$Query = sprintf('SELECT %s, %s FROM %s WHERE %s ORDER BY %s ASC', $FieldID, $FieldName, $TableName, $Criteria, $FieldName);
				$Select = sprintf('<select id="%s" name="%s">', $Name, $Name);
				if ($Recordset->Open($Query)) {
					while(!$Recordset->IsEOF()) {
						$Select .= sprintf('<option value="%s"%s>%s</option>',
							$Recordset->GetFieldValue($FieldID),
							($Recordset->GetFieldValue($FieldID)==$Selected?' selected':''),
							$Recordset->GetFieldValue($FieldName));
						$Recordset->MoveNext();
					}
				}
				else die($Database->GetLastError());
				$this->Fields[$key]['JSSelect'] = $Select.'</select>'; 
				return true;
			}
		}
		
		return false;
	}
	
	public function DisplayAddForm($Name, $Action, $ClassPrefix="")
	{
		$form = sprintf('<form id="%s" name="%s" method="POST" action="%s" class="%sTable"><table border="0" cellspacing="0" cellpadding="4">', $Name, $Name, $Action, $ClassPrefix);
		$Validate = "";
		
		foreach($this->Fields as $Field)
		{
			if ($Field['JSMin2'] != null) $Min = $Field['JSMin2'];
			else $Min = $Field['JSMin'];
			
			if ($Field['JSMax2'] != null) $Max = $Field['JSMax2'];
			else $Max = $Field['JSMax'];
				
//			print('<br />'.$Field['Field'].'('.$Field['JSType']->GetValue().', Min='.$Min.', Max='.$Max.')');
			
			if ($Field['Extra'] != 'auto_increment') {
				if ($Field['Hidden']) {
					$form .= sprintf('<input type="hidden" name="%s" value="%s" />', $Field['Field'], $Field['Value']);
				}
				else {
					$form .= sprintf('<tr class="%sRow"><td><label for="%s" class="%sLabel">%s</label></td><td>', $ClassPrefix, $Field['Field'], $ClassPrefix, $Field['JSLabel']);
					
					if ($Field['JSSelect'] != null) {
						$form .= $Field['JSSelect'];
					}
					else {
						switch($Field['JSType']->GetValue())
						{
							case 'date':
								break;
							case 'datetime':
								break;
							case 'string':
								$form .= sprintf('<input type="text" name="%s" id="%s" value="" maxlength="%u" class="%sInput" />',
								$Field['Field'], $Field['Field'], $Max, $ClassPrefix
								);
								$Validate .= sprintf("\nValidate &= CheckString('%s', '%s', '%u', '%u');", $Field['Field'], $Field['JSLabel'], 0, $Max);
								break;
							case 'text':
								$form .= sprintf('<textarea name="%s" id="%s" class="%sText">%s</textarea>', $Field['Field'], $Field['Field'], $ClassPrefix, $Field['Default']);
								break;
							case 'integer':
								if ($Field['Null'] == "NO" && ($Max - $Min)<=256) {
									//Affichage d'une liste déroulante -> pas de javascript
									$form .= sprintf('<select name="%s" id="%s" class="%sSelect">', $Field['Field'], $Field['Field'], $ClassPrefix);
									for($i=$Min; $i<=$Max; $i++)
										$form .= sprintf("<option%s>%u</option>", ($Field['Default']==$i?' selected':''), $i);
									$form .= '</select>';
								}
										elseif ($Field['Null'] == "NO") {
										//Saisie d'un nombre
						}
							else {
								// Saisie d'un nombre + champ caché pour définir si null
								}
								break;
								case 'boolean':
								$form .= sprintf('<input name="%s" id="%s" type="checkbox"%s class="%sText" />', $Field['Field'], $Field['Field'], ($Field['Default']==1?' checked':''), $ClassPrefix);
								break;
								case 'decimal':
										$form .= sprintf('<input type="text" name="%s" id="%s" value="" maxlength="%u" class="%sInput" />',
										$Field['Field'], $Field['Field'], $Max, $ClassPrefix
										);
												break;
						}
						
					}
					
					$form .= '</td></tr>';
						
				}
			}
		}
		$form .= '<tr><td colspan="2"><input type="button" value="OK" OnClick="Validate'.$Name.'();" /></td></tr></table></form>';
		$form .= '<script type="text/javascript">
				var ValidationMessage;
				
				function CheckString(ObjectID, Label, Min, Max)
				{
					var obj = document.getElementById(ObjectID);
					if (obj.value.length<Min) {
						ValidationMessage += "\nField ["+Label+"] has a minimum length of "+Min+".";
						return false;
					}

					if (obj.value.length>Max) {
						ValidationMessage += "\nField ["+Label+"] has a maximum length of "+Max+".";
						return false;
					}
				
					return true;
				}
				
				
				
				function Validate'.$Name.'() { var Validate = true;';
		$form .= $Validate;
		$form .= "\n".'if(!Validate) alert(ValidationMessage);
	else alert("OK");}</script>';
		
		print $form;
	}
}

/*class CDBForm
 {
protected $m_Fields = array();
protected $m_Databasename = '';
protected $m_Table = '';
protected $m_Database;
protected $m_Labels = array();

public function __construct(&$database, $databasename, $table)
{
$this->m_Fields = $database->GetDBTableFields($databasename, $table);
$this->m_Databasename = $databasename;
$this->m_Table = $table;
$this->m_Database = &$database;
}

public function DisplayEditForm($criteria, $labels=null)
{
$Query = "SELECT * FROM `" . $this->m_Table . "`".($criteria!=''?' WHERE '.$criteria:'');
	
$recordset = new CRecordset($this->m_Database);

if ($recordset->OpenRecordset($Query)) {
print_r($this->m_Fields);

print('<br><br><br><table border="1" cellspacing="2" cellpadding="1">');
$row_format = '<tr><td>%s</td><td>%s</td>';
$input = '';
$input_format = '';
$hidden_row = false;
$name = '';

foreach($this->m_Fields as $field_index => $field_info) {
$name = ($field_info['COMMENT']==''?$field_info['NAME']:$field_info['COMMENT']);
$hidden_row = false;
	
if ($field_info['PRIMARY_KEY'] == '1') {
$input_format = '<input type="text" disabled name="%s" value="%s">';
$hidden_row = true;
}
else {
switch($field_info['TYPE'])
{
case 'string':
$input_format = '<input type="text" name="%s" maxlength="'.$field_info['LEN'].'" value="%s">';
break;
case 'blob':
$input_format = '<textarea name="%s">%s</textarea>';
break;
default:
$input_format = '%s-%s';
break;

}
}

$input = sprintf($input_format, $field_info['NAME'], $recordset->GetFieldValue($field_info['NAME']));

printf($row_format, $name, $input);
	
}
print('</table>');
	
}

}
}*/


?>
