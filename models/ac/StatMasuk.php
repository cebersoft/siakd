<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: StatMasuk - Modeling untuk master status masuk mahasiswa
*/
class StatMasuk extends Zend_Db_Table
{
    protected $_name = 'acc.v_stat_masuk';
	protected $_primary='id_stat_masuk';
	
	function setStatMasuk($id,$nm_stat,$jns_msk){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from acc.f_stat_masuk_ins($id,'$nm_stat',$jns_msk)");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_stat_masuk_ins'];
		}
		return $return;
	}
	
	function delStatMasuk($id){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from acc.f_stat_masuk_del($id)");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_stat_masuk_del'];
		}
		return $return;
	}
}