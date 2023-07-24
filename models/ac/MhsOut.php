<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: mahasiswa out - Modeling untuk mahasiswa out
*/
class MhsOut extends Zend_Db_Table
{
    protected $_name = 'acc.v_mhs_keluar';
	protected $_primary='nim';

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

	function getMhsOutByAngkatanProdiTanggal($angkatan,$prodi,$startDate,$endDate){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$angkatan = $this->to_pg_array($angkatan);
		$prodi = $this->to_pg_array($prodi);
		if($startDate==""){
			$startDate="1900-01-01";
		}
		if($endDate==""){
			$endDate="3000-01-01";
		}
		$stmt=$db->query("select * from acc.f_mhs_keluar_fby_angkatan_prodi_tanggal('$angkatan','$prodi','$startDate','$endDate')");
		$data=$stmt->fetchAll();
		return $data;
	}

	function getMhsOutByNim($nim){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from acc.f_mhs_keluar_fby_nim('$nim')");
		$data=$stmt->fetchAll();
		return $data;
	}

	function setMhsOut($nim,$id_keluar,$tglOut){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from acc.f_mhs_keluar_ins('$nim','$id_keluar','$tglOut')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_mhs_keluar_ins'];
		}
		return $return;
	}

	function delMhsOut($nim){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from acc.f_mhs_keluar_del('$nim')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_mhs_keluar_del'];
		}
		return $return;
	}
}