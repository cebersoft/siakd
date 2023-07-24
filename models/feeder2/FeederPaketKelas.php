<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: Paket Kelas - Modeling untuk paket kelas khusus feeder
*/
class FeederPaketkelas
{

	function updNidnLapPaketKelas($nidn_lap,$kd_paket){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from feeder.f_paket_kelas_update_nidnlap('$nidn_lap','$kd_paket')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_paket_kelas_update_nidnlap'];
		}
		return $return;
	}
}