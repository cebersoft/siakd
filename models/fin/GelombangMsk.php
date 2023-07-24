<?php
/*
	Programmer	: Tiar Aristian
	Release		: Agustus 2016
	Module		: gelombang masuk - Modeling untuk gelombang masuk
*/
class GelombangMsk extends Zend_Db_Table
{
    protected $_name = 'fin.v_gelombang_masuk';
	protected $_primary='id_gelombang';
	
	function getGelombangById($id_gel){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from fin.f_gelombang_masuk_by_id('$id_gel')");
		$data=$stmt->fetchAll();
		return $data;
	}
	
	function setGelombang($id_gel,$nm_gel,$urutan){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from fin.f_gelombang_masuk_ins('$id_gel','$nm_gel',$urutan)");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_gelombang_masuk_ins'];
		}
		return $return;
	}
	
	function delGelombang($id_gel){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from fin.f_gelombang_masuk_del('$id_gel')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_gelombang_masuk_del'];
		}
		return $return;
	}
	
	function updGelombang($id_gel,$nm_gel,$urutan,$old_id){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from fin.f_gelombang_masuk_upd('$id_gel','$nm_gel',$urutan,'$old_id')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_gelombang_masuk_upd'];
		}
		return $return;
	}
}