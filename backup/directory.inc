<?php namespace Harmony\backup;

/*		}
		catch (CDBException $e) {
		    print($e->DisplayExceptionAndDie());
		    return false;
		}

*/
	class CBackupDirectory extends CBaseObject
	{
		private $Directory = "";
		private $Zip = null;

		public function __construct($directory)
		{
			$this->Directory = $directory;
		}
		
		private function BackupOneDirectory($Archive, $Directory)
		{
			try {
				if (is_dir($Directory)) {
				    if ($dh = opendir($Directory)) {
				        while (($file = readdir($dh)) !== false) {
				        	if ($file != '.' && $file != '..') {
				        		if (is_dir($Directory.'/'.$file)) {
				        			// Close and re-open the archive because a limitation
									$this->Zip->close();		// TEST FREDTGZ
									if ($this->Zip->open($Archive, ZIPARCHIVE::CREATE))
										$this->BackupOneDirectory($Archive, $Directory.'/'.$file);
									else
										throw new CException($this->SaveErrorMessage($Directory.'/'.$file), $this->Zip->status);
								}
								else {
					        		if (!$this->Zip->addFile($Directory.'/'.$file, $Directory.'/'.$file)) {
										throw new CException($this->SaveErrorMessage($Directory.'/'.$file), $this->Zip->status);
									}
								}
							}
				        }
				        
				        closedir($dh);
				    }
				}
			}
			catch (CException $e) {
			    print($e->DisplayException());
			    return false;
			}

		}
	
		private function SaveErrorMessage($context="")
		{
			$return = "";
			
			switch($this->Zip->status)
			{
				case (ZipArchive::ER_OK):
					$return = 'No error';
					break;
				case (ZipArchive::ER_MULTIDISK):
					$return = 'archives ZIP multi-disques non supportées'; 
					break;
				case (ZipArchive::ER_RENAME):
					$return = 'échec du changement de nom du fichier temporaire';  
					break;
				case (ZipArchive::ER_CLOSE):
					$return = 'échec de la fermeture de l\'archive ZIP'; 
					break;
				case (ZipArchive::ER_SEEK):
					$return = 'erreur de pointeur'; 
					break;
				case (ZipArchive::ER_READ):
					$return = 'Read error'; 
					break;
				case (ZipArchive::ER_WRITE):
					$return = 'Write error'; 
					break;
				case (ZipArchive::ER_CRC):
					$return = 'CRC error';  
					break;
				case (ZipArchive::ER_ZIPCLOSED):
					$return = 'ZIP archive closed';  
					break;
				case (ZipArchive::ER_NOENT):
					$return = 'The file '.$context.' does not exist';  
					break;
				case (ZipArchive::ER_EXISTS):
					$return = 'The file '.$context.' already exist';  
					break;
				case (ZipArchive::ER_OPEN):
					$return = 'impossible d\'ouvrir le fichier '.$context;  
					break;
				case (ZipArchive::ER_TMPOPEN):
					$return = 'échec lors de la création du fichier temporaire'; 
					break;
				case (ZipArchive::ER_ZLIB):
					$return = 'Zlib error'; 
					break;
				case (ZipArchive::ER_MEMORY):
					$return = 'échec d\'allocation mémoire';  
					break;
				case (ZipArchive::ER_CHANGED):
					$return = 'l\'entrée a été modifiée';  
					break;
				case (ZipArchive::ER_COMPNOTSUPP):
					$return = 'méthode de compression non supportée'; 
					break;
				case (ZipArchive::ER_EOF):
					$return = 'EOF prématuré'; 
					break;
				case (ZipArchive::ER_INVAL):
					$return = 'Invalid argument'; 
					break;
				case (ZipArchive::ER_NOZIP):
					$return = 'ce n\'est pas une archive ZIP'; 
					break;
				case (ZipArchive::ER_INTERNAL):
					$return = 'Internal error'; 
					break;
				case (ZipArchive::ER_INCONS):
					$return = 'archive ZIP incohérente';
					break;
				case (ZipArchive::ER_REMOVE):
					$return = 'impossible d\'effacer le fichier '.$context;
					break;
				case (ZipArchive::ER_DELETED):
					$return = 'l\'entrée a été supprimée';
					break;
			}
			
			return $return;		
		}
	
		public function Backup($Archive, &$return="")
		{
			try {
				$this->Zip = new ZipArchive();
				
				if ($this->Zip->open($Archive, ZIPARCHIVE::CREATE)) {
					$this->BackupOneDirectory($Archive, $this->Directory);
					$this->Zip->close();
					return true;
				}
				else
					throw new CException(SaveErrorMessage($this->Zip->status), $this->Zip->status);
			}
			catch (CException $e) {
			    print($e->DisplayExceptionAndDie());
			    return false;
			}
		}
	}
?> 
