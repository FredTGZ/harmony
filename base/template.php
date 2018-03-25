<?php  namespace Harmony;
if (! defined('HARMONY_INCLUDE')) die("Harmony Library is not loaded !");
	/**\brief Template class system
	 *\ingroup base
	 *
	 */ 
	class CTemplate
	{	
		private $m_Data = array();
		private $m_Files = array();
		private $m_TemplatesDirectory = ".";
		private $m_CompiledCode = array();
		private $m_UncompiledCode = array();
		
		/**\brief Constructor
		 *\param[in]	$TemplatesDirectory		Templates directory (default is current dir ("."))
		 */		 		 		
		public function __construct($TemplatesDirectory=".")
		{
			$this->SetTemplatesDirectory($TemplatesDirectory);
		}
	
		public function GetVarsNumber()
		{
			return (count($this->m_Data['.'][0]));
		}
	
		/*
		 * Destroys this template object. Should be called when you're done with it, in order
		 * to clear out the template data so you can load/parse a new template set.
		 */
		public function __destruct()
		{
			$this->m_Data = array();
		}
	
		private function SetTemplatesDirectory($dir)
		{
			try {
				if (!is_dir($dir))
				{
					throw new CTemplateException($dir.' is not a directory');
					return false;
				}
		
				$this->m_TemplatesDirectory = $dir;
				return true;
			}
			catch (CTemplateException $e)
			{
			    print($e->DisplayException());
			    return false;
			}
		}
	
		public function GetFileArray()
		{
			return(array_keys($this->m_Files));
		}
	
	
		public function AddTemplate($key, $filename)
		{
			$this->AddFile($key, $filename);
		}
		
		public function Reload($key, $filename)
		{
			$this->m_Files[$key] = $this->MakeFilename($filename);;
		}
		
		public function AddFileIfNotDone($key, $filename)
		{
			if (!array_key_exists($key, $this->m_Files)) {
				$this->m_Files[$key] = $this->MakeFilename($filename);;
			}
		}
		
		/**
		 * Sets the template filenames for handles. $filename_array
		 * should be a hash of handle => filename pairs.
		 */
		public function AddFile($key, $filename)
		{
			try
			{
				if (!array_key_exists($key, $this->m_Files)) {
					$this->m_Files[$key] = $this->MakeFilename($filename);;
				}
				//else throw new CTemplateException('Template '.$key." already added !");
			}
			catch (CTemplateException $e) {
			    print($e->DisplayExceptionAndDie());
			    return false;
			}
		}


		public function set_filenames($filename_array)
		{
			if (!is_array($filename_array))
			{
				return false;
			}
			reset($filename_array);
			while(list($handle, $filename) = each($filename_array)) 
			{
				//print("<br>DEBUG : Add Template &lt;".$handle.'&gt; =&gt; '.$this->MakeFilename($filename));
				$this->m_Files[$handle] = $this->MakeFilename($filename);
			}
	
			return true;
		}
	
		function Export($handle, $filename)
		{
			if (!$this->LoadFile($handle))
				die("CTemplate->Display(): Couldn't load template file for handle $handle");
			
			$code = $this->m_UncompiledCode[$handle];

			$compiled_code = $this->compile($code, true, 'result');

			$result='';
			eval($compiled_code);
			
			if ($hFile = fopen($filename, 'w+')) {
				fwrite($hFile, $result);
				fclose($hFile);
			}
			
			$this->m_CompiledCode[$handle];
			
		}
	
		function GetHtml($handle)
		{
			ob_start();
			$this->Display($handle);
			$code = ob_get_contents();
			ob_end_clean();
			return($code);
		}
		
		function Display($handle)
		{
			if ($this->LoadFile($handle))  {
				if (!isset($this->m_CompiledCode[$handle]) || empty($this->m_CompiledCode[$handle]))
					$this->m_CompiledCode[$handle] = $this->compile($this->m_UncompiledCode[$handle]);
				eval($this->m_CompiledCode[$handle]);
	
				return true;
			}
		}
	
		function AssignVars($vararray)
		{
			reset ($vararray);
			while (list($key, $val) = each($vararray)) {
				$this->m_Data['.'][0][$key] = $val;
			}
	
			return true;
		}
	
		public function GetVar($varname)
		{
			if (array_key_exists($varname, $this->m_Data['.'][0]))
				return($this->m_Data['.'][0][$varname]);
			else return null;		
		}
	
		function AssignVar($varname, $varval)
		{
			$this->m_Data['.'][0][$varname] = $varval;
			return true;
		}
	
		public function MakeFilename($filename)
		{
			try
			{
				if (substr($filename, 0, 1) != '/')
		       		$filename = $this->m_TemplatesDirectory . '/' . $filename;
		
				if (!file_exists($filename)) throw new CTemplateException('File '.$filename.' does not exist');
			}
			catch (CTemplateException $e) {
			    print($e->DisplayExceptionAndDie());
			    return false;
			}
			
			return $filename;
		}
	
		private function LoadFile($handle)
		{
			try {
				// If the file for this handle is already loaded and compiled, do nothing.
				if (isset($this->m_UncompiledCode[$handle]) && !empty($this->m_UncompiledCode[$handle]))
				{
					return true;
				}
		
				// If we don't have a file assigned to this handle, die.
				if (!isset($this->m_Files[$handle]))
					throw new CTemplateException("No file specified for handle $handle");
		
				$filename = $this->m_Files[$handle];
		
				$str = implode("", @file($filename));

				if (empty($str))
					throw new CTemplateException("File $filename for handle $handle is empty");
		
				$this->m_UncompiledCode[$handle] = $str;
		
				return true;
				
			}
			catch (CTemplateException $e) {
				$e->DisplayExceptionAndDie();				
			}
		}
	
	
	
		/**
		 * Compiles the given string of code, and returns
		 * the result in a string.
		 * If "do_not_echo" is true, the returned code will not be directly
		 * executable, but can be used as part of a variable assignment
		 * for use in assign_code_from_handle().
		 */
		private function compile($code, $do_not_echo = false, $retvar = '')
		{
			// replace \ with \\ and then ' with \'.
			$code = str_replace('\\', '\\\\', $code);
			$code = str_replace('\'', '\\\'', $code);
	
			// change template varrefs into PHP varrefs
	
			// This one will handle varrefs WITH namespaces
			$varrefs = array();
			preg_match_all('#\{(([a-z0-9\-_]+?\.)+?)([a-z0-9\-_]+?)\}#is', $code, $varrefs);
			$varcount = sizeof($varrefs[1]);
			for ($i = 0; $i < $varcount; $i++)
			{
				$namespace = $varrefs[1][$i];
				$varname = $varrefs[3][$i];
				$new = $this->generate_block_varref($namespace, $varname);
	
				$code = str_replace($varrefs[0][$i], $new, $code);
			}
	
			// This will handle the remaining root-level varrefs
			$code = preg_replace('#\{([a-z0-9\-_]*?)\}#is', '\' . ( ( isset($this->m_Data[\'.\'][0][\'\1\']) ) ? $this->m_Data[\'.\'][0][\'\1\'] : \'\' ) . \'', $code);
	
			// Break it up into lines.
			$code_lines = explode("\n", $code);
	
			$block_nesting_level = 0;
			$block_names = array();
			$block_names[0] = ".";
	
			// Second: prepend echo ', append ' . "\n"; to each line.
			$line_count = sizeof($code_lines);
			for ($i = 0; $i < $line_count; $i++)
			{
				$code_lines[$i] = chop($code_lines[$i]);
				if (preg_match('#<!-- BEGIN (.*?) -->#', $code_lines[$i], $m))
				{
					$n[0] = $m[0];
					$n[1] = $m[1];
	
					// Added: dougk_ff7-Keeps templates from bombing if begin is on the same line as end.. I think. :)
					if ( preg_match('#<!-- END (.*?) -->#', $code_lines[$i], $n) )
					{
						$block_nesting_level++;
						$block_names[$block_nesting_level] = $m[1];
						if ($block_nesting_level < 2)
						{
							// Block is not nested.
							$code_lines[$i] = '$_' . $n[1] . '_count = ( isset($this->m_Data[\'' . $n[1] . '.\']) ) ?  sizeof($this->m_Data[\'' . $n[1] . '.\']) : 0;';
							$code_lines[$i] .= "\n" . 'for ($_' . $n[1] . '_i = 0; $_' . $n[1] . '_i < $_' . $n[1] . '_count; $_' . $n[1] . '_i++)';
							$code_lines[$i] .= "\n" . '{';
						}
						else
						{
							// This block is nested.
	
							// Generate a namespace string for this block.
							$namespace = implode('.', $block_names);
							// strip leading period from root level..
							$namespace = substr($namespace, 2);
							// Get a reference to the data array for this block that depends on the
							// current indices of all parent blocks.
							$varref = $this->generate_block_data_ref($namespace, false);
							// Create the for loop code to iterate over this block.
							$code_lines[$i] = '$_' . $n[1] . '_count = ( isset(' . $varref . ') ) ? sizeof(' . $varref . ') : 0;';
							$code_lines[$i] .= "\n" . 'for ($_' . $n[1] . '_i = 0; $_' . $n[1] . '_i < $_' . $n[1] . '_count; $_' . $n[1] . '_i++)';
							$code_lines[$i] .= "\n" . '{';
						}
	
						// We have the end of a block.
						unset($block_names[$block_nesting_level]);
						$block_nesting_level--;
						$code_lines[$i] .= '} // END ' . $n[1];
						$m[0] = $n[0];
						$m[1] = $n[1];
					}
					else
					{
						// We have the start of a block.
						$block_nesting_level++;
						$block_names[$block_nesting_level] = $m[1];
						if ($block_nesting_level < 2)
						{
							// Block is not nested.
							$code_lines[$i] = '$_' . $m[1] . '_count = ( isset($this->m_Data[\'' . $m[1] . '.\']) ) ? sizeof($this->m_Data[\'' . $m[1] . '.\']) : 0;';
							$code_lines[$i] .= "\n" . 'for ($_' . $m[1] . '_i = 0; $_' . $m[1] . '_i < $_' . $m[1] . '_count; $_' . $m[1] . '_i++)';
							$code_lines[$i] .= "\n" . '{';
						}
						else
						{
							// This block is nested.
	
							// Generate a namespace string for this block.
							$namespace = implode('.', $block_names);
							// strip leading period from root level..
							$namespace = substr($namespace, 2);
							// Get a reference to the data array for this block that depends on the
							// current indices of all parent blocks.
							$varref = $this->generate_block_data_ref($namespace, false);
							// Create the for loop code to iterate over this block.
							$code_lines[$i] = '$_' . $m[1] . '_count = ( isset(' . $varref . ') ) ? sizeof(' . $varref . ') : 0;';
							$code_lines[$i] .= "\n" . 'for ($_' . $m[1] . '_i = 0; $_' . $m[1] . '_i < $_' . $m[1] . '_count; $_' . $m[1] . '_i++)';
							$code_lines[$i] .= "\n" . '{';
						}
					}
				}
				else if (preg_match('#<!-- END (.*?) -->#', $code_lines[$i], $m))
				{
					// We have the end of a block.
					unset($block_names[$block_nesting_level]);
					$block_nesting_level--;
					$code_lines[$i] = '} // END ' . $m[1];
				}
				else
				{
					// We have an ordinary line of code.
					if (!$do_not_echo)
					{
						$code_lines[$i] = 'echo \'' . $code_lines[$i] . '\' . "\\n";';
					}
					else
					{
						$code_lines[$i] = '$' . $retvar . '.= \'' . $code_lines[$i] . '\' . "\\n";'; 
					}
				}
			}
	
			// Bring it back into a single string of lines of code.
			$code = implode("\n", $code_lines);
			return $code	;
	
		}
	
	
		/**
		 * Generates a reference to the given variable inside the given (possibly nested)
		 * block namespace. This is a string of the form:
		 * ' . $this->m_Data['parent'][$_parent_i]['$child1'][$_child1_i]['$child2'][$_child2_i]...['varname'] . '
		 * It's ready to be inserted into an "echo" line in one of the templates.
		 * NOTE: expects a trailing "." on the namespace.
		 */
		private function generate_block_varref($namespace, $varname)
		{
			// Strip the trailing period.
			$namespace = substr($namespace, 0, strlen($namespace) - 1);
	
			// Get a reference to the data block for this namespace.
			$varref = $this->generate_block_data_ref($namespace, true);
			// Prepend the necessary code to stick this in an echo line.
	
			// Append the variable reference.
			$varref .= '[\'' . $varname . '\']';
	
			$varref = '\' . ( ( isset(' . $varref . ') ) ? ' . $varref . ' : \'\' ) . \'';
	
			return $varref;
	
		}

		/**
		 * Generates a reference to the array of data values for the given
		 * (possibly nested) block namespace. This is a string of the form:
		 * $this->m_Data['parent'][$_parent_i]['$child1'][$_child1_i]['$child2'][$_child2_i]...['$childN']
		 *
		 * If $include_last_iterator is true, then [$_childN_i] will be appended to the form shown above.
		 * NOTE: does not expect a trailing "." on the blockname.
		 */
		private function generate_block_data_ref($blockname, $include_last_iterator)
		{
			// Get an array of the blocks involved.
			$blocks = explode(".", $blockname);
			$blockcount = sizeof($blocks) - 1;
			$varref = '$this->m_Data';
			// Build up the string with everything but the last child.
			for ($i = 0; $i < $blockcount; $i++)
			{
				$varref .= '[\'' . $blocks[$i] . '.\'][$_' . $blocks[$i] . '_i]';
			}
			// Add the block reference for the last child.
			$varref .= '[\'' . $blocks[$blockcount] . '.\']';
			// Add the iterator for the last child if requried.
			if ($include_last_iterator)
			{
				$varref .= '[$_' . $blocks[$blockcount] . '_i]';
			}
	
			return $varref;
		}
	}
?>
