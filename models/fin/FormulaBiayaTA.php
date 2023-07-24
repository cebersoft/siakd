<?php
/*
	Programmer	: Tiar Aristian
	Release		: Agustus 2016
	Module		: Formula Biaya TA - Modeling untuk formula biaya TA
*/
class FormulaBiayaTA extends Zend_Db_Table
{	
	function getFormulaBiayaTAByPeriode($id_akt,$kd_prodi,$id_komp,$kd_periode){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from fin.f_formula_biaya_ta_by_periode('$id_akt','$kd_prodi','$id_komp','$kd_periode')");
		$data=$stmt->fetchAll();
		return $data;
	}
	
	function getFormulaBiayaTAByAktProdi($id_akt,$kd_prodi){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from fin.f_formula_biaya_ta_by_angkatan_prodi('$id_akt','$kd_prodi')");
		$data=$stmt->fetchAll();
		return $data;
	}
	
	function setFormulaBiayaTA($id_akt,$kd_prd,$id_komp,$kd_periode,$intval,$nominal,$id_paket,$id_param,$minval){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from fin.f_formula_biaya_ta_ins('$id_akt','$kd_prd','$id_komp','$kd_periode',$intval,$nominal,'$id_paket','$id_param',$minval)");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_formula_biaya_ta_ins'];
		}
		return $return;
	}
	
	function delFormulaBiayaTA($id_akt,$kd_prd,$id_komp,$kd_periode){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from fin.f_formula_biaya_ta_del('$id_akt','$kd_prd','$id_komp','$kd_periode')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_formula_biaya_ta_del'];
		}
		return $return;
	}
}