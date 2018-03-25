<?php
// '

require("quickformfield.php");


class QuickForm extends CBaseObject
{
	protected $Fields = array();
	protected $LabelClassName=null;
	protected $FieldClassName=array();
	protected $MandatorySymbol="*";
	protected $LabelFieldSeparator=':';
	const MANDATORY_FIELD=true;
	const NON_MANDATORY_FIELD=false;
	
	private function GetFieldClassName($FieldType)
	{
		if (array_key_exists($FieldType, $this->FieldClassName)) return $this->FieldClassName[$FieldType];
		else return null;
	}
	
	public function SetFieldClassName($FieldType, $ClassName)
	{
		$Type = new FieldType();
		$Type->SetValue($FieldType);
		$this->FieldClassName[$FieldType] = $ClassName;
	}
	
	public  function SetLabelClassName($ClassName)
	{
		$this->LabelClassName = $ClassName;
	}
	
	public function AddField($label, $name, $type, $value, $params=null, $mandatory=QuickFormField::NON_MANDATORY_FIELD)
	{
		$this->Fields[] = new QuickFormField($label, $name, $type, $value, $params, $mandatory);
	}
	
	private function GetJSFunctions()
	{
		$ret = "";
		$ret .= '		function ValidateText(id, label, min, max)
		{
			var field = document.getElementById(id);
			if (field.value.length<min) return "Field ["+label+"] size < " + min.toString();
			if (field.value.length>max) return "Field ["+label+"] size > " + max.toString();
			return "";
		}'."\r\n";
		
		$ret .= '		function ValidateEmail(id, label)
		{
			var field = document.getElementById(id);
			var re = /^(([^<>()[]\.,;:s@"]+(.[^<>()[]\.,;:s@"]+)*)|(".+"))@(([[0-9]{1,3}.[0-9]{1,3}.[0-9]{1,3}.[0-9]{1,3}])|(([a-zA-Z-0-9]+.)+[a-zA-Z]{2,}))$/;
			if (re.test(field.value)) return "";
			else return "Field ["+label+"] is not a valid email!";
		}';
		
		return $ret;
	}
	
	public function Display($TableID="", $DebugMode=false, $UseFormTag=true)
	{
		$Fields = '';
		$Javascript = $this->GetJSFunctions()."\r\n\r\n";
		$JavascriptGlobal = "function ValidateForm() { var tmp = \"\"; var test = \"\";\r\n\r\n";
		
		foreach($this->Fields as $Field) {
			$Javascript .= "\t".$Field->GetJavascript()."\r\n";
			
			$Fields .= $Field->Display($this->MandatorySymbol, $this->LabelFieldSeparator, $this->LabelClassName, $this->GetFieldClassName($Field->GetType()));
			$JavascriptGlobal .= sprintf("tmp=Validate_%s(); if (tmp != \"\") test+=tmp+\"\\r\\n\";\r\n", $Field->GetName());
		}
		$JavascriptGlobal .= " alert(test); }";
		print('<script type="text/javascript">'."\r\n".$Javascript.$JavascriptGlobal."</script>\r\n");
		if ($UseFormTag) print("<form>");
		print("<table cellspacing=\"0\" cellpadding=\"0\" id=\"$TableID\">\r\n".$Fields.'</table>');
		if ($UseFormTag) print('<input type="submit" OnClick="ValidateForm()" /></form>');
	}
	
	public function SetMandatorySymbol($Symbol) { $this->MandatorySymbol = $Symbol; }
	public function SetLabelFieldSeparator($Symbol) { $this->LabelFieldSeparator = $Symbol; }
}


?>