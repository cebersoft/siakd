<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: Tugas - Modeling untuk tugas
*/
class Tugas extends Zend_Db_Table
{
    	protected $_name = 'lms.v_tugas';
	protected $_primary='id_tugas';

	function getTugasByPaket($kd_paket){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from lms.f_tugas_fby_paket_kelas('$kd_paket')");
		$data=$stmt->fetchAll();
		return $data;
	}

	function getTugasByKelompok($id){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from lms.f_tugas_fby_kelompok('$id')");
		$data=$stmt->fetchAll();
		return $data;
	}

	function getTugasById($id){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from lms.f_tugas_fby_id('$id')");
		$data=$stmt->fetchAll();
		return $data;
	}

	function setTugas($kd_paket,$jdl,$knt,$tgl1,$tgl2,$param,$kd_dsn,$rps,$file,$link,$kel){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from lms.f_tugas_ins('$kd_paket','$jdl','$knt','$tgl1','$tgl2','$param','$kd_dsn','$rps','$file','$link','$kel')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_tugas_ins'];
		}
		return $return;
	}

	function delTugas($id){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from lms.f_tugas_del('$id')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_tugas_del'];
		}
		return $return;
	}

}