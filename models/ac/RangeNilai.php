<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: Range nilai  - Modeling untuk master range nilai
*/
class RangeNilai extends Zend_Db_Table
{
    protected $_name = 'acc.v_range_nilai0';
	protected $_primary='id_range_hdr';
	
	function getRangeNilaiHDRById($id_range){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from acc.f_range_nilai0_fby_id('$id_range')");
		$data=$stmt->fetchAll();
		return $data;
	}
	
	function getRangeNilaiDTLByIdHdr($id_range){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from acc.f_range_nilai1_fby_id_hdr('$id_range')");
		$data=$stmt->fetchAll();
		return $data;
	}
	
	function setRangeNilaiHdr($id_range,$nm_range,$ind_tunda){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from acc.f_range_nilai0_ins('$id_range','$nm_range','$ind_tunda')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_range_nilai0_ins'];
		}
		return $return;
	}
	
	function setRangeNilaiDtl($id_range_hdr,$nilai_min,$id_indeks){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from acc.f_range_nilai1_ins('$id_range_hdr',$nilai_min,'$id_indeks')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_range_nilai1_ins'];
		}
		return $return;
	}
	
	function delRangeNilai($id_range){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from acc.f_range_nilai_del('$id_range')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_range_nilai_del'];
		}
		return $return;
	}
	
	function delRangeNilaiDtl($id_range_dtl){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from acc.f_range_nilai1_del('$id_range_dtl')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_range_nilai1_del'];
		}
		return $return;
	}
	
	function updRangeNilaiHdr($new_id_range,$nm_range,$ind_tunda,$old_id_range){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from acc.f_range_nilai0_upd('$new_id_range','$nm_range','$ind_tunda','$old_id_range')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_range_nilai0_upd'];
		}
		return $return;
	}
}