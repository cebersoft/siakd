<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: Mata Kuliah - Modeling untuk master Mata Kuliah
*/
class AnnouncementFileDsn extends Zend_Db_Table
{
    protected $_name = 'acv.v_announcement_file_dsn';
	protected $_primary='id_announcement_file_dsn';

	
	function getAnnouncementFileDsnById($id_announcement_file_dsn){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from acv.f_announcement_file_dsn_fby_id('$id_announcement_file_dsn')");
		$data=$stmt->fetchAll();
		return $data;
	}

	function setAnnouncementFileDsn($id_announcement_dsn,$nama_file){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from acv.f_announcement_file_dsn_ins('$id_announcement_dsn','$nama_file')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_announcement_file_dsn_ins'];
		}
		return $return;
	}

	function delAnnouncementFileDsn($id_announcement_file_dsn){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from acv.f_announcement_file_dsn_del('$id_announcement_file_dsn')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_announcement_file_dsn_del'];
		}
		return $return;
	}

	function updAnnouncementFileDsn($id_announcement_dsn,$nama_file,$id_announcement_file_dsn){
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