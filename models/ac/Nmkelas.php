<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: Nama Kelas - Modeling untuk master nama kelas
*/
class Nmkelas extends Zend_Db_Table
{
    protected $_name = 'acc.v_nm_kelas';
	protected $_primary='id_nm_kelas';
	
	function setNmKelas($id,$nm){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from acc.f_nm_kelas_ins('$id','$nm')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_nm_kelas_ins'];
		}
		return $return;
	}
	
	function delNmKelas($id){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from acc.f_nm_kelas_del('$id')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_nm_kelas_del'];
		}
		return $return;
	}
}