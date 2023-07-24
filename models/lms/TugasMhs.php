<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: Tugas Mahasiswa - Modeling untuk tugas mhs
*/
class TugasMhs extends Zend_Db_Table
{
    	protected $_name = 'lms.v_tugas_mhs';
	protected $_primary='id_tugas_mhs';

	function getTugasMhsByTugas($id_tugas){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from lms.f_tugas_mhs_fby_tugas('$id_tugas')");
		$data=$stmt->fetchAll();
		return $data;
	}

	function getTugasMhsById($id){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from lms.tugas_mhs where id_tugas_mhs='$id'");
		$data=$stmt->fetchAll();
		return $data;
	}
	
	function updNlTugasMhs($id,$nilai){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from lms.f_tugas_mhs_nilai_upd('$id',$nilai)");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_tugas_mhs_nilai_upd'];
		}
		return $return;
	}
	
	function getTugasMhsByTugasKuliah($id_tugas,$kd_kuliah){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from lms.f_tugas_mhs_fby_tugas_kuliah('$id_tugas','$kd_kuliah')");
		$data=$stmt->fetchAll();
		return $data;
	}

	function setTugasMhs($id_tgs,$kd_kul,$resp,$file,$link){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from lms.f_tugas_mhs_ins('$id_tgs','$kd_kul','$resp','$file','$link')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_tugas_mhs_ins'];
		}
		return $return;
	}

	function delTugasMhs($id){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from lms.f_tugas_mhs_del('$id')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_tugas_mhs_del'];
		}
		return $return;
	}


}