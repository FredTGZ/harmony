<?php namespace Harmony\dbforms;

class CDBSelect extends CFormControl
{
	public function __construct($name, $title, &$database, $table, $field_value, $field_name, $criteria=null, $default=null, $label_name=null, $class=null)
	{
		parent::__construct($name, $title, false, false, $class);

		if ($label_name !== null) {
			$this->m_Content .= "\n\r".'<script type="text/javascript" language="javascript">'."\r\n";
			$this->m_Content .= "\t".'function OnChange'.$name.'()'."\r\n";
			$this->m_Content .= "\t".'{'."\r\n";
			$this->m_Content .= "\t\t".'element = document.getElementById("'.$name.'");'."\r\n";
			$this->m_Content .= "\t\t".'target_element = document.getElementById("'.$label_name.'");'."\r\n";
			$this->m_Content .= "\t\t".'target_element.value = element.options[element.selectedIndex].innerHTML;'."\r\n";		
			$this->m_Content .= "\t".'}'."\r\n";
			$this->m_Content .= '</script>'."\r\n";
		}

		$this->m_Content .= sprintf('<select %s name="%s" id="%s"%s>', ($this->m_Class==null?'':'class="'.$this->m_Class.'"'), $name, $name,  ' onchange="OnChange'.$name.'()"');
		$default_text = '';
		$query = sprintf("SELECT `%s`, `%s` FROM `%s` %s", $field_value, $field_name, $table, ($criteria===null?'':' WHERE 1=1 AND '.$criteria.' '));
		$recordset = new CRecordset($database);
		
		if ($recordset->OpenRecordset($query)) {
		
			if (!$recordset->IsEOF()) $default_text = $recordset->GetFieldValue($field_name);
			
			while(!$recordset->IsEOF()) {
				$this->m_Content .= sprintf('<option %svalue="%s">%s</option>',
					($recordset->GetFieldValue($field_value)==$default?'selected':''),
					$recordset->GetFieldValue($field_value),
					$recordset->GetFieldValue($field_name));
					
				if ($recordset->GetFieldValue($field_value)==$default) {
					$default_text = $recordset->GetFieldValue($field_name);
				}
				
				$recordset->MoveNext();
			}
		}
		
		$this->m_Content .= sprintf("</select>");

		if ($label_name !== null) {
			$this->m_Content .= sprintf('<input type="hidden" id="%s" name="%s" value="%s">', $label_name, $label_name, $default_text);
		}

	}
}


?>
