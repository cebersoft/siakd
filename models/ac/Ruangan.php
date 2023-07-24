<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: Ruangan - Modeling untuk master ruangan
*/
class Ruangan extends Zend_Db_Table
{
    protected $_name = 'acc.v_ruangan';
	protected $_primary='kd_ruangan';

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

	function getRuanganByKd($kd_ruangan){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from acc.f_ruangan_fby_kd('$kd_ruangan')");
		$data=$stmt->fetchAll();
		return $data;
	}

	function getRuanganByKat($id_kat){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$id_kat = $this->to_pg_array($id_kat);
		$stmt=$db->query("select * from acc.f_ruangan_fby_kategori('$id_kat')");
		$data=$stmt->fetchAll();
		return $data;
	}

	function setRuangan($kd_ruangan,$nm_ruangan,$kpsts,$kpsts_ujian,$id_kat){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from acc.f_ruangan_ins('$kd_ruangan','$nm_ruangan',$kpsts,$kpsts_ujian,'$id_kat')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_ruangan_ins'];
		}
		return $return;
	}

	function delRuangan($kd_ruangan){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from acc.f_ruangan_del('$kd_ruangan')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_ruangan_del'];
		}
		return $return;
	}

	function updRuangan($kd_ruangan,$nm_ruangan,$kpsts,$kpsts_ujian,$id_kat,$old_kd_ruangan){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from acc.f_ruangan_upd('$kd_ruangan','$nm_ruangan',$kpsts,$kpsts_ujian,'$id_kat','$old_kd_ruangan')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_ruangan_upd'];
		}
		return $return;
	}
}