<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: Mata Kuliah - Modeling untuk master Mata Kuliah
*/
class AnnouncementKategori extends Zend_Db_Table
{
    protected $_name = 'acv.v_announcement_kategori';
	protected $_primary='id_announcement_kategori';

	
	function getAnnouncementKategoriById($id_announcement_kategori){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from acv.f_announcement_kategori_fby_id('$id_announcement_kategori')");
		$data=$stmt->fetchAll();
		return $data;
	}

	function setAnnouncementKategori($announcement_kategori){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from acv.f_announcement_kategori_ins('$announcement_kategori')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_announcement_kategori_ins'];
		}
		return $return;
	}

	function delAnnouncementKategori($id_announcement_kategori){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from acv.f_announcement_kategori_del('$id_announcement_kategori')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_announcement_kategori_del'];
		}
		return $return;
	}

	function updAnnouncementKategori($announcement_kategori,$id_announcement_kategori){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from acv.f_announcement_kategori_upd('$announcement_kategori','$id_announcement_kategori')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_announcement_kategori_upd'];
		}
		return $return;
	}
}