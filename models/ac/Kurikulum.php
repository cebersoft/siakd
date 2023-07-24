<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: Kurikulum - Modeling untuk master kurikulum
*/
class Kurikulum extends Zend_Db_Table
{
    protected $_name = 'acc.v_kurikulum';
	protected $_primary='id_kurikulum';

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

	function getKurByProdi($kd_prodi){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$kd_prodi = $this->to_pg_array($kd_prodi);
		$stmt=$db->query("select * from acc.f_kurikulum_fby_prodi('$kd_prodi')");
		$data=$stmt->fetchAll();
		return $data;
	}

	function getKurByProdiPeriode($kd_prodi,$kd_periode){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$kd_prodi = $this->to_pg_array($kd_prodi);
		$stmt=$db->query("select * from acc.f_kurikulum_fby_prodi_periode('$kd_prodi','$kd_periode')");
		$data=$stmt->fetchAll();
		return $data;
	}

	function getKurById($id){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from acc.f_kurikulum_fby_id('$id')");
		$data=$stmt->fetchAll();
		return $data;
	}

	function setKur($kd_kurikulum,$nm_kurikulum,$kd_periode,$status,$kd_prodi,$smt,$sks_l,$sks_w, $sks_p){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from acc.f_kurikulum_ins('$kd_kurikulum','$nm_kurikulum','$kd_periode','$status','$kd_prodi',$smt,$sks_l,$sks_w,$sks_p)");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_kurikulum_ins'];
		}
		return $return;
	}

	function delKur($id){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from acc.f_kurikulum_del('$id')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_kurikulum_del'];
		}
		return $return;	
	}

	function updKur($kd_kurikulum,$nm_kurikulum,$kd_periode,$status,$kd_prodi,$smt,$sks_l,$sks_w, $sks_p, $id_kurikulum){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from acc.f_kurikulum_upd('$kd_kurikulum','$nm_kurikulum','$kd_periode','$status','$kd_prodi',$smt,$sks_l,$sks_w,$sks_p,'$id_kurikulum')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_kurikulum_upd'];
		}
		return $return;
	}
}