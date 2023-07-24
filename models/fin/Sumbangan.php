<?php
/*
	Programmer	: Tiar Aristian
	Release		: Agustus 2016
	Module		: Sumbangan - Modeling untuk sumbangan
*/
class Sumbangan extends Zend_Db_Table
{	
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
	
	function getSumbanganByAktProdi($id_akt,$kd_prd){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$id_akt = $this->to_pg_array($id_akt);
		$kd_prd = $this->to_pg_array($kd_prd);
		$stmt=$db->query("select * from fin.f_sumbangan_by_angkatan_prodi('$id_akt','$kd_prd')");
		$data=$stmt->fetchAll();
		return $data;
	}
	
	function getSumbanganDtlByAktProdi($id_akt,$kd_prd){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$id_akt = $this->to_pg_array($id_akt);
		$kd_prd = $this->to_pg_array($kd_prd);
		$stmt=$db->query("select * from fin.f_sumbangan_detil_by_angkatan_prodi('$id_akt','$kd_prd')");
		$data=$stmt->fetchAll();
		return $data;
	}
	
	function getSumbanganDtlByNim($nim){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from fin.f_sumbangan_detil_by_nim('$nim')");
		$data=$stmt->fetchAll();
		return $data;
	}
	
	function setSumbangan($nim,$id_komp,$kd_periode,$nominal){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from fin.f_sumbangan_set('$nim','$id_komp','$kd_periode',$nominal)");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_sumbangan_set'];
		}
		return $return;
	}
	
	function delSumbangan($nim,$id_komp,$kd_periode){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from fin.f_sumbangan_del('$nim','$id_komp','$kd_periode')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_sumbangan_del'];
		}
		return $return;
	}
}