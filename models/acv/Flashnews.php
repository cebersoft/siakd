<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: Mata Kuliah - Modeling untuk master Mata Kuliah
*/
class Flashnews extends Zend_Db_Table
{
    protected $_name = 'acv.v_flashnews';
	protected $_primary='id_flashnews';

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
    function queryFlashnews($tgl1,$tgl2){
        // database
        $db=Zend_Registry::get('dbAdapter');
  
        if(($tgl1=='')or($tgl2=='')){
            $stmt=$db->query("select * from  acv.f_flashnews_query('01-01-01','01-01-01')");
        }else{
            $stmt=$db->query("select * from  acv.f_flashnews_query('$tgl1','$tgl2')");
        }
        $data=$stmt->fetchAll();
        return $data;
    }

	function getFlashnewsById($id_flashnews){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from acv.f_flashnews_fby_id('$id_flashnews')");
		$data=$stmt->fetchAll();
		return $data;
	}

	function setFlashnews($konten,$status,$date_expired,$username_created){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from acv.f_flashnews_ins('$konten',$status,'$date_expired','$username_created')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_flashnews_ins'];
		}
		return $return;
	}

	function delFlashnews($id_flashnews){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from acv.f_flashnews_del('$id_flashnews')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_flashnews_del'];
		}
		return $return;
	}

	function updFlashnews($konten,$status,$date_expired,$username_edited,$id_flashnews){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from acv.f_flashnews_upd('$konten',$status,'$date_expired','$username_edited','$id_flashnews')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_flashnews_upd'];
		}
		return $return;
	}
}