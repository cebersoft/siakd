<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: Kelompok LMS - Modeling untuk kelompok LMS
*/
class KelompokLms extends Zend_Db_Table
{
    	protected $_name = 'lms.v_kelompok_praktikum_lms';
	protected $_primary='kd_kelompok_praktikum';

	function getKelompokLmsByKelas($kd_kelas){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from lms.f_kelompok_praktikum_lms_fby_kelas('$kd_kelas')");
		$data=$stmt->fetchAll();
		return $data;
	}

	function getKelompokLmsById($id){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from lms.f_kelompok_praktikum_lms_fby_id('$id')");
		$data=$stmt->fetchAll();
		return $data;
	}

}