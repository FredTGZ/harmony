<?php  namespace Harmony;
if (! defined('HARMONY_INCLUDE')) die("Harmony Library is not loaded !");
/*
[tag|=param0]param1[/tag]
[tag|=param0]param1\n

"#\[tag=([\w]+?://[\w\#$%&~/.\-;:=,?@\[\]+]*?)\]([^?\n\r\t].*?)\[/tag\]#is";
[tag(\]|\=\w\])](\w))

<html_tag>{0} : {1}</html_tag>

tag
param*/

	require_once("basefile.php");
	require_once("template.php");
	//if(!defined("BBCODE_UID_LEN")) define("BBCODE_UID_LEN", 10);
	
	
/**\brief BBCode Tag
 *\ingroup base
 */ 	class CBBTag
	{
		public $m_Name = "";
		public $m_Param = "";
		public $m_Close = "";
		public $m_EndOfLine = false;
		public $m_HTML = "";
		
		function CBBTag($name,$param, $close, $html, $eol=false)
		{
			$this->m_Name = "";
			$this->m_Param = false;
			$this->m_Close = "";
			$this->m_EndOfLine = false;
			$this->m_HTMLCode = "";
		}
	}
	
	/**\brief BBFile management
	 *\ingroup base
	 */
 	class CBBCodeFile extends CBaseFile
	{
		private $m_Buffer;
		private $m_RenderedBuffer;
		private $m_UID;
		private $m_Template=null;
		private $m_TemplateArray=array();
		protected $m_BBTags = array();
		protected $m_BBTagsHTMLOpen = array();
		protected $m_BBTagsHTMLOpenParam = array();
		protected $m_BBTagsHTMLClose = array();
		
		public function __construct($filename)
		{
			parent::__construct($filename);
			
			$this->m_BBTags = array( "b" => "b", "i" => "i", "u" => "u");
			$this->m_BBTagsHTMLOpen = array( "b" => "<b>", "i" => "<i>", "u" => "<u>");
			$this->m_BBTagsHTMLOpenParam = array( "b" => "<b>", "i" => "<i>", "u" => "<u>");
			$this->m_BBTagsHTMLClose = array( "b" => "</b>", "i" => "</i>", "u" => "</u>");
			//"table", "row", "cell", "popup"
			/*
			Balise simple : #\[TAG\]#is
			Balise paramètre : #\[TAG=(.*?)\]#is
			Balise fermeture : #\[/TAG\]#is
			*/
			$pos = strrpos($filename, '/');
			if ($pos != false) $this->m_Directory = substr($filename, 0, $pos+1);
			else $this->m_Directory = ''; 
			mt_srand( (double) microtime() * 1000000);
			$this->m_Buffer = file_get_contents ($filename, false);
			$pos = strpos($this->m_Buffer, "\n");
			$this->m_Buffer = substr($this->m_Buffer, $pos+1);
			//$this->m_Template = new CTemplate($TemplateDirectory);
			$this->m_Template = new CTemplate(HARMONY_INCLUDE."/templates");
			$this->LoadTemplate();
		}
		
		public function GetHTML()
		{
			$patterns = array();
			$replacements = array();
			
			$patterns[0] = '/\n\s*\[(.*?)\]/';
			$patterns[1] = '/\[\/cell\]\r\n/';
			$patterns[1] = '/\[\/table\]\r\n/';
			/*$patterns[1] = '/marron/';
			$patterns[2] = '/renard/';*/
			$replacements[0] = "\n[\\1]";
			$replacements[1] = "[cell]";
			/*$replacements[1] = 'brun';
			$replacements[2] = 'grizzly';*/
			/*$this->m_RenderedBuffer = str_replace("row]\r\n", "row]", $this->m_RenderedBuffer);
			$this->m_RenderedBuffer = str_replace("cell]\r\n", "row]", $this->m_RenderedBuffer);
			$this->m_RenderedBuffer = str_replace("table]\r\n", "row]", $this->m_RenderedBuffer);
			$this->m_RenderedBuffer = str_replace("\r\n[*]", "[*]", $this->m_RenderedBuffer);*/
			$this->FirstPass();
			$this->m_RenderedBuffer = preg_replace($patterns, $replacements, $this->m_RenderedBuffer);
			//print $this->m_RenderedBuffer;
			$this->SecondPass();
			//\n"
			/*
			table]\n	-> table]
			row]\n	-> table]
			
			*/
			$this->m_RenderedBuffer = str_replace("\n", "<br>", $this->m_RenderedBuffer);
			return $this->m_RenderedBuffer;
		}
		
		
		private function LoadTemplate()
		{
			$tpl_filename = $this->m_Template->MakeFilename('bbcode.tpl');
			$tpl = fread(fopen($tpl_filename, 'r'), filesize($tpl_filename));
		
			// replace \ with \\ and then ' with \'.
			$tpl = str_replace('\\', '\\\\', $tpl);
			$tpl  = str_replace('\'', '\\\'', $tpl);
		
			// strip newlines.
			$tpl  = str_replace("\n", '', $tpl);
		
			// Turn template blocks into PHP assignment statements for the values of $bbcode_tpls..
			$tpl = preg_replace('#<!-- BEGIN (.*?) -->(.*?)<!-- END (.*?) -->#', "\n" . '$bbcode_tpls[\'\\1\'] = \'\\2\';', $tpl);
			
			$bbcode_tpls = array();
			eval($tpl);
			$this->m_TemplateArray = $bbcode_tpls;
	
		
		
			global $lang;//!!!!!!!!!!!!!!
		
			$this->m_TemplateArray['olist_open'] = str_replace('{LIST_TYPE}', '\\1', $this->m_TemplateArray['olist_open']);
			$this->m_TemplateArray['color_open'] = str_replace('{COLOR}', '\\1', $this->m_TemplateArray['color_open']);
			$this->m_TemplateArray['size_open'] = str_replace('{SIZE}', '\\1', $this->m_TemplateArray['size_open']);
			$this->m_TemplateArray['quote_open'] = str_replace('{L_QUOTE}', $lang['Quote'], $this->m_TemplateArray['quote_open']);
			$this->m_TemplateArray['quote_username_open'] = str_replace('{L_QUOTE}', $lang['Quote'], $this->m_TemplateArray['quote_username_open']);
			$this->m_TemplateArray['quote_username_open'] = str_replace('{L_WROTE}', $lang['wrote'], $this->m_TemplateArray['quote_username_open']);
			$this->m_TemplateArray['quote_username_open'] = str_replace('{USERNAME}', '\\1', $this->m_TemplateArray['quote_username_open']);
			$this->m_TemplateArray['code_open'] = str_replace('{L_CODE}', $lang['Code'], $this->m_TemplateArray['code_open']);
			$this->m_TemplateArray['img'] = str_replace('{URL}', '\\1', $this->m_TemplateArray['img']);
			// We do URLs in several different ways..
			$this->m_TemplateArray['url1'] = str_replace('{URL}', '\\1', $this->m_TemplateArray['url']);
			$this->m_TemplateArray['url1'] = str_replace('{DESCRIPTION}', '\\1', $this->m_TemplateArray['url1']);
			$this->m_TemplateArray['url2'] = str_replace('{URL}', 'http://\\1', $this->m_TemplateArray['url']);
			$this->m_TemplateArray['url2'] = str_replace('{DESCRIPTION}', '\\1', $this->m_TemplateArray['url2']);
			$this->m_TemplateArray['url3'] = str_replace('{URL}', '\\1', $this->m_TemplateArray['url']);
			$this->m_TemplateArray['url3'] = str_replace('{DESCRIPTION}', '\\2', $this->m_TemplateArray['url3']);
			$this->m_TemplateArray['url4'] = str_replace('{URL}', 'http://\\1', $this->m_TemplateArray['url']);
			$this->m_TemplateArray['url4'] = str_replace('{DESCRIPTION}', '\\3', $this->m_TemplateArray['url4']);
			
			$this->m_TemplateArray['doclink'] = str_replace('{DOCUMENT}', '\\1', $this->m_TemplateArray['doclink']);
			$this->m_TemplateArray['doclink'] = str_replace('{DESCRIPTION}', '\\2', $this->m_TemplateArray['doclink']);

			$this->m_TemplateArray['popup1'] = str_replace('{URL}', '\\1', $this->m_TemplateArray['popup']);
			$this->m_TemplateArray['popup1'] = str_replace('{DESCRIPTION}', '\\1', $this->m_TemplateArray['popup1']);
			$this->m_TemplateArray['popup2'] = str_replace('{URL}', 'http://\\1', $this->m_TemplateArray['popup']);
			$this->m_TemplateArray['popup2'] = str_replace('{DESCRIPTION}', '\\1', $this->m_TemplateArray['popup2']);
			$this->m_TemplateArray['popup3'] = str_replace('{URL}', '\\1', $this->m_TemplateArray['popup']);
			$this->m_TemplateArray['popup3'] = str_replace('{DESCRIPTION}', '\\2', $this->m_TemplateArray['popup3']);
			$this->m_TemplateArray['popup4'] = str_replace('{URL}', 'http://\\1', $this->m_TemplateArray['popup']);
			$this->m_TemplateArray['popup4'] = str_replace('{DESCRIPTION}', '\\3', $this->m_TemplateArray['popup4']);
			$this->m_TemplateArray['popup5'] = str_replace('{URL}', 'main.php?document=\\1', $this->m_TemplateArray['popup']);
			$this->m_TemplateArray['popup5'] = str_replace('{URL}', 'main.php?document=\\1', $this->m_TemplateArray['popup5']);
			$this->m_TemplateArray['popup5'] = str_replace('{WIDTH}', '\\2', $this->m_TemplateArray['popup5']);
			$this->m_TemplateArray['popup5'] = str_replace('{HEIGHT}', '\\3', $this->m_TemplateArray['popup5']);
			$this->m_TemplateArray['popup5'] = str_replace('{DESCRIPTION}', '\\4', $this->m_TemplateArray['popup5']);
			$this->m_TemplateArray['popup6'] = str_replace('{URL}', 'main.php?document=\\1', $this->m_TemplateArray['popup_simple']);
			$this->m_TemplateArray['popup6'] = str_replace('{DESCRIPTION}', '\\2', $this->m_TemplateArray['popup6']);
	
			$this->m_TemplateArray['row2_open'] = str_replace('{ROWCLASS}', '\\1', $this->m_TemplateArray['row2_open']);
			$this->m_TemplateArray['cell2_open'] = str_replace('{CELLCLASS}', '\\1', $this->m_TemplateArray['cell2_open']);
			$this->m_TemplateArray['table2_open'] = str_replace('{TABLE_CAPTION}', '\\1', $this->m_TemplateArray['table2_open']);

			$this->m_TemplateArray['email'] = str_replace('{EMAIL}', '\\1', $this->m_TemplateArray['email']);
		
			define("BBCODE_TPL_READY", true);
		}
		
		private function FirstPassPDA($text, $uid, $open_tag, $close_tag, $close_tag_new, $mark_lowest_level, $func, $open_regexp_replace = false)
		{
			$open_tag_count = 0;
		
			if (!$close_tag_new || ($close_tag_new == ''))
			{
				$close_tag_new = $close_tag;
			}
		
			$close_tag_length = strlen($close_tag);
			$close_tag_new_length = strlen($close_tag_new);
			$uid_length = strlen($uid);
		
			$use_function_pointer = ($func && ($func != ''));
		
			$stack = array();
		
			if (is_array($open_tag))
			{
				if (0 == count($open_tag))
				{
					// No opening tags to match, so return.
					return $text;
				}
				$open_tag_count = count($open_tag);
			}
			else
			{
				// only one opening tag. make it into a 1-element array.
				$open_tag_temp = $open_tag;
				$open_tag = array();
				$open_tag[0] = $open_tag_temp;
				$open_tag_count = 1;
			}
		
			$open_is_regexp = false;
		
			if ($open_regexp_replace)
			{
				$open_is_regexp = true;
				if (!is_array($open_regexp_replace))
				{
					$open_regexp_temp = $open_regexp_replace;
					$open_regexp_replace = array();
					$open_regexp_replace[0] = $open_regexp_temp;
				}
			}
		
			if ($mark_lowest_level && $open_is_regexp)
			{
				message_die(GENERAL_ERROR, "Unsupported operation for bbcode_first_pass_pda().");
			}
		
			// Start at the 2nd char of the string, looking for opening tags.
			$curr_pos = 1;
			while ($curr_pos && ($curr_pos < strlen($text)))
			{
				$curr_pos = strpos($text, "[", $curr_pos);
		
				// If not found, $curr_pos will be 0, and the loop will end.
				if ($curr_pos)
				{
					// We found a [. It starts at $curr_pos.
					// check if it's a starting or ending tag.
					$found_start = false;
					$which_start_tag = "";
					$start_tag_index = -1;
		
					for ($i = 0; $i < $open_tag_count; $i++)
					{
						// Grab everything until the first "]"...
						$possible_start = substr($text, $curr_pos, strpos($text, ']', $curr_pos + 1) - $curr_pos + 1);
		
						//
						// We're going to try and catch usernames with "[' characters.
						//
						if( preg_match('#\[quote=\\\"#si', $possible_start, $match) && !preg_match('#\[quote=\\\"(.*?)\\\"\]#si', $possible_start) )
						{
							// OK we are in a quote tag that probably contains a ] bracket.
							// Grab a bit more of the string to hopefully get all of it..
							if ($close_pos = strpos($text, '"]', $curr_pos + 9))
							{
								if (strpos(substr($text, $curr_pos + 9, $close_pos - ($curr_pos + 9)), '[quote') === false)
								{
									$possible_start = substr($text, $curr_pos, $close_pos - $curr_pos + 2);
								}
							}
						}
		
						// Now compare, either using regexp or not.
						if ($open_is_regexp)
						{
							$match_result = array();
							if (preg_match($open_tag[$i], $possible_start, $match_result))
							{
								$found_start = true;
								$which_start_tag = $match_result[0];
								$start_tag_index = $i;
								break;
							}
						}
						else
						{
							// straightforward string comparison.
							if (0 == strcasecmp($open_tag[$i], $possible_start))
							{
								$found_start = true;
								$which_start_tag = $open_tag[$i];
								$start_tag_index = $i;
								break;
							}
						}
					}
		
					if ($found_start)
					{
						// We have an opening tag.
						// Push its position, the text we matched, and its index in the open_tag array on to the stack, and then keep going to the right.
						$match = array("pos" => $curr_pos, "tag" => $which_start_tag, "index" => $start_tag_index);
						array_push($stack, $match);
						//
						// Rather than just increment $curr_pos
						// Set it to the ending of the tag we just found
						// Keeps error in nested tag from breaking out
						// of table structure..
						//
						$curr_pos += strlen($possible_start);
					}
					else
					{
						// check for a closing tag..
						$possible_end = substr($text, $curr_pos, $close_tag_length);
						if (0 == strcasecmp($close_tag, $possible_end))
						{
							// We have an ending tag.
							// Check if we've already found a matching starting tag.
							if (sizeof($stack) > 0)
							{
								// There exists a starting tag.
								$curr_nesting_depth = sizeof($stack);
								// We need to do 2 replacements now.
								$match = array_pop($stack);
								$start_index = $match['pos'];
								$start_tag = $match['tag'];
								$start_length = strlen($start_tag);
								$start_tag_index = $match['index'];
		
								if ($open_is_regexp)
								{
									$start_tag = preg_replace($open_tag[$start_tag_index], $open_regexp_replace[$start_tag_index], $start_tag);
								}
		
								// everything before the opening tag.
								$before_start_tag = substr($text, 0, $start_index);
		
								// everything after the opening tag, but before the closing tag.
								$between_tags = substr($text, $start_index + $start_length, $curr_pos - $start_index - $start_length);
		
								// Run the given function on the text between the tags..
								if ($use_function_pointer)
								{
									$between_tags = $func($between_tags, $uid);
								}
		
								// everything after the closing tag.
								$after_end_tag = substr($text, $curr_pos + $close_tag_length);
		
								// Mark the lowest nesting level if needed.
								if ($mark_lowest_level && ($curr_nesting_depth == 1))
								{
									if ($open_tag[0] == '[code]')
									{
										$code_entities_match = array('#<#', '#>#', '#"#', '#:#', '#\[#', '#\]#', '#\(#', '#\)#', '#\{#', '#\}#');
										$code_entities_replace = array('&lt;', '&gt;', '&quot;', '&#58;', '&#91;', '&#93;', '&#40;', '&#41;', '&#123;', '&#125;');
										$between_tags = preg_replace($code_entities_match, $code_entities_replace, $between_tags);
									}
									$text = $before_start_tag . substr($start_tag, 0, $start_length - 1) . ":$curr_nesting_depth:$uid]";
									$text .= $between_tags . substr($close_tag_new, 0, $close_tag_new_length - 1) . ":$curr_nesting_depth:$uid]";
								}
								else
								{
									if ($open_tag[0] == '[code]')
									{
										$text = $before_start_tag . '&#91;code&#93;';
										$text .= $between_tags . '&#91;/code&#93;';
									}
									else
									{
										if ($open_is_regexp)
										{
											$text = $before_start_tag . $start_tag;
										}
										else
										{
											$text = $before_start_tag . substr($start_tag, 0, $start_length - 1) . ":$uid]";
										}
										$text .= $between_tags . substr($close_tag_new, 0, $close_tag_new_length - 1) . ":$uid]";
									}
								}
		
								$text .= $after_end_tag;
		
								// Now.. we've screwed up the indices by changing the length of the string.
								// So, if there's anything in the stack, we want to resume searching just after it.
								// otherwise, we go back to the start.
								if (sizeof($stack) > 0)
								{
									$match = array_pop($stack);
									$curr_pos = $match['pos'];
		//							bbcode_array_push($stack, $match);
		//							++$curr_pos;
								}
								else
								{
									$curr_pos = 1;
								}
							}
							else
							{
								// No matching start tag found. Increment pos, keep going.
								++$curr_pos;
							}
						}
						else
						{
							// No starting tag or ending tag.. Increment pos, keep looping.,
							++$curr_pos;
						}
					}
				}
			} // while
		
			return $text;
		
		} // bbencode_first_pass_pda()
		
	
		private function FirstPass()
		{
			// pad it with a space so we can distinguish between FALSE and matching the 1st char (index 0).
			// This is important; bbencode_quote(), bbencode_list(), and bbencode_code() all depend on it.
			$this->m_RenderedBuffer = " " . $this->m_Buffer;
		
			// [CODE] and [/CODE] for posting code (HTML, PHP, C etc etc) in your posts.
			$this->m_RenderedBuffer = $this->FirstPassPDA($this->m_RenderedBuffer, $this->m_UID, '[code]', '[/code]', '', true, '');
		
			// [QUOTE] and [/QUOTE] for posting replies with quote, or just for quoting stuff.
			$this->m_RenderedBuffer = $this->FirstPassPDA($this->m_RenderedBuffer, $this->m_UID, '[quote]', '[/quote]', '', false, '');
			$this->m_RenderedBuffer = $this->FirstPassPDA($this->m_RenderedBuffer, $this->m_UID, '/\[quote=(\\\".*?\\\")\]/is', '[/quote]', '', false, '', "[quote:".$this->m_UID."=\\1]");
		
			// [list] and [list=x] for (un)ordered lists.
			$open_tag = array();
			$open_tag[0] = "[list]";
		
			// unordered..
			$this->m_RenderedBuffer = $this->FirstPassPDA($this->m_RenderedBuffer, $this->m_UID, $open_tag, "[/list]", "[/list:u]", false, 'CBBCodeFile::replace_listitems');
		
			$open_tag[0] = "[list=1]";
			$open_tag[1] = "[list=a]";
		
			// ordered.
			$this->m_RenderedBuffer = $this->FirstPassPDA($this->m_RenderedBuffer, $this->m_UID, $open_tag, "[/list]", "[/list:o]",  false, 'CBBCodeFile::replace_listitems');
		
			// [color] and [/color] for setting text color
			$this->m_RenderedBuffer = preg_replace("#\[color=(\#[0-9A-F]{6}|[a-z\-]+)\](.*?)\[/color\]#si", "[color=\\1:".$this->m_UID."]\\2[/color:".$this->m_UID."]", $this->m_RenderedBuffer);
		
			// [size] and [/size] for setting text size
			$this->m_RenderedBuffer = preg_replace("#\[size=([1-2]?[0-9])\](.*?)\[/size\]#si", "[size=\\1:".$this->m_UID."]\\2[/size:".$this->m_UID."]", $this->m_RenderedBuffer);
		
			// [b] and [/b] for bolding text.
			$this->m_RenderedBuffer = preg_replace("#\[b\](.*?)\[/b\]#si", "[b:".$this->m_UID."]\\1[/b:".$this->m_UID."]", $this->m_RenderedBuffer);
		
			// [u] and [/u] for underlining text.
			$this->m_RenderedBuffer = preg_replace("#\[u\](.*?)\[/u\]#si", "[u:".$this->m_UID."]\\1[/u:".$this->m_UID."]", $this->m_RenderedBuffer);
		
			// [i] and [/i] for italicizing text.
			$this->m_RenderedBuffer = preg_replace("#\[i\](.*?)\[/i\]#si", "[i:".$this->m_UID."]\\1[/i:".$this->m_UID."]", $this->m_RenderedBuffer);
		
			// [img]image_url_here[/img] code..
			$this->m_RenderedBuffer = preg_replace("#\[img\]((http|ftp|https|ftps)://)([^ \?&=\#\"\n\r\t<]*?(\.(jpg|jpeg|gif|png)))\[/img\]#sie", "'[img:".$this->m_UID."]\\1' . str_replace(' ', '%20', '\\3') . '[/img:".$this->m_UID."]'", $this->m_RenderedBuffer);
			// [img]image_url_here[/img] code..
			$this->m_RenderedBuffer = preg_replace("#\[img\]([^ \?&=\#\"\n\r\t<]*?)\[/img\]#sie", "'[img:".$this->m_UID."]".$this->m_Directory."\\1' . str_replace(' ', '%20', '\\3') . '[/img:".$this->m_UID."]'", $this->m_RenderedBuffer);
		
			// Remove our padding from the string..
			$this->m_RenderedBuffer = substr($this->m_RenderedBuffer, 1);
		
			// Titles
			$this->m_RenderedBuffer = preg_replace("#\[t1\](.*?)\[/t1\]#si", "[t1:".$this->m_UID."]\\1[/t1:".$this->m_UID."]", $this->m_RenderedBuffer);
			$this->m_RenderedBuffer = preg_replace("#\[t2\](.*?)\[/t2\]#si", "[t2:".$this->m_UID."]\\1[/t2:".$this->m_UID."]", $this->m_RenderedBuffer);
			$this->m_RenderedBuffer = preg_replace("#\[t3\](.*?)\[/t3\]#si", "[t3:".$this->m_UID."]\\1[/t3:".$this->m_UID."]", $this->m_RenderedBuffer);

			// Table
			//$this->m_RenderedBuffer = preg_replace("#\[table\](.*?)\[/table\]#si", "[table:".$this->m_UID."]\\1[/table:".$this->m_UID."]", $this->m_RenderedBuffer);
			//$this->m_RenderedBuffer = preg_replace("#\[row\](.*?)\[/row\]#si", "[row:".$this->m_UID."]\\1[/row:".$this->m_UID."]", $this->m_RenderedBuffer);
			//$this->m_RenderedBuffer = preg_replace("#\[cell\](.*?)\[/cell\](\n?)#si", "[cell:".$this->m_UID."]\\1[/cell:".$this->m_UID."]", $this->m_RenderedBuffer);
		}
		
		
		
		private function SecondPassCode()
		{
			global $lang;
		
			$code_start_html = $this->m_TemplateArray['code_open'];
			$code_end_html =  $this->m_TemplateArray['code_close'];
		
			// First, do all the 1st-level matches. These need an htmlspecialchars() run,
			// so they have to be handled differently.
			$match_count = preg_match_all("#\[code:1:".$this->m_UID."\](.*?)\[/code:1:".$this->m_UID."\]#si", $this->m_RenderedBuffer, $matches);
		
			for ($i = 0; $i < $match_count; $i++)
			{
				$before_replace = $matches[1][$i];
				$after_replace = $matches[1][$i];
		
				// Replace 2 spaces with "&nbsp; " so non-tabbed code indents without making huge long lines.
				$after_replace = str_replace("  ", "&nbsp; ", $after_replace);
				// now Replace 2 spaces with " &nbsp;" to catch odd #s of spaces.
				$after_replace = str_replace("  ", " &nbsp;", $after_replace);
		
				// Replace tabs with "&nbsp; &nbsp;" so tabbed code indents sorta right without making huge long lines.
				$after_replace = str_replace("\t", "&nbsp; &nbsp;", $after_replace);
		
				// now Replace space occurring at the beginning of a line
				$after_replace = preg_replace("/^ {1}/m", '&nbsp;', $after_replace);
		
				$str_to_match = "[code:1:$".$this->m_UID."]" . $before_replace . "[/code:1:".$this->m_UID."]";
		
				$replacement = $code_start_html;
				$replacement .= $after_replace;
				$replacement .= $code_end_html;
		
				$this->m_RenderedBuffer = str_replace($str_to_match, $replacement, $this->m_RenderedBuffer);
			}
		
			// Now, do all the non-first-level matches. These are simple.
			$this->m_RenderedBuffer = str_replace("[code:".$this->m_UID."]", $code_start_html, $this->m_RenderedBuffer);
			$this->m_RenderedBuffer = str_replace("[/code:".$this->m_UID."]", $code_end_html, $this->m_RenderedBuffer);
		} // bbencode_second_pass_code()
	
		private function SecondPass()
		{
			global $lang;
		
			$this->m_RenderedBuffer = preg_replace('#(script|about|applet|activex|chrome):#is', "\\1&#058;", $this->m_RenderedBuffer);
		
			// pad it with a space so we can distinguish between FALSE and matching the 1st char (index 0).
			// This is important; bbencode_quote(), bbencode_list(), and bbencode_code() all depend on it.
			$this->m_RenderedBuffer = " " . $this->m_RenderedBuffer;
		
			// First: If there isn't a "[" and a "]" in the message, don't bother.
			if (! (strpos($this->m_RenderedBuffer, "[") && strpos($this->m_RenderedBuffer, "]")) )
			{
				// Remove padding, return.
				$this->m_RenderedBuffer = substr($this->m_RenderedBuffer, 1);
				return $this->m_RenderedBuffer;
			}
	
	
			// [CODE] and [/CODE] for posting code (HTML, PHP, C etc etc) in your posts.
			$this->SecondPassCode();
		
			// [QUOTE] and [/QUOTE] for posting replies with quote, or just for quoting stuff.
			$this->m_RenderedBuffer = str_replace("[quote:".$this->m_UID."]", $this->m_TemplateArray['quote_open'], $this->m_RenderedBuffer);
			$this->m_RenderedBuffer = str_replace("[/quote:".$this->m_UID."]", $this->m_TemplateArray['quote_close'], $this->m_RenderedBuffer);
		
			// New one liner to deal with opening quotes with usernames...
			// replaces the two line version that I had here before..
			$this->m_RenderedBuffer = preg_replace("/\[quote:".$this->m_UID."=\"(.*?)\"\]/si", $this->m_TemplateArray['quote_username_open'], $this->m_RenderedBuffer);
		
			// [list] and [list=x] for (un)ordered lists.
			// unordered lists
			$this->m_RenderedBuffer = str_replace("[list:".$this->m_UID."]", $this->m_TemplateArray['ulist_open'], $this->m_RenderedBuffer);
			// li tags
			$this->m_RenderedBuffer = str_replace("[*:".$this->m_UID."]", $this->m_TemplateArray['listitem'], $this->m_RenderedBuffer);
			// ending tags
			$this->m_RenderedBuffer = str_replace("[/list:u:".$this->m_UID."]", $this->m_TemplateArray['ulist_close'], $this->m_RenderedBuffer);
			$this->m_RenderedBuffer = str_replace("[/list:o:".$this->m_UID."]", $this->m_TemplateArray['olist_close'], $this->m_RenderedBuffer);
			// Ordered lists
			$this->m_RenderedBuffer = preg_replace("/\[list=([a1]):".$this->m_UID."\]/si", $this->m_TemplateArray['olist_open'], $this->m_RenderedBuffer);
		
			// colours
			$this->m_RenderedBuffer = preg_replace("/\[color=(\#[0-9A-F]{6}|[a-z]+):".$this->m_UID."\]/si", $this->m_TemplateArray['color_open'], $this->m_RenderedBuffer);
			$this->m_RenderedBuffer = str_replace("[/color:".$this->m_UID."]", $this->m_TemplateArray['color_close'], $this->m_RenderedBuffer);
		
			// size
			$this->m_RenderedBuffer = preg_replace("/\[size=([1-2]?[0-9]):".$this->m_UID."\]/si", $this->m_TemplateArray['size_open'], $this->m_RenderedBuffer);
			$this->m_RenderedBuffer = str_replace("[/size:".$this->m_UID."]", $this->m_TemplateArray['size_close'], $this->m_RenderedBuffer);
		
			// [b] and [/b] for bolding text.
			$this->m_RenderedBuffer = str_replace("[b:".$this->m_UID."]", $this->m_TemplateArray['b_open'], $this->m_RenderedBuffer);
			$this->m_RenderedBuffer = str_replace("[/b:".$this->m_UID."]", $this->m_TemplateArray['b_close'], $this->m_RenderedBuffer);
		
			// [u] and [/u] for underlining text.
			$this->m_RenderedBuffer = str_replace("[u:".$this->m_UID."]", $this->m_TemplateArray['u_open'], $this->m_RenderedBuffer);
			$this->m_RenderedBuffer = str_replace("[/u:".$this->m_UID."]", $this->m_TemplateArray['u_close'], $this->m_RenderedBuffer);
		
			// [i] and [/i] for italicizing text.
			$this->m_RenderedBuffer = str_replace("[i:".$this->m_UID."]", $this->m_TemplateArray['i_open'], $this->m_RenderedBuffer);
			$this->m_RenderedBuffer = str_replace("[/i:".$this->m_UID."]", $this->m_TemplateArray['i_close'], $this->m_RenderedBuffer);
		
			// Patterns and replacements for URL and email tags..
			$patterns = array();
			$replacements = array();
		
			// [img]image_url_here[/img] code..
			// This one gets first-passed..
			$patterns[] = "#\[img:".$this->m_UID."\]([^?].*?)\[/img:".$this->m_UID."\]#i";
			$replacements[] = $this->m_TemplateArray['img'];


			// matches a [url]xxxx://www.phpbb.com[/url] code..
			$patterns[] = "#\[url\]([\w]+?://[\w\#$%&~/.\-;:=,?@\[\]+]*?)\[/url\]#is";
			$replacements[] = $this->m_TemplateArray['url1'];
		
			// [url]www.phpbb.com[/url] code.. (no xxxx:// prefix).
			$patterns[] = "#\[url\]((www|ftp)\.[\w\#$%&~/.\-;:=,?@\[\]+]*?)\[/url\]#is";
			$replacements[] = $this->m_TemplateArray['url2'];
		
			// [url=xxxx://www.phpbb.com]phpBB[/url] code..
			$patterns[] = "#\[url=([\w]+?://[\w\#$%&~/.\-;:=,?@\[\]+]*?)\]([^?\n\r\t].*?)\[/url\]#is";
			$replacements[] = $this->m_TemplateArray['url3'];
		
			// [url=www.phpbb.com]phpBB[/url] code.. (no xxxx:// prefix).
			$patterns[] = "#\[url=((www|ftp)\.[\w\#$%&~/.\-;:=,?@\[\]+]*?)\]([^?\n\r\t].*?)\[/url\]#is";
			$replacements[] = $this->m_TemplateArray['url4'];

			// [url=www.phpbb.com]phpBB[/url] code.. (no xxxx:// prefix).
			$patterns[] = "#\[doclink=([^?\n\r\t].*?)\]([^?\n\r\t].*?)\[/doclink\]#is";
			$replacements[] = $this->m_TemplateArray['doclink'];
		
			// [email]user@domain.tld[/email] code..
			$patterns[] = "#\[email\]([a-z0-9&\-_.]+?@[\w\-]+\.([\w\-\.]+\.)?[\w]+)\[/email\]#si";
			$replacements[] = $this->m_TemplateArray['email'];
	
	
			// matches a [popup]xxxx://www.phpbb.com[/popup] code..
			$patterns[] = "#\[popup\]([\w]+?://[\w\#$%&~/.\-;:=,?@\[\]+]*?)\[/popup\]#is";
			$replacements[] = $this->m_TemplateArray['popup1'];
		
			// [popup]www.phpbb.com[/popup] code.. (no xxxx:// prefix).
			$patterns[] = "#\[popup\]((www|ftp)\.[\w\#$%&~/.\-;:=,?@\[\]+]*?)\[/popup\]#is";
			$replacements[] = $this->m_TemplateArray['popup2'];
		
			// [popup=xxxx://www.phpbb.com]phpBB[/popup] code..
			$patterns[] = "#\[popup=([\w]+?://[\w\#$%&~/.\-;:=,?@\[\]+]*?)\]([^?\n\r\t].*?)\[/popup\]#is";
			$replacements[] = $this->m_TemplateArray['popup3'];
		
			// [popup=www.phpbb.com]phpBB[/popup] code.. (no xxxx:// prefix).
			$patterns[] = "#\[popup=((www|ftp)\.[\w\#$%&~/.\-;:=,?@\[\]+]*?)\]([^?\n\r\t].*?)\[/popup\]#is";
			$replacements[] = $this->m_TemplateArray['popup4'];
	
	
			// [popup=document_name,800,600]phpBB[/url] code.. (no xxxx:// prefix).
			$patterns[] = "#\[popup=([\w\#$%&~/.\-;:=,?@\[\]+]*?)\,([\w\#$%&~/.\-;:=,?@\[\]+]*?)\,([\w\#$%&~/.\-;:=,?@\[\]+]*?)\]([^?\n\r\t].*?)\[/popup\]#is";
			$replacements[] = $this->m_TemplateArray['popup5'];
			
			// [popup=document_name]phpBB[/popup] code.. (no xxxx:// prefix).
			$patterns[] = "#\[popup=([\w\#$%&~/.\-;:=,?@\[\]+]*?)\]([^?\n\r\t].*?)\[/popup\]#is";
			$replacements[] = $this->m_TemplateArray['popup6'];
	
			$patterns[] = "#\[cell=(.*?)\]#is";
			$replacements[] = $this->m_TemplateArray['cell2_open'];

			$patterns[] = "#\[cell\]#is";
			$replacements[] = $this->m_TemplateArray['cell_open'];

			$patterns[] = "#\[/cell\]#is";
			$replacements[] = $this->m_TemplateArray['cell_close'];

			$patterns[] = "#\[row=(.*?)\]#is";
			$replacements[] = $this->m_TemplateArray['row2_open'];
		
			$patterns[] = "#\[row\]#is";
			$replacements[] = $this->m_TemplateArray['row_open'];

			$patterns[] = "#\[/row\]#is";
			$replacements[] = $this->m_TemplateArray['row_close'];

			$patterns[] = "#\[table=(.*?)\]#is";
			$replacements[] = $this->m_TemplateArray['table2_open'];
		
			$patterns[] = "#\[table\]#is";
			$replacements[] = $this->m_TemplateArray['table_open'];

			$patterns[] = "#\[/table\]#is";
			$replacements[] = $this->m_TemplateArray['table_close'];
			
			$this->m_RenderedBuffer = preg_replace($patterns, $replacements, $this->m_RenderedBuffer);
	
			// Titles
			$this->m_RenderedBuffer = str_replace("[t1:".$this->m_UID."]", $this->m_TemplateArray['t1_open'], $this->m_RenderedBuffer);
			$this->m_RenderedBuffer = str_replace("[t2:".$this->m_UID."]", $this->m_TemplateArray['t2_open'], $this->m_RenderedBuffer);
			$this->m_RenderedBuffer = str_replace("[t3:".$this->m_UID."]", $this->m_TemplateArray['t3_open'], $this->m_RenderedBuffer);
			$this->m_RenderedBuffer = str_replace("[/t1:".$this->m_UID."]", $this->m_TemplateArray['t1_close'], $this->m_RenderedBuffer);
			$this->m_RenderedBuffer = str_replace("[/t2:".$this->m_UID."]", $this->m_TemplateArray['t2_close'], $this->m_RenderedBuffer);
			$this->m_RenderedBuffer = str_replace("[/t3:".$this->m_UID."]", $this->m_TemplateArray['t3_close'], $this->m_RenderedBuffer);
		
			// Table
			$this->m_RenderedBuffer = str_replace("[table:".$this->m_UID."]", $this->m_TemplateArray['table_open'], $this->m_RenderedBuffer);
			$this->m_RenderedBuffer = str_replace("[table2:".$this->m_UID."]", $this->m_TemplateArray['table2_open'], $this->m_RenderedBuffer);
			$this->m_RenderedBuffer = str_replace("[/table:".$this->m_UID."]", $this->m_TemplateArray['table_close'], $this->m_RenderedBuffer);
			$this->m_RenderedBuffer = str_replace("[row2:".$this->m_UID."]", $this->m_TemplateArray['row2_open'], $this->m_RenderedBuffer);
			$this->m_RenderedBuffer = str_replace("[row:".$this->m_UID."]", $this->m_TemplateArray['row_open'], $this->m_RenderedBuffer);
			
			
			$this->m_RenderedBuffer = str_replace("[/row:".$this->m_UID."]", $this->m_TemplateArray['row_close'], $this->m_RenderedBuffer);
			$this->m_RenderedBuffer = str_replace("[cell:".$this->m_UID."]", $this->m_TemplateArray['cell_open'], $this->m_RenderedBuffer);
			$this->m_RenderedBuffer = str_replace("[/cell:".$this->m_UID."]", $this->m_TemplateArray['cell_close'], $this->m_RenderedBuffer);


			// Purge des lignes contenant un saut de lignes inadéquat
			//$this->m_RenderedBuffer = str_replace("[/cell:".$this->m_UID."]", $this->m_TemplateArray['cell_close'], $this->m_RenderedBuffer);

//</td>

			// Remove our padding from the string..
			$this->m_RenderedBuffer = substr($this->m_RenderedBuffer, 1);
		} // bbencode_second_pass()

		/**
		 * This is used to change a [*] tag into a [*:$uid] tag as part
		 * of the first-pass bbencoding of [list] tags. It fits the
		 * standard required in order to be passed as a variable
		 * function into bbencode_first_pass_pda().
		 */
		public static function replace_listitems($text, $uid)
		{
			$text = str_replace("[*]", "[*:$uid]", $text);
		
			return $text;
		}
	}
?>
