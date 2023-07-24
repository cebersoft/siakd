<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: Kelas LMS - Modeling untuk kelas LMS
*/
class KelasLms extends Zend_Db_Table
{
    	protected $_name = 'lms.v_kelas_lms';
	protected $_primary='kd_kelas';

	function getKelasLmsByPeriodeProdiJenis($kd_periode,$kd_prodi,$jns_kelas){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from lms.f_kelas_lms_fby_periode_prodi_jns('$kd_periode','$kd_prodi','$jns_kelas')");
		$data=$stmt->fetchAll();
		return $data;
	}

}