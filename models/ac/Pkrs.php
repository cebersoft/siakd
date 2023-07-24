<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: PKRS - Modeling untuk PKRS-PKRS TA
*/
class Pkrs extends Zend_Db_Table
{
	function getPkrsByNimPeriode($nim,$kd_periode){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from acc.f_pkrs_fby_nim_periode('$nim','$kd_periode')");
		$data=$stmt->fetchAll();
		return $data;
	}
	
	function getPkrsTAByNimPeriode($nim,$kd_periode){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from acc.f_pkrs_ta_fby_nim_periode('$nim','$kd_periode')");
		$data=$stmt->fetchAll();
		return $data;
	}
	
	function getPkrsByNimPaket($nim,$kd_paket){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from acc.f_pkrs_fby_nim_paket_kelas('$nim','$kd_paket')");
		$data=$stmt->fetchAll();
		return $data;
	}
	
	function setPkrs($nim,$kd_paket,$sks_ded,$exe,$mode,$ta,$kd_per_mulai){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from acc.f_pkrs_ins('$nim','$kd_paket',$sks_ded,$exe,'$mode','$ta','$kd_per_mulai')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_pkrs_ins'];
		}
		return $return;
	}
	
	function delPkrs($nim,$kd_paket){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from acc.f_pkrs_del('$nim','$kd_paket')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_pkrs_del'];
		}
		return $return;
	}
	
	function updSksPkrs($nim,$kd_paket,$sks_taken){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from acc.f_pkrs_upd_sks('$nim','$kd_paket',$sks_taken)");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_pkrs_upd_sks'];
		}
		return $return;
	}
	
	function execPkrs($nim,$kd_paket,$ta){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from acc.f_pkrs_exec('$nim','$kd_paket','$ta')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_pkrs_exec'];
		}
		return $return;
	}
	
	function cancelPkrs($nim,$kd_paket,$ta){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from acc.f_pkrs_cancel('$nim','$kd_paket','$ta')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_pkrs_cancel'];
		}
		return $return;
	}
}