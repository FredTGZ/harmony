<?php namespace Harmony\database;
 if (! defined('HARMONY_INCLUDE')) die("Harmony Library is not loaded !");

/** \brief Manage and display recordset exception.
 *
 * CRecordsetException extends CException to manage database exception. Used by
 * CRecordset class.
 * 
 * \ingroup database
 * \ingroup Exception 
 */    
class CRecordsetException extends \Harmony\CException 
{
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
