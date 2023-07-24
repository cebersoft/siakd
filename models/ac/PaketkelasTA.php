<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: Paket Kelas TA- Modeling untuk paket kelas TA
*/
class PaketkelasTA extends Zend_Db_Table
{
    protected $_name = 'acc.v_paket_kelas_ta';
	protected $_primary='kd_paket_kelas';

	function getPaketKelasTAByKd($kd_paket){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from acc.f_paket_kelas_ta_fby_kd('$kd_paket')");
		$data=$stmt->fetchAll();
		return $data;
	}

	function getPaketKelasTAByKelas($kd_kelas){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from acc.f_paket_kelas_ta_fby_kelas('$kd_kelas')");
		$data=$stmt->fetchAll();
		return $data;
	}

	function getPaketKelasTAByPeriodeProdi($kd_periode,$kd_prodi){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from acc.f_paket_kelas_ta_fby_periode_prodi('$kd_periode','$kd_prodi')");
		$data=$stmt->fetchAll();
		return $data;
	}
}