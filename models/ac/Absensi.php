<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: Absensi - Modeling untuk absensi
*/
class Absensi extends Zend_Db_Table
{
	function getAbsensiByPerkuliahan($id_perkuliahan){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from acc.f_absensi_fby_perkuliahan('$id_perkuliahan')");
		$data=$stmt->fetchAll();
		return $data;
	}

	function getAbsensiByPaketKelas($kd_paket){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from acc.f_absensi_fby_paket_kelas('$kd_paket')");
		$data=$stmt->fetchAll();
		return $data;
	}

	function getAbsensiByNimPeriode($nim,$kd_periode){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from acc.f_mhs_kuliah_absensi_fby_nim_periode('$nim','$kd_periode')");
		$data=$stmt->fetchAll();
		return $data;
	}

	function setAbsensi($id_perkuliahan,$nim,$stat){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from acc.f_absensi_ins('$id_perkuliahan','$nim',$stat)");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_absensi_ins'];
		}
		return $return;
	}

	function delAbsensi($id_perkuliahan,$nim){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from acc.f_absensi_del('$id_perkuliahan','$nim')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_absensi_del'];
		}
		return $return;
	}

	function updAbsensi($stat,$id_perkuliahan,$nim){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from acc.f_absensi_upd($stat,'$id_perkuliahan','$nim')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_absensi_upd'];
		}
		return $return;
	}
}