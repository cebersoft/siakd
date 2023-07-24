<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: Tim teaching - Modeling untuk tim teaching
*/
class TimTeaching extends Zend_Db_Table
{
    	protected $_name = 'acc.v_tim_teaching';
	protected $_primary='id_tim_teaching';

	function getTimTeachingByKelas($kd_kelas){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from acc.f_tim_teaching_fby_kelas('$kd_kelas')");
		$data=$stmt->fetchAll();
		return $data;
	}

	function getTimTeachingByDosenPeriode($kd_dosen,$kd_periode){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from acc.f_tim_teaching_fby_dosen_periode('$kd_dosen','$kd_periode')");
		$data=$stmt->fetchAll();
		return $data;
	}

	function getTimTeachingByDosenPeriodeProdi($kd_dosen,$kd_periode,$kd_prodi){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from acc.f_tim_teaching_fby_dosen_periode_prodi('$kd_dosen','$kd_periode','$kd_prodi')");
		$data=$stmt->fetchAll();
		return $data;
	}
	
	function setTimTeaching($kd_kelas,$kd_dosen){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from acc.f_tim_teaching_ins('$kd_kelas','$kd_dosen')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_tim_teaching_ins'];
		}
		return $return;
	}

	function delTimTeaching($id){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from acc.f_tim_teaching_del('$id')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_tim_teaching_del'];
		}
		return $return;
	}
}