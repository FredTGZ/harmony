<?php  namespace Harmony;
if (! defined('HARMONY_INCLUDE')) die("Harmony Library is not loaded !");
/*******************************************************************************
 * Base objects module for Harmony PHP Library
 *
 * Author: FredTGZ
 * Description:
 * Manage errors and display error messages.
 *
 ******************************************************************************/       
	//error_reporting(E_ALL);

	assert_options(ASSERT_ACTIVE, 1);
	assert_options(ASSERT_WARNING, 1);
	assert_options(ASSERT_BAIL, 0);
	assert_options(ASSERT_QUIET_EVAL, 0);

	assert_options(ASSERT_CALLBACK, 'Harmony\myAssertHandler');
	set_error_handler("Harmony\myErrorHandler");
	set_exception_handler('Harmony\exception_handler');

	/**\brief This class create a HTML table to display properly an error message.
	 *\ingroup base
	 *
	 *This class contains only static functions to create a HTML table displaying an exception information.	 
	 */ 
	class CErrorTable
	{
		public static $BackgroundColor = "#ECE9D8";
		/**
		 *\param[in]	$title		Table title (caption)
		 *\param[in]	$color		Font color (default: red)
		 *\param[in]	$fontsize	Font size (family is "Sans-serif", default is 8)
		 *\param[in]	$width		Table width
		 *\return		Return the table declaration and caption		 
		 */		 		
		public static function GetHeader($title, $color="red", $fontsize=10, $width=400)
		{
			return sprintf("<div style=\"display: block; position: relative; z-index: 100; margin-left: 10px;\"><table border=\"0\" cellspacing=\"0\" cellpadding=\"2\"style=\"background-color: white; background-image: none; font-family: Sans-serif; border: thin %s solid;\"width=\"%u\"><caption style=\"color: white; background-color: %s; font-weight: bold; font-size: %upt;\">%s</caption>", $color, $width, $color, $fontsize, $title);
		}

		/**
		 *\return		Return the table declaration and caption		 
		 */		 		
		public static function GetFooter()
		{
			return sprintf("</table></div><br>");
		}

		/**
		 *\param[in]	$text1		First cell text
		 *\param[in]	$text2		Second cell text
		 *\param[in]	$fontsize	Font size (family is "Sans-serif", default is 8)
		 *\return		Return the html row declaration		 
		 */		 		
		public static function GetRow($text1, $text2, $fontsize=8)
		{
			return sprintf("<tr style=\"vertical-align: top; background-color: %s; font-size: %upt;\"><td width=\"100\"><b>%s</b></td><td colspan=\"2\">%s</td></tr>", self::$BackgroundColor, $fontsize, $text1, $text2);
		}

		/**
		 *\param[in]	$title		Variable title
		 *\param[in]	$var		Variable name
		 *\param[in]	$value		Variable value
		 *\param[in]	$fontsize	Font size (family is "Sans-serif", default is 8)
		 *\return		Return the html row declaration		 
		 */		 		
		public static function GetRowVar($title, $var, $value, $fontsize=8)
		{
			if (! is_object($value))
				return sprintf("<tr style=\"vertical-align: top; background-color: %s; font-size: %upt;\"><td width=\"100\" style=\"font-size: %upt;\"><b>%s</b></td><td>%s</td><td>%s</td></tr>", self::$BackgroundColor, $fontsize-2, $fontsize, $title, $var, $value);
			else return '';
		}

		/**This function browse all values from an array end use GetRowVar() to display it.
		 *\param[in]	$title		Variable title
		 *\param[in]	$array		Variable array
		 *\param[in]	$fontsize	Font size (family is "Sans-serif", default is 8)
		 *\return		Return the html row declaration		 
		 */		 		
		public static function GetRowArray($title, $array, $fontsize=8)
		{
			$i=0;
			$ret = "";
			
			foreach($array as $var => $value) {
			
				if ($i == 0) {
					$ret .= self::GetRowVar($title, $var, $value, $fontsize);
					$i++;
				}
				else
					$ret .= self::GetRowVar("", $var, $value, $fontsize);
			}

			return $ret;
		}
	}

	/**Manage asserts
	 *
	 *\param[in]	$file	 Source filename
	 *\param[in]	$line	 Source line
	 *\param[in]	$code	 Source php code
	 */
	function myAssertHandler($file, $line, $code)
	{
		print(CErrorTable::GetHeader("ASSERTION FAILED", "orange"));
		print(CErrorTable::GetRow("File", '<a href="file://'.$file.'">'.$file.'</a>'));
		print(CErrorTable::GetRow("Line", $line));
		print(CErrorTable::GetRow("Code", $code));
		print(CErrorTable::GetFooter());
		
		global $_LOG;
		if (isset($_LOG) && $_LOG !== null) {
			$message = sprintf("%s line %s : %s", $file, $line, $code);
			$_LOG->AddLog($message, CLogFile::$Error);
		}
	}

	
	/** Manage errors
	 *
	 *\param[in]	$errno	 	Error number
	 *\param[in]	$errstr	 	Error string
	 *\param[in]	$errfile	Error source filename
	 *\param[in]	$errline	Error source line number
	 *\param[in]	$errcontext	Error context
	 */	 	
	function myErrorHandler($errno, $errstr, $errfile, $errline, $errcontext)
	{
		switch ($errno) {
			case E_USER_ERROR:
			case E_ERROR:
			case E_CORE_ERROR:
			case E_COMPILE_ERROR:
			//case E_RECOVERABLE_ERROR:
				print(CErrorTable::GetHeader("ERROR [" . $errno . "]", "orange"));
				break;
			case E_USER_WARNING:
			case E_WARNING:
			case E_CORE_WARNING:
			case E_COMPILE_WARNING:
				if (substr($errstr, 0, 8) == "assert()") return;
				print(CErrorTable::GetHeader("WARNING [" . $errno . "]", "orange"));
				break;
			case E_NOTICE:
			case E_USER_NOTICE:
				if (!defined('ERROR_MGT_NOTICE_OFF')) print(CErrorTable::GetHeader("NOTICE [" . $errno . "]", "green"));
				break;
			case E_PARSE:
			case E_STRICT:
				print(CErrorTable::GetHeader("XXX [" . $errno . "]", "black"));
				echo "Unknown error: [$errno] $errstr<br />\n";
				break;
			case E_DEPRECATED:
				return;
			default:
				print(CErrorTable::GetHeader("UNKNOWN ERROR [" . $errno . "]", "red"));
				break;
		}
		
		
		if ((($errno != E_NOTICE) && ($errno != E_USER_NOTICE)) || !defined('ERROR_MGT_NOTICE_OFF'))
		{
			print(CErrorTable::GetRow("File", '<a href="file:///'.$errfile.'">'.$errfile.'</a>'));
			print(CErrorTable::GetRow("Line", $errline));
			print(CErrorTable::GetRow("Code", $errno));
			print(CErrorTable::GetRow("Message", $errstr));
			//print(CErrorTable::GetRowArray("Context", $errcontext));
			print(CErrorTable::GetFooter());
	
			global $_LOG;
			if (isset($_LOG) && $_LOG !== null) {
				$message = sprintf("%s line %s : %s - %s", $errfile, $errline, $errno, $errstr);
				$_LOG->AddLog($message, CLogFile::$Error);
			}
		}
	}
	
	/** Manage non catched exceptions
	 *\param[in]		$exception	Exception to display
	 *	 	*/
	function exception_handler($exception)
	{
		echo "Non catched exception : " , $exception->getMessage(), "\n";

		global $_LOG;
		if (isset($_LOG) && $_LOG !== null) {
			$message = sprintf("%s line %s : %s - %s",
				$exception->getFile(),
				$exception->getLine(),
				$exception->getCode(),
				$exception->getMessage());
			$_LOG->AddLog($message, CLogFile::$Error);
		}
	}
	
	
	function myErrorHandlerNULL($errno, $errstr, $errfile, $errline, $errcontext)
	{
	}
	
	function exception_handlerNULL($exception)
	{
	}

	function myAssertHandlerNULL($file, $line, $code)
	{
	}

	function DisableErrorManagement()
	{
		assert_options(ASSERT_ACTIVE, 0);
		assert_options(ASSERT_WARNING, 0);
		assert_options(ASSERT_BAIL, 0);
		assert_options(ASSERT_QUIET_EVAL, 0);
		assert_options(ASSERT_CALLBACK, 'myAssertHandlerNULL');
		set_error_handler('myErrorHandlerNULL');
		set_exception_handler('exception_handlerNULL');
		error_reporting(0);
	}

?>
