<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: Mata Kuliah - Modeling untuk master Mata Kuliah
*/
class ArsipKategori extends Zend_Db_Table
{
    protected $_name = 'acv.v_arsip_kategori';
	protected $_primary='id_arsip_kategori';

	
	function getArsipKategoriById($id_arsip_kategori){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from acv.f_arsip_kategori_fby_id('$id_arsip_kategori')");
		$data=$stmt->fetchAll();
		return $data;
	}

	function setArsipKategori($arsip_kategori){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from acv.f_arsip_kategori_ins('$arsip_kategori')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_arsip_kategori_ins'];
		}
		return $return;
	}

	function delArsipKategori($id_arsip_kategori){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from acv.f_arsip_kategori_del('$id_arsip_kategori')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_arsip_kategori_del'];
		}
		return $return;
	}

	function updArsipKategori($arsip_kategori,$id_arsip_kategori){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from acv.f_arsip_kategori_upd('$arsip_kategori','$id_arsip_kategori')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_arsip_kategori_upd'];
		}
		return $return;
	}
}