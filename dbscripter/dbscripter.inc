<?php namespace Harmony\dbscripter;

class CDBScripter extends CBaseObject
{

	protected $m_database = null;
	
	public function __construct(&$database)
	{
		$this->m_database  = &$database;
	}

	public function ScriptTable($table_name)
	{
		$Fields = $this->m_database->GetDBTableFields($table_name);
		$Script = '';
		foreach($Fields as $Field) {
			$type = '';
			$field_string = "`" . $Field['Field'] . "`";
			$field_string.= ' ' . $Field['Type'];
			
			if ($Field['Null'] == 'NO') {
				$field_string .= ' not null';
				if ($Field['Default'] !== null) {
				$field_string .= " default '" . $Field['Default'] . "'";
					/*if (
						($Field['NUMERIC'] == '1' && $Field['Default'] == '0')
						|| ($Field['TYPE'] == 'datetime' && $Field['Default'] == '0000-00-00 00:00:00')
						|| ($Field['TYPE'] == 'date' && $Field['DEFAULT'] == '0000-00-00')
						|| ($Field['TYPE'] == 'timestamp' && $Field['DEFAULT'] == '0000-00-00 00:00:00')
						) {
						$field_string .= '';
					}
					else {
						if ($Field['NUMERIC'] == '1') $field_string .= " default " . $Field['DEFAULT'];
						else $field_string .= " default '" . $Field['DEFAULT'] . "'";
					
					}*/
				}
				
			}
			else $field_string .= ' null';
			
			print_r($Field);
			$script .= sprintf("%s,\n", $field_string);
			
			
			print '<br><br>=>'.$field_string;
			//print_r($Field);
			print("<br><br>");
		
		}
		//sqlfile = new CSQLFile();
		
	}
}


?>
