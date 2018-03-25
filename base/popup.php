<?php  namespace Harmony;
if (! defined('HARMONY_INCLUDE')) die("Harmony Library is not loaded !");
/**\brief Popup windows management
 *\ingroup base
 */
class CPopupWindow
{
	private $Title;
	
	
	public static $dependent="dependent=yes,";
	public static $menubar="menubar=yes,";
	public static $resizable="resizable=yes,";
	public static $scrollbars="scrollbars=yes,";
	public static $statusbar="status=yes,";
	public static $toolbar="toolbar=yes,";
	public static $notdependent="dependent=no,";
	public static $nomenubar="menubar=no,";
	public static $notresizable="resizable=no,";
	public static $noscrollbars="scrollbars=no,";
	public static $nostatusbar="status=no,";
	public static $notoolbar="toolbar=no,";
/*
dependent= 	yes|no 	JavaScript 1.2Netscape 4.0 	Si oui (yes), la fen�tre sera ferm�e si sa fen�tre parent est ferm�e. Si non (no = r�glage par d�faut), la fen�tre reste ouverte si sa fen�tre parent est ferm�e .
hotkeys= 	yes|no 	JavaScript 1.2Netscape 4.0 	Si non (no), les raccourcis clavier pour commander le navigateur dans la fen�tre sont d�sactiv�s. Si oui(yes= r�glage par d�faut), les raccourcis clavier du navigateur dans la fen�tre restent actifs.
innerHeight= 	[pixels] 	JavaScript 1.2Netscape 4.0 	hauteur du domaine d'affichage de la nouvelle fen�tre en pixels par exemple innerHeight=200.
innerWidth= 	[pixels] 	JavaScript 1.2Netscape 4.0 	largeur du domaine d'affichage de la nouvelle fen�tre en pixels par exemple innerWidth=200.
left= 	[pixels] 	JavaScript 1.2Netscape 4.0MS IE 4.0 	Valeur horizontale du coin sup�rieur gauche de la nouvelle fen�tre en pixels par exemple left=100.
location= 	yes|no 	JavaScript 1.0Netscape 2.0MS IE 3.0 	Si oui (yes), la fen�tre re�oit sa propre barre d'adresse URL. Si non (no = r�glage par d�faut), elle ne re�oit pas de barre d'adresse, pour l'Explorer Internet cependant seulement si la cha�ne de caract�res facultative contient au moins une option. Netscape 6.1 n'interpr�te pas cette mention.
menubar= 	yes|no 	JavaScript 1.0Netscape 2.0MS IE 3.0 	Si oui (yes), la fen�tre re�oit sa propre barre de menus avec les commandes du navigateur. Si non (no = r�glage par d�faut), elle ne re�oit pas de barre de menus, pour l'Explorer Internet cependant seulement si la cha�ne de caract�res facultative contient au moins une option.
resizable= 	yes|no 	JavaScript 1.0Netscape 2.0MS IE 3.0 	Si oui (yes),l'utilisateur peut modifier la taille de la fen�tre. Si non (no), l'utilisateur ne peut pas modifier la taille de la fen�tre. Le r�glage par d�faut est no, pour l'Explorer Internet cependant seulement si la cha�ne de caract�res facultative contient au moins une option.
screenX= 	[pixels] 	JavaScript 1.2Netscape 4.0 	valeur horizontale du coin en haut et � gauche de la nouvelle fen�tre en pixels par exemple screenX=100.
screenY= 	[pixels] 	JavaScript 1.2Netscape 4.0 	valeur verticale du coin en haut et � gauche de la nouvelle fen�tre en pixels par exemple screenY=30.
scrollbars= 	yes|no 	JavaScript 1.0Netscape 2.0MS IE 3.0 	Si oui (yes), la fen�tre re�oit des barres de d�filement. Si non, (no), l'utilisateur ne peut pas faire d�filer la fen�tre. Le r�glage par d�faut est no, pour l'Explorer Internet cependant seulement si la cha�ne de caract�res facultative contient au moins une option.
status= 	yes|no 	JavaScript 1.0Netscape 2.0MS IE 3.0 	Si oui (yes), la fen�tre re�oit sa propre barre d'�tat. Si non (no= r�glage par d�faut), elle ne re�oit pas de barre d'�tat; pour l'Explorer Internet cependant seulement si la cha�ne de caract�res facultative contient au moins une option.
toolbar= 	yes|no 	JavaScript 1.0Netscape 2.0MS IE 3.0 	Si oui (yes), la fen�tre re�oit sa propre barre d'outils. Si non (no= r�glage par d�faut), elle ne re�oit pas de barre d'outils; pour l'Explorer Internet cependant seulement si la cha�ne de caract�res facultative contient au moins une option.
top= 	[pixels] 	JavaScript 1.2Netscape 4.0MS IE 4.0 	Valeur verticale du coin sup�rieur gauche de la nouvelle fen�tre en pixels par exemple top=100.
width= 	[pixels] 	JavaScript 1.0Netscape 2.0MS IE 3.0 	largeur de la nouvelle fen�tre en pixels par exemple width=400.
*/
	public static function AddJavaScript()
	{
		print(self::GetJavaScript());
	}

	public static function GetJavaScript()
	{
		$ret = '<script type="text/javascript">';
		$ret .= 'function openpopup(Url, WWidth, WHeight, WProperties)';
		$ret .= '{';
		$ret .= 'window.open(Url, \'\', \'width=\' + (WWidth+20) + \',height=\' + (WHeight+20) + \',\' + WProperties);';
		$ret .= '}';
		$ret .= '</script>';
		
		return $ret;
	}
	
	private static function PrepareProperties($properties=',')
	{
		return(substr($properties, 0, strlen($properties)-1));
	}
	
	public static function Open($url, $width, $height, $properties=',')
	{
		printf("<script type=\"text/javascript\">openpopup('%s', '%u', '%u', '%s');</script>", $url, $width, $height, self::PrepareProperties($properties));
	}

	public static function CreateLink($label, $url, $width, $height, $properties=',')
	{
		printf("<span style=\"cursor: pointer;\" onclick=\"openpopup('%s', %u, %u, '%s')\"><u>%s</u></span>", $url, $width, $height, self::PrepareProperties($properties), $label);
	}
}

?>
