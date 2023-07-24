<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: kalender finance - Modeling untuk master kalender finance
*/
class KalenderFin extends Zend_Db_Table
{
	protected $_name = 'fin.v_kalender_fin';
	protected $_primary='kd_aktivitas';
	
	function getKalenderFinByPeriode($kd_periode){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from fin.f_kalender_fin_fby_periode('$kd_periode')");
		$data=$stmt->fetchAll();
		return $data;
	}
	
	function getKalenderFinByPeriodeAktivitas($kd_periode,$kd_aktivitas){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from fin.f_kalender_fin_fby_periode_aktivitas('$kd_periode','$kd_aktivitas')");
		$data=$stmt->fetchAll();
		return $data;
	}
	
	function getKalenderFinByAktivitas($kd_aktivitas){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from fin.f_kalender_fin_fby_aktivitas('$kd_aktivitas')");
		$data=$stmt->fetchAll();
		return $data;
	}
	
	function setKalenderFin($kd_periode,$kd_aktivitas,$start_date,$end_date){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from fin.f_kalender_fin_ins('$kd_periode','$kd_aktivitas','$start_date','$end_date')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_kalender_fin_ins'];
		}
		return $return;
	}
	
	function updKalenderFin($kd_periode,$kd_aktivitas,$start_date,$end_date){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from fin.f_kalender_fin_upd('$kd_periode','$kd_aktivitas','$start_date','$end_date')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_kalender_fin_upd'];
		}
		return $return;
	}

	function delKalenderFin($kd_periode,$kd_aktivitas){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from fin.f_kalender_fin_del('$kd_periode','$kd_aktivitas')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_kalender_fin_del'];
		}
		return $return;
	}
}