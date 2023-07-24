<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: Jadwal - Modeling untuk jadwal
*/
class Jadwal extends Zend_Db_Table
{
	function getJadwalByPeriode($kd_periode){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from acc.f_jadwal_kuliah_fby_periode('$kd_periode')");
		$data=$stmt->fetchAll();
		return $data;
	}

	function getJadwalByKey($kd_periode,$id_hari,$id_slot,$id_ruangan){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from acc.f_jadwal_kuliah_fby_periode_hari_slot_ruangan('$kd_periode','$id_hari','$id_slot','$id_ruangan')");
		$data=$stmt->fetchAll();
		return $data;
	}

	function setJadwal($kd_periode,$id_hari,$id_slot,$id_ruangan,$kd_paket_kelas){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from acc.f_jadwal_kuliah_ins('$kd_periode','$id_hari','$id_slot','$id_ruangan','$kd_paket_kelas')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_jadwal_kuliah_ins'];
		}
		return $return;
	}

	function delJadwal($kd_periode,$id_hari,$id_slot,$kd_ruangan){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from acc.f_jadwal_kuliah_del('$kd_periode','$id_hari','$id_slot','$kd_ruangan')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_jadwal_kuliah_del'];
		}
		return $return;
	}

	function updJadwal($kd_periode,$id_hari,$id_slot,$id_ruangan,$kd_paket_kelas,$old_id_hari,$old_id_slot,$old_kd_ruangan){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from acc.f_jadwal_kuliah_upd('$kd_periode','$id_hari','$id_slot','$id_ruangan','$kd_paket_kelas','$old_id_hari','$old_id_slot','$old_kd_ruangan')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_jadwal_kuliah_upd'];
		}
		return $return;
	}
}