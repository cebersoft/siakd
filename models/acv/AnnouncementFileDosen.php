<?php
/*
	Programmer	: Ahmad Muhaimin
	Release		: Agustus 2019
	Module		: Announcement File Dosen - Modeling untuk master Announcement File Dosen
*/
class AnnouncementFileDosen extends Zend_Db_Table
{
    protected $_name = 'acv.v_announcement_file_dsn';
	protected $_primary='id_announcement_file_dsn';


	function getAnnouncementFileDosenById($id_announcement_file_dsn){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from acv.f_announcement_file_dsn_fby_id('$id_announcement_file_dsn')");
		$data=$stmt->fetchAll();
		return $data;
	}

	function setAnnouncementFileDosen($id_announcement_dsn,$nama_file){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from acv.f_announcement_file_dsn_ins('$id_announcement_dsn','$nama_file')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_announcement_file_dsn_ins'];
		}
		return $return;
	}

	function delAnnouncementFileDosen($id_announcement_file_dsn){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from acv.f_announcement_file_dsn_del('$id_announcement_file_dsn')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_announcement_file_dsn_del'];
		}
		return $return;
	}

	function updAnnouncementFileDosen($id_announcement_dsn,$nama_file,$id_announcement_file_dsn){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from acv.f_announcement_file_dsn_upd('$id_announcement_dsn','$nama_file','$id_announcement_file_dsn')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_announcement_file_dsn_upd'];
		}
		return $return;
	}
}