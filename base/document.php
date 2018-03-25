<?php  namespace Harmony;
if (! defined('HARMONY_INCLUDE')) die("Harmony Library is not loaded !");

/*
Header:
=======
<!--{TITLE:}{CACHE:TRUE|FALSE}{REVISIONS:[YYYY-MM-DD HH:MM:SS,XXXX][YYYY-MM-DD HH:MM:SS,XXXX][YYYY-MM-DD HH:MM:SS,XXXX]}-->
<!--{TITLE:}{OPTION:VALUE}{OPTION:VALUE}{OPTION:VALUE}-->

Options     Description
----------- --------------------------------------------------------------------
TITLE       Obligatoire. Titre du document
REVISIONS   Obligatoire. Date de modifications et de leurs auteurs.
            [NNN:YYYY-MM-DD HH:MM:SS,XXXX]
            NNN                  Numéro de la révision (commence à 0)
            YYYY-MM-DD HH:MM:SS  Date de la révision
            XXXX                 Nom du rédacteur
CACHE       Facultatif (TRUE par défaut). Définit à TRUE ou FALSE, si c'est
            FALSE alors aucun cache ne sera créé.
CSS         Facultatif. URL de la feuille de style utilisée en adjonction de la
            principale.
KEYWORDS    Facultatif. Mots clés, sépararés par des virgules ",".
LANGUAGE	Facultatif (Langue du système par défaut). Peut prendre les valeurs
            DE, FR, EN, SP.

*/

/**\brief Document header
 *\ingroup base
 */
class CDocumentHeader
{
	protected $m_Title = null;
	protected $m_Revisions = array();
	protected $m_Cache = false;
	protected $m_CSS = array();
	protected $m_Keywords = array();
	protected $m_Language = null;
	
	public function GetTitle()	{ return $this->m_Title;		}
	public function UseCache()	{ return $this->m_Cache;		}
	
	public function GetData()
	{
		$ret = sprintf("<!--{title:%s}{cache:%s}",
			$this->m_Title,
			($this->m_Cache?'true':'false'));
		
		$ret .= "{revisions:";
		
		foreach($this->m_Revisions as $revision) {
			$ret .= sprintf("[%s=>%s]", $revision['DATE'], $revision['USER']);
		}
		
		$ret .= "}";
		
		$ret .= "{css:";
		
		foreach($this->m_CSS as $css) {
			$ret .= sprintf("%s,", $css);
		}
		
		if (substr($ret, strlen($ret)-1) == ',')
			$ret = substr($ret, 0, strlen($ret)-1);
		
		$ret .= "}";

		$ret .= "{keywords:";
		
		foreach($this->m_Keywords as $keyword) {
			$ret .= sprintf("%s,", $keyword);
		}
		
		if (substr($ret, strlen($ret)-1) == ',')
			$ret = substr($ret, 0, strlen($ret)-1);

		return $ret;
	}
	
	public function __construct($data)
	{
		$data = explode('{', $data);
		$version=0;
		
		foreach($data as $option_value) {
			$option_value = substr($option_value, 0, strlen($option_value) - 1);
			$pos = strpos($option_value, ':');
			
			if ($pos > 0) {
				$option_name = strtoupper(substr($option_value, 0, $pos));			
				$option_value = substr($option_value, $pos+1);

				switch($option_name) {
					case 'TITLE':
						$this->m_Title = $option_value;
						break;
					case 'REVISIONS':
						$revision = 0;
						$this->m_Revisions = array();
						$data_rev = explode(']', $option_value);
						
						foreach($data_rev as $rev_infos) {
							if (strlen($rev_infos) >=21) {
								$rev_infos = substr($rev_infos, 0, strlen($rev_infos));
								$date_year = substr($rev_infos, 1, 4);
								$date_month = substr($rev_infos, 6, 2);
								$date_day = substr($rev_infos, 9, 2);
								$date_hour = substr($rev_infos, 12, 2);
								$date_minute = substr($rev_infos, 15, 2);
								$date_second = substr($rev_infos, 18, 2);
								$date = mktime($date_hour, $date_minute, $date_second, $date_month, $date_day, $date_year);
								$user = substr($rev_infos, 21);
								$this->m_Revisions[$version++] = array( 'DATE' => $date, 'USER' => $user);
							}
						}

						break;
					case 'CACHE':
							$this->m_Cache = (strtoupper($option_value) == "TRUE");
						break;
					case 'CSS':
						$this->m_CSS = explode(",", $option_value);
						//$this->m_CSS = $option_value;
						break;
					case 'KEYWORDS':
						$this->m_Keywords = explode(",", $option_value);
						break;
					case 'LANGUAGE':
						$this->m_Language = strtolower($option_value);
						break;
					default:
						// Unknown option !!!
						break;
				}

			}
			else {
			
			}
		}
	
	}
}

/**\brief Document
 *\ingroup base
 */
class CDocument
{
	public $Header;
	public $File;
	
	public function __construct($file=null)
	{
		if ($file == null) {
			$this->Header = new CDocumentHeader();
		}
		else {
			$this->File = new CBaseFile($file);
		}
	}
	


}
?>
