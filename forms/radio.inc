<?php namespace Harmony\form;

class CRadio extends CFormControl
{
	public function __construct($name, $title)
	{
		parent::__construct($name, $title);
	}
	
	public function AddOption($name, $value)
	{
		$this->m_Content .= sprintf('<label><input type="radio" name="%s" value="%s">%s</label><br>', $this->m_Name, $value, $name);
	}
}
?>
