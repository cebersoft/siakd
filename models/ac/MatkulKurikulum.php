<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: Mata Kuliah Kurikulum - Modeling untuk master kurikulum-Mata Kuliah
*/
class MatkulKurikulum extends Zend_Db_Table
{
    protected $_name = 'acc.v_matkul_kurikulum';
	protected $_primary='id_mk_kurikulum';

	function getMatkulByKurikulum($id_kurikulum){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from acc.f_matkulkurikulum_fby_kurikulum('$id_kurikulum')");
		$data=$stmt->fetchAll();
		return $data;
	}

	function getMatkulTAByKurikulum($id_kurikulum){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from acc.f_matkulkurikulum_ta_fby_kurikulum('$id_kurikulum')");
		$data=$stmt->fetchAll();
		return $data;
	}

	function getMatkulKurikulumById($id_matkul_kurikulum){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from acc.f_matkulkurikulum_fby_id('$id_matkul_kurikulum')");
		$data=$stmt->fetchAll();
		return $data;
	}

	function setMatkulKurikulum($id_mk,$id_kurikulum,$kd_mk,$sks_tm, $sks_p, $sks_pl, $sks_s, $jns_mk, $smt_def, $id_kat_mk){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from acc.f_matkulkurikulum_ins('$id_mk','$id_kurikulum','$kd_mk',$sks_tm, $sks_p, $sks_pl, $sks_s, $jns_mk, $smt_def, '$id_kat_mk')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_matkulkurikulum_ins'];
		}
		return $return;
	}

	function delMatkulKurikulum($id_mk_kurikulum){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from acc.f_matkulkurikulum_del('$id_mk_kurikulum')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_matkulkurikulum_del'];
		}
		return $return;
	}

	function updMatkulKurikulum($id_kurikulum,$kd_mk,$sks_tm, $sks_p, $sks_pl, $sks_s, $jns_mk, $smt_def, $id_kat_mk,$id_mk_kurikulum){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from acc.f_matkulkurikulum_upd('$id_kurikulum','$kd_mk',$sks_tm, $sks_p, $sks_pl, $sks_s, $jns_mk, $smt_def,'$id_kat_mk','$id_mk_kurikulum')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_matkulkurikulum_upd'];
		}
		return $return;
	}
}