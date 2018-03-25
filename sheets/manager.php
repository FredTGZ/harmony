<?php if (! defined('HARMONY_INCLUDE')) die("Harmony Library is not loaded !");
	
	class CSheetManager
	{
		protected $DocumentDirectory = ".";
		protected $DocumentTemplatesDirectory = null;
		protected $UserDocumentDirectory = ".";
		protected $CurrentUser = "";
		protected $CurrentAction = null;
		protected $ConfigFile = null;
		protected $Templates = null;
		protected $MaxSnapshots = 20;
		protected $Dictionary = null;
		
		
		
		public function __construct(&$templates, $ConfigFile, $UserNickname, $CodePage='ISO-8859-1')
		{
			$this->ConfigFile = new CConfigFile($ConfigFile, $CodePage);
			$this->MaxSnapshots = $this->ConfigFile->GetConfigValue("MaxSnapshots");
			$this->DocumentDirectory = $this->ConfigFile->GetConfigValue("DocumentDirectory");
			$this->DocumentTemplatesDirectory = $this->ConfigFile->GetConfigValue("DocumentTemplatesDirectory");
			$this->CurrentUser = $UserNickname;
			$this->UserDocumentDirectory = $this->DocumentDirectory . '/' . $this->CurrentUser;
			$this->Dictionary = new CDictionnaryFile($this->ConfigFile->GetConfigValue("Language"));
					
			$this->Templates = $templates;
			$this->Templates->AddTemplate("viewer_header", 'sheets/viewer_header.tpl');
			$this->Templates->AddTemplate("viewer_footer", 'sheets/viewer_footer.tpl');
			$this->Templates->AddTemplate("manager_header", 'sheets/manager_header.tpl');
			$this->Templates->AddTemplate("manager_footer", 'sheets/manager_footer.tpl');
			$this->Templates->AddTemplate("manager_line", 'sheets/manager_line.tpl');
			$this->Templates->AddTemplate("manager_files_header", 'sheets/manager_files_header.tpl');
			$this->Templates->AddTemplate("manager_files_footer", 'sheets/manager_files_footer.tpl');
			$this->Templates->AddTemplate("menubar", 'sheets/menubar.tpl');
			$this->Templates->AddTemplate("manager_tddeditor", 'sheets/editor.tpl');
		
			$this->Templates->AssignVar('LABEL_MY_DOCUMENTS', $this->Dictionary->Translate('mydocuments'));
			$this->Templates->AssignVar('LABEL_NEW', $this->Dictionary->Translate('new'));
			$this->Templates->AssignVar('LABEL_MANAGER', $this->Dictionary->Translate('manager'));
			$this->Templates->AssignVar('LABEL_TEMPLATES', $this->Dictionary->Translate('template').'s');
			$this->Templates->AssignVar('LABEL_CLOSE', $this->Dictionary->Translate('close'));
			$this->Templates->AssignVar('LABEL_EDIT', $this->Dictionary->Translate('edit'));
			$this->Templates->AssignVar('LABEL_PRINT', $this->Dictionary->Translate('print'));
			$this->Templates->AssignVar('LABEL_SNAPSHOT', $this->Dictionary->Translate('snapshot'));
			$this->Templates->AssignVar('LABEL_PREVIOUS', $this->Dictionary->Translate('previous'));
			$this->Templates->AssignVar('LABEL_NEXT', $this->Dictionary->Translate('next'));
			$this->Templates->AssignVar('LABEL_PAGE', $this->Dictionary->Translate('page'));
						
			$this->Templates->AssignVar('ManagerTitle', $this->ConfigFile->GetConfigValue("ManagerTitle"));
			$this->Templates->AssignVar('ManagerIntroduction', $this->ConfigFile->GetConfigValue("ManagerIntroduction"));
			$this->Templates->AssignVar('MaxSnapshotsMessage', $this->ConfigFile->GetConfigValue("MaxSnapshotsMessage"));

			$this->CreateUserTree();		
		}
		
		private function CountSnapshots($Directory, $File)
		{
				
			$Prefix = substr($File, 0, strlen($File)-4).'_';
			$PrefixLength = strlen($Prefix);
			$Count = 0;
				
			if (is_dir($Directory)) {
				if ($dh = opendir($Directory)) {
					while (($file = readdir($dh)) !== false) {
						if (!is_dir($Directory.$file)) {
							if (substr($file, 0, $PrefixLength) == $Prefix)
								$Count++;
						}
					}
					closedir($dh);
				}
			}
			return $Count;
		}
		
		protected function DisplayManager()
		{
			$content = $this->Templates->GetHtml("manager_header");
			$content .= $this->Templates->GetHtml("manager_files_header");
			$i = 0;

			if (is_dir($this->UserDocumentDirectory)) {
			    if ($dh = opendir($this->UserDocumentDirectory)) {
			        while (($file = readdir($dh)) !== false) {
			        if (is_dir($this->UserDocumentDirectory.'/'.$file) == false)
			        	if (strtolower(substr($file, strlen($file)-4))== '.tda') {
			        		$ret = CTemplateData::ReadTDAHeader($this->TemplatesDirectory, $this->DocumentTemplatesDirectory, $this->UserDocumentDirectory, $file);

							$File = new CBaseFile($this->UserDocumentDirectory.'/'.$file);
							$File->GetInformations();
				
							$Snapshots = $this->CountSnapshots($this->UserDocumentDirectory.'/snapshots', $file);
							if ($Snapshots==0) $Snapshots='';
							
							$i++;
			            	$this->Templates->AssignVar("FILE_IMAGE", $ret['IMAGE']);
			            	$this->Templates->AssignVar("FILE_S", substr($file,0,strlen($file)-4));
			            	$this->Templates->AssignVar("FILE_DATE",$File->GetLastModificationDate($this->Dictionary->Translate('DEFAULT_DATETIME_FORMAT', false)));
			            	$this->Templates->AssignVar("FILE_SIZE",$File->GetSize(CBaseFile::$FilesizeFormatKB).'&nbsp;'.$this->Dictionary->Translate('Kb'));
			            	$this->Templates->AssignVar("FILE", $file);
			            	$this->Templates->AssignVar("FILE_TYPE", $ret['TITLE']);
			            	$this->Templates->AssignVar("FILE_SNAPSHOTS", $Snapshots);
			            	$this->Templates->AssignVar("FILE_ACTIONS", 'View/Snapshots/Delete');
			        	
			            	$this->Templates->AssignVar("VIEW", $this->Dictionary->Translate("View"));
			            	
			            	$content .= $this->Templates->GetHtml("manager_line");
						}
			        }
			        closedir($dh);
			    }
			}
			
			$content .= $this->Templates->GetHtml("manager_files_footer");
			$content .= $this->Templates->GetHtml("manager_footer");

			return $content;
		}
		
		protected function CreateUserTree()
		{
			if (! is_dir($this->UserDocumentDirectory)) {
				if (mkdir($this->UserDocumentDirectory, 0777)) {
	
					$http403 = new CHtmlFile($this->UserDocumentDirectory.'/index.html');
					$http403->MakeSimpleFile('', '');
	
					if (mkdir($this->UserDocumentDirectory.'/snapshots', 0777)) {
						$http403 = new CHtmlFile($this->UserDocumentDirectory.'/snapshots/index.html');
						$http403->MakeSimpleFile('', '');
					}
				}
			}
		}

		public function GetTDDList($name="tdd")
		{
			$ret = sprintf('<select name="%s" style="width: 260px;">', $name);
			$tdd = array();
				
			if (is_dir($this->DocumentTemplatesDirectory)) {
			    if ($dh = opendir($this->DocumentTemplatesDirectory)) {
			        while (($file = readdir($dh)) !== false) {
				        if (is_dir($this->DocumentTemplatesDirectory.'/'.$file) && $file != '.' && $file != '..') {
							$infos = CTemplateDataDefinitions::ReadTDDHeader($this->Templates, $this->DocumentTemplatesDirectory.'/'.$file, $file.'.tdd');
							$tdd[$infos['TITLE']] = $file;
				        }
			        }
			        closedir($dh);
			    }
			}

			ksort($tdd);
			
			foreach($tdd as $title => $filename)
				$ret .= sprintf('<option value="%s">%s</option>', $filename, $title);

			$ret.= '</select>';
			return $ret;
		}
	
		public function GetSubAction()
		{
			return \Harmony\CHTTPServer::GetVar('docaction');
		}
	
		public function NeedHeader($action)
		{
			switch($action)
			{
				case 'view':
					return false;
					if (\Harmony\CHTTPServer::GetVar('docaction') == "edit") return false;
					else return false;
				case 'create':
				case 'new':
				case 'manage':
					return true;
				/*case 'tddeditor':
					return false;*/
				default:
					return true;
			}
		}
	
	
		public function Action($action="manage", $document=null, $page=null, $refresh=true)
		{
			$ret = "";
			$this->CurrentAction = $action;
			if ($document==null) $document = \Harmony\CHTTPServer::GetVar("document");
			if ($page==null) $page = \Harmony\CHTTPServer::GetVar("page");
				
			switch($action)
			{
				case 'tddeditor':
					$ret = $this->Templates->GetHtml("manager_header");
					$ret .= $this->Templates->GetHtml("manager_tddeditor");
					$ret .= $this->Templates->GetHtml("manager_footer");

					break;
				case 'new':
					
					$this->Templates->AddTemplate("new", "sheets_manager_new.tpl");
					$this->Templates->AssignVar('SELECT_TDD', $this->GetTDDList($name="tdd"));
					$ret = $this->Templates->GetHtml("manager_header");
					$ret .= $this->Templates->GetHtml("new");
					$ret .= $this->Templates->GetHtml("manager_footer");
					break;
				case 'view':
					$tda = new CTemplateData($this->Templates,
						$this->DocumentTemplatesDirectory,
						$this->UserDocumentDirectory,
						$document,
						$refresh,
						null,
						$this->Dictionary,
						$this->MaxSnapshots);
					
					$this->Templates->AssignVar("DOCUMENT", $document);
					
					$this->Templates->AssignVar('PageCurrent', $tda->GetCurrentPage() + 1);
					$this->Templates->AssignVar('PageTotal', $tda->GetTotalPage());

					$tda->AssignVar("MENUBAR", $this->Templates->GetHtml("menubar"));

					if ($this->GetSubAction() == CTemplateData::$ActionEdit) {
						$content = '';
						//$content = $this->Templates->GetHtml("manager_tddeditor_header");
						$content .= $tda->Action($page);
						//$content .= $this->Templates->GetHtml("manager_tddeditor_footer");
						return $content;
					}
					else return $tda->Action($page);

					break;
				case 'create':
					$TDA = new CTemplateData($this->Templates, $this->DocumentTemplatesDirectory,
						$this->UserDocumentDirectory,
						$_POST['document_title'].'.tda',
						false,
						$_POST['tdd'],
						$this->Dictionary);
					return $this->DisplayManager();
					break;
				case 'manage':
				default:
					return $this->DisplayManager();
					break;
			}
			
			return $ret;
		}
	}
?>
