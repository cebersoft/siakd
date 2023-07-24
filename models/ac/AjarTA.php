<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: AjarTA - Modeling untuk ajar khusus TA
*/
class AjarTA extends Zend_Db_Table
{
    protected $_name = 'acc.v_ajar_ta';
	protected $_primary='id_ajar';

	function getAjarTAByKurikulum($kd_kur){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from acc.f_ajar_ta_fby_kurikulum('$kd_kur')");
		$data=$stmt->fetchAll();
		return $data;
	}

	function setAjarTA($kd_dosen,$id_mk_kur){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from acc.f_ajar_ta_ins('$kd_dosen','$id_mk_kur')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_ajar_ta_ins'];
		}
		return $return;
	}

	function delAjarTA($id_ajar){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from acc.f_ajar_ta_del('$id_ajar')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_ajar_ta_del'];
		}
		return $return;
	}
}