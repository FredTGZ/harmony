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
            NNN                  Num�ro de la r�vision (commence � 0)
            YYYY-MM-DD HH:MM:SS  Date de la r�vision
            XXXX                 Nom du r�dacteur
CACHE       Facultatif (TRUE par d�faut). D�finit � TRUE ou FALSE, si c'est
            FALSE alors le aucun cache ne sera cr��.
CSS         Facultatif. URL de la feuille de style utilis�e en adjonction de la
            principale.
KEYWORDS    Facultatif. Mots cl�s, s�parar�s par des virgules ",".
LANGUAGE	Facultatif (Langue du syst�me par d�faut). Peut prendre les valeurs
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
