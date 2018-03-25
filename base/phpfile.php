<?php  namespace Harmony;
if (! defined('HARMONY_INCLUDE')) die("Harmony Library is not loaded !");
require_once("basefile.php");

/**\brief PHP File management
 *\ingroup base
 */
class CPHPFile
{	
	public function __construct($filename)
	{
		parent::__construct($filename);
	}
	
	public function GetHTML()
	{
		ob_start();
		include($this->Filename);
		$code = ob_get_contents();
		ob_end_clean();
		return($code);
	}
}

?>
