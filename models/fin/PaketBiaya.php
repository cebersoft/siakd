<?php
/*
	Programmer	: Tiar Aristian
	Release		: Agustus 2016
	Module		: Paket Biaya - Modeling untuk paket biaya
*/
class PaketBiaya extends Zend_Db_Table
{
    protected $_name = 'fin.v_paket_biaya';
	protected $_primary='id_paket';
	
	function getPaketBiayaById($id_paket){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from fin.f_paket_biaya_by_id('$id_paket')");
		$data=$stmt->fetchAll();
		return $data;
	}
	
	function getPaketBiayaByStat($status){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from fin.f_paket_biaya_by_stat('$status')");
		$data=$stmt->fetchAll();
		return $data;
	}
	
	function setPaketBiaya($id,$nm){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from fin.f_paket_biaya_ins('$id','$nm')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_paket_biaya_ins'];
		}
		return $return;
	}
	
	function delPaketBiaya($id){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from fin.f_paket_biaya_del('$id')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_paket_biaya_del'];
		}
		return $return;
	}
	
	function updPaketBiaya($id,$nm,$oldId){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from fin.f_paket_biaya_upd('$id','$nm','$oldId')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_paket_biaya_upd'];
		}
		return $return;
	}
	
	function updStatPaketBiaya($stat,$id){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from fin.f_paket_biaya_upd_stat('$stat','$id')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_paket_biaya_upd_stat'];
		}
		return $return;
	}
}