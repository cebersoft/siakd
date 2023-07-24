<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: Dosbim - Modeling untuk dosen pembimbing
*/
class Dosbim extends Zend_Db_Table
{
	function getDosbimByPeriode($kd_periode,$kd_prodi){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from acc.f_dosbim_fby_periode_prodi('$kd_periode','$kd_prodi')");
		$data=$stmt->fetchAll();
		return $data;
	}

	function getDosbimByPeriodeDosen($kd_periode,$kd_dosen){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from acc.f_dosbim_fby_periode_dosen('$kd_periode','$kd_dosen')");
		$data=$stmt->fetchAll();
		return $data;
	}

	function setDosbim($kd_dosen,$kd_periode,$kd_prodi){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from acc.f_dosbim_ins('$kd_dosen','$kd_periode','$kd_prodi')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_dosbim_ins'];
		}
		return $return;
	}

	function delDsbm($kd_dosen,$kd_periode,$kd_prodi){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from acc.f_dosbim_del('$kd_dosen','$kd_periode','$kd_prodi')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_dosbim_del'];
		}
		return $return;
	}
}