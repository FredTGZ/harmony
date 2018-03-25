<?php namespace Harmony\ajax;

	class CAjaxComplexResponse
	{
	}


	class CAjaxService extends \Harmony\CBaseObject
	{
		public $Name = "";
		public $ID = "";
		public $ResponseText = "";
		public $ResponseXML = null;
		protected $CodePage = "UTF-8";
		protected $OpenedTag = "";
		public $ReturnFile = false;
		private $FileHeader = null;
		public $Data;
		
		
		public function __construct($codepage="UTF-8")
		{
			if (array_key_exists('HTTP_AJAXSERVICENAME',  $_SERVER))
				$this->Name = $_SERVER['HTTP_AJAXSERVICENAME'];
			if (array_key_exists('HTTP_AJAXSERVICEID', $_SERVER))
				$this->ID = $_SERVER['HTTP_AJAXSERVICEID'];
		
			$this->SetCodepage($codepage);
			//$this->SetName($name);
			$this->ResponseText = null;
			$this->ResponseXML = null;

			$this->Data = new \stdClass();
		}
		
		public function SendResponseOK($Message="")
		{
			$this->ResponseText = 'OK'.$Message;
			$this->SendResponse();			
		}
		
		public function SendResponseKO($Message="")
		{
			$this->ResponseText = 'KO'.$Message;
			$this->SendResponse();
		}		
		
		public function SendFile($Filename, $ContentType, $Content, $CodePage=null)
		{
			header("Content-type: ".$ContentType."; charset=".($CodePage==null?$this->CodePage:$CodePage));
			header("Content-Disposition: attachment; filename=".$Filename);
			header("Expires: 0");
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Cache-Control: private",false);
			die($Content);
		}
		
		public function CountParams()
		{
			$i=0;
			while(\Harmony\CHTTPServer::GetVar("param".$i) !== null) $i++;
			
			return $i;
		}
	
		public function GetParam($i)
		{
			$p = \Harmony\CHTTPServer::GetVar("param".$i);
			return $p;
		}
	
		public function SetCodepage($codepage)
		{
			$this->CodePage = $codepage;
		}
	
		
		public function AddResponseXMLStartTag($id, $value=null)
		{
			if ($value != null)
				$this->ResponseXML .= sprintf("\t\t<%s id=\"%s\">\n", $id, $value/*htmlentities($value, ENT_QUOTES, $this->CodePage)*/);
			else
				$this->ResponseXML .= sprintf("\t\t<%s>\n", $id);
			$this->OpenedTag = $id;
			
		}
	
		public function AddResponseXMLStopTag()
		{
			if ($this->OpenedTag=="") return;
			$this->ResponseXML .= sprintf("\t\t</%s>\n", $this->OpenedTag);
			$this->OpenedTag = "";
		}
	
		public function AddResponseXMLParam($id, $value)
		{
			$this->ResponseXML .= sprintf("\t\t\t<%s>%s</%s>\n", $id, $value/*htmlentities($value, ENT_QUOTES, $this->CodePage)*/, $id);
		}
		
		
		public function SendResponseJSON()
		{
			$this->ResponseText = json_encode($this->Data);
			$this->SendResponse();
		}
		
		public function SendResponse()
		{
			if ($this->ResponseText != null) {
				header("Content-type: text/html; charset=".$this->CodePage);
				header("AjaxServiceName: ". $this->Name);
				header("AjaxServiceID: ".$this->ID);
				print($this->ResponseText);
			}
			else {
				header("Content-type: text/xml");
				header("AjaxServiceName: ".$this->Name);
				header("AjaxServiceID: ".$this->ID);			

				$dom = new \DOMDocument("1.0", $this->CodePage);
				$xml = "<?xml version=\"1.0\" encoding=\"".$this->CodePage."\" ?>\n<root>\n\t<provider>Harmony Library</provider>\n\t<data>\n".$this->ResponseXML."\t</data>\n</root>\n";
				$sxe = simplexml_load_string($xml);
				$dom_sxe = dom_import_simplexml($sxe);
				$dom_sxe = $dom->importNode($dom_sxe, true);
				$dom_sxe = $dom->appendChild($dom_sxe);
				
				
				
				print($dom->saveXML());

			}
			                           
			die();
		}
	}
?>