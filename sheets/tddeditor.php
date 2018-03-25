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
/* Spécifications de l'éditeur
 * 
 * Permet de gérer plusieurs pages
 * Chaque page a une image de fond (upload)
 * 
 * Possibilité de charger des images supplémentaires
 * 
 * La liste des champs disponibles est placées à droite
 * un aperçu de la page sur la gauche
 * 
 * Champ1	|Aperçu de la page	| 
 * Champ2	| 					|
 * Champ3	| 					| 
 * 			|  					|
 * 			| 					|
 * 			| 					|
 * 			| 					|
 * 
 * En double cliquant sur un champ, on le place par défaut en (0,0), width=100px height=25px, font-size=12pt, font-family=Sans-Serif
 * 
 * 
 */
?>
