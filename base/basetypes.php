<?php  namespace Harmony;
if (! defined('HARMONY_INCLUDE')) die("Harmony Library is not loaded !");
/**\brief Base types
 *\ingroup base
 */ 
class CString
{
	private $m_content = null;
	private $m_length = null;
	
	public function __construct($content=null)
	{
		$this->m_content = $content;
		$this->RefreshLength();
	}
	
	public function Display()
	{
		print($this->m_content);
	}
	
	private function RefreshLength()
	{
		$this->m_length = strlen($this->m_content);
	}
	
	public function GetLength()
	{
		return strlen($this->m_content);
	}
	
	public function Substring($start, $length=1)
	{
		try {
			if ((($start+$length)<=$this->m_length) || ($start<0))
				return new CString(substr($this->m_content, $start, $length));
			else throw new CException("Out of range.");
		}
		catch(CException $e) {
		    print($e->DisplayExceptionAndDie());
		    return false;
		}
	}
	
	public function Replace($search, $replace_with)
	{
		$count = 0;
		$this->m_content = str_replace($search, $replace_with, $this->m_content, $count);
		$this->RefreshLength();
		return $count;
	}
	
	public static function GetHtmlText($text)
	{
		$ret = new CString($text);
		$ret->Replace("<", "&gt;");
		$ret->Replace(">", "&lt;");
		$ret->Replace("\r", "");
		$ret->Replace("\n", "<br>\n");
		return $ret;
	}

/**
 *
 *
 * DATES
 * ----------------------------------------------------------------------------- 
 * d 	Short date 	10/12/2002
 * D 	Long date 	December 10, 2002
 * t 	Short time 	10:11 PM
 * T 	Long time 	10:11:29 PM
 * f 	Full date & time 	December 10, 2002 10:11 PM
 * F 	Full date & time (long) 	December 10, 2002 10:11:29 PM
 * g 	Default date & time 	10/12/2002 10:11 PM
 * G 	Default date & time (long) 	10/12/2002 10:11:29 PM
 * M 	Month day pattern 	December 10
 * r 	RFC1123 date string 	Tue, 10 Dec 2002 22:11:29 GMT
 * s 	Sortable date string 	2002-12-10T22:11:29
 * u 	Universal sortable, local time 	2002-12-10 22:13:50Z
 * U 	Universal sortable, GMT 	December 11, 2002 3:13:50 AM
 * Y 	Year month pattern 	December, 2002
 * 
 *
 *
 */       
	
	public function CSharpFormat($format)
	{
		$args = func_get_args();
		$count = func_num_args() - 1;
		$formats = array();
		
		for($i=0; $i<$count;$i++) $formats[$i] = "{$i}";
	
	}
}

class CIPAdress extends CString
{
	private $ip_part = array();
	private $ip_type = 'ip4';
	public $ip_string = '';
	public static $IP4 = 'ip4';
	public static $IP6 = 'ip6';

	public function __construct($ip_string)
	{
		try {
			$this->ip_part = explode('.', $ip_string);
			
			foreach($this->ip_part as $index => $ip)
				if (! is_numeric($ip)) throw new CException($ip_string . ' is not a valid ip adress ! ['.$ip. '] is not numeric !');

			if ($index == 3) $this->ip_type = CIPAdress::$IP4;
			elseif ($index == 5) $this->ip_type = CIPAdress::$IP6;
			else throw new CException($ip_string . ' is not a valid ip4 or ip6 adress !');
			
			$this->ip_string = $ip_string; 
		}
		catch(CException $e) {
		    print($e->DisplayExceptionAndDie());
		    return false;
		}
	}
	
	public function Display()
	{
		print($this->ip_string);
	}
	
	public function Ping()
	{
		return shell_exec("ping " . $this->ip_string);
	}
}

?>
