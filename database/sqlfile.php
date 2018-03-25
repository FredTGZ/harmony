<?php namespace Harmony\database;
 if (! defined('HARMONY_INCLUDE')) die("Harmony Library is not loaded !");
	
	/**\ingroup database
	 *
	 */ 
	class CSQLFile extends \Harmony\CBaseFile
	{
		public function __construct($filename)
		{
			parent::__construct($filename);
		}
			
		public function GetHTML()
		{
			die("This function must be renewed !");	
		}
	
		public function ExecuteScript(&$database)
		{
			$file_content = $this->GetContent();
			$file_content = str_replace("\r\n", "\n", $file_content);
			$file_content = str_replace(";\n", "\n", $file_content);

			$queries = explode("\n", $file_content);
			
			foreach($queries as $query) {
				if ($query != '') {
					$database->ExecuteSQLQuery($query);
				}
			}
		}
	}
?>
