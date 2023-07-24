<?php
/*
	Programmer	: Tiar Aristian
	Release		: Agustus 2016
	Module		: Kompensasi periode - Biaya - Modeling untuk kompensasi biaya
*/
class Kompensasi extends Zend_Db_Table
{	
	function getKompensasiByNimPeriode($nim,$kd_periode){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from fin.f_kompensasi_biaya_by_nim_periode('$nim','$kd_periode')");
		$data=$stmt->fetchAll();
		return $data;
	}
	
	function setKompensasiBiaya($nim,$kd_periode,$id_komp,$rule,$hardnom,$param,$param_ded,$multiply,$ket){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from fin.f_kompensasi_biaya_ins('$nim','$kd_periode','$id_komp','$rule',$hardnom,'$param',$param_ded,$multiply,'$ket')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_kompensasi_biaya_ins'];
		}
		return $return;
	}
	function delKompensasiBiaya($nim,$kd_periode,$id_komp){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from fin.f_kompensasi_biaya_del('$nim','$kd_periode','$id_komp')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_kompensasi_biaya_del'];
		}
		return $return;
	}

}