<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: MK TA approver - Modeling untuk mk ta approver
*/
class MatkulTaApp extends Zend_Db_Table
{
    	protected $_name = 'acc.v_matkul_ta_approver';
	protected $_primary='id_matkul_ta_approver';

	function getAppByMkKur($id_mk_kur){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from acc.f_matkul_ta_approver_fby_mkkur('$id_mk_kur')");
		$data=$stmt->fetchAll();
		return $data;
	}

	function setApp($id_mk_kur,$perihal,$bag,$sys){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from acc.f_matkul_ta_approver_ins('$id_mk_kur','$perihal','$bag','$sys')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_matkul_ta_approver_ins'];
		}
		return $return;
	}

	function delApp($id){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from acc.f_matkul_ta_approver_del('$id')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_matkul_ta_approver_del'];
		}
		return $return;
	}
}