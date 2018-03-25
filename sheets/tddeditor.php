<?php if (! defined('HARMONY_INCLUDE')) die("Harmony Library is not loaded !");
class CTDDEditor
{
	private $TemplateDataDefinitions = null;
	
	public function __construct($templatedir, $filename)
	{
		$this->TemplateDataDefinitions = new CTemplateDataDefinitions($templatedir, $filename);
		
		$this->TemplateDataDefinitions->ReadFile();
	
	}


}
/* Sp�cifications de l'�diteur
 * 
 * Permet de g�rer plusieurs pages
 * Chaque page a une image de fond (upload)
 * 
 * Possibilit� de charger des images suppl�mentaires
 * 
 * La liste des champs disponibles est plac�es � droite
 * un aper�u de la page sur la gauche
 * 
 * Champ1	|Aper�u de la page	| 
 * Champ2	| 					|
 * Champ3	| 					| 
 * 			|  					|
 * 			| 					|
 * 			| 					|
 * 			| 					|
 * 
 * En double cliquant sur un champ, on le place par d�faut en (0,0), width=100px height=25px, font-size=12pt, font-family=Sans-Serif
 * 
 * 
 */
?>
