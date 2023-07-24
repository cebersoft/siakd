<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: Paket Kelas - Modeling untuk paket kelas
*/
class Paketkelas extends Zend_Db_Table
{
    protected $_name = 'acc.v_paket_kelas';
	protected $_primary='kd_paket_kelas';

	function getPaketKelasByKd($kd_paket){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from acc.f_paket_kelas_fby_kd('$kd_paket')");
		$data=$stmt->fetchAll();
		return $data;
	}

	function getPaketKelasByKelas($kd_kelas){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from acc.f_paket_kelas_fby_kelas('$kd_kelas')");
		$data=$stmt->fetchAll();
		return $data;
	}
	
	function getPaketKelasByPeriode($kd_periode){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from acc.f_paket_kelas_fby_periode('$kd_periode')");
		$data=$stmt->fetchAll();
		return $data;
	}

	function getPaketKelasByPeriodeProdi($kd_periode,$kd_prodi){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from acc.f_paket_kelas_fby_periode_prodi('$kd_periode','$kd_prodi')");
		$data=$stmt->fetchAll();
		return $data;
	}

	function setPaketKelas($kd_kelas,$id_nm_kls){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from acc.f_paket_kelas_ins('$kd_kelas','$id_nm_kls')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_paket_kelas_ins'];
		}
		return $return;
	}

	function delPaketKelas($kd_paket_kelas){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from acc.f_paket_kelas_del('$kd_paket_kelas')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_paket_kelas_del'];
		}
		return $return;
	}
}