<?php

class FieldType extends CEnum
{
	public function __construct() { parent::__construct("text", "hidden", "password", "email", "integer"); }
}

class QuickFormField
{
	protected $Label;
	protected $Name;
	protected $Type;
	protected $Value;
	protected $Mandatory=false;
	protected $Params = array();
	const MANDATORY_FIELD=true;
	const NON_MANDATORY_FIELD=false;
	

	public function __construct($label, $name, $type, $value, $params=null, $mandatory=QuickFormField::NON_MANDATORY_FIELD)
	{
		try {
			$this->Type = new FieldType();
			$this->Type->SetValue($type);

			if (is_array($params)) $this->Params = $params;
			else if($params != null) throw new CException("Bad parameter definition, not an array!");
			
			$this->Name = str_replace(' ', '', $name);
			$this->Label = $label;
			$this->SetValue($value);
		
			if (! $this->CheckParams()) throw new CException("Bad parameters!");
			$this->Mandatory = $mandatory;
		}
		catch (CException $e) { $e->DisplayExceptionAndDie(); }
	}

	private function CheckParams()
	{
		switch($this->Type->GetValue()) {
			case "text":
				return ($this->CheckRequiredParam("length.minimum", "integer")!==false && $this->CheckRequiredParam("length.maximum", "integer"));
			case "integer":
				return (/*$this->CheckRequiredParam("length.minimum", "integer")!==false
				&& $this->CheckRequiredParam("length.maximum", "integer")
				&& */$this->CheckRequiredParam("value.minimum", "integer")
				&& $this->CheckRequiredParam("value.maximum", "integer"));
				case "hidden":
				$this->CheckRequiredParam("length.maximum");
				break;
			case "password":
				$this->CheckRequiredParam("length.minimum");
				$this->CheckRequiredParam("length.maximum");
				break;
			case "email":
				return true;
			default:
				return false;
				break;
		}
	}
	
	public function GetName() { return $this->Name; }
	public function GetType() { return $this->Type->GetValue(); }
	
	public function GetJavascript()
	{
		$ret = sprintf("function Validate_%s() { ", $this->Name);
		
		switch($this->Type->GetValue()) {
			case "text":
				$ret .= sprintf('return ValidateText("%s", "%s", %u, %u);', $this->Name, $this->Label, $this->Params['length.minimum'], $this->Params['length.maximum']);
				break;
			case "hidden":
				break;
			case "password":
				break;
			case "email":
				$ret .= sprintf('return ValidateEmail("%s","%s");', $this->Name, $this->Label);
				break;
			default:
				break;
		}
						
		$ret .= ' }';
		return $ret;
	}
	
	private function CheckRequiredParam($name, $type='text')
	{
		if(isset($this->Params[$name])) {
			switch($type) {
				case "text":
					return true;
				case "integer":
					return is_int($this->Params[$name]);
				default:
					return false;				
			}
		}
		else {
			print("<br />Missing parameter [$name]!");
			return false;
		}
	}

	public function SetValue($value) { $this->Value = $value; }

	public function Display($MandatorySymbol=null, $LabelFieldSeparator=null,$LabelClassName=null, $FieldClassName=null)
	{
		$Mandatory = "";
		
		$begin = sprintf('<tr><td %s><label%s>%s%s%s</label></td><td %s>',
					($LabelClassName!=null?" class=\"$LabelClassName\"":""),
					($LabelClassName!=null?" class=\"$LabelClassName\"":""),
					$this->Label,
					($this->Mandatory?$MandatorySymbol:''),
					$LabelFieldSeparator,
					($FieldClassName!=null?" class=\"$FieldClassName\"":""));
		$end = '</td></tr>';
		
		switch($this->Type->GetValue()) {
			case "text":
				return $begin.sprintf('<input type="text" id="%s" name="%s" %s value="%s" maxlength="%u" />',  $this->Name, $this->Name, ($FieldClassName!=null?" class=\"$FieldClassName\"":""), $this->Value, $this->Params['length.maximum']).$end;
			case "integer":
				if (($this->Params['value.maximum'] - $this->Params['value.minimum']) < 120) {
					$list = '<select name="%s" id="%s">';
					for ($i=$this->Params['value.minimum']; $i<=$this->Params['value.maximum']; $i++)
						$list .= sprintf('<option value="%s"%s>%s</option>', $i, ($i==$this->Value?' selected ':''), $i);
					return $begin.$list.'</select>'.$end;
				}
				else
					return $begin.sprintf('<input type="text" name="%s" value="%s" maxlength="%u" />',  $this->Name, $this->Value, $this->Params['value.maximum']).$end;
			case "hidden":
				
				break;
			case "password":
				break;
			case "email":
				return $begin.sprintf('<input type="text" id="%s" name="%s" %s value="%s" maxlength="%u" />',  $this->Name, $this->Name, ($FieldClassName!=null?" class=\"$FieldClassName\"":""), $this->Value, 255).$end;
				break;
			default:
				break;
		}
	}
}

?>