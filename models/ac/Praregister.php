<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: Pra Register - Modeling untuk pra registrasi periode mahasiswa
*/
class Praregister extends Zend_Db_Table
{

	function getPraRegisterByPeriodeAngkatanProdi($kd_periode,$id_angkatan,$kd_prodi){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from acc.f_mhs_prareg_periode_fby_periode_angkatan_prodi('$kd_periode','$id_angkatan','$kd_prodi')");
		$data=$stmt->fetchAll();
		return $data;
	}

	function getPraRegisterByNimPeriode($nim,$kd_periode){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from acc.f_mhs_prareg_periode_fby_nim_periode('$nim','$kd_periode')");
		$data=$stmt->fetchAll();
		return $data;
	}
	
	function getPraRegisterByNim($nim){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from acc.f_mhs_prareg_periode_fby_nim('$nim')");
		$data=$stmt->fetchAll();
		return $data;
	}

	function setPraRegister($nim,$kd_periode,$kd_reg){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from acc.f_mhs_prareg_periode_ins('$nim','$kd_periode','$kd_reg')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_mhs_prareg_periode_ins'];
		}
		return $return;
	}

	function delPraRegister($nim,$kd_periode){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from acc.f_mhs_prareg_periode_del('$nim','$kd_periode')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_mhs_prareg_periode_del'];
		}
		return $return;
	}

}