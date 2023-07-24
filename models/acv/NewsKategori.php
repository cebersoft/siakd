<?php
/*
	Programmer	: Ahmad Muhaimin
	Release		: Agustus 2019
	Module		: News Kategori - Modeling untuk master News Kategori
*/
class NewsKategori extends Zend_Db_Table
{
    protected $_name = 'acv.v_news_kategori';
	protected $_primary='id_news_kategori';

	function getNewsKategoriByid($id_news_kategori){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from acv.f_news_kategori_fby_id('$id_news_kategori')");
		$data=$stmt->fetchAll();
		return $data;
	}

	function setNewsKategori($news_kategori){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from acv.f_news_kategori_ins('$news_kategori')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_news_kategori_ins'];
		}
		return $return;
	}

	function delNewsKategori($id_news_kategori){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from acv.f_news_kategori_del('$id_news_kategori')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_news_kategori_del'];
		}
		return $return;
	}

	function updNewsKategori($news_kategori,$id_news_kategori){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from acv.f_news_kategori_upd('$news_kategori','$id_news_kategori')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_news_kategori_upd'];
		}
		return $return;
	}
}