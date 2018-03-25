<?php if (! defined('HARMONY_INCLUDE')) die("Harmony Library is not loaded !");
	/**\brief ff
	 *\ingroup sheets
	 *
	 */ 
	class CTemplateData extends CIniFile
	{
		//private $m_Files;
		private $BaseTemplates;
		private $Data;
		private $Formulas;
		private $DataDefinitions = null;
		private $Templates;
		private $TemplatesCount = 0;
		private $Refresh = true;
		private $TemplateName = "";
		public static $ActionDisplay='view';
		public static $ActionEdit='edit';
		public static $ActionSave='save';
		public $DocumentDirectory = null;
		private $m_CategoryClass = "category";
		public $MaxSnapshots=20;
		
		public static function ReadTDAHeader($BaseTemplates, $DocumentTemplatesDirectory, $documentdir, $Filename)
		{
			//$this->GetInformations();
			if ($hFile = fopen($documentdir.'/'.$Filename, "r")) {
				fgets($hFile);
				$buffer = fgets($hFile);
				$tddfile=substr($buffer, 4, strlen($buffer)-6);
				$tdddir=substr($tddfile, 0, strlen($tddfile)-4);
				$ret = CTemplateDataDefinitions::ReadTDDHeader($BaseTemplates, $DocumentTemplatesDirectory.'/'.$tdddir, $tddfile);
			}
			else print("Can't open file $Filename !");
			
			return $ret;
		}

		public function SetCategoryClass($CategoryClass)
		{
			$this->m_CategoryClass = $CategoryClass;		
		}
		
		public function __construct($BaseTemplates, $templatedir, $documentdir, $Filename, $refresh=true, $tddfornewfile=null, $dictionary=null, $MaxSnapshots=20)
		{
			parent::__construct($documentdir, $Filename, ($tddfornewfile!== null));
			$this->MaxSnapshots = $MaxSnapshots;
			$this->BaseTemplates = $BaseTemplates;

			try {
				if ($tddfornewfile !== null) $tddfile = $tddfornewfile.'.tdd';
				else $tddfile = $this->GetVar('FILES', 'TDD');

				$this->Name = substr($tddfile, 0, strlen($tddfile)-4);
				$templatedir .= '/'.$this->Name;

				// Load TDD file
				$this->DataDefinitions = new CTemplateDataDefinitions($BaseTemplates, $templatedir, $tddfile);

				$templates = explode(";", $this->DataDefinitions->GetVar('INFOS', 'TPL'));
	
				foreach($templates as $key => $value)
					$templates[$key] = $value;
					
				$this->Templates = new CTemplate($templatedir);
				$this->Templates->set_filenames($templates);
				$this->Templates->AssignVars(array('LOAD_IMAGE' => \Harmony\CHTTPServer::GetScriptDomain().'/image.php?template='.$this->Name.'&image='));
						
				$this->TemplatesCount = count($templates);
				$this->Refresh = $refresh;

				// This is a new file (creation of a TDA file)
				if ($tddfornewfile!== null) {
					$this->DataDefinitions->CreateBlankTDA($documentdir, $Filename);
					$this->ReadFile();
				}
			}
			catch(CException $e) {
				$e->DisplayException();
			}
			
			$this->Refresh();
		}


		public function AssignVars($array)
		{
			$this->Templates->AssignVars($array);
		}
		
		public function AssignVar($var, $value)
		{
			$this->Templates->AssignVar($var, $value);
		}

		public function GetFileArray()
		{
			return $this->Templates->GetFileArray();
		}
		
		public function GetCopyOfTemplateDataDefinition()
		{
			return $this->DataDefinitions;
		}
		
		public function DisableCheck()
		{
			$this->Refresh = false;			
		}
		
		private function Refresh()
		{
			$this->ReadFile();
			$temp_array = $this->DataDefinitions->GetFieldNames();
			foreach($temp_array as $key => $value) $temp_array[$key] = null;
			$this->Data = array_merge($temp_array,
									$this->GetSectionVars('DATA'));
			if ($this->Refresh == true) $ret = $this->CheckData();
			$this->Templates->AssignVars($this->Data);
			$this->Templates->AssignVars(array('TEMPLATE_DIR' => $this->m_Directory));
		}
		
		private function CheckData()
		{
			$ret = "";
			
			foreach($this->Data as $key => $value) {
				if (!$this->DataDefinitions->CheckVar($key, $value)) $ret .='<BR>Erreur sur le champ '.$this->DataDefinitions->GetFieldName($key).' ('.$key.') => >'.$value.'<';
			}
			
			return $ret;
		}
		
		
		public function GetCurrentPage()
		{
			$page = \Harmony\CHTTPServer::GetVar('page');
			if ($page == '') return 0;
			else return $page;
		}
		
		public function GetTotalPage()
		{
			return $this->TemplatesCount;
		}
		
		private function Display($index=0)
		{
			$this->Formulas = $this->DataDefinitions->CalculateFormulas($this->Data);		
			$this->Templates->AssignVars($this->Formulas);
//			print("<BR>---> $this->TemplatesCount");
			
			if ($index === null) $index=0;
			return $this->Templates->GetHtml($index);
		}
		
		public function Export($index=0)
		{
			$this->Formulas = $this->DataDefinitions->CalculateFormulas($this->Data);
			
			$this->Templates->AssignVars($this->Formulas);
			$save_filename = substr($this->m_Filename, 0, strlen($this->m_Filename)-4);
			$now = '_'.Date("Ymd_His");
			$i = 0;
			$this->Templates->Export($i, $this->m_Directory.'/'.$save_filename.$now.'_'.$i.'.html');
		}

		public function CountSnapshots()
		{
			$Prefix = substr($this->m_Filename, 0, strlen($this->m_Filename)-4).'_';
			$PrefixLength = strlen($Prefix);
			$Count = 0;
			
			if (is_dir($this->m_Directory.'/snapshots/')) {
				if ($dh = opendir($this->m_Directory.'/snapshots/')) {
					while (($file = readdir($dh)) !== false) {
						if (!is_dir($this->m_Directory.'/snapshots/'.$file)) {
							if (substr($file, 0, $PrefixLength) == $Prefix)
								$Count++;
						}
					}
					closedir($dh);
				}
			}
			return $Count;
		}
		
		
		
		private function Edit()
		{
			//$tmpTemplate = new CTemplate($this->TemplatesDirectory);
			$this->BaseTemplates->AddFile('sheets_tda_editor_header', 'sheets/tda_editor_header.tpl');
			$this->BaseTemplates->AddFile('sheets_tda_editor_headline', 'sheets/tda_editor_headline.tpl');
			$this->BaseTemplates->AddFile('sheets_tda_editor_footer', 'sheets/tda_editor_footer.tpl');
			$this->BaseTemplates->AddFile('sheets_tda_editor_line', 'sheets/tda_editor_line.tpl');

			$ret = "";
			$categories = array_flip($this->DataDefinitions->GetFieldCategories());

			$previous_category=-1;

			foreach($categories as $category => $value) {
				if ($previous_category==-1) $previous_category = $category;
				$categories[$category] = '';
			}
				
			$buffer = "";
			$previous_row = "";
			$first_col="";
			$second_col="";
			$type= "";
			$this->Refresh();
			//$category = "";
        	
			foreach($this->Data as $key => $value) {
				$category = $this->DataDefinitions->GetFieldCategory($key);
				$row = $this->DataDefinitions->GetFieldRow($key);

			
				if (($row == "" || $row != $previous_row) || ($category != $previous_category && $category != '')) {
					
					$this->BaseTemplates->AssignVar('VarKey', $first_col);
					$this->BaseTemplates->AssignVar('VarValue', $second_col);
					$categories[$previous_category] .= $this->BaseTemplates->GetHtml('sheets_tda_editor_line');
					$first_col = $this->DataDefinitions->GetFieldName($key);

					if (substr($first_col, 0, 1) == "{") {
						$first_col = substr($first_col, 1, strlen($first_col) - 2);
						$first_col = $this->DataDefinitions->GetFormula($first_col);
					}

		        	$first_col = str_replace(' ', '&nbsp;', $first_col).':&nbsp;';
		        		
					$second_col = $this->DataDefinitions->DisplayInputControl($key, $value)."&nbsp;";
					$previous_row = $row;
					if ($category != '') $previous_category = $category;
					
				}
				else {
		        	$second_col .= $this->DataDefinitions->GetFieldName($key).'&nbsp;'.$this->DataDefinitions->DisplayInputControl($key, $value)."&nbsp;";
				}
				
				
			}
			
			
			$document = \Harmony\CHTTPServer::GetVar('document');
			$page = $this->GetCurrentPage();
	
			$this->BaseTemplates->AssignVar('VarKey', $first_col);
			$this->BaseTemplates->AssignVar('VarValue', $second_col);
			$categories[$previous_category] .= $this->BaseTemplates->GetHtml('sheets_tda_editor_line');

			$this->BaseTemplates->AssignVar('TemplateImage', \Harmony\CHTTPServer::GetScriptDomain().'image.php?template='.$this->Name.'&image='.$this->DataDefinitions->GetLogo());
			$this->BaseTemplates->AssignVar('DocumentTitle', substr($document, 0, strlen($document)-4));
			$this->BaseTemplates->AssignVar('TemplateTitle', $this->DataDefinitions->GetTitle());
			$this->BaseTemplates->AssignVar('TemplateAuthor', $this->DataDefinitions->GetAuthor());
			$this->BaseTemplates->AssignVar('TemplateVersion', $this->DataDefinitions->GetVersion());
			$this->BaseTemplates->AssignVar('TemplateDescription', $this->DataDefinitions->GetDescription());
			$this->BaseTemplates->AssignVar('Document', $document);
			$this->BaseTemplates->AssignVar('Page', $page);
			$this->BaseTemplates->AssignVar('SaveButtonText', "Sauver");

			$ret = $this->BaseTemplates->GetHtml('sheets_tda_editor_header');

			foreach($categories as $key => $value) {
				$this->BaseTemplates->AssignVar('Key', $key/*$this->m_CategoryClass*/);
				$this->BaseTemplates->AssignVar('Values', $value);
				$ret .= $this->BaseTemplates->GetHtml('sheets_tda_editor_headline');
			}
	
			$ret .= $this->BaseTemplates->GetHtml('sheets_tda_editor_footer');

			return $ret;
		}
		
		
		private function Save($Filename=null)
		{
			if ($Filename === null)
				$Filename = $this->m_Filename;
			
			$temp_filename = $this->m_Directory.'/'.$Filename.'.tmp';
			
			if (file_exists($temp_filename)) unlink($temp_filename);
			
			if ($FILE=fopen($temp_filename, "w+")) {
				$EOL = "\r\n";
				fwrite($FILE, '[FILES]'.$EOL);
				fwrite($FILE, 'TDD='.$this->GetVar('FILES', 'TDD').$EOL);
//				fwrite($FILE, 'TPL='.$this->GetVar('FILES', 'TPL').$EOL);
				fwrite($FILE, $EOL);
				fwrite($FILE, '[DATA]'.$EOL);
				
				foreach($_POST as $key => $value) {
					if (substr($key, 0, 6) == "FIELD_") {
						fwrite($FILE, substr($key, 6)."=".str_replace("\n", "<br>",$value).$EOL);
					}
				}
				
				fclose($FILE);
				
				if (file_exists($this->m_Directory.'/'.$Filename)) unlink($this->m_Directory.'/'.$Filename);
				
				if (copy($temp_filename, $this->m_Directory.'/'.$Filename)) {
					unlink($temp_filename);
					$this->Refresh();
				}
				else return false;
			}
			else return false;
					
			return true;
		}
				
		public static function GetSnapshotFilename($Filename)
		{
			return 'snapshots/'.substr($Filename, 0, strlen($Filename)-4).'_'.date("Ymd_His", time()).'.tda';
		} 

		
		/***********************************************************************
		 *
		 *
		 *
		 *
		 **********************************************************************/		 		 		 		 		
		public function Action($Param=null)
		{
			$action = \Harmony\CHTTPServer::GetVar('docaction');
			if ($action == '') $action = CTemplateData::$ActionDisplay;
			
			switch($action) {
				case 'edit':
					return $this->Edit();
					break;
				case 'save':
					if (true === $this->Save()) {
						return $this->Display();
					}
					else die("Erreur de sauvegarde !");
					break;
				case 'snapshot':
					if ($this->CountSnapshots() < $this->MaxSnapshots) {
						$filename = $this->m_Directory.'/'.CTemplateData::GetSnapshotFilename($this->m_Filename);
						$this->Save($filename);
					}
					else {
						// Trop de capture, limite atteinte !
						
						
					}
					//break;
				case 'view':
					return $this->Display($Param);
					break;
			}
		}
	}
?>
