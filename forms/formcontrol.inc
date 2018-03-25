<?php namespace Harmony\form;
	class CFormControl
	{
		protected $m_Content = null;
		protected $m_Title = null;
		protected $m_Name = null;
		protected $m_Invisible = false;
		protected $m_ColSpan = false;
		protected $m_Class = null;
	
		public function __construct($Name, $Title, $invisible=false, $colspan=false, $class=null)
		{
			$this->m_Name = $Name;
			$this->m_Title = $Title;
			$this->m_Content = "";
			$this->m_Invisible = $invisible;
			$this->m_ColSpan = $colspan;
			$this->m_Class = $class;
		}
		
		public function Display()
		{
			if (false === $this->m_Invisible) {
				if ($this->m_ColSpan)
					printf('<tr style="vertical-align:top;"><td colspan="2" style="text-align: center;">%s</td></tr>', $this->m_Content);
				else
					printf('<tr style="vertical-align:top;"><td%s>%s</td><td>%s</td></tr>', ($this->m_Class==null?'':' class="'.$this->m_Class.'"'), $this->m_Title, $this->m_Content);
			}
			else
				printf($this->m_Content);
		}
		
		public function GetValidationScript()
		{
			return "";
		}
	}
?>
