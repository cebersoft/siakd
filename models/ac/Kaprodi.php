<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: Kaprodi - Modeling untuk master kaprodi
*/
class Kaprodi extends Zend_Db_Table
{
    protected $_name = 'acc.v_kaprodi';
    protected $_primary='id_kaprodi';

    function getKaprodiByKdProdiPeriode($kd_prodi,$periode){
	// database
	$db=Zend_Registry::get('dbAdapter');
	$stmt=$db->query("select * from acc.f_kaprodi_fby_prodi_periode('$kd_prodi','$periode')");
	$data=$stmt->fetchAll();
	return $data;
    }

    function getKaprodiByProdi($kd_prodi){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from acc.f_kaprodi_fby_prodi('$kd_prodi')");
		$data=$stmt->fetchAll();
		return $data;
	}

    function setKaprodi($kd_dosen,$kd_prodi,$tgl){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from acc.f_kaprodi_ins('$kd_dosen','$kd_prodi','$tgl')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_kaprodi_ins'];
		}
		return $return;
    }
    
    function delKaprodi($id){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from acc.f_kaprodi_del('$id')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_kaprodi_del'];
		}
		return $return;
    }

}