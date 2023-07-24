<?php
/*
	Programmer	: Ahmad Muhaimin
	Release		: Agustus 2019
	Module		: Arsip - Modeling untuk master Arsip
*/
class Arsip extends Zend_Db_Table
{
    protected $_name = 'acv.v_arsip';
	protected $_primary='id_arsip';


	function getMatkulById($id_arsip){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from acv.f_arsip_fby_id('$id_arsip')");
		$data=$stmt->fetchAll();
		return $data;
	}

	function setMatkul($nm_arsip,$id_arsip_kategori,$username_created,$date_created,$time_created){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from acv.f_arsip_ins('$nm_arsip','$id_arsip_kategori','$username_created',$date_created,$time_created)");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_arsip_ins'];
		}
		return $return;
	}

	function delMatkul($id_arsip){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from acv.f_arsip_del('$id_arsip')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_arsip_del'];
		}
		return $return;
	}

	function updMatkul($nm_arsip,$id_arsip_kategori,$username_created,$date_created,$time_created,$id_arsip){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from acv.f_arsip_upd('$nm_arsip','$id_arsip_kategori','$username_created',$date_created,$time_created,'$id_arsip')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_arsip_upd'];
		}
		return $return;
	}
}