<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: RPS - Modeling untuk RPS
*/
class Rps extends Zend_Db_Table
{
	protected $_name = 'acc.v_rps';
	protected $_primary='id_rps';

	function getRpsByMkKur($id_mk_kur){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from acc.f_rps_fby_matkul_kurikulum('$id_mk_kur')");
		$data=$stmt->fetchAll();
		return $data;
	}

	function getRpsById($id_rps){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from acc.f_rps_fby_id('$id_rps')");
		$data=$stmt->fetchAll();
		return $data;
	}

	function getRpsByMkKurPeriode($id_mk_kur,$kd_periode){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from acc.f_rps_fby_matkul_kurikulum_periode('$id_mk_kur','$kd_periode')");
		$data=$stmt->fetchAll();
		return $data;
	}

	function setRps($id_mk_kur,$per,$capaian){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from acc.f_rps_ins('$id_mk_kur','$per','$capaian')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_rps_ins'];
		}
		return $return;
	}

	function delRps($id){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from acc.f_rps_del('$id')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_rps_del'];
		}
		return $return;
	}

	function updRps($id,$per,$capaian){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from acc.f_rps_upd('$id','$per','$capaian')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_rps_upd'];
		}
		return $return;
	}

	function getRpsDetilByRps($id_rps){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from acc.f_rps_detil_fby_rps('$id_rps')");
		$data=$stmt->fetchAll();
		return $data;
	}

	function setRpsDtl($id_rps,$mg,$kmp,$bhn,$bnt,$krt,$bbt,$ind){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from acc.f_rps_detil_ins('$id_rps',$mg,'$kmp','$bhn','$bnt','$krt',$bbt,'$ind')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_rps_detil_ins'];
		}
		return $return;
	}

	function delRpsDtl($id){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from acc.f_rps_detil_del('$id')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_rps_detil_del'];
		}
		return $return;
	}


}