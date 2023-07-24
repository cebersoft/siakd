<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: Alumni - Modeling untuk alumni
*/
class Alumni extends Zend_Db_Table
{
    protected $_name = 'acc.v_alumni';
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

	function getAlumniByAngkatanProdiTanggal($angkatan,$prodi,$startDate,$endDate){
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
		$stmt=$db->query("select * from acc.f_alumni_fby_angkatan_prodi_tanggal('$angkatan','$prodi','$startDate','$endDate')");
		$data=$stmt->fetchAll();
		return $data;
	}

	function getAlumniByNim($nim){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from acc.f_alumni_fby_nim('$nim')");
		$data=$stmt->fetchAll();
		return $data;
	}

	function setAlumni($nim,$tgllulus,$noijz,$nosk,$judul,$tglsk,$ipk){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from acc.f_alumni_ins('$nim','$tgllulus','$noijz','$nosk','$judul','$tglsk',$ipk)");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_alumni_ins'];
		}
		return $return;
	}

	function delAlumni($nim){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from acc.f_alumni_del('$nim')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_alumni_del'];
		}
		return $return;
	}
}