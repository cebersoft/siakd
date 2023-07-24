<?php
/*
	Programmer	: Tiar Aristian
	Release		: Agustus 2016
	Module		: Formula Biaya - Modeling untuk formula biaya
*/
class FormulaBiaya extends Zend_Db_Table
{	
	function getFormulaBiayaByBiaya($id_akt,$kd_prd,$stat_msk,$id_gel,$id_komp){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from fin.f_formula_biaya_by_biaya('$id_akt','$kd_prd','$stat_msk','$id_gel','$id_komp')");
		$data=$stmt->fetchAll();
		return $data;
	}
	
	function getFormulaBiayaByPeriode($id_akt,$kd_prd,$stat_msk,$id_gel,$kd_reg,$kd_periode){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from fin.f_formula_biaya_by_periode('$id_akt','$kd_prd','$stat_msk','$id_gel','$kd_reg','$kd_periode')");
		$data=$stmt->fetchAll();
		return $data;
	}
	
	function setFormulaBiaya($id_akt,$kd_prd,$stat_msk,$id_gel,$id_komp,$thn,$smt,$statreg,$rule,$hardnom,$param,$multiply,$nomform){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from fin.f_formula_biaya_ins('$id_akt','$kd_prd',$stat_msk,'$id_gel','$id_komp',$thn,'$smt','$statreg','$rule',$hardnom,'$param',$multiply,$nomform)");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_formula_biaya_ins'];
		}
		return $return;
	}

	function delFormulaBiaya($id_akt,$kd_prd,$stat_msk,$id_gel,$id_komp,$thn,$smt,$statreg){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from fin.f_formula_biaya_del('$id_akt','$kd_prd',$stat_msk,'$id_gel','$id_komp',$thn,'$smt','$statreg')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_formula_biaya_del'];
		}
		return $return;
	}
	
	function updUrutanFormulaBiaya($id_akt,$kd_prd,$stat_msk,$id_gel,$id_komp,$thn,$smt,$statreg,$urutan){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from fin.f_formula_biaya_upd_urutan('$id_akt','$kd_prd',$stat_msk,'$id_gel','$id_komp',$thn,'$smt','$statreg',$urutan)");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_formula_biaya_upd_urutan'];
		}
		return $return;
	}
	
	function getFormulaBiayaGrupByAktPrd($id_akt,$kd_prd){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from fin.f_formula_biaya_grup_periode_by_angkatan_prodi('$id_akt','$kd_prd')");
		$data=$stmt->fetchAll();
		return $data;
	}
}