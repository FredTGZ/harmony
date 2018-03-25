<?php  namespace Harmony;
if (! defined('HARMONY_INCLUDE')) die("Harmony Library is not loaded !");
/*
[tag|=param0]param1[/tag]
[tag|=param0]param1\n

"#\[tag=([\w]+?://[\w\#$%&~/.\-;:=,?@\[\]+]*?)\]([^?\n\r\t].*?)\[/tag\]#is";
[tag(\]|\=\w\])](\w))

<html_tag>{0} : {1}</html_tag>

tag
param*/

	require_once("basefile.php");
	require_once("template.php");
//	if(!defined("BBCODE_UID_LEN")) define("BBCODE_UID_LEN", 10);

	class CBBCode2File extends CBaseFile
	{
		private $m_Buffer;
		private $m_RenderedBuffer;
		private $m_UID;
		private $m_Template=null;
		private $m_TemplateArray=array();
		private static $IndexOpenTag = 1;
		private static $IndexCloseTag = 4;
		private static $IndexParam = 2;
		private static $IndexContent = 3;
	
		public function __construct($filename)
		{
			parent::__construct($filename);
			
			$this->m_Buffer = file_get_contents ($filename, false);
			$pos = strpos($this->m_Buffer, "\n");
			$this->m_Buffer = substr($this->m_Buffer, $pos+1);
			
			$matches = array();
			$flags = 0;
			
			/**
			 *1. Recherche des balises [{0}={1}] et [/{0}]
			 *2. Recherche de ]*[			 
			 *
			 */			 			 			
			
			//	$a = preg_match_all ("#\[(\w+)\=?(\w+?|)\](.+?)\[/(\w+)\]|$#si", $this->m_Buffer, $matches, PREG_OFFSET_CAPTURE);
				$a = preg_match_all ("#\[(\w+)\=?(\w+?|)\]#si", $this->m_Buffer, $OpenTags, PREG_OFFSET_CAPTURE);
				$b = preg_match_all ("#\](.+?)\[|$#si", $this->m_Buffer, $Contents, PREG_OFFSET_CAPTURE);
				$c = preg_match_all ("#\[/(\w+)\=?(\w+?|)\]#si", $this->m_Buffer, $CloseTags, PREG_OFFSET_CAPTURE);
		//	$a = preg_match_all ("#\[(\w+)\=?(\w+?|)\](.+?)(\[/(\w+)\]|\n)#si", $this->m_Buffer, $matches);
			//$a = preg_match_all ("#\[(\w+)(\=\w+?|)?\]#si", $this->m_Buffer, $matches);
print('<textarea cols="80" rows="10">'.$this->m_Buffer."</textarea>");
print('<br><br>');print('<br><br>');
print_r($OpenTags[0]);	// TEXTES
print('<br>');			
print_r($OpenTags[1]);	// TAGS
print('<br>');
print_r($OpenTags[2]);	// PARAMETRES


print('<br><br>');
print_r($Contents[0]);
print('<br><br>');
print_r($Contents[1]);
$TextBegin = substr($this->m_Buffer, 0, $OpenTags[0][0][1]);
$TextBegin = substr($this->m_Buffer, 0, strlen($this->m_Buffer));
print('<br><br>');
print_r($CloseTags);
print('<br><br>');print('<br><br>');
$tag_index = 0;
print $TextBegin;
foreach($OpenTags[1] as $key => $value)
{
	print('<br><br>');
	print('tag : '.$value[0]);
	print(', paramètre='.$OpenTags[2][$key][0]);
	print(', contenu='.$Contents[1][$key][0]);
	$tag_index++;
}
			/*print('<br><br>');
			$OpenTag = $matches[1];
			$Params = $matches[2];
			$Content = $matches[3];
			$CloseTag = $matches[4];
			print("<br><b>Balise d'ouverture : </b><br>"); print_r($OpenTag);print("<br>");
			print("<br><b>Paramètres : </b><br>"); print_r($Params);print("<br>");
			print("<br><b>Contenu : </b><br>"); print_r($Content);print("<br>");
			print("<br><b>Balise de fermeture : </b><br>"); print_r($CloseTag);print("<br>");*/
			/*
			//$bbcontent
			foreach($OpenTag as $key => $value) $this->GetHTMLTag($value, $Params[$key], $Content[$key]);
			*/
		}
		
		private function GetHTMLTag($bbtag, $param, $content)
		{
			print ('<br>tag=' . $bbtag[0].', param='.$param . ', content=' . $content);
		
		}
		
		public function GetHTML()
		{
	

			//return $this->m_Buffer;
		}
	}
?>
