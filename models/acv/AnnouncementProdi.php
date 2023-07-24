<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: Mata Kuliah - Modeling untuk master Mata Kuliah
*/
class AnnouncementProdi extends Zend_Db_Table
{
    	protected $_name = 'acv.v_announcement_prodi';
	protected $_primary='id_announcement_prodi';

	function getAnnouncementProdiByAnn($id_announcement){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from acv.f_announcement_prodi_fby_announcement('$id_announcement')");
		$data=$stmt->fetchAll();
		return $data;
	}

	function setAnnouncementProdi($id_announcement_mhs,$id_prodi){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from acv.f_announcement_prodi_ins('$id_announcement_mhs','$id_prodi')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_announcement_prodi_ins'];
		}
		return $return;
	}

	function delAnnouncementProdi($id_announcement_prodi){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from acv.f_announcement_prodi_del('$id_announcement_prodi')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_announcement_prodi_del'];
		}
		return $return;
	}

}