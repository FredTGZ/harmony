<?php namespace Harmony\database;
 if (! defined('HARMONY_INCLUDE')) die("Harmony Library is not loaded !");
/*! \brief
*
*
*
*/
class CBaseRecordset extends CRecordset
{
	protected $TableName = null;
	protected $Fields = array();
	protected $PrimaryKeys = array();

	protected $SelectQuery1 = "";
	protected $SelectQuery2 = "";
	protected $UpdateQuery1 = "";
	protected $UpdateQuery2 = "";
	
	protected $InsertQuery = "";
	
	protected $OldValues = null;
	protected $BaseCriteria = "";

	protected $CanSelect = true;
	protected $CanUpdate = true;
	protected $CanInsert = true;
	protected $CanDelete = true;
	
	//!Constructor
	public function __construct(&$database, $tablename)
	{
		parent::__construct($database);
		
		if ($tablename !== null) {
			$this->TableName = $tablename;
			$this->Fields = $this->Database->GetDBTableFields($tablename);
			if ($this->Fields === false) $this->Fields = array();
			$this->Values = array();
	
			$this->InsertQuery = "INSERT INTO `".$tablename."` (";
			$this->SelectQuery1 = "SELECT ";
			$this->SelectQuery2 = " WHERE ";
			
			$this->UpdateQuery1 = "UPDATE `" . $tablename . "` SET ";
			$this->UpdateQuery2 = " WHERE ";
			foreach($this->Fields as $field) {
				$this->SelectQuery1 .= '`'.$field['Field'].'`, ';
				$this->InsertQuery .= '`'.$field['Field'].'`, ';
				$this->Values[$field['Field']] = null;
	
				if ($field['Key'] == 'PRI') {
					$this->SelectQuery2 .= $field['Field'].'=\'%s\' ';
				}
			}
			
			$this->InsertQuery = substr($this->InsertQuery, 0, strlen($this->InsertQuery)-2) . ') VALUES(';
			$this->SelectQuery1 = substr($this->SelectQuery1, 0, strlen($this->SelectQuery1)-2);
			$this->SelectQuery1 .= ' FROM '.$this->TableName;
		}
	}


	//!	Open a recordset
	public function Open(	$criteria=""		/*! Query filter whithout the WHERE keyword ("MYFIELD='666'" for example)*/,
							$order=""			/*! Query order without the ORDRE keyword ("MYFIELD ASC" for example)*/,
							$LimitMin=-1		/*! */,
							$RowCount=-1		/*! */) 
  	{
		return $this->OpenRecordset($criteria, $order, $LimitMin, $RowCount);  
	}

	//!	Open a recordset
	public function OpenRecordset(
								$criteria="" 	/*! Query filter whithout the WHERE keyword ("MYFIELD='666'" for example)*/,
								$order="" 		/*! Query order without the ORDRE keyword ("MYFIELD ASC" for example)*/,
								$LimitMin=-1 	/*! */,
								$RowCount=-1 	/*! */)
	{
		/*if ($RowCount>0) $limit = ' LIMIT '.$LimitMin.', '.$RowCount;	
		else $limit = '';*/
	
		$this->BaseCriteria = $criteria;

		$crit = '';
		$ord='';
		
		if ($this->BaseCriteria != "") $crit = " WHERE ".$this->BaseCriteria;
		
		if ($order != "") $ord = ' ORDER BY '.$order;

		$query = $this->SelectQuery1.$crit.$ord;//.$limit;

		if (parent::OpenRecordset($query, $LimitMin, $RowCount)) {
			$this->CopyValues();
			return true;
		}
		else return false;
	}

	public function GetCount($criteria="")
	{
		if ($criteria == "")
			$crit = ($this->BaseCriteria!=""?" WHERE ".$this->BaseCriteria:"");
		else
			$crit = " WHERE ".($this->BaseCriteria!=""?$this->BaseCriteria." AND ". $criteria:$criteria);

		$query = sprintf("SELECT COUNT(*) FROM `%s`%s", $this->TableName, $crit);
		return $this->Database->GetFirst($query);
	}

	//! Move to the next recordset
	public function MoveNext()
	{
		parent::MoveNext();
		$this->CopyValues();
	}
	
	//!Store updated values
	private function CopyValues()
	{
		$this->OldValues = $this->Values;
	}

	//! Set a field value
	public function SetFieldValue($fieldname, $value, $formula=false)
	{
		assert($this->CanUpdate == true);
		
		try {
			if (array_key_exists($fieldname, $this->Values)) {
				if ($formula)
					$this->Values[$fieldname] = '-+*@#{'.$value.'}#@*+-';
				else
					$this->Values[$fieldname] = $value;
			}
			else throw new CRecordsetException("field [".$fieldname."] can't be found !", 0);
		}
		catch(CRecordsetException $e) {
			$e->DisplayException();
		}			
	}
	
	public function Revert()
	{
		 $this->Values = $this->OldValues;	
	}
	
	private function ParseFunction($value, $field)
	{
		if ((substr($value, 0, 6) == '-+*@#{') && (substr($value, strlen($value)-6) == '}#@*+-')) {
			return substr($value, 6, strlen($value) - 2*6);
		}
		else {
			if ($value === null || $field['Extra']== 'auto_increment') return "NULL";
			else return "'" . $value ."'";
		}
	}
	
	public function Insert()
	{
		assert($this->CanInsert == true);
		
		$query = $this->InsertQuery;
		
		foreach($this->Fields as $field) {
			$value = $this->GetFieldValue($field['Field']);
			
			if ((substr($value, 0, 6) == '-+*@#{') && (substr($value, strlen($value)-6) == '}#@*+-')) {
				$query .= substr($value, 6, strlen($value) - 2*6) . ', ';
			}
			else {
				if (($value === null) || ($value == "" && $field['Null'] == 'YES') || $field['Extra']== 'auto_increment') $query .= "NULL, ";
				else $query .= "'" . $value ."', ";
			}
			
		}	

		$query = substr($query, 0, strlen($query)-2) . ')';

		return $this->Database->ExecuteSQLQuery($query);
	}
	
	public function Update()
	{
		assert($this->CanUpdate == true);
		
		$query_update = $this->UpdateQuery1;
		$tmp = '';

		foreach($this->Values as $fieldname => $value) {
			$field_index = $this->GetFieldIndex($fieldname);
			
			if ($this->OldValues[$fieldname] != $value) {
				$tmp .= sprintf("`%s`=%s, ", $fieldname, $this->ParseFunction($value, $this->Fields[$field_index])); // A mettre en forme
			}	
		}

		if ($tmp != '') {
			$query_update .= $tmp;
			$query_update = substr($query_update, 0, strlen($query_update) -2);
			$query_update .= $this->UpdateQuery2;
			$query_update .= $this->BaseCriteria;

			return $this->Database->ExecuteSQLQuery($query_update);
		}
		else return true;
	}
	
	public static function Generate(&$database, $tablename)
	{
		return new CBaseRecordset($database, $tablename);
	} 
	
	
	/**Permet de r�cup�rer les valeurs directement sous la forme $Recordset->NomDuChamp
	 * 
	 * @param string $name
	 */
	public function __get($name)
	{
		if (!isset($this->$name)) {
			try {
				return $this->GetFieldValue($name);
			}
			catch (CDBException $e) {
				$e->DisplayExceptionAndDie();
			}
		}
	}
	
	public function __set($name, $value)
	{
		if (!isset($this->$name)) {
			try {
				return $this->SetFieldValue($name, $value);
			}
			catch (CDBException $e) {
				$e->DisplayExceptionAndDie();
			}
		}
	}
}
?>
