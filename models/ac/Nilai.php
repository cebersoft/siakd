<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: Nilai - Modeling untuk nilai
*/
class Nilai extends Zend_Db_Table
{
    protected $_name = 'acc.v_mhs_kuliah_nilai';
	protected $_primary='kd_kuliah';

	function getNilaiByPaket($kd_paket){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from acc.f_mhs_kuliah_nilai_fby_paket_kelas('$kd_paket')");
		$data=$stmt->fetchAll();
		return $data;
	}

	function getNilaiByKd($kd_kuliah){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from acc.f_mhs_kuliah_nilai_fby_kd('$kd_kuliah')");
		$data=$stmt->fetchAll();
		return $data;
	}
	
	function getNilaiByNimPeriode($nim,$kd_periode){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from acc.f_mhs_kuliah_nilai_all_fby_nim_periode('$nim','$kd_periode')");
		$data=$stmt->fetchAll();
		return $data;
	}

	function updNilai($p1,$p2,$p3,$p4,$p5,$p6,$p7,$p8,$uts,$uas,$kd_kuliah){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from acc.f_mhs_kuliah_nilai_upd($p1,$p2,$p3,$p4,$p5,$p6,$p7,$p8,$uts,$uas,'$kd_kuliah')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_mhs_kuliah_nilai_upd'];
		}
		return $return;
	}

	function updStatNilai($stat,$kd_kuliah){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from acc.f_mhs_kuliah_nilai_upd_stat($stat,'$kd_kuliah')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_mhs_kuliah_nilai_upd_stat'];
		}
		return $return;
	}

	function getTranskripKurikulumByNim($nim){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from acc.f_transkrip_kurikulum_fby_nim('$nim')");
		$data=$stmt->fetchAll();
		return $data;
	}

	function getTranskripByNimKurikulum($nim,$kurikulum){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from acc.f_transkrip_fby_nim_kurikulum('$nim','$kurikulum')");
		$data=$stmt->fetchAll();
		return $data;
	}


}