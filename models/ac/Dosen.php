<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: Dosen - Modeling untuk master dosen
*/
class Dosen
{
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

	function getDosenByKd($kd){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from acc.f_dosen_fby_kd('$kd')");
		$data=$stmt->fetchAll();
		return $data;
	}	

	function getDosenByStatus($status){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from acc.f_dosen_fby_stat('$status')");
		$data=$stmt->fetchAll();
		return $data;
	}

	function getDosenWali(){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from acc.f_dosenwali_f()");
		$data=$stmt->fetchAll();
		return $data;	
	}

	function getDosenByKatStatusHb($idKat,$stat,$a_hb){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$idKat = $this->to_pg_array($idKat);
		$stat = $this->to_pg_array($stat);
		$a_hb = $this->to_pg_array($a_hb);
		$stmt=$db->query("select * from acc.f_dosen_fby_kat_stat_hb('$idKat','$stat','$a_hb')");
		$data=$stmt->fetchAll();
		return $data;
	}

	function setDosen($nm,$g_dpn,$g_blk,$nidn,$a_hb,$id_kat,$tmpLhr,$tglLhr,$jk,$agm,$kwn,$aktif,$alamat,$kota,$nik,$kontak,$email_k,$email_l,$jab,$pang,$dw){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from acc.f_dosen_ins('$nm','$g_dpn','$g_blk','$nidn','$a_hb','$id_kat','$tmpLhr','$tglLhr','$jk','$agm','$kwn','$aktif','$alamat','$kota','$nik','$kontak','$email_k','$email_l','$jab','$pang','$dw')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_dosen_ins'];
		}
		return $return;
	}

	function delDosen($kd){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from acc.f_dosen_del('$kd')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_dosen_del'];
		}
		return $return;	
	}

	function updDosen($nm,$g_dpn,$g_blk,$nidn,$a_hb,$id_kat,$tmpLhr,$tglLhr,$jk,$agm,$kwn,$aktif,$alamat,$kota,$nik,$kontak,$email_k,$email_l,$jab,$pang,$dw,$kd_dosen){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from acc.f_dosen_upd('$nm','$g_dpn','$g_blk','$nidn','$a_hb','$id_kat','$tmpLhr','$tglLhr','$jk','$agm','$kwn','$aktif','$alamat','$kota','$nik','$kontak','$email_k','$email_l','$jab','$pang','$dw', '$kd_dosen')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_dosen_upd'];
		}
		return $return;
	}
	
	function updStatDosen($stat,$kd){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from acc.f_dosen_upd_stat('$stat','$kd')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_dosen_upd_stat'];
		}
		return $return;	
	}
	
	function updPwdDosen($pwd,$kd_dsn){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from acc.f_dosen_upd_pwd('$kd_dsn','$pwd')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_dosen_upd_pwd'];
		}
		return $return;	
	}
	
	function updPwdDosen2($pwd,$kd_dsn,$old_pwd){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from acc.f_dosen_upd_pwd2('$kd_dsn','$pwd','$old_pwd')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_dosen_upd_pwd2'];
		}
		return $return;	
	}
}