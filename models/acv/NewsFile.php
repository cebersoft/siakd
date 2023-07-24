<?php
/*
	Programmer	: Ahmad Muhaimin
	Release		: Agustus 2019
	Module		: News File - Modeling untuk master News File
*/
class NewsFile extends Zend_Db_Table
{
    protected $_name = 'acv.v_news_file';
	protected $_primary='id_news_file';


	function getNewsFileById($id_news_file){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from acv.f_news_file_fby_id('$id_news_file')");
		$data=$stmt->fetchAll();
		return $data;
	}

	function setNewsFile($id_news,$nm_file){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from acv.f_news_file_ins('$id_news','$nm_file')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_news_file_ins'];
		}
		return $return;
	}

	function delNewsFile($id_news_file){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from acv.f_news_file_del('$id_news_file')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_news_file_del'];
		}
		return $return;
	}

	function updNewsFile($id_news,$nm_file,$id_news_file){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from acv.f_news_file_upd('$id_news','$nm_file','$id_news_file')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_news_file_upd'];
		}
		return $return;
	}
}