<?php namespace Harmony\database;
 if (! defined('HARMONY_INCLUDE')) die("Harmony Library is not loaded !");
class CViewRecordset extends CBaseRecordset
{
	public function __construct(&$database, $query)
	{
		$this->CanSelect = true;
		$this->CanUpdate = false;
		$this->CanInsert = false;
		$this->CanDelete = false;
		parent::__construct($database, null);
		$this->InsertQuery = null;
		$this->SelectQuery1 = $query;
		$this->SelectQuery2 = "";
		$this->UpdateQuery1 = "";
		$this->UpdateQuery2 = "";
	}

	/*Public function OpenRecordset($LimitMin=-1, $RowCount=-1)
	{
		parent::OpenRecordset();
	}*/



}

?>
