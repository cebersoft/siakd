<?php
/*
	Programmer	: Ahmad Muhaimin
	Release		: Agustus 2019
	Module		: Announcement File Mahasiswa - Modeling untuk master Announcement File Mahasiswa
*/
class AnnouncementFileMahasiswa extends Zend_Db_Table
{
    protected $_name = 'acv.v_announcement_file_mhs';
	protected $_primary='id_announcement_file_mhs';


	function getAnnouncementFileMahasiswaById($id_announcement_file_mhs){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from acv.f_announcement_file_mhs_fby_id('$id_announcement_file_mhs')");
		$data=$stmt->fetchAll();
		return $data;
	}

	function setAnnouncementFileMahasiswa($id_announcement_mhs,$nama_file){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from acv.f_announcement_file_mhs_ins('$id_announcement_mhs','$nama_file')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_announcement_file_mhs_ins'];
		}
		return $return;
	}

	function delAnnouncementFileMahasiswa($id_announcement_file_mhs){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from acv.f_announcement_file_mhs_del('$id_announcement_file_mhs')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_announcement_file_mhs_del'];
		}
		return $return;
	}

	function updAnnouncementFileMahasiswa($id_announcement_mhs,$nama_file,$id_announcement_file_mhs){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from acv.f_announcement_file_mhs_upd('$id_announcement_mhs','$nama_file','$id_announcement_file_mhs')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_announcement_file_mhs_upd'];
		}
		return $return;
	}
}