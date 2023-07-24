<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: Nilai TA - Modeling untuk nilai TA
*/
class NilaiTA extends Zend_Db_Table
{
    	protected $_name = 'acc.v_mhs_kuliah_ta_nilai';
	protected $_primary='kd_kuliah';

	function getNilaiTAByPaket($kd_paket){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from acc.f_mhs_kuliah_ta_nilai_fby_paket_kelas('$kd_paket')");
		$data=$stmt->fetchAll();
		return $data;
	}

	function getNilaiTAByKd($kd_kuliah){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from acc.f_mhs_kuliah_ta_nilai_fby_kd('$kd_kuliah')");
		$data=$stmt->fetchAll();
		return $data;
	}

	function updNilaiTA($p1,$p2,$p3,$p4,$p5,$p6,$p7,$p8,$pemb1,$pemb2,$pemb3,$noreg,$judul,$uji1,$uji2,$uji3,$uji4,$kd_kuliah){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from acc.f_mhs_kuliah_ta_nilai_upd($p1,$p2,$p3,$p4,$p5,$p6,$p7,$p8,'$pemb1','$pemb2','$pemb3','$noreg','$judul','$uji1','$uji2','$uji3','$uji4','$kd_kuliah')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_mhs_kuliah_ta_nilai_upd'];
		}
		return $return;
	}

	function updStatNilaiTA($stat,$kd_kuliah){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from acc.f_mhs_kuliah_ta_nilai_upd_stat($stat,'$kd_kuliah')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_mhs_kuliah_ta_nilai_upd_stat'];
		}
		return $return;
	}

	function updPengujiJadwalTA($pj1,$pj2,$pj3,$pj4,$tgl,$kd_kuliah){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from acc.f_mhs_kuliah_ta_nilai_upd_penguji_tanggal('$pj1','$pj2','$pj3','$pj4','$tgl','$kd_kuliah')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_mhs_kuliah_ta_nilai_upd_penguji_tanggal'];
		}
		return $return;
	}
}