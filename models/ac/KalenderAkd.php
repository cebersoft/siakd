<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: kalender akademik - Modeling untuk master kalender akademik
*/
class KalenderAkd extends Zend_Db_Table
{
	protected $_name = 'acc.v_kalender_akd';
	protected $_primary='kd_aktivitas';
	
	function getKalenderAkdByPeriode($kd_periode){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from acc.f_kalender_akd_fby_periode('$kd_periode')");
		$data=$stmt->fetchAll();
		return $data;
	}
	
	function getKalenderAkdByPeriodeAktivitas($kd_periode,$kd_aktivitas){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from acc.f_kalender_akd_fby_periode_aktivitas('$kd_periode','$kd_aktivitas')");
		$data=$stmt->fetchAll();
		return $data;
	}
	
	function getKalenderAkdByAktivitas($kd_aktivitas){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from acc.f_kalender_akd_fby_aktivitas('$kd_aktivitas')");
		$data=$stmt->fetchAll();
		return $data;
	}
	
	function setKalenderAkd($kd_periode,$kd_aktivitas,$start_date,$end_date){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from acc.f_kalender_akd_ins('$kd_periode','$kd_aktivitas','$start_date','$end_date')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_kalender_akd_ins'];
		}
		return $return;
	}
	
	function updKalenderAkd($kd_periode,$kd_aktivitas,$start_date,$end_date){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from acc.f_kalender_akd_upd('$kd_periode','$kd_aktivitas','$start_date','$end_date')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_kalender_akd_upd'];
		}
		return $return;
	}

	function delKalenderAkd($kd_periode,$kd_aktivitas){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from acc.f_kalender_akd_del('$kd_periode','$kd_aktivitas')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_kalender_akd_del'];
		}
		return $return;
	}
}