<?php
/*
	Programmer	: Tiar Aristian
	Release		: Agustus 2016
	Module		: Biaya - Modeling untuk biaya
*/
class Biaya extends Zend_Db_Table
{	
	function getBiayaById($id_akt,$kd_prd,$stat_msk,$id_gel,$id_komp){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from fin.f_biaya_by_id('$id_akt','$kd_prd','$stat_msk','$id_gel','$id_komp')");
		$data=$stmt->fetchAll();
		return $data;
	}
	
	function getBiayaByAktProdi($id_akt,$kd_prd){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from fin.f_biaya_by_angkatan_prodi('$id_akt','$kd_prd')");
		$data=$stmt->fetchAll();
		return $data;
	}
	
	function setBiaya($id_akt,$kd_prd,$stat_msk,$id_gel,$id_komp,$nominal,$id_pkt){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from fin.f_biaya_ins('$id_akt','$kd_prd',$stat_msk,'$id_gel','$id_komp',$nominal,'$id_pkt')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_biaya_ins'];
		}
		return $return;
	}
	
	function delBiaya($id_akt,$kd_prd,$stat_msk,$id_gel,$id_komp){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from fin.f_biaya_del('$id_akt','$kd_prd',$stat_msk,'$id_gel','$id_komp')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_biaya_del'];
		}
		return $return;
	}
	
	function updBiaya($id_akt,$kd_prd,$stat_msk,$id_gel,$id_komp,$nominal,$id_pkt){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from fin.f_biaya_upd('$id_akt','$kd_prd',$stat_msk,'$id_gel','$id_komp',$nominal,'$id_pkt')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_biaya_upd'];
		}
		return $return;
	}
}