<?php namespace Harmony\ajax;
	class CAjaxClient extends \Harmony\CBaseObject
	{
		private $BufferJS = null;
		private $AjaxSupportEnabled = false;
		
		public function __construct()
		{
			$EOL = \Harmony\CBaseFile::$EOL;
			$this->BufferJS = \Harmony\CBaseFile::$TAB2.'<script type="text/javascript">'.$EOL;
			$this->BufferJS .= file_get_contents(HARMONY_INCLUDE.'/ajax/ajax.js').$EOL;	
			$this->BufferJS .= \Harmony\CBaseFile::$TAB2.'</script>'.$EOL;
		}
		
		public function LoadAjaxSupport($display=true)
		{
			if ($this->AjaxSupportEnabled) {
				$e = new \Harmony\CException("Ajax support already loaded !");
				$e->DisplayException();
			}

			$this->AjaxSupportEnabled = true;
			if ($display) print $this->BufferJS;
			else return $this->BufferJS;	
		}
		
		public static function GetCallbackFunctionName($Service)
		{
			return sprintf('AjaxCB_%s', str_replace("/", "_", $Service));	
		}
		
		public static function GetCallServiceFunctionName($Service)
		{
			return sprintf('AjaxCS_%s', str_replace("/", "_", $Service));	
		}
		
		public static function CallAjaxService($Service, $Params)
		{
			$ret = sprintf('<script type="text/javascript">%s(%s);</script>'.\Harmony\CBaseFile::$EOL,
				CAjaxClient::GetCallServiceFunctionName($Service),
				CAjaxClient::ConvertArrayToJS($Params));
			return $ret;		
		}

		public static function CreateCallServiceFunction($Service)
		{
			$ret = sprintf('<script type="text/javascript">function %s(params) { Ajax.CallService("%s.php", params, %s); }</script>'.\Harmony\CBaseFile::$EOL,
				CAjaxClient::GetCallServiceFunctionName($Service),
				$Service,
				CAjaxClient::GetCallbackFunctionName($Service));
			return $ret;		
		}
		
		public static function CreateSimpleResponseFunction($Service, $TagID)
		{
			$ret = sprintf('<script type="text/javascript">function %s(response) { document.getElementById("%s").innerHTML = response; }</script>'.\Harmony\CBaseFile::$EOL,
				CAjaxClient::GetCallbackFunctionName($Service), $TagID);
			return $ret;		
		}
		
		public function CallSimpleAjaxService($Service, $Params, $TagID)
		{
			$ret = CAjaxClient::CreateSimpleResponseFunction($Service, $TagID);
			$ret .= CAjaxClient::CreateCallServiceFunction($Service);
			$ret .= CAjaxClient::CallAjaxService($Service, $Params);
			return $ret;		
		}
		
		private static function ConvertArrayToJS($Params)
		{
			$ret = "Array(";
			
			if (is_array($Params) /*&& count($Params)>1*/ ) {
				foreach($Params as $Param)
					$ret .= sprintf("'%s', ", $Param);
				
				if (strlen($ret)>6) $ret = substr($ret, 0, strlen($ret)-2);
				$ret .= ')';
				
			}
			else $ret = sprintf("'%s'", $Params);
			return $ret;		
		}
	}
?>