<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: Pendidikan Dosen - Modeling untuk pendidikan dosen
*/
class PendDosen extends Zend_Db_Table
{
    protected $_name = 'acc.v_pend_dosen';
	protected $_primary='id_pend_dosen';

	function getPendByKdDosen($kd_dosen){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from acc.f_pend_dosen_fby_dosen('$kd_dosen')");
		$data=$stmt->fetchAll();
		return $data;
	}
	
	function setPendDosen($kd_dosen,$jenjang,$thnmsk,$thnlls,$jur,$inst,$cat){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from acc.f_pend_dosen_ins('$kd_dosen','$jenjang','$thnmsk','$thnlls','$jur','$inst','$cat')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_pend_dosen_ins'];
		}
		return $return;
	}
	
	function delPendDosen($id_pend_dosen){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from acc.f_pend_dosen_del('$id_pend_dosen')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_pend_dosen_del'];
		}
		return $return;
	}
}