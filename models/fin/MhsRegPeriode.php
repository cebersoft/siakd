<?php
/*
	Programmer	: Tiar Aristian
	Release		: Agustus 2016
	Module		: Mahasiswa reg periode - Biaya - Modeling untuk status her registrasi mahasiswa
*/
class MhsRegPeriode extends Zend_Db_Table
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
	
	function getMhsRegPeriodeByNim($nim){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from fin.f_mhs_reg_periode_by_nim('$nim')");
		$data=$stmt->fetchAll();
		return $data;
	}
	
	function getMhsRegPeriodeByNimPeriode($nim,$periode){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from fin.f_mhs_reg_periode_by_nim_periode('$nim','$periode')");
		$data=$stmt->fetchAll();
		return $data;
	}
	
	function getMhsRegPeriodeByAktProdi($akt,$prd){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$akt = $this->to_pg_array($akt);
		$prd = $this->to_pg_array($prd);
		$stmt=$db->query("select * from fin.f_mhs_reg_periode_by_angkatan_prodi('$akt','$prd')");
		$data=$stmt->fetchAll();
		return $data;
	}
	
	function getMhsRegPeriodeByAktProdiPeriode($akt,$prd,$periode){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$akt = $this->to_pg_array($akt);
		$prd = $this->to_pg_array($prd);
		$stmt=$db->query("select * from fin.f_mhs_reg_periode_by_angkatan_prodi_periode('$akt','$prd','$periode')");
		$data=$stmt->fetchAll();
		return $data;
	}
}