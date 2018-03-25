<?php namespace Harmony\languages;
if (! defined('HARMONY_INCLUDE')) die("Harmony Library is not loaded !");

class CDictionnaryFile extends \Harmony\CXMLFile
{
	protected $m_Variables=array();
	protected $m_Language = "en";
	
	public function GetLanguage()
	{
		return $this->m_Language;
	}
	
	public function __construct($language = "en")
	{
		$this->m_Language = $language;
		parent::__construct(HARMONY_INCLUDE."/languages/dic/".$language.".xml", 'utf-8');
		$this->GetVars();
	}
	
	public function Translate($Id, $FirstLetterUpper=true)
	{
		if (array_key_exists($Id, $this->m_Variables)) {
			$ret = $this->m_Variables[$Id];
		}
		else $ret = $Id;
		
		if ($FirstLetterUpper) $ret = strtoupper(substr($ret, 0, 1)) . substr($ret, 1);
		
		return $ret;
	}
	
	
	protected function GetVars()
	{
		foreach($this->XMLData as $key => $value) {
			switch($value['tag']) {
				case 'DICTIONARY':
					break;
				case 'TRANSLATION':
					$VarName = $value['attributes']['ID'];
					//$VarValue = str_replace(' ', '&nbsp;', $value['attributes']['VALUE']);
					$VarValue = $value['attributes']['VALUE'];
					$this->m_Variables[$VarName] = $VarValue;
					break;
				default:
					break;		
			}
		}
	}

}

?>
