<?php
/*
	Programmer	: Tiar Aristian
	Release		: Agustus 2016
	Module		: Mhs-Gelombang - Modeling untuk relasi mahasiswa gelombang
*/
class MhsGelombang extends Zend_Db_Table
{	
	protected $_name = 'fin.v_mhs_gelombang';
	protected $_primary='nim';
	
	function getMhsGelombangByNim($nim){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from fin.f_mhs_gelombang_by_nim('$nim')");
		$data=$stmt->fetchAll();
		return $data;
	}
	
	function getMhsGelombangByAktPrdodi($akt,$prodi){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from fin.f_mhs_gelombang_by_angkatan_prodi('$akt','$prodi')");
		$data=$stmt->fetchAll();
		return $data;
	}
	
	function setMhsGel($nim,$id_gel){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from fin.f_mhs_gelombang_set('$id_gel','$nim')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_mhs_gelombang_set'];
		}
		return $return;
	}
}