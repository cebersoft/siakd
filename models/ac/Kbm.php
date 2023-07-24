<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: KBM - Modeling untuk kbm/perkuliahan
*/
class Kbm extends Zend_Db_Table
{
    protected $_name = 'acc.v_perkuliahan';
	protected $_primary='id_perkuliahan';

	function getKbmById($id_perkuliahan){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from acc.f_perkuliahan_fby_id('$id_perkuliahan')");
		$data=$stmt->fetchAll();
		return $data;
	}

	function getKbmByPaket($kd_paket_kelas){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from acc.f_perkuliahan_fby_paket_kelas('$kd_paket_kelas')");
		$data=$stmt->fetchAll();
		return $data;
	}

	function setKbm($tgl,$materi,$media,$kejadian,$starttime,$endtime,$tempat,$kdpaket){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from acc.f_perkuliahan_ins('$tgl','$materi','$media','$kejadian','$starttime','$endtime','$tempat','$kdpaket')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_perkuliahan_ins'];
		}
		return $return;
	}

	function updKbm($tgl,$materi,$media,$kejadian,$starttime,$endtime,$tempat,$id_perkuliahan){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from acc.f_perkuliahan_upd('$tgl','$materi','$media','$kejadian','$starttime','$endtime','$tempat','$id_perkuliahan')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_perkuliahan_upd'];
		}
		return $return;
	}

	function delKbm($id_perkuliahan){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from acc.f_perkuliahan_del('$id_perkuliahan')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_perkuliahan_del'];
		}
		return $return;
	}
}