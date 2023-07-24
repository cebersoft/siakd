<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: Diskusi Mahasiswa - Modeling untuk diskusi mhs
*/
class DiskusiMhs extends Zend_Db_Table
{
    	protected $_name = 'lms.v_diskusi_mhs';
	protected $_primary='id_diskusi_mhs';

	function getDiskusiMhsByDiskusi($id_diskusi){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from lms.f_diskusi_mhs_fby_diskusi('$id_diskusi')");
		$data=$stmt->fetchAll();
		return $data;
	}
	
	function updNlDiskusiMhs($id,$nilai){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from lms.f_diskusi_mhs_nilai_upd('$id',$nilai)");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_diskusi_mhs_nilai_upd'];
		}
		return $return;
	}
	
	function getDiskusiMhsByDiskusiKuliah($id_diskusi,$kd_kuliah){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from lms.f_diskusi_mhs_fby_diskusi_kuliah('$id_diskusi','$kd_kuliah')");
		$data=$stmt->fetchAll();
		return $data;
	}

	function setDiskusiMhs($id_tgs,$kd_kul,$resp){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from lms.f_diskusi_mhs_ins('$id_tgs','$kd_kul','$resp')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_diskusi_mhs_ins'];
		}
		return $return;
	}

	function delDiskusiMhs($id){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from lms.f_diskusi_mhs_del('$id')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_diskusi_mhs_del'];
		}
		return $return;
	}


}