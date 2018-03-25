<?php if (! defined('HARMONY_INCLUDE')) die("Harmony Library is not loaded !");
	function LoadCommonResources($language="english")
	{
		global $week_days;
		global $months;
		global $strings;
		global $phpbb_version;
		global $lang;

		if ($phpbb_version == 2)
			require_once(PHPBB_PATH."/language/lang_".$language."/lang_main.php");
		elseif ($phpbb_version == 3)
			require_once(PHPBB_PATH."/language/".$language."/common.php");		
		
		$week_days[0] = $lang['datetime']['Monday'];
		$week_days[1] = $lang['datetime']['Tuesday'];
		$week_days[2] = $lang['datetime']['Wednesday'];
		$week_days[3] = $lang['datetime']['Thursday'];
		$week_days[4] = $lang['datetime']['Friday'];
		$week_days[5] = $lang['datetime']['Saturday'];
		$week_days[6] = $lang['datetime']['Sunday'];

		$months[0] = $lang['datetime']['January'];
		$months[1] = $lang['datetime']['February'];
		$months[2] = $lang['datetime']['March'];
		$months[3] = $lang['datetime']['April'];
		$months[4] = $lang['datetime']['May'];
		$months[5] = $lang['datetime']['June'];
		$months[6] = $lang['datetime']['July'];
		$months[7] = $lang['datetime']['August'];
		$months[8] = $lang['datetime']['September'];
		$months[9] = $lang['datetime']['October'];
		$months[10] = $lang['datetime']['November'];
		$months[11] = $lang['datetime']['December'];

		switch(strtolower($language)) {
			case 'fr_x_strict':
			case 'french':
//				$week_days = array("Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi", "Dimanche");
//				$months = array("Janvier", "Février", "Mars", "Avril", "Mai", "Juin", "Juillet", "Août", "septembre", "Octobre", "Novembre", "Décembre");
				$strings = array(	"ADD" => "Ajouter",
									"UPDATE" => "Mise à jour",
									"DELETE" => "Supprimer",
									"PREVIOUS" => "Précédent",
									"CURRENT" => "Courant",
									"NEXT" => "Suivant",
									"PRIVATE_MSG" => "Message privé",
									"CONTACT" => "Contacter",
									"COLOR_BLACK" => "noir",
									"COLOR_SILVER" => "argent",
									"COLOR_GRAY" => "gris",
									"COLOR_WHITE" => "blanc",
									"COLOR_MAROON" => "marron",
									"COLOR_RED" => "rouge",
									"COLOR_PURPLE" => "violet",
									"COLOR_FUCHSIA" => "fuchsia",
									"COLOR_GREEN" => "vert",
									"COLOR_LIME" => "lime",
									"COLOR_OLIVE" => "olive",
									"COLOR_YELLOW" => "jaune",
									"COLOR_NAVY" => "marine",
									"COLOR_BLUE" => "bleu",
									"COLOR_TEAL" => "teal",
									"COLOR_AQUA" => "turquoise"									
								);
				break;
			case 'deutch':
//				$week_days = array("Montag", "Dienstag", "Mittwoch", "Donnerstag", "Freitag", "Samstag", "Sonntag");
//				$months = array("Januar", "Februar", "März", "April", "Mai", "Juni", "Juli", "August", "September", "Oktober", "November", "Dezember");
				$strings = array(	"ADD" => "Ajouter",
									"UPDATE" => "Mise à jour",
									"DELETE" => "Supprimer",
									"PREVIOUS" => "Précédent",
									"CURRENT" => "Courant",
									"NEXT" => "Suivant",
									"PRIVATE_MSG" => "Private Message",
									"CONTACT" => "Contact",
									"COLOR_BLACK" => "black",
									"COLOR_SILVER" => "silver",
									"COLOR_GRAY" => "gray",
									"COLOR_WHITE" => "white",
									"COLOR_MAROON" => "maroon",
									"COLOR_RED" => "red",
									"COLOR_PURPLE" => "purple",
									"COLOR_FUCHSIA" => "fuchsia",
									"COLOR_GREEN" => "green",
									"COLOR_LIME" => "lime",
									"COLOR_OLIVE" => "olive",
									"COLOR_YELLOW" => "yellow",
									"COLOR_NAVY" => "navy",
									"COLOR_BLUE" => "blue",
									"COLOR_TEAL" => "teal",
									"COLOR_AQUA" => "aqua");
				break;
			case 'spanish':
//				$week_days = array("Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado", "Domingo");
//				$months = array("Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");
				$strings = array(	"ADD" => "Ajouter",
									"UPDATE" => "Mise à jour",
									"DELETE" => "Supprimer",
									"PREVIOUS" => "Précédent",
									"CURRENT" => "Courant",
									"NEXT" => "Suivant",
									"PRIVATE_MSG" => "Private Message",
									"CONTACT" => "Contact",
									"COLOR_BLACK" => "black",
									"COLOR_SILVER" => "silver",
									"COLOR_GRAY" => "gray",
									"COLOR_WHITE" => "white",
									"COLOR_MAROON" => "maroon",
									"COLOR_RED" => "red",
									"COLOR_PURPLE" => "purple",
									"COLOR_FUCHSIA" => "fuchsia",
									"COLOR_GREEN" => "green",
									"COLOR_LIME" => "lime",
									"COLOR_OLIVE" => "olive",
									"COLOR_YELLOW" => "yellow",
									"COLOR_NAVY" => "navy",
									"COLOR_BLUE" => "blue",
									"COLOR_TEAL" => "teal",
									"COLOR_AQUA" => "aqua");
				break;
			case 'italian':
//				$week_days = array("Lunedì", "Martedì", "Mercoledì", "Giovedì", "Venerdì", "Sabato", "Domenica");
//				$months = array("Gennaio", "Febbraio", "Marzo", "Aprile", "Maggio", "Giugno", "Luglio", "Agosto", "Settembre", "Ottobre", "Novembre", "Dicembre");
				$strings = array(	"ADD" => "Aggiungere",
									"UPDATE" => "Collocamento",
									"DELETE" => "Sopprimere",
									"PREVIOUS" => "Precedente",
									"CURRENT" => "Seguente",
									"NEXT" => "Corrente",
									"PRIVATE_MSG" => "Private Message",
									"CONTACT" => "Contact",
									"COLOR_BLACK" => "black",
									"COLOR_SILVER" => "silver",
									"COLOR_GRAY" => "gray",
									"COLOR_WHITE" => "white",
									"COLOR_MAROON" => "maroon",
									"COLOR_RED" => "red",
									"COLOR_PURPLE" => "purple",
									"COLOR_FUCHSIA" => "fuchsia",
									"COLOR_GREEN" => "green",
									"COLOR_LIME" => "lime",
									"COLOR_OLIVE" => "olive",
									"COLOR_YELLOW" => "yellow",
									"COLOR_NAVY" => "navy",
									"COLOR_BLUE" => "blue",
									"COLOR_TEAL" => "teal",
									"COLOR_AQUA" => "aqua");
				break;
			case 'english':
			default:
//				$week_days = array("Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday");
//				$months = array("January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");
				$strings = array(	"ADD" => "Add",
									"UPDATE" => "Update",
									"DELETE" => "Delete",
									"PREVIOUS" => "Previous",
									"CURRENT" => "Current",
									"NEXT" => "Next",
									"PRIVATE_MSG" => "Private Message",
									"CONTACT" => "Contact",
									"COLOR_BLACK" => "black",
									"COLOR_SILVER" => "silver",
									"COLOR_GRAY" => "gray",
									"COLOR_WHITE" => "white",
									"COLOR_MAROON" => "maroon",
									"COLOR_RED" => "red",
									"COLOR_PURPLE" => "purple",
									"COLOR_FUCHSIA" => "fuchsia",
									"COLOR_GREEN" => "green",
									"COLOR_LIME" => "lime",
									"COLOR_OLIVE" => "olive",
									"COLOR_YELLOW" => "yellow",
									"COLOR_NAVY" => "navy",
									"COLOR_BLUE" => "blue",
									"COLOR_TEAL" => "teal",
									"COLOR_AQUA" => "aqua"	);
				break;
		}
	}
?>
