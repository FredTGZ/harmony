<?php  namespace Harmony;
if (! defined('HARMONY_INCLUDE')) die("Harmony Library is not loaded !");

/*

class CHtmlDocument extends CBaseObject
{
	private $m_TableOpened = false;
	private $m_Content = '';
	private $m_ContentBuffer = '';
	private $m_TableColDef = false;
	
	
	public function GetContent()
	{
		return $this->m_Content;
	}
	
	private function AddContent($content)
	{
		if ($this->m_ContentBuffer != '')
			$this->m_ContentBuffer .= $content;
		else
			$this->m_Content .= $content;
	}

	private function BufferToContent()
	{
		$this->m_Content .= $this->m_ContentBuffer;
		$this->m_ContentBuffer = '';
	}
	

	public function AddParagraph(string $content, string $class=null)
	{
		$this->AddContent(sprintf("<p%s>%s</p>", $content, ($class===null?'':'class="'.$class.'"')));
	}


	////////////////////////////////////////////////////////////////////////////
	// TABLE FUNCTIONS
	////////////////////////////////////////////////////////////////////////////
	public function CreateTable(int $border, int $cellspacing, int $cellpadding, string $class=null)
	{
		$this->m_TableOpened = true;
		$this->m_ContentBuffer = sprintf('<table border="%u" cellspacing="%u" cellpadding="%u"%s>%s</p>', $content, ($style===null?'':$style));
	}

	public function DefineTableColumns(array $coldef)
	{
		if($this->m_TableOpened) {
			$this->m_TableColDef = '';


		}	
	}

	public function CloseTable()
	{
		if($this->m_TableOpened) {
			$this->m_TableOpened = true;
			$this->BufferToContent();
		}
	}
}
*/
?>
