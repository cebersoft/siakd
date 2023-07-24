<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: Paket Kelas LMS - Modeling untuk paket kelas LMS
*/
class PaketkelasLms extends Zend_Db_Table
{
    	protected $_name = 'lms.v_paket_kelas_lms';
	protected $_primary='kd_paket_kelas';

	function getPaketKelasLmsByKelas($kd_kelas){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from lms.f_paket_kelas_lms_fby_kelas('$kd_kelas')");
		$data=$stmt->fetchAll();
		return $data;
	}

	function getPaketKelasLmsById($kd_paket_kelas){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from lms.f_paket_kelas_lms_fby_id('$kd_paket_kelas')");
		$data=$stmt->fetchAll();
		return $data;
	}

}