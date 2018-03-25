<?php  namespace Harmony;
if (! defined('HARMONY_INCLUDE')) die("Harmony Library is not loaded !");
/**\brief Timer
 *\ingroup base
 */
class CTimer
{
	private $m_microtime;
	
	/**
	 *\param[in]	$start	
	 */	 	
	public function __construct($start=true)
	{
		if($start) $this->Start();
	}

	public function Start()
	{
		$this->m_microtime = microtime(true);
	}
	
	public function GetTimer()
	{
		return (1000 * round(microtime(true) - $this->m_microtime, 3));
	}
}
?>
