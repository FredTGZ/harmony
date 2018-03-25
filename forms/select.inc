<?php namespace Harmony\form;
class CSelect extends CFormControl
{
	private $m_Closed = false;
	
	public function __construct($name, $title, $class=null)
	{
		parent::__construct($name, $title);

		$this->m_Content = sprintf('<select name="%s"%s>'."\r\n", $name,($class==null?'':' class="'.$class.'"'));
		
	}
	
	public function AddOption($selected, $value, $name, $style='', $disabled=false)
	{
		try {
			if ($this->m_Closed == true)
				throw new CException("Control is already closed !");
			
			$this->m_Content .= sprintf("\t".'<option%s value="%s">%s</option>'."\r\n",
							($selected==true?' selected':'').($disabled==true?' disabled':'').($style!=''?' style="'.$style.'"':''),
							$value,
							$name);
							
		}
		catch (CException $e) {
		    print($e->DisplayExceptionAndDie());
		    return false;
		}

	}
	
	public function CloseSelect()
	{
		$this->m_Content .= "</select>\r\n";
		$this->m_Closed = true;
	}
	
	public function Display()
	{
		try {
			if ($this->m_Closed == false)
				throw new CException("Control is not closed !");

			parent::Display();
		}
		catch (CException $e) {
		    print($e->DisplayExceptionAndDie());
		    return false;
		}
	}
}

?>
