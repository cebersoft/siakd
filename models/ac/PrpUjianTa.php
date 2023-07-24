<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: Prp Ujian TA - Modeling untuk kuliah pengajuan ujian TA
*/
class PrpUjianTa extends Zend_Db_Table
{
    	protected $_name = 'v_prp_ujian_ta0';
	protected $_primary='id_prp_ujian_ta0';

	function getPrpByPeriodeNim($kd_periode,$nim){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from acc.f_prp_ujian_ta0_fby_periode_nim('$kd_periode','$nim')");
		$data=$stmt->fetchAll();
		return $data;
	}

	function getPrpByPeriodePemb($kd_periode,$kd_dosen){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from acc.f_prp_ujian_ta0_fby_periode_pembimbing('$kd_periode','$kd_dosen')");
		$data=$stmt->fetchAll();
		return $data;
	}

	function getPrpById($id){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from acc.f_prp_ujian_ta0_fby_id('$id')");
		$data=$stmt->fetchAll();
		return $data;
	}

	function getPrpApproverByPrp($id){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from acc.f_prp_ujian_ta1_approver_fby_id0('$id')");
		$data=$stmt->fetchAll();
		return $data;
	}

	function getPrpApproverByPaketKelas($kd_paket){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from acc.f_prp_ujian_ta1_approver_fby_paket_kelas('$kd_paket')");
		$data=$stmt->fetchAll();
		return $data;
	}

	function setPrp($tgl, $kd_kuliah, $kk, $doc){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from acc.f_prp_ujian_ta0_ins('$tgl', '$kd_kuliah', '$kk', '$doc')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_prp_ujian_ta0_ins'];
		}
		return $return;
	}

	function delPrp($id){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from acc.f_prp_ujian_ta0_del('$id')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_prp_ujian_ta0_del'];
		}
		return $return;
	}

	function appPrpPemb($id, $num){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from acc.f_prp_ujian_ta0_pemb_app('$id', $num)");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_prp_ujian_ta0_pemb_app'];
		}
		return $return;
	}

	function updStatusPrpApproverPemb($id, $stat, $apr, $note){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from acc.f_prp_ujian_ta1_approver_status_upd('$id', $stat, '$apr', '$note')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_prp_ujian_ta1_approver_status_upd'];
		}
		return $return;
	}

}