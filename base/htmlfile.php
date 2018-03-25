<?php  namespace Harmony;
if (! defined('HARMONY_INCLUDE')) die("Harmony Library is not loaded !");
require_once("basefile.php");

/**\brief HTML File management
 *\ingroup base
 */
class CHTMLFile extends CBaseFile
{
	/**\brief Constructor
	 *
	 *\param[in]	$filename	HTML filename
	 *\return		Nothing
	 */	 	 	 	
	public function __construct($filename)
	{
		parent::__construct($filename);
	}
	
	/**\brief Get document content (HTML format)
	 *
	 *\return		HTML document content
	 */	 	 	 	
	public function GetHTML()
	{
		return file_get_contents($this->m_Filename);
	}
	
	public function MakeSimpleFile($title, $message)
	{
		$content = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">';
		$content .= "<html>\n\t<head>\n\t\t<title>".$title."</title>\n\t</head>\n\t<body>\n\t";
		$content .= $message;
		$content .= "\n\t</body>\n</html>";
		$this->SetContent($content);
	}
}


?>
