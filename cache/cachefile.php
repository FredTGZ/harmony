<?php namespace Harmony\cache;
	/***************************************************************************
	 * CCacheFileHeader
	 *
	 * Author: Herrou Frederic
	 * 
	 * History:
	 * --------
	 * 2007-04-23 Herrou Frederic  Original version 	 	 	 
	 *
	 **************************************************************************/	 	 	 	 	
/*
Header:
=======
<!--{TITLE:}{CACHE:TRUE|FALSE}{REVISIONS:[NNN:YYYY-MM-DD HH:MM:SS,XXXX][YYYY-MM-DD HH:MM:SS=>XXXX][YYYY-MM-DD HH:MM:SS=>XXXX]}-->
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
            FALSE alors le aucun cache ne sera créé.
CSS         Facultatif. URL de la feuille de style utilisée en adjonction de la
            principale.
KEYWORDS    Facultatif. Mots clés, sépararés par des virgules ",".
LANGUAGE	Facultatif (Langue du système par défaut). Peut prendre les valeurs
            DE, FR, EN, SP.
*/	
	//!
	
	class CCacheFile
	{
		protected $m_Filename;
		protected $m_Header;
		
		
		public Function ReadHeader()
		{
		
		}
	
	
	}




?>
