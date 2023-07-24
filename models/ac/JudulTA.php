<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: Kuliah TA - Modeling untuk kuliah mahasiswa TA
*/
class JudulTA extends Zend_Db_Table
{
    	protected $_name = 'acc.v_judul_ta';
	protected $_primary='id_judul_ta';

	function getJudulTAByKdKuliah($kd_kuliah){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from acc.f_judul_ta_fby_kuliah('$kd_kuliah')");
		$data=$stmt->fetchAll();
		return $data;
	}

	function getJudulTAByPembimbingPeriodeProdi($kd_dosen,$kd_periode,$kd_prodi){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from acc.f_judul_ta_fby_pembimbing_periode_prodi('$kd_dosen','$kd_periode','$kd_prodi')");
		$data=$stmt->fetchAll();
		return $data;
	}

	function setJudulTA($kd_kuliah, $judul){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from acc.f_judul_ta_ins('$kd_kuliah', '$judul')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_judul_ta_ins'];
		}
		return $return;
	}

	function delJudulTA($id){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from acc.f_judul_ta_del('$id')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_judul_ta_del'];
		}
		return $return;
	}

	function updStatJudulTA($id, $stat, $kd_dosen){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from acc.f_judul_ta_status_upd('$id', $stat, '$kd_dosen')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_judul_ta_status_upd'];
		}
		return $return;
	}

}