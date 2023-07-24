<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: Angkatan - Modeling untuk master angkatan
*/
class Angkatan extends Zend_Db_Table
{
    	protected $_name = 'acc.v_angkatan';
	protected $_primary='id_angkatan';

	function to_pg_array($set) {
	    settype($set, 'array'); 
	    $result = array();
	    foreach ($set as $t) {
	        if (is_array($t)) {
	            $result[] = to_pg_array($t);
	        } else {
	            $t = str_replace('"', '\\"', $t); 
	            if (! is_numeric($t)) 
	                $t = '"' . $t . '"';
	            $result[] = $t;
	        }
	    }
	    return '{' . implode(",", $result) . '}';
	}

	function getAngkatanQuery($angkatan){
		// database
		$where = '';
		$db=Zend_Registry::get('dbAdapter');
		if(!empty($angkatan)){
			$angkatan = $this->to_pg_array($angkatan);
			$where .= " and id_angkatan = any('$angkatan')";
		}
		$stmt=$db->query("select * from acc.v_angkatan where 1=1 $where");
		$data=$stmt->fetchAll();
		return $data;
	}

	function setAngkatan($akt){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from acc.f_angkatan_ins('$akt')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_angkatan_ins'];
		}
		return $return;
	}

	function delAngkatan($akt){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from acc.f_angkatan_del('$akt')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_angkatan_del'];
		}
		return $return;
	}
}