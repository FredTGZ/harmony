<?php  namespace Harmony;
if (! defined('HARMONY_INCLUDE')) die("Harmony Library is not loaded !");

	/**\brief Enumeration prototype
	 *\ingroup base
	 */
 	class CEnum
	{
		private $m_Values = array();
		protected $m_Value;
		
		
		public function __construct()
		{
			$this->m_Values = func_get_args();
		}
			
		public function SetValue($value)
		{
			if (in_array($value, $this->m_Values, true))
				$this->m_Value = $value;
			else
				die($value . " is not a valid value.");
		}

		public function GetValue() {return $this->m_Value; }
		
		public function GetAvailableValues()
		{
			return $this->m_Values;
		}
	}

?>
