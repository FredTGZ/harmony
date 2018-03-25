<?php

	/*******************************************************************************
	 * Harmony PHP Library
	 *
	 * Author: FredTGZ
	 * Description:
	 * Main entry, include this in your script and then call Harmony_LoadModule to
	 * load specific modules. 
	 *
	 ******************************************************************************/
	
	class CHarmony
	{	
		private static function Kill($message)
		{
			die(sprintf('<br><br><br><br><br><center><span style="color: red; font-family: Sans-Serif; font-weight: bold; font-size: 12pt;">%s</span></center>', $message));
		}
		
		public static function Init()
		{
			if (! defined('HARMONY_INCLUDE'))
				define('HARMONY_INCLUDE', '../include/harmony');
				
			global $HarmonyLoadedModules;
			$HarmonyLoadedModules = array();

			$ver = CHarmony::GetVersion();
			
			if (version_compare(phpversion(), $ver['phpver']) < 0)
				die ("You need php version &gt;= ".$ver['phpver']);
		}
		
		///Get module information
		public static function GetModuleInfos($name)
		{
			global $loadmodule;
			global $module_infos;
			if (file_exists(HARMONY_INCLUDE.'/'.$name.'/module.version')) {
				include(HARMONY_INCLUDE.'/'.$name.'/module.version');
				$module_infos['isloaded'] = CHarmony::ModuleIsLoaded($name);
				
				//2011-02-21 FredTGZ - Recherche de la version de fichier la plus récente
				if ($module_infos['version'] == '0') {
					$module_infos['version'] = 0;
				
				    if ($dh = opendir(HARMONY_INCLUDE.'/'.$name)) {
				        while (($file = readdir($dh)) !== false) {
				        	if (!is_dir(HARMONY_INCLUDE.'/'.$name.'/'.$file)) {
				        		$tmp = filemtime(HARMONY_INCLUDE.'/'.$name.'/'.$file);
				        		$module_infos['version'] = max($module_infos['version'], $tmp);
							}
						}
						closedir($dh);
				    }
				    
				    $module_infos['version']=date("Ymd", $module_infos['version']);
				}
			    
				return $module_infos;
			}
			else return false;
		}
	
		public static function GetVersion()
		{
			global $harmony_infos;
			global $harmony_minphpver;

			include(HARMONY_INCLUDE.'/harmony.version');
			
			return $harmony_infos;
		}
		
		public static function LoadModule($name)
		{
			global $loadmodule;
			$name = strtolower($name);

			$filename = HARMONY_INCLUDE.'/'.$name.'/module.php';

			if (file_exists($filename)) {
	
				if ($loadmodule !== false) {
					require_once(HARMONY_INCLUDE.'/'.$name.'/module.php');
					global $HarmonyLoadedModules;
					$HarmonyLoadedModules[] = $name;
				}
				else {
					include(HARMONY_INCLUDE.'/'.$name.'/module.php');
				}
			}
			else {
				CHarmony::Kill('Harmony Fatal Error: Cannot load the module ['.$name.'] !<br><br>May be you should define the HARMONY_INCLUDE variable...<br>HARMONY_INCLUDE='.HARMONY_INCLUDE);
			}
		}
	
		public static function IncludeModule($name)
		{
			if (! CHarmony::ModuleIsLoaded($name)) {
				CHarmony::LoadModule($name);
			}
		}
		
		public static function RequireModule($name)
		{
			if (! CHarmony::ModuleIsLoaded($name)) {
				die("This module need the module named ".$name);
			}
		}
	
		public static function ModuleIsLoaded($name)
		{
			global $HarmonyLoadedModules;
			return in_array ($name, $HarmonyLoadedModules, true);
		}
		
		public static function ListModules()
		{
		    if ($dh = opendir(HARMONY_INCLUDE)) {
		    	print('<table border="1" cellspacing="0" cellpadding="2" style="font-family: Sans-Serif; font-size: 8pt; color: black; text-align: left;">');
			            printf('<tr style="vertical-align: top;"><th>%s</th><th align="center">%s</th><th>%s</th><th align="center">%s</th><th>%s</th></tr>',
							'Module',
							'Loaded ?',
							'Name',
							'Version',
							'Description'
							);
				print('<caption style="font-weight: bold; font-size: 12pt;">Harmony: Module list</caption>');
				print('<colgroup><col width="75"><col width="65"><col width="150"><col width="60"><col width="250"></colgroup>');

				$row = 0;
				
		        while (($file = readdir($dh)) !== false) {
		        	if (is_dir(HARMONY_INCLUDE.'/'.$file) && $file != '.' && $file != '..' && $file != 'admin' && $file != 'templates') {
		        		$infos = CHarmony::GetModuleInfos($file);
			            if ($infos !== false) {
							printf('<tr style="vertical-align: top; background-color: %s;"><td>%s</td><td align="center">%s</td><td>%s</td><td align="center">%s</td><td>%s</td></tr>',
								($row++%2==1?'white':'silver'),
								$file,
								'<span style="color: '.($infos['isloaded']?'green;">Yes':'red;">No').'</span>',
								$infos['name'],
								$infos['version'],
								$infos['description']
								);
						}
					}
				}
				closedir($dh);
				print('</table>');
			}
	    }
	}
	
	CHarmony::Init();				// Initialize library
	CHarmony::LoadModule('base');	// Loading base module
	
	global $ClientBrowser;
	$ClientBrowser = new Harmony\CClientBrowser();
?>
