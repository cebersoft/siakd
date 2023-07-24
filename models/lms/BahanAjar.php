<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: Bahan Ajar - Modeling untuk bahan ajar
*/
class BahanAjar extends Zend_Db_Table
{
    	protected $_name = 'lms.v_bahan_ajar';
	protected $_primary='id_bahan_ajar';

	function getBahanAjarByKelas($kd_kelas){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from lms.f_bahan_ajar_fby_kelas('$kd_kelas')");
		$data=$stmt->fetchAll();
		return $data;
	}

	function getBahanAjarById($id){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from lms.f_bahan_ajar_fby_id('$id')");
		$data=$stmt->fetchAll();
		return $data;
	}

	function setBahanAjar($kd_kelas,$jdl,$file,$ket,$link,$kd_dsn,$rps){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from lms.f_bahan_ajar_ins('$kd_kelas','$jdl','$file','$ket','$link','$kd_dsn','$rps')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_bahan_ajar_ins'];
		}
		return $return;
	}

	function delBahanAjar($id){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from lms.f_bahan_ajar_del('$id')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_bahan_ajar_del'];
		}
		return $return;
	}

}