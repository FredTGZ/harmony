<?php
	function GetColorList($Default)
	{
		global $strings;
		$ColorList = sprintf('<option%s style="color: black; background-color: black;" value="black">%s</option>', ($Default=='black'?' selected':''), $strings['COLOR_BLACK']);
		$ColorList .= sprintf('<option%s style="color: silver; background-color: silver;" value="silver">%s</option>', ($Default=='silver'?' selected':''), $strings['COLOR_SILVER']);
		$ColorList .= sprintf('<option%s style="color: gray; background-color: gray;" value="gray">%s</option>', ($Default=='gray'?' selected':''), $strings['COLOR_GRAY']);
		$ColorList .= sprintf('<option%s style="color: white; background-color: white;" value="white">%s</option>', ($Default=='white'?' selected':''), $strings['COLOR_WHITE']);
		$ColorList .= sprintf('<option%s style="color: maroon; background-color: maroon;" value="maroon">%s</option>', ($Default=='maroon'?' selected':''), $strings['COLOR_MAROON']);
		$ColorList .= sprintf('<option%s style="color: red; background-color: red;" value="red">%s</option>', ($Default=='red'?' selected':''), $strings['COLOR_RED']);
		$ColorList .= sprintf('<option%s style="color: purple; background-color: purple;" value="purple">%s</option>', ($Default=='purple'?' selected':''), $strings['COLOR_PURPLE']);
		$ColorList .= sprintf('<option%s style="color: fuchsia; background-color: fuchsia;" value="fuchsia">%s</option>', ($Default=='fuchsia'?' selected':''), $strings['COLOR_FUCHSIA']);
		$ColorList .= sprintf('<option%s style="color: green; background-color: green;" value="green">%s</option>', ($Default=='green'?' selected':''), $strings['COLOR_GREEN']);
		$ColorList .= sprintf('<option%s style="color: lime; background-color: lime;" value="lime">%s</option>', ($Default=='lime'?' selected':''), $strings['COLOR_LIME']);
		$ColorList .= sprintf('<option%s style="color: olive; background-color: olive;" value="olive">%s</option>', ($Default=='olive'?' selected':''), $strings['COLOR_OLIVE']);
		$ColorList .= sprintf('<option%s style="color: yellow; background-color: yellow;" value="yellow">%s</option>', ($Default=='yellow'?' selected':''), $strings['COLOR_YELLOW']);
		$ColorList .= sprintf('<option%s style="color: navy; background-color: navy;" value="navy">%s</option>', ($Default=='navy'?' selected':''), $strings['COLOR_NAVY']);
		$ColorList .= sprintf('<option%s style="color: blue; background-color: blue;" value="blue">%s</option>', ($Default=='blue'?' selected':''), $strings['COLOR_BLUE']);
		$ColorList .= sprintf('<option%s style="color: teal; background-color: teal;" value="teal">%s</option>', ($Default=='teal'?' selected':''), $strings['COLOR_TEAL']);
		$ColorList .= sprintf('<option%s style="color: aqua; background-color: aqua;" value="aqua">%s</option>', ($Default=='aqua'?' selected':''), $strings['COLOR_AQUA']);
		return $ColorList;
	}

	function GetLanguageList($Default)
	{
		$LanguageList = sprintf('<option%s value="english">english</option>', ($Default=='english'?' selected':''));
		$LanguageList .= sprintf('<option%s value="french">french</option>', ($Default=='french'?' selected':''));
		$LanguageList .= sprintf('<option%s value="german">german</option>', ($Default=='german'?' selected':''));
		$LanguageList .= sprintf('<option%s value="italian">italian</option>', ($Default=='italian'?' selected':''));
		$LanguageList .= sprintf('<option%s value="spanish">spanish</option>', ($Default=='spanish'?' selected':''));
		return $LanguageList;
	}

	function GetFontList($Default)
	{
		$FontList = '';
		$FontList .= sprintf('<option value="Monospace" %s>Monospace</option>', ($Default=='Monospace'?' selected':''));
		$FontList .= sprintf('<option value="Serif" %s>Serif</option>', ($Default=='Serif'?' selected':''));
		$FontList .= sprintf('<option value="Sans-Serif" %s>Sans-Serif</option>', ($Default=='Sans-Serif'?' selected':''));
		$FontList .= sprintf('<option value="Cursive" %s>Cursive</option>', ($Default=='Cursive'?' selected':''));
		$FontList .= sprintf('<option value="Fantasy" %s>Fantasy</option>', ($Default=='Fantasy'?' selected':''));
		return $FontList;
	}

	function GetFontsizeList($Default)
	{
		$FontsizeList = '';
		for ($i=6; $i<12; $i++)
			$FontsizeList .= sprintf('<option value="%u" %s>%s</option>', $i, ($Default==$i?' selected':''), $i);
			
		for ($i=12; $i<=28; $i++)
			$FontsizeList .= sprintf('<option value="%u" %s>%s</option>', $i, ($Default==$i?' selected':''), $i);
			
		$FontsizeList .= sprintf('<option value="36" %s>36</option>', ($Default==36?' selected':''));
		$FontsizeList .= sprintf('<option value="48" %s>48</option>', ($Default==48?' selected':''));
		$FontsizeList .= sprintf('<option value="72" %s>72</option>', ($Default==72?' selected':''));

		return $FontsizeList;
	}


	function GetParagraphStyle($varname, $family, $bold, $italic, $underline, $color, $background_color)
	{
		$ret = '<table border="0" cellspacing="0" cellpadding="2" style="background-color:#F0EEE2; font-family: Sans-Serif; font-size: 10pt; border: 1px black solid; vertical-align: top;"><tr>';
		$ret .= '<td width="1" style="background-color: red;"></td>';
		$ret .= '<td><table style="font-family: Sans-Serif; font-size: 10pt;"><tr><td>Font:&nbsp;</td><td>';
		$ret .= sprintf('<select name="%s_FAMILY" style="font-family: Sans-Serif; font-size: 10pt;">', $varname);
		$ret .= GetFontList($family);
		$ret .= '</select></td></tr>';
		
		$ret .= '<tr><td>Size:</td><td>';
		$ret .= sprintf('<select name="%s_SIZE" style="font-family: Sans-Serif; font-size: 10pt;">', $varname);
		$ret .= GetFontsizeList(10);
		$ret .= '</select>&nbsp;pt</td></tr></table>';
		
		$ret .= '</td><td>';
		
		$ret .= sprintf('<table border="0" style="font-family: Sans-Serif; font-size: 10pt;"><tr><td>Text&nbsp;color:&nbsp;</td><td><select name="%s_COLOR" style="font-family: Sans-Serif; font-size: 10pt;">', $varname);
		$ret .= sprintf('<option%s value="default">browser</option>', ($background_color=='default'?' selected':''));
		$ret .= GetColorList($color);
		$ret .= '</select></td></tr>';

		$ret .= sprintf('<tr><td>Background&nbsp;color:&nbsp;</td><td><select name="%s_BACKGROUND_COLOR" style="font-family: Sans-Serif; font-size: 10pt;">', $varname);
		$ret .= sprintf('<option%s value="transparent">transparent</option>', ($background_color=='transparent'?' selected':''));
		$ret .= GetColorList($background_color);
		$ret .= '</select></td></tr></table>';
		
		$ret .= '</td><td>';
		
		$ret .= sprintf('<input type="checkbox" name="%s_BOLD"%s>&nbsp;Bold', $varname, ($bold?' checked':''));
		$ret .= sprintf('<br><input type="checkbox" name="%s_ITALIC"%s>&nbsp;Italic', $varname, ($italic?' checked':''));
		$ret .= sprintf('<br><input type="checkbox" name="%s_ITALIC"%s>&nbsp;Underline', $varname, ($underline?' checked':''));
		$ret .= '</td><td>';
		$ret .= sprintf('<input type="checkbox" name="%s_STRIKETHROUGH"%s>&nbsp;Strikethrough', $varname, ($underline?' checked':''));
		$ret .= sprintf('<br><input type="checkbox" name="%s_OVERLINE"%s>&nbsp;Overline', $varname, ($underline?' checked':''));
		
		$ret .= '</td><td>';

		$ret .= '</td></tr></table>';
		
		return $ret;
	}
?>
