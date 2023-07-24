<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: Kuliah TA - Modeling untuk kuliah mahasiswa TA
*/
class KuliahTA extends Zend_Db_Table
{
    	protected $_name = 'acc.v_mhs_kuliah_ta';
	protected $_primary='kd_kuliah';

	function getKuliahTAByNimPeriode($nim,$kd_periode){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from acc.f_mhs_kuliah_ta_fby_nim_periode('$nim','$kd_periode')");
		$data=$stmt->fetchAll();
		return $data;
	}

	function getKuliahTAByNim($nim){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from acc.f_mhs_kuliah_ta_fby_nim('$nim')");
		$data=$stmt->fetchAll();
		return $data;
	}

	function getKuliahTAByKd($kd_kuliah){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from acc.f_mhs_kuliah_ta_fby_kd('$kd_kuliah')");
		$data=$stmt->fetchAll();
		return $data;
	}

	function getKuliahTAByPaket($kd_paket){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from acc.f_mhs_kuliah_ta_fby_paket_kelas('$kd_paket')");
		$data=$stmt->fetchAll();
		return $data;
	}

	function getKuliahTAByDosbimPeriodeProdi($kd_dosen,$kd_periode,$kd_prodi){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from acc.f_mhs_kuliah_ta_fby_dosbim_periode_prodi('$kd_dosen','$kd_periode','$kd_prodi')");
		$data=$stmt->fetchAll();
		return $data;
	}

	function getKuliahTAByDosjiPeriodeProdi($kd_dosen,$kd_periode,$kd_prodi){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from acc.f_mhs_kuliah_ta_fby_dosji_periode_prodi('$kd_dosen','$kd_periode','$kd_prodi')");
		$data=$stmt->fetchAll();
		return $data;
	}

	function getKuliahTAByDosjiPeriode($kd_dosen,$kd_periode){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from acc.f_mhs_kuliah_ta_fby_dosji_periode('$kd_dosen','$kd_periode')");
		$data=$stmt->fetchAll();
		return $data;
	}

	function setKuliahTA($nim,$kd_paket,$kd_periode_mulai,$approved){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from acc.f_mhs_kuliah_ta_ins('$nim','$kd_paket','$kd_periode_mulai','$approved')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_mhs_kuliah_ta_ins'];
		}
		return $return;
	}

	function delKuliahTA($kd_kuliah){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from acc.f_mhs_kuliah_ta_del('$kd_kuliah')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_mhs_kuliah_ta_del'];
		}
		return $return;
	}

	function updAppKuliahTA($approved,$kd_kuliah){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from acc.f_mhs_kuliah_ta_upd_app('$approved','$kd_kuliah')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_mhs_kuliah_ta_upd_app'];
		}
		return $return;
	}

	function setKuliahTALog($asal,$username_a,$nim_a,$dsn_a,$kd_kuliah,$aksi,$ket){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from log.f_log_mhs_kuliah_ta_ins($asal,'$username_a','$nim_a','$dsn_a','$kd_kuliah','$aksi','$ket')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_log_mhs_kuliah_ta_ins'];
		}
		return $return;
	}

	function getKuliahTALog($kd_kuliah){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from log.f_log_mhs_kuliah_ta_fby_kd_kuliah_ta('$kd_kuliah')");
		$data=$stmt->fetchAll();
		return $data;
	}

	function getLastKuliahTAByNim($nim){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from acc.f_mhs_kuliah_ta_fby_nim('$nim') where judul notnull order by kd_periode_mulai desc limit 1");
		$data=$stmt->fetchAll();
		return $data;
	}

}
