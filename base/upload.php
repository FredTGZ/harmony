<?php  namespace Harmony;

define("UPLOADFILE_ID", "3.1415926535897932384");

class CUploadFile extends CBaseObject
{
	protected $LastErrorMessage='';
	protected $LastErrorCode=0;
	
	public static function AddForm($url="")
	{
		if ($url == "") $url = \Harmony\CHTTPServer::GetScriptURL();
		printf('<FORM method="POST" action="%s" ENCTYPE="multipart/form-data"><INPUT type="hidden" name="MAX_FILE_SIZE" VALUE="2048"><INPUT type="hidden" name="UPLOAD_FILE" VALUE="%s"><INPUT type="file" name="upload_filename">&nbsp;<INPUT type="submit"></FORM>', $url, UPLOADFILE_ID);
	}
	
	public static function AddUploadForm($id, $maxsize=2048)
	{
		printf('<INPUT type="hidden" name="UPLOAD_FILE%04u_SIZE" VALUE="%lu">
				<INPUT type="file" size="15" name="UPLOAD_FILE%04u_NAME">&nbsp;(max %.1f Kb)', 
				$id, $maxsize, $id, ($maxsize/1024));
	}
	
	public static function SaveFileIfAny($Destination)
	{
		for ($FileNumber=1; $FileNumber<=9999; $FileNumber++) {
			$filename_tag = sprintf("UPLOAD_FILE%04u_NAME", $FileNumber);

			if (isset($_FILES[$filename_tag])) {
				$LastErrorCode = $_FILES[$filename_tag]['error'];
				
				switch($LastErrorCode)
				{
					case UPLOAD_ERR_INI_SIZE:
						$LastErrorMessage = "Uploaded file exceed upload_max_filesize in php.ini";
						return false;
						break;
					
					case UPLOAD_ERR_FORM_SIZE:
						$LastErrorMessage = "Uploaded file exceed MAX_FILE_SIZE specified in the form.";
						return false;
						break;
					case UPLOAD_ERR_PARTIAL:
						$LastErrorMessage = "Le fichier n'a été que partiellement téléchargé.";
						return false; 
						break;
				
					case UPLOAD_ERR_NO_FILE:
						$LastErrorMessage = "Aucun fichier n'a été téléchargé.";
						return false; 
						break;
					case UPLOAD_ERR_NO_TMP_DIR:
						$LastErrorMessage = "Un dossier temporaire est manquant. Introduit en PHP 4.3.10 et PHP 5.0.3.";
						return false; 
						break;
					
					case UPLOAD_ERR_CANT_WRITE:
						$LastErrorMessage = "Échec de l'écriture du fichier sur le disque. Introduit en PHP 5.1.0.";
						return false; 
						break;
					
					case UPLOAD_ERR_EXTENSION:
						$LastErrorMessage = "L\'envoi de fichier est arrêté par l\'extension. Introduit en PHP 5.2.0.";
						return false; 
						break;
					default:
						$src = $_FILES[$filename_tag]['tmp_name'];
						$dest = $Destination.'/'.$_FILES[$filename_tag]['name'];
						if (file_exists($dest)) unlink($dest);
						rename($src, $dest);
						
						break;
				}
			}
			else return;
		}
	}
}

/*
UPLOAD_ERR_INI_SIZE

UPLOAD_ERR_FORM_SIZE
Valeur : 2. Le fichier téléchargé excède la taille de MAX_FILE_SIZE, qui a été spécifiée dans le formulaire HTML. 

UPLOAD_ERR_PARTIAL
Valeur : 3. Le fichier n'a été que partiellement téléchargé. 

UPLOAD_ERR_NO_FILE
Valeur : 4. Aucun fichier n'a été téléchargé. 

UPLOAD_ERR_NO_TMP_DIR
Valeur : 6. Un dossier temporaire est manquant. Introduit en PHP 4.3.10 et PHP 5.0.3. 

UPLOAD_ERR_CANT_WRITE
Valeur : 7. Échec de l'écriture du fichier sur le disque. Introduit en PHP 5.1.0. 

UPLOAD_ERR_EXTENSION
*/


/*if ((isset($_FILES['filename']['fichier'])&&($_FILES['nom_du_fichier']['error'] == UPLOAD_ERR_OK)) {
$chemin_destination = '/var/www/fichiers/';
move_uploaded_file($_FILES['nom_du_fichier']['tmp_name'], $chemin_destination.$_FILES['nom_du_fichier']['name']);
}*/
//print_r($_FILES);


?>
