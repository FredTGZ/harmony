<?php if (! defined('HARMONY_INCLUDE')) die("Harmony Library is not loaded !");

function WriteConfigFile()
{
	$ret = false;
	$args = func_get_args();
	$arg_num = func_num_args();
	if ($arg_num%2 == 1) $arg_num--;
	
	$filename = PHPBB_THIS_MODULE_PATH.'/config.inc';
	
	if ($hConfigFile = fopen($filename, 'w')) {
		fwrite($hConfigFile, "<?php\n");

		fwrite($hConfigFile, "\t//This file has been automatically generated, don't edit this file but use your module admin page.\n");

		for ($i=0; $i<$arg_num; $i+=2) {
		
			$value = str_replace('"', '\"', $args[$i+1]);
			$value = str_replace("\\", "\\\\", $value);
			
			fwrite($hConfigFile, "\tdefine(\"".$args[$i]."\", \"".$value."\");\n");
		}
		
		fwrite($hConfigFile, "?>");
		fclose($hConfigFile);
		$ret = true;
	}

	return $ret;
}



?>
