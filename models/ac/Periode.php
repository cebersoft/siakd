<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: Periode - Modeling untuk master periode
*/
class Periode extends Zend_Db_Table
{
    protected $_name = 'acc.v_per_akd';
	protected $_primary='kd_periode';

	function getPeriodeByKd($kd_periode){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from acc.f_per_akd_fby_kd('$kd_periode')");
		$data=$stmt->fetchAll();
		return $data;
	}

	function getPeriodeByStatus($id_stat){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from acc.f_per_akd_fby_status($id_stat)");
		$data=$stmt->fetchAll();
		return $data;
	}
	
	function getPeriodeByTgl($tgl){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from acc.f_per_akd_fby_tanggal('$tgl')");
		$data=$stmt->fetchAll();
		return $data;
	}

	function setPeriode($thn_awal,$thn_akhir,$id_smt,$id_stat,$tgl_awal,$tgl_akhir){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from acc.f_per_akd_ins($thn_awal,$thn_akhir,'$id_smt',$id_stat,'$tgl_awal','$tgl_akhir')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_per_akd_ins'];
		}
		return $return;
	}

	function delPeriode($kd_periode){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from acc.f_per_akd_del('$kd_periode')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_per_akd_del'];
		}
		return $return;
	}

	function updPeriode($tgl_awal,$tgl_akhir,$kd_periode){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from acc.f_per_akd_upd('$tgl_awal','$tgl_akhir','$kd_periode')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_per_akd_upd'];
		}
		return $return;
	}

	function movePeriode($param){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from acc.f_per_akd_stat_move($param)");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_per_akd_stat_move'];
		}
		return $return;
	}
}