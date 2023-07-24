<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: Mata Kuliah - Modeling untuk master Mata Kuliah
*/
class Matkul extends Zend_Db_Table
{
    protected $_name = 'acc.v_matkul';
	protected $_primary='id_mk';

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

	function getMatkulByProdi($kd_prodi){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$kd_prodi = $this->to_pg_array($kd_prodi);
		$stmt=$db->query("select * from acc.f_matkul_fby_prodi('$kd_prodi')");
		$data=$stmt->fetchAll();
		return $data;
	}

	function getMatkulById($id_mk){
		// database
		$db=Zend_Registry::get('dbAdapter');
		//$stmt=$db->query("select * from acc.f_matkul_fby_id('$id_mk')");
		$stmt=$db->query("select * from acc.f_matkul_fby_id2('$id_mk')");
		$data=$stmt->fetchAll();
		return $data;
	}

	function setMatkul($nm_mk,$kd_prodi){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from acc.f_matkul_ins('$nm_mk','$kd_prodi')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_matkul_ins'];
		}
		return $return;
	}

	function delMatkul($id_mk){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from acc.f_matkul_del('$id_mk')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_matkul_del'];
		}
		return $return;
	}

	function updMatkul($nm_mk,$kd_prodi,$id_mk){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from acc.f_matkul_upd('$nm_mk','$kd_prodi','$id_mk')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_matkul_upd'];
		}
		return $return;
	}
}