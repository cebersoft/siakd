<?php

class Survey
{    
	function getQuiGen0ByPeriodeProdi($per,$prd){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from srv.v_qui_gen0 where kd_periode='$per' and kd_prodi='$prd'");
		$data=$stmt->fetchAll();
		return $data;
	}
	
	function getKuisionerGenByNimGroupByQui0($nim){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select id_qui0, count(id_qui1) as n_qui1_ans from srv.v_kuisioner_gen where nim='$nim' group by id_qui0");
		$data=$stmt->fetchAll();
		return $data;
	}
	
	function getQuiGen1ByQuiGen0($id_qui0){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from srv.v_qui_gen1 where id_qui0='$id_qui0' order by urutan");
		$data=$stmt->fetchAll();
		return $data;
	}
	
	function setKuisionerGen($nim,$id_qui1,$a_angka,$val_num,$val_text){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from srv.f_kuisioner_gen_ins('$nim','$id_qui1','$a_angka','$val_num','$val_text')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_kuisioner_gen_ins'];
		}
		return $return;
	}
	
	
	//-- Kul
	
	function getQuiKul0ByPeriodeProdi($per,$prd){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from srv.v_qui_kuliah0 where kd_periode='$per' and kd_prodi='$prd'");
		$data=$stmt->fetchAll();
		return $data;
	}
	
	function getKuisionerKulByKdKulGroupByQui0($kd_kuliah){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select id_qui0, count(id_qui1) as n_qui1_ans from srv.v_kuisioner_kuliah where kd_kuliah='$kd_kuliah' group by id_qui0");
		$data=$stmt->fetchAll();
		return $data;
	}
	
	function getQuiKul1ByQuiKul0($id_qui0){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from srv.v_qui_kuliah1 where id_qui0='$id_qui0' order by urutan");
		$data=$stmt->fetchAll();
		return $data;
	}
	
	function setKuisionerKul($kd_kuliah,$id_qui1,$a_angka,$val_num,$val_text){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from srv.f_kuisioner_kuliah_ins('$kd_kuliah','$id_qui1','$a_angka','$val_num','$val_text')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_kuisioner_kuliah_ins'];
		}
		return $return;
	}
}