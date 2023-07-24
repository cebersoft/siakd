<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: Kuliah - Modeling untuk kuliah mahasiswa
*/
class Kuliah extends Zend_Db_Table
{
    protected $_name = 'acc.v_mhs_kuliah';
	protected $_primary='kd_kuliah';

	function getKuliahByNimPeriode($nim,$kd_periode){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from acc.f_mhs_kuliah_fby_nim_periode('$nim','$kd_periode') order by a_teori desc, kode_mk ");
		$data=$stmt->fetchAll();
		return $data;
	}

	function getKuliahByNim($nim){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from acc.f_mhs_kuliah_fby_nim('$nim')");
		$data=$stmt->fetchAll();
		return $data;
	}

	function getKuliahByKd($kd_kuliah){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from acc.f_mhs_kuliah_fby_kd('$kd_kuliah')");
		$data=$stmt->fetchAll();
		return $data;
	}

	function getKuliahByPaket($kd_paket){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from acc.f_mhs_kuliah_fby_paket_kelas('$kd_paket')");
		$data=$stmt->fetchAll();
		return $data;
	}	

	function setKuliah($nim,$kd_paket,$approved){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from acc.f_mhs_kuliah_ins('$nim','$kd_paket','$approved')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_mhs_kuliah_ins'];
		}
		return $return;
	}

	function copyKuliah($nim_to,$nim_from,$kd_periode,$approved){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from acc.f_mhs_kuliah_copy('$nim_to','$nim_from','$kd_periode','$approved')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_mhs_kuliah_copy'];
		}
		return $return;
	}	

	function delKuliah($kd_kuliah){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from acc.f_mhs_kuliah_del('$kd_kuliah')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_mhs_kuliah_del'];
		}
		return $return;
	}

	function updSKSKuliah($sks_take,$kd_kuliah){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from acc.f_mhs_kuliah_upd_sks($sks_take,'$kd_kuliah')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_mhs_kuliah_upd_sks'];
		}
		return $return;
	}

	function updAppKuliah($approved,$kd_kuliah){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from acc.f_mhs_kuliah_upd_app('$approved','$kd_kuliah')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_mhs_kuliah_upd_app'];
		}
		return $return;
	}

	function setKuliahLog($asal,$username_a,$nim_a,$dsn_a,$kd_kuliah,$aksi,$ket){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from log.f_log_mhs_kuliah_ins($asal,'$username_a','$nim_a','$dsn_a','$kd_kuliah','$aksi','$ket')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_log_mhs_kuliah_ins'];
		}
		return $return;
	}
	
	function getKuliahLog($kd_kuliah){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from log.f_log_mhs_kuliah_fby_kd_kuliah('$kd_kuliah')");
		$data=$stmt->fetchAll();
		return $data;
	}

}
