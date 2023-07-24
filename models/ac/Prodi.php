<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: Prodi - Modeling untuk master prodi
*/
class Prodi extends Zend_Db_Table
{
    	protected $_name = 'acc.v_prodi';
	protected $_primary='kd_prodi';

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

	function getProdiQuery($prodi){
		// database
		$where='';
        	$db=Zend_Registry::get('dbAdapter');
		if(!empty($prodi)){
			$prodi = $this->to_pg_array($prodi);
			$where .= " and kd_prodi = any('$prodi')";
		}
		$stmt=$db->query("select * from acc.v_prodi where 1=1 $where");
		$data=$stmt->fetchAll();
		return $data;
	}

	function getProdiByKd($kd){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from acc.f_prodi_fby_kd('$kd')");
		$data=$stmt->fetchAll();
		return $data;
	}
	
	function setProdi($kd,$nm,$jenjang){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from acc.f_prodi_ins('$kd','$nm','$jenjang')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_prodi_ins'];
		}
		return $return;
	}
	
	function updProdi($kd,$nm,$jenjang,$old_kd){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from acc.f_prodi_upd('$kd','$nm','$jenjang','$old_kd')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_prodi_upd'];
		}
		return $return;
	}
	
	function delProdi($kd){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from acc.f_prodi_del('$kd')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_prodi_del'];
		}
		return $return;
	}
}