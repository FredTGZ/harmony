<?php namespace Harmony\javascript;

/**\brief Window management with javascript
  * \ingroup javascript
 */
class CJavascriptWindow
{
	public $Directories = true;
	public $Location = true;
	public $Menubar = true;
	public $Resizable = true;
	public $Scrollbars = true;
	public $Status = true;
	public $Toolbar = true;
	public $Width=0;
	public $Height=0;
	public $Url = '';
	public $Name = '';
	
	public function Open()
	{
		$ret = 'window.open(\'' . $this->Url . '\', \'' . $this->Name . '\', \'';
		$ret .= 'directories='.($this->Directories?'yes':'no');
		$ret .= ', location='.($this->Location?'yes':'no');
		$ret .= ', menubar='.($this->Menubar?'yes':'no');
		$ret .= ', resizable='.($this->Resizable?'yes':'no');
		$ret .= ', scrollbars='.($this->Scrollbars?'yes':'no');
		$ret .= ', status='.($this->Status?'yes':'no');
		$ret .= ', toolbar='.($this->Toolbar?'yes':'no');
		$ret .= ($this->Width!=0?', width='.$this->Width:'');
		$ret .= ($this->Height!=0?', height='.$this->Height:'');
		$ret .= '\');';
		
		return $ret;
	}
}

/**\brief Popup management with javascript
  * \ingroup javascript
 */
class CJavascriptPopupWindow extends CJavascriptWindow
{
	public function __construct()
	{
		$Directories = false;
		$Location = false;
		$Menubar = false;
		$Resizable = false;
		$Scrollbars = true;
		$Status = false;
		$Toolbar = false;
		$Width=640;
		$Height=480;
	}
}

/**\brief Base javascript class
  * \ingroup javascript
 */
class CJavascript
{
	public static $Begin = "<script language=\"Javascript\">\n";
	public static $End = "</script>\n";
	
	public static function Alert($message)
	{
		printf('<script language="javascript">alert("%s");</script>', $message);
	}
	
	public static function Redirect($url)
	{
		printf('<script language="javascript">document.location = "%s";</script>', $url);
	}
	
	public static function GoBack()
	{
		print('<script language="javascript">history.back();</script>');	
	}
	
	public static function CloseWindow()
	{
		print('<script language="javascript">window.close();</script>');
	}

	public static function RedirectParentWindow($url)
	{
		print('<script language="javascript">');
		print('if (window.opener && !window.opener.closed) window.opener.location = \''.$url.'\';');
		print('</script>');
	}
	
	
	public static function RefreshParentWindow()
	{
		print('<script language="javascript">');
		print('if (window.opener && !window.opener.closed) window.opener.location.reload();');
		print('</script>');
	}
	
	public static function OpenPopup(CJavascriptPopupWindow $window)
	{
		print CJavascript::$Begin . $window->Open() . CJavascript::$End;
	}
	
	public static function DeclareArray($array)
	{
		try {
			if (!is_array($array))
				throw new CException("Parameter is not an array !");
			
			foreach($array as $key => $value) {
				
				
			}
			
		}
		catch (CException $e) {
			$e->DisplayExceptionAndDie();
		}		
	}
}
?>
