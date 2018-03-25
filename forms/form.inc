<?php namespace Harmony\form;


class CForm
{
	private $m_Controls = array();
	private $m_Action = null;
	private $m_Method = "post";
	private $m_ContentType = null;
	private $m_Name = null;
	private $m_Charsets = null;
	private $m_Target= null;
	private $m_OnSubmit= null;
	private $m_OnReset= null;
	
	public static $POST="post";
	public static $GET="get";

	public function __construct($Method=null, $Action=null, $Name=null)
	{
		try {
			if (($Method != CForm::$POST) && ($Method != CForm::$GET))
				throw new CException("Method must be GET or POST.");
	
			$this->m_Action =$Action;
			$this->m_Method = $Method;
			$this->m_Name = $Name;
		}
		catch (CException $e) {
		    print($e->DisplayExceptionAndDie());
		    return false;
		}
	}
	
	public function AddControl(&$control)
	{
		$this->m_Controls[] = &$control;
	}
	
	public function Display()
	{
		printf("<form%s%s%s>\n\t<table border=0 style=\"text-align=center; vertical-align:top;\">",
			($this->m_Name!==null?' name="'.$this->m_Name.'"':''),
			($this->m_Method!==null?' method="'.$this->m_Method.'"':''),
			($this->m_Action!==null?' action="'.$this->m_Action.'"':'')	
		);
		
		foreach($this->m_Controls as $control) {
			$control->Display();
		}
		
		print("\r\n\t\t</table>");
		print("\r\n\t</form>"); 
	}
	
	public function AddHiddenInformation($name, $value)
	{
		$this->AddControl(new CInputHidden($name, $value));
		//$this->AddControl(new CInputText($name, '<i><font color="blue">'.$name.'</font></i>', $value));
	}

	public function AddDisabledInformation($title, $name, $value, $display_value=null, $class=null)
	{
		if ($display_value!= null) {
			$this->AddHiddenInformation($name, $value);
			$length = strlen($display_value);
			$this->AddControl(new CInputText("", $title, $display_value, $length, $length, true, $class));
		}
		else
			$this->AddControl(new CInputText($name, $title, $value, null, null, true, $class));
	}
	
	public function AddSubmitButton($label)
	{
		$this->AddControl(new CInputSubmit("", $label));
	}
}
?>
