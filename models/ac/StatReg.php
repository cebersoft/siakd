<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: StatReg - Modeling untuk master status registrasi
*/
class StatReg extends Zend_Db_Table
{
    protected $_name = 'acc.v_status_reg_periode';
	protected $_primary='kd_status_reg';
	
	function setStatReg($id,$nm_stat,$id_stat_aktif,$krs){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from acc.f_stat_reg_periode_ins('$id','$nm_stat','$id_stat_aktif','$krs')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_stat_reg_periode_ins'];
		}
		return $return;
	}
	
	function delStatReg($id){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from acc.f_stat_reg_periode_del('$id')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_stat_reg_periode_del'];
		}
		return $return;
	}
}