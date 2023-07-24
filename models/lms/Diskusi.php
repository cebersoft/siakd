<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: Tugas - Modeling untuk diskusi
*/
class Diskusi extends Zend_Db_Table
{
    	protected $_name = 'lms.v_diskusi';
	protected $_primary='id_diskusi';

	function getDiskusiByPaket($kd_paket){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from lms.f_diskusi_fby_paket_kelas('$kd_paket')");
		$data=$stmt->fetchAll();
		return $data;
	}

	function getDiskusiByKelompok($kel){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from lms.f_diskusi_fby_kelompok('$kel')");
		$data=$stmt->fetchAll();
		return $data;
	}

	function getDiskusiById($id){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from lms.f_diskusi_fby_id('$id')");
		$data=$stmt->fetchAll();
		return $data;
	}

	function setDiskusi($kd_paket,$jdl,$knt,$tgl1,$tgl2,$param,$kd_dsn,$rps,$kel){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from lms.f_diskusi_ins('$kd_paket','$jdl','$knt','$tgl1','$tgl2','$param','$kd_dsn','$rps','$kel')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_diskusi_ins'];
		}
		return $return;
	}

	function delDiskusi($id){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from lms.f_diskusi_del('$id')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_diskusi_del'];
		}
		return $return;
	}

}