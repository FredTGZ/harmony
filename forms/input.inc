<?php namespace Harmony\form;
class CInputValidationEnum extends CEnum
{
	public function __construct()
	{
		parent::__construct("INTEGER", "FLOAT", "STRING", "EMAIL", "DATE");
	}
}

class CInputText extends CFormControl
{
	public function __construct($name, $title, $value, $Size=null, $MaxLength=0, $ReadOnly=false, $class=null)
	{
		parent::__construct($name, $title, false, false, $class);
		$this->m_Content = sprintf('<input type="text" id ="%s" name="%s" %s%s%s %s value="%s">',
				$name,
				$name,
				($Size===null?'':' SIZE="'.$Size.'"'),
				($MaxLength===0?'':' MAXLENGTH="'.$MaxLength.'"'),
				($ReadOnly===true?' READONLY':''),
				($class!=null?' class="'.$class.'"':''),
				$value);
	}
	
	public function GetValidationScript($type, $Min=null, $Max=null)
	{
		$ctype = CInputValidationEnum($type);
		
		switch($ctype->GetValue())
		{
			case "INTEGER":
				break;
			case "FLOAT":
				break;
			case "STRING":
				break;
			case "EMAIL":
				break;
			case "DATE":
				break;
		}
	}
}

class CInputHidden extends CFormControl
{
	public function __construct($name, $value)
	{
		parent::__construct($name, "", true);
		$this->m_Content = sprintf('<input type="hidden" id="%s" name="%s" value="%s">', $name, $name, $value);
	}
}

class CInputSubmit extends CFormControl
{
	public function __construct($name, $value)
	{
		parent::__construct($name, "", false);
		$this->m_ColSpan = true;
		$this->m_Content = sprintf('<input type="submit" id="%s" name="%s" value="%s">', $name, $name, $value);
	}
}
?>
