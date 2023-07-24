<?php
/*
	Programmer	: Tiar Aristian
	Release		: Agustus 2016
	Module		: Komponen Biaya - Modeling untuk komponen biaya
*/
class KompBiaya extends Zend_Db_Table
{
    protected $_name = 'fin.v_komponen_biaya';
	protected $_primary='id_komp';
	
	function getKompBiayaById($id_komp){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from fin.f_komponen_biaya_by_id('$id_komp')");
		$data=$stmt->fetchAll();
		return $data;
	}
	
	function getKompBiayaByStat($stat){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from fin.f_komponen_biaya_by_stat('$stat')");
		$data=$stmt->fetchAll();
		return $data;
	}
	
	function setKompBiaya($id,$nm,$ta){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from fin.f_komponen_biaya_ins('$id','$nm','$ta')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_komponen_biaya_ins'];
		}
		return $return;
	}
	
	function delKompBiaya($id){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from fin.f_komponen_biaya_del('$id')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_komponen_biaya_del'];
		}
		return $return;
	}
	
	function updKompBiaya($id,$nm,$oldId){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from fin.f_komponen_biaya_upd('$id','$nm','$oldId')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_komponen_biaya_upd'];
		}
		return $return;
	}
	
	function updStatKompBiaya($stat,$id){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from fin.f_komponen_biaya_upd_stat('$stat','$id')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_komponen_biaya_upd_stat'];
		}
		return $return;
	}
}