<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: Dosji - Modeling untuk dosen penguji
*/
class Dosji extends Zend_Db_Table
{
	function getDosjiByPeriode($kd_periode,$kd_prodi){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from acc.f_dosji_fby_periode_prodi('$kd_periode','$kd_prodi')");
		$data=$stmt->fetchAll();
		return $data;
	}

	function getDosjiByPeriodeDosen($kd_periode,$kd_dosen){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from acc.f_dosji_fby_periode_dosen('$kd_periode','$kd_dosen')");
		$data=$stmt->fetchAll();
		return $data;
	}

	function setDosji($kd_dosen,$kd_periode,$kd_prodi){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from acc.f_dosji_ins('$kd_dosen','$kd_periode','$kd_prodi')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_dosji_ins'];
		}
		return $return;
	}

	function delDosji($kd_dosen,$kd_periode,$kd_prodi){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from acc.f_dosji_del('$kd_dosen','$kd_periode','$kd_prodi')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_dosji_del'];
		}
		return $return;
	}
}