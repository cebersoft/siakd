<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: Mata Kuliah - Modeling untuk master Mata Kuliah
*/
class News extends Zend_Db_Table
{
    protected $_name = 'acv.v_news';
	protected $_primary='id_news';

	function to_pg_array($set) {
        settype($set, 'array');
        $result = array();
        foreach ($set as $t) {
            if (is_array($t)) {
                $result[] = to_pg_array($t);
            } else {
                $t = str_replace('"', '\\"', $t);
                if (! is_numeric($t))
                    $t = '"' . $t . '"';
                    $result[] = $t;
            }
        }
        return '{' . implode(",", $result) . '}';
    }
    function queryNews($id_news_kategori,$tgl1,$tgl2){
        // database
        $db=Zend_Registry::get('dbAdapter');
        $id_news_kategori=$this->to_pg_array($id_news_kategori);
        if(($tgl1=='')or($tgl2=='')){
            $stmt=$db->query("select * from  acv.f_news_query('$id_news_kategori','01-01-01','01-01-01')");
        }else{
            $stmt=$db->query("select * from  acv.f_news_query('$id_news_kategori','$tgl1','$tgl2')");
        }
        $data=$stmt->fetchAll();
        return $data;
    }
	
	function getNewsById($id_news){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from acv.f_news_fby_id('$id_news')");
		$data=$stmt->fetchAll();
		return $data;
	}

	function setNews($judul,$konten,$id_news_kategori,$image,$tags,$status,$date_expired,$username_created){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from acv.f_news_ins('$judul','$konten','$id_news_kategori','$image','$tags',$status,'$date_expired','$username_created')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_news_ins'];
		}
		return $return;
	}

	function delNews($id_news){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from acv.f_news_del('$id_news')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_news_del'];
		}
		return $return;
	}

	function updNews($judul,$konten,$id_news_kategori,$image,$tags,$status,$date_expired,$username_edited,$id_news){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from acv.f_news_upd('$judul','$konten','$id_news_kategori','$image','$tags',$status,'$date_expired','$username_edited','$id_news')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_news_upd'];
		}
		return $return;
	}
}