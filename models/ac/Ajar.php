<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: Ajar - Modeling untuk ajar dosen
*/
class Ajar extends Zend_Db_Table
{
    protected $_name = 'acc.v_ajar';
	protected $_primary='id_ajar';

	function getAjarByDosen($kd_dosen){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from acc.f_ajar_fby_dosen('$kd_dosen')");
		$data=$stmt->fetchAll();
		return $data;
	}

	function getAjarByKurikulum($kd_kur){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from acc.f_ajar_fby_kurikulum('$kd_kur')");
		$data=$stmt->fetchAll();
		return $data;
	}

	function setAjar($kd_dosen,$id_mk_kur){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from acc.f_ajar_ins('$kd_dosen','$id_mk_kur')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_ajar_ins'];
		}
		return $return;
	}

	function delAjar($id_ajar){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from acc.f_ajar_del('$id_ajar')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_ajar_del'];
		}
		return $return;
	}
}