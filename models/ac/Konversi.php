<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: Konversi - Modeling untuk nilai konversi
*/
class Konversi
{
	function getKonversiByIdMhs($id_mhs){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from acc.f_nilai_transfer_fby_mahasiswa('$id_mhs')");
		$data=$stmt->fetchAll();
		return $data;
	}
	
	function getKonversiByNim($nim){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from acc.f_nilai_transfer_fby_mahasiswa_pt('$nim')");
		$data=$stmt->fetchAll();
		return $data;
	}

	function setKonversi($nim,$id_mk_kur,$sks_deducted,$index_new,$kd_mk_asal,$nm_mk_asal,$index_old,$sks_old){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from acc.f_nilai_transfer_ins('$nim','$id_mk_kur',$sks_deducted,'$index_new','$kd_mk_asal','$nm_mk_asal','$index_old',$sks_old)");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_nilai_transfer_ins'];
		}
		return $return;
	}

	function delKonversi($nim,$id_mk_kur){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from acc.f_nilai_transfer_del('$nim','$id_mk_kur')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_nilai_transfer_del'];
		}
		return $return;
	}

	function getKonversiByNimMkKur($nim,$id_mk_kur){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from acc.f_nilai_transfer_fby_mahasiswa_pt_mk_kurikulum('$nim','$id_mk_kur')");
		$data=$stmt->fetchAll();
		return $data;
	}
	
	function updKonversi($nim,$id_mk_kur,$sks_deducted,$index_new,$kd_mk_asal,$nm_mk_asal,$index_old,$sks_old,$nim_old,$id_mk_kur_old){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from acc.f_nilai_transfer_upd('$nim','$id_mk_kur',$sks_deducted,'$index_new','$kd_mk_asal','$nm_mk_asal','$index_old',$sks_old,'$nim_old','$id_mk_kur_old')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_nilai_transfer_upd'];
		}
		return $return;
	}
}