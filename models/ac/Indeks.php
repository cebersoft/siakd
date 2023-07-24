<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: Index - Modeling untuk master indeks nilai
*/
class Indeks extends Zend_Db_Table
{
    protected $_name = 'acc.v_indeks_nilai';
	protected $_primary='id_indeks';
	
	function setIndeks($indeks,$bobot){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from acc.f_indeks_nilai_ins('$indeks',$bobot)");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_indeks_nilai_ins'];
		}
		return $return;	
	}
	
	function delIndeks($id_indeks){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from acc.f_indeks_nilai_del('$id_indeks')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_indeks_nilai_del'];
		}
		return $return;	
	}
}