<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: Aturan Nilai - Modeling untuk master aturan nilai
*/
class AturanNilai extends Zend_Db_Table
{
	
	function getAturanNilaiByProdi($kd_prodi){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select kd_prodi, kd_periode, id_range_hdr, nama_range from acc.f_aturan_nilai_fby_prodi('$kd_prodi') group by kd_prodi, kd_periode, id_range_hdr, nama_range order by kd_periode desc");
		$data=$stmt->fetchAll();
		return $data;
	}
	
	function getAturanNilaiByProdiPeriode($kd_prodi,$kd_periode){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from acc.f_aturan_nilai_fby_prodi_periode('$kd_prodi','$kd_periode')");
		$data=$stmt->fetchAll();
		return $data;
	}
	
	function setAturanNilai($kd_prodi,$kd_periode,$id_range){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from acc.f_aturan_nilai_ins('$kd_prodi','$kd_periode','$id_range')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_aturan_nilai_ins'];
		}
		return $return;
	}
	
	function delAturanNilai($kd_prodi,$kd_periode){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from acc.f_aturan_nilai_del('$kd_prodi','$kd_periode')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_aturan_nilai_del'];
		}
		return $return;
	}
}