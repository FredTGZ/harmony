<?php  namespace Harmony;
if (! defined('HARMONY_INCLUDE')) die("Harmony Library is not loaded !");
	require_once("exception.php");
	/**\brief XML File Exception
	 *\ingroup base
	 *\ingroup exception 
	 *
	 */ 
	class CXMLException extends CException 
	{
		/** Constructor
		 *\param[in]	$message	Error message
		 *\param[in]	$code	 	Error code (default is 0)
		 */		 		
		public function __construct($message, $code = 0)
		{
			parent::__construct($message, $code);
		}
		
		/*! \brief Display exception message in HTML, then die.
		 *	
		 * Format and display exception message in an html table.	 
		 */		
		public function DisplayException()
		{
			//Todo add your modification here.
			parent::DisplayException();
		}
	}

?>
