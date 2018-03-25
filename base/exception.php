<?php  namespace Harmony;
if (! defined('HARMONY_INCLUDE')) die("Harmony Library is not loaded !");
	/**\defgroup exception Exception management*/
		
	/** \brief Manage exception.
	 * \ingroup exception
	 *
	 * Manage exception, format exception message.
	 */	 	 	
	class CException extends \Exception 
	{
		///Class name
		protected $class = null;
		///Function name
		protected $function = null;
		///Formated string (normaly ClassName::FunctionName( ))
		protected $location = null;
		
		/** Constructor
		 *\param[in]	$message	Error message
		 *\param[in]	$code	 	Error code (default is 0)
		 */		 		
		public function __construct($message, $code=0)
		{
			parent::__construct($message, $code);
			$array_trace = $this->getTrace();
			$this->class = $array_trace[0]['class'];
			$this->function = $array_trace[0]['function'];
			$this->location = $this->class;
			if ($this->class != "") $this->location .= "::";
			if ($this->function != "") $this->location .= $this->function . '(&nbsp;)';
			$this->Log();
		}

		/** \brief Return a html string for table header.
		 *\param[in]	$title	Error formating string
		*/
		protected function GetTableHeader($title="Error %s")
		{
			return sprintf("<div><table border=\"0\" cellspacing=\"2\" cellpadding=\"0\"style=\"font-family: Sans-serif; border: 1px red solid; background-color: white;\" width=\"600\"><caption style=\"color: white; background-color: red; font-weight: bold;\">" . $title. "</caption>", $this->code);
		}	 		 		 		

		/** \brief Return a html string for table footer.
		*/		
		protected function GetTableFooter()
		{
			return sprintf("</table></div>");
		}	 		 		 		

		/** \brief Display exception message in HTML.
		 *	
		 *Print a HTML string containing exception informations.		 	
		 */		
		public function DisplayException()
		{
			$ret = CErrorTable::GetHeader(get_class($this)." - Code " . $this->code, "red");
			$ret .= CErrorTable::GetRow("File", $this->file);
			$ret .= CErrorTable::GetRow("Line", $this->line);
			$ret .= CErrorTable::GetRow("Location", $this->location);
			$ret .= CErrorTable::GetRow("Code", $this->code);
			$ret .= CErrorTable::GetRow("Message", $this->message);
			$ret .= CErrorTable::GetFooter();
			print($ret);
			$this->Log();
		}
		
		/** \brief Add the current message into the log
		 * 
		 */
		protected function Log()
		{
			global $_LOG;
			if (isset($_LOG) && $_LOG !== null) {
				$message = sprintf("%s=%s - %s", $this->location, $this->code, $this->message);
				$_LOG->AddLog($message, CLogFile::$Error);
			}
		}
		
		/** \brief Display exception message in HTML, then die.
		 *
		 *Same as DisplayException but call die() after this. So the exception is designed as a fatal exception.	 
		 */		
		public function DisplayExceptionAndDie()
		{
			$this->DisplayException();
			die();
		}
	}
?>
