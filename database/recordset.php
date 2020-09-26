<?php  namespace Harmony\database;
if (! defined('HARMONY_INCLUDE')) die("Harmony Library is not loaded !");
/*******************************************************************************
 *
 *
 * Author: Herrou Fr�d�ric
 * Filename: recordset.inc
 ******************************************************************************/
 
 
 /** \brief Recordset access
 * 
 * \ingroup database
 */    
class CRecordset extends \Harmony\CBaseObject
{
	protected $QueryIndex = 0;
	protected $RowIndex = -1;
	protected $Values = null;
	protected $RowCount = -1;
	protected $Database = null;
	protected $CurrentQuery = "";
	private static $InvalidIndex = -1;
	
	public static function EmptyIsNull($value)
	{
		if ($value === '' || $value===null) return null;
		else return $value;
	}
	
	public static function ConvertDatetime($value)
	{
		$length = strlen($value);
		
		if (($value == null) || !($length == 10 || $length == 19)) return null;
		
		$Year=(int) substr($value, 0, 4);
		$Month=(int) substr($value, 5, 2);
		$Day=(int) substr($value, 8, 2);
		
		if (strlen($value) == 19) {
			$Hour=(int) substr($value, 11, 2);
			$Minute=(int) substr($value, 14, 2);
			$Second=(int) substr($value, 17, 2);
		}
		else $Hour=$Minute=$Second=(int) 0;
		return mktime ($Hour, $Minute, $Second, $Month, $Day, $Year);
	}

	//! Constructor
	public function __construct(\Harmony\database\CDatabase &$database)
	{
		try {
			if ($database === null) throw new CRecordsetException("CRecordset:CRecordset() : \$database parameter is null");
			$this->Database = &$database;
		}
		catch(CRecordsetException $e) {
			$e->DisplayException();
			die();
		}
	}
		
	//!Open the recordset using the query.
	public function Open($criteria="", $order="", $LimitMin=-1, $RowCount=-1) 
  	{
		return $this->OpenRecordset($criteria, $order, $LimitMin, $RowCount);  
	}

	//!Open the recordset using the query.
	public function OpenRecordset($SQLQuery=""	/*! */,	
								$LimitMin=-1 	/*! */,
								$RowCount=-1 	/*! */)
	{
		if ($RowCount>0) $limit = ' LIMIT '.$LimitMin.', '.$RowCount;	
		else $limit = '';
		
		$this->CurrentQuery = $SQLQuery . $limit;
		$this->QueryIndex = $this->Database->ExecuteSQLQuery($this->CurrentQuery);

		if (false === $this->QueryIndex) return false;
		
		$this->RowCount = $this->Database->GetCount($this->QueryIndex);
		$this->RowIndex = 0;
		
		if (false === $this->MoveNext($this->Database))
			$this->RowIndex = self::$InvalidIndex;
		
			return true;
	}
	
	//!Move to a specified tupple.
	public function Move($index)
	{
		if($this->RowIndex == $this->RowCount)  {
			$this->RowIndex = self::$InvalidIndex;
			return false;
		}
		
		if (false === $this->Database->DataSeek($this->QueryIndex, $index)) {
			$this->RowIndex = self::$InvalidIndex;
			return false;
		}
		
		$this->Values = $this->Database->FetchQuery($this->QueryIndex);
		
		if ($this->Values === false) {
			$this->RowIndex = self::$InvalidIndex;		
			return false;
		}
		
		$this->RowIndex = $index+1;
		return true;
	}
	
	//!Move to the next tupple.
	public function MoveNext()
	{
		return($this->Move($this->RowIndex));
	}
	
	//!Get query recordset in an array.
	public function GetFieldArray()
	{
		return $this->Values;		
	}
	
	//!Get field names in an array.
	public function GetFieldNames()
	{
		return array_keys ($this->Values);
	}
	
	//!Get the type of a given field.
	public function GetFieldType($fieldname)
	{
		$field_index = $this->GetFieldIndex($fieldname);

		if ($field_index>=0) return $this->Database->GetFieldType($this->QueryIndex, $field_index);
		else return false;
	}

	//!Get the length of a given field.
	public function GetFieldLen($fieldname)
	{
		$field_index = $this->GetFieldIndex($fieldname);
		
		if ($field_index>=0) return $this->Database->GetFieldLen($this->QueryIndex, $field_index);
		else return false;
	}

	//!Get the index of a given fieldname.
	protected function GetFieldIndex($fieldname)
	{
		$keys = $this->GetFieldNames();
		$index = array_search($fieldname, $keys);
		
		if ($index === false) return -1;
		else return array_search($fieldname, $keys);
	}

	//!Get a field value.
	public function GetFieldValue($Fieldname)
	{
		return ($this->Values[$Fieldname]);
	}
	
	//!Get the last error.
	public function GetLastError()
	{
		return ($this->Database->GetLastError());
	}
	
	//!To know if your are at the BOF
	public function IsBOF()
	{
		return($this->RowIndex == 0);
	}
	
	//!To know if your are at the EOF
	public function IsEOF()
	{
		return ( ($this->RowIndex == -1) || ($this->RowIndex > $this->RowCount) );
	}
	
	public function GetRecordCount()
	{
		return ($this->RowCount);
	}
	
	public function GetCurrentQuery()
	{
		return ($this->CurrentQuery);
	}
}

////////////////////////////////////////////////////////////////////////////////////////////////////
?>
