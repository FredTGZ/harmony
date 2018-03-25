<?php namespace Harmony\cache;
	//require_once("phpfile.inc");
	//require_once("bbfile.inc");

/**\brief Cache management
 *\ingroup cache
 *
 */ 
	class CCache
	{
		public static $TypeBB = 'bb';
		public static $TypeHTML = 'html';
		public static $TypePHP = 'php';
		public static $TypeCache = 'cache';
		public static $TypeUnknown = '';
		
		public static $ExtensionBB = '.bb';
		public static $ExtensionHTML = '.html';
		public static $ExtensionPHP = '.php';
		public static $ExtensionCache = '.cache';

		public static $ModeDefault = 0;		// Recalculate if source is more recent
		public static $ModeRecalculate = 1;	// Force recalculation
		public static $ModeCacheOnly = 2;	// Never recalculate
		public static $ModePHPDB = 3;		// Never recalculate
		
		
		
		public function GetDocument($document, $mode = 0, &$File=null)
		{
			//global $document;
			$filetype = "";
			$source_more_recent = false;
			$renewed = false;
			$filename = CCache::GetFilename($document, $filetype, $mode, $renewed);
			$code = "";
			$header = null;

			switch($filetype)
			{
				case (CCache::$TypeBB):
					$File = new CBBCodeFile($filename);
					$header = $File->GetHeader();
					break;
				case (CCache::$TypePHP):
					$File = new CPHPFile($filename);
					$header = $File->GetHeader();
					break;
				case (CCache::$TypeHTML):
					$File = new CHTMLFile($filename);
					$header = $File->GetHeader();
					break;
				case (CCache::$TypeCache):
					$filename = $document.".cache";
					$File = new CHTMLFile($filename);
					break;
				default:
					$code = 404;
					$header = null;
					$File = null;
					return;
					break;
			}


			$code = $File->GetHTML();
			if ($renewed) CCache::Create($document, $header, $code);

			return $code;
		}
		
		
		/*
		*
		*
		*/				
		private static function CompareWithCache($filename, $filename_cache)
		{
			if (file_exists($filename_cache)) {
				if (filemtime($filename_cache)<filemtime($filename))
					return true;
				else
					return false;
			}
			else return true;
		}
		
		
		/*! \brief Return the full cache filename for a specified file
		*
		*
		*
		*		
		*/						
		public static function GetFilename($filepath, &$filetype, $mode, &$renewed)
		{
			$cache_filename = $filepath.CCache::$ExtensionCache;
			$testing_filename = '';
		
			$true_filename = "";
			$filetype = CCache::$TypeUnknown;
			$renewed = false;
			
			if ((($mode == CCache::$ModeDefault) || ($mode == CCache::$ModeCacheOnly)) && (file_exists($filepath.CCache::$ExtensionCache))) {
				$filetype = CCache::$TypeCache;
				$true_filename = $filepath.CCache::$ExtensionCache;

				if ($mode == CCache::$ModeCacheOnly) return($true_filename);
			}
			
			if (file_exists($filepath.(CCache::$ExtensionBB))) {
				$testing_filename = $filepath.CCache::$ExtensionBB;
				
				if (($mode == CCache::$ModeRecalculate) || (($mode == CCache::$ModeDefault) && CCache::CompareWithCache($testing_filename, $cache_filename))) {
					$filetype = CCache::$TypeBB;
					$true_filename = $testing_filename;
					$renewed = true;
				}
				else $true_filename = $testing_filename;
			}
			elseif (file_exists($filepath.CCache::$ExtensionPHP)) {
				$testing_filename = $filepath.CCache::$ExtensionPHP;
				
				if (($mode == CCache::$ModeRecalculate) || (($mode == CCache::$ModeDefault) && CCache::CompareWithCache($testing_filename, $cache_filename))) {
					$filetype = CCache::$TypePHP;
					$true_filename = $testing_filename;
					$renewed = true;
				}
			}
			elseif (file_exists($filepath.CCache::$ExtensionHTML)) {
				$testing_filename = $filepath.CCache::$ExtensionHTML;
				
				if (($mode == CCache::$ModeRecalculate) || (($mode == CCache::$ModeDefault) && CCache::CompareWithCache($testing_filename, $cache_filename))) {
					$filetype = CCache::$TypeHTML;
					$true_filename = $testing_filename;
					$renewed = true;
				}
			}
		
			return($true_filename);
		}
		
		public static function GetSource($filepath, &$filetype)
		{
			$base_filename = substr($filepath, 0, strlen($filepath)-6);
		
			if (file_exists($base_filename.CCache::$ExtensionBB)) {
				$filetype = CCache::$TypeBB;
				return($base_filename.CCache::$ExtensionBB);
			}
		
			if (file_exists($base_filename.CCache::$ExtensionPHP)) {
				$filetype = CCache::$TypePHP;
				return($base_filename.CCache::$ExtensionPHP);
			}
		
			if (file_exists($base_filename.CCache::$ExtensionHTML)) {
				$filetype = CCache::$TypeHTML;
				return($base_filename.CCache::$ExtensionHTML);
			}
		
			$filetype = 'UNKNOWN';
			return('');
		}
		
		public static function Create($filename, $header, $content)
		{
			if ($FILE = fopen($filename.'.'.(CCache::$TypeCache), "w+")) {
				if ($header != null) fwrite($FILE, $header->GetData());
				else fwrite($FILE, "<!-- HEADER ERROR -->");
				fwrite($FILE, $content);
				fclose($FILE);				
			}
		}
		
		public static function DisplayCache($filename, &$File)
		{
			return file_get_contents($filename.'.'.(CCache::$TypeCache));
		}
	}
?>
