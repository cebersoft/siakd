<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: Jadwal ujian - Modeling untuk jadwal ujian
*/
class JadwalUjian extends Zend_Db_Table
{
	function getJadwalByPeriode($kd_periode,$jns_ujian){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from acc.f_jadwal_ujian_fby_periode('$kd_periode',$jns_ujian)");
		$data=$stmt->fetchAll();
		return $data;
	}

	function getJadwalByKey($kd_periode,$tgl,$id_slot,$id_ruangan){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from acc.f_jadwal_ujian_fby_periode_tanggal_slot_ruangan('$kd_periode','$tgl','$id_slot','$id_ruangan')");
		$data=$stmt->fetchAll();
		return $data;
	}

	function setJadwal($kd_periode,$tgl,$id_slot,$id_ruangan,$kd_paket_kelas,$jns_ujian){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from acc.f_jadwal_ujian_ins('$kd_periode','$tgl','$id_slot','$id_ruangan','$kd_paket_kelas',$jns_ujian)");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_jadwal_ujian_ins'];
		}
		return $return;
	}

	function delJadwal($kd_periode,$tgl,$id_slot,$kd_ruangan){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from acc.f_jadwal_ujian_del('$kd_periode','$tgl','$id_slot','$kd_ruangan')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_jadwal_ujian_del'];
		}
		return $return;
	}

	function updJadwal($kd_periode,$tgl,$id_slot,$id_ruangan,$kd_paket_kelas,$old_tgl,$old_id_slot,$old_kd_ruangan){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from acc.f_jadwal_ujian_upd('$kd_periode','$tgl','$id_slot','$id_ruangan','$kd_paket_kelas','$old_tgl','$old_id_slot','$old_kd_ruangan')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_jadwal_ujian_upd'];
		}
		return $return;
	}
	
	function getMhsKuliahJadwalUjianByNimPeriode($nim,$per,$jns){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from acc.f_mhs_kuliah_jadwal_ujian_fby_nim_periode('$nim','$per',$jns)");
		$data=$stmt->fetchAll();
		return $data;
	}
}