<?php

//http://fr.wikipedia.org/wiki/RSS_(format)
/*class CRSS2File extends CXMLFile
{
	public function __construct($filename, $title, $description, $link)
	{
		if (! file_exists($filename)) {
			// Creation du fichier XML RSS
			$string = '<rss version="2.0">';	
			$string .= '<channel>';	
			$string .= '</channel>';	
			$string .= '</rss>';	
			
		}

		parent::__construct($filename);
	}
	
	public function AddItem($title=null, $description=null, $date=null, $link=null, $guid=null)
	{
		if ($title===null && $description===null) return false;
		
		$string = "<item>";
		
	    if ($title !== null)		$string .= sprintf("<title>%s</title>", $title);
	    if ($description !== null)	$string .= sprintf("<description>%s</description>", $$description);
	    if ($date !== null)			$string .= sprintf("<pubDate>%s</pubDate>", $date);
	    if ($link !== null)			$string .= sprintf("<link>%s</link>", $link);
	    if ($guid !== null)			$string .= sprintf("<guid>%s</guid>", $guid);

		$string .= "<item>";
	}

}*/

?>
