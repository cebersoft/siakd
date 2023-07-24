<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: Kelas - Modeling untuk kelas
*/
class Kelas extends Zend_Db_Table
{
    protected $_name = 'acc.v_kelas';
	protected $_primary='kd_kelas';
	
	function getKelasByPeriode($kd_periode){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from acc.f_kelas_fby_periode('$kd_periode')");
		$data=$stmt->fetchAll();
		return $data;
	}

	function getKelasByPeriodeProdiJenis($kd_periode,$kd_prodi,$jns_kelas){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from acc.f_kelas_fby_periode_prodi_jns('$kd_periode','$kd_prodi','$jns_kelas')");
		$data=$stmt->fetchAll();
		return $data;
	}

	function getKelasByKd($kd_kelas){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from acc.f_kelas_fby_kd('$kd_kelas')");
		$data=$stmt->fetchAll();
		return $data;
	}

	function setKelas($id_ajar,$kd_periode,$id_jns_kls,$ttpMuka){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from acc.f_kelas_ins('$id_ajar','$kd_periode','$id_jns_kls',$ttpMuka)");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_kelas_ins'];
		}
		return $return;
	}

	function delKelas($kd_kelas){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from acc.f_kelas_del('$kd_kelas')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_kelas_del'];
		}
		return $return;
	}

	function updKelas($nm_p1,$nm_p2,$nm_p3,$nm_p4,$nm_p5,$nm_p6,$nm_p7,$nm_p8,$p_p1,$p_p2,$p_p3,$p_p4,$p_p5,$p_p6,$p_p7,$p_p8,$p_uts,$p_uas,$note,$ttpmk,$kd_kelas){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from acc.f_kelas_upd('$nm_p1','$nm_p2','$nm_p3','$nm_p4','$nm_p5','$nm_p6','$nm_p7','$nm_p8',$p_p1,$p_p2,$p_p3,$p_p4,$p_p5,$p_p6,$p_p7,$p_p8,$p_uts,$p_uas,'$note',$ttpmk,'$kd_kelas')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_kelas_upd'];
		}
		return $return;
	}
}