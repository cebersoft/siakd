<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: Mata Kuliah - Modeling untuk master Mata Kuliah
*/
class AnnouncementDw extends Zend_Db_Table
{
    	protected $_name = 'acv.v_announcement_dw';
	protected $_primary='id_announcement_dw';

	function getAnnouncementDwByAnn($id_announcement){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from acv.f_announcement_dw_fby_announcement('$id_announcement')");
		$data=$stmt->fetchAll();
		return $data;
	}

	function setAnnouncementDw($id_announcement_mhs,$id_dw){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from acv.f_announcement_dw_ins('$id_announcement_mhs','$id_dw')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_announcement_dw_ins'];
		}
		return $return;
	}

	function delAnnouncementDw($id_announcement_dw){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from acv.f_announcement_dw_del('$id_announcement_dw')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_announcement_dw_del'];
		}
		return $return;
	}

}