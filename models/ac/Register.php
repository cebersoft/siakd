<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: Register - Modeling untuk registrasi periode mahasiswa
*/
class Register extends Zend_Db_Table
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

	function getRegisterByPeriodeAngkatanProdi($kd_periode,$id_angkatan,$kd_prodi){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from acc.f_mhs_reg_periode_fby_periode_angkatan_prodi('$kd_periode','$id_angkatan','$kd_prodi')");
		$data=$stmt->fetchAll();
		return $data;
	}

	function getRegisterByPeriodeAngkatanProdiStatus($kd_periode,$id_angkatan,$kd_prodi,$stat_reg){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$kd_prodi = $this->to_pg_array($kd_prodi);
		$id_angkatan= $this->to_pg_array($id_angkatan);
		$stat_reg= $this->to_pg_array($stat_reg);
		$stmt=$db->query("select * from acc.f_mhs_reg_periode_fby_periode_angkatan_prodi_status('$kd_periode','$id_angkatan','$kd_prodi','$stat_reg')");
		$data=$stmt->fetchAll();
		return $data;
	}

	function getRegisterByNimPeriode($nim,$kd_periode){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from acc.f_mhs_reg_periode_fby_nim_periode('$nim','$kd_periode')");
		$data=$stmt->fetchAll();
		return $data;
	}
	
	function getRegisterByNim($nim){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from acc.f_mhs_reg_periode_fby_nim('$nim')");
		$data=$stmt->fetchAll();
		return $data;
	}

	function setRegister($nim,$kd_periode,$kd_reg){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from acc.f_mhs_reg_periode_ins('$nim','$kd_periode','$kd_reg')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_mhs_reg_periode_ins'];
		}
		return $return;
	}

	function updRegister($nim,$kd_periode,$kd_reg){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from acc.f_mhs_reg_periode_upd('$nim','$kd_periode','$kd_reg')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_mhs_reg_periode_upd'];
		}
		return $return;
	}

	function delRegister($nim,$kd_periode){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from acc.f_mhs_reg_periode_del('$nim','$kd_periode')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_mhs_reg_periode_del'];
		}
		return $return;
	}

	function setRegisterLog($asal,$username_a,$nim_a,$nim,$kd_periode,$aksi,$ket){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from log.f_log_mhs_reg_periode_ins($asal,'$username_a','$nim_a','$nim','$kd_periode','$aksi','$ket')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_log_mhs_reg_periode_ins'];
		}
		return $return;
	}
	
	function getRegisterLog($nim,$kd_periode){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from log.f_log_mhs_mhs_reg_periode_fby_nim_periode('$nim','$kd_periode')");
		$data=$stmt->fetchAll();
		return $data;
	}
}