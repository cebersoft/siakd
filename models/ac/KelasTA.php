<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: Kelas TA - Modeling untuk kelas TA
*/
class KelasTA extends Zend_Db_Table
{
    protected $_name = 'acc.v_kelas_ta';
	protected $_primary='kd_kelas';

	function getKelasTAByPeriodeProdi($kd_periode,$kd_prodi){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from acc.f_kelas_ta_fby_periode_prodi('$kd_periode','$kd_prodi')");
		$data=$stmt->fetchAll();
		return $data;
	}

	function getKelasTAByKd($kd_kelas){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from acc.f_kelas_ta_fby_kd('$kd_kelas')");
		$data=$stmt->fetchAll();
		return $data;
	}

	function setKelasTA($id_ajar,$kd_periode){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from acc.f_kelas_ta_ins('$id_ajar','$kd_periode')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_kelas_ta_ins'];
		}
		return $return;
	}

	function delKelasTA($kd_kelas){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from acc.f_kelas_ta_del('$kd_kelas')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_kelas_ta_del'];
		}
		return $return;
	}

	function updKelasTA($nm_p1,$nm_p2,$nm_p3,$nm_p4,$nm_p5,$nm_p6,$nm_p7,$nm_p8,$p_p1,$p_p2,$p_p3,$p_p4,$p_p5,$p_p6,$p_p7,$p_p8,$note,$kd_kelas){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from acc.f_kelas_ta_upd('$nm_p1','$nm_p2','$nm_p3','$nm_p4','$nm_p5','$nm_p6','$nm_p7','$nm_p8',$p_p1,$p_p2,$p_p3,$p_p4,$p_p5,$p_p6,$p_p7,$p_p8,'$note','$kd_kelas')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_kelas_ta_upd'];
		}
		return $return;
	}
}