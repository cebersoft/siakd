<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: Mata Kuliah - Modeling untuk master Mata Kuliah
*/
class AnnouncementAngkatan extends Zend_Db_Table
{
    	protected $_name = 'acv.v_announcement_angkatan';
	protected $_primary='id_announcement_angkatan';

	
	function getAnnouncementAngkatanById($id_announcement_angkatan){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from acv.f_announcement_angkatan_fby_id('$id_announcement_angkatan')");
		$data=$stmt->fetchAll();
		return $data;
	}

	function getAnnouncementAngkatanByAnn($id_announcement){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from acv.f_announcement_angkatan_fby_announcement('$id_announcement')");
		$data=$stmt->fetchAll();
		return $data;
	}

	function setAnnouncementAngkatan($id_announcement_mhs,$id_angkatan){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from acv.f_announcement_angkatan_ins('$id_announcement_mhs','$id_angkatan')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_announcement_angkatan_ins'];
		}
		return $return;
	}

	function delAnnouncementAngkatan($id_announcement_angkatan){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from acv.f_announcement_angkatan_del('$id_announcement_angkatan')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_announcement_angkatan_del'];
		}
		return $return;
	}

	function updAnnouncementAngkatan($id_announcement_mhs,$id_angkatan,$id_announcement_angkatan){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from acv.f_announcement_angkatan_upd('$id_announcement_mhs','$id_angkatan','$id_announcement_angkatan')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_announcement_angkatan_upd'];
		}
		return $return;
	}
}