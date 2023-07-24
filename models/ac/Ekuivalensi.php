<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: Ekuivalensi - Modeling untuk ekuivalensi
*/
class Ekuivalensi extends Zend_Db_Table
{
    protected $_name = 'acc.v_ekuivalensi0';
	protected $_primary='id_ekuivalensi';
	
	function getEkuivalensiHdrById($id_ekuivalensi){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from acc.f_ekuivalensi0_fby_id('$id_ekuivalensi')");
		$data=$stmt->fetchAll();
		return $data;
	}
	
	function getEkuivalensiDtlById($id_ekuivalensi){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from acc.f_ekuivalensi1_fby_id('$id_ekuivalensi')");
		$data=$stmt->fetchAll();
		return $data;
	}
	
	function getEkuivalensiHdrByKur($oldKdKur,$newKdKur){
		// database
		$db=Zend_Registry::get('dbAdapter');
		// filter kelas
		$query=$db->query("select id_ekuivalensi from acc.v_ekuivalensi0 where id_kurikulum_lama='$oldKdKur' and id_kurikulum_baru='$newKdKur' limit 1");
		$data=$query->fetchAll();
		return $data;
	}
	
	function setEkuivalensi($id_kur_lama,$id_kur_baru){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from acc.f_ekuivalensi0_ins('$id_kur_lama','$id_kur_baru')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_ekuivalensi0_ins'];
		}
		return $return;
	}
	
	function setEkuivalensiDtl($id_ekuivalensi,$id_mk_kur_lm,$id_mk_kur_br){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from acc.f_ekuivalensi1_ins('$id_ekuivalensi','$id_mk_kur_lm','$id_mk_kur_br')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_ekuivalensi1_ins'];
		}
		return $return;
	}
	
	function delEkuivalensi($id_ekv){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from acc.f_ekuivalensi_del('$id_ekv')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_ekuivalensi_del'];
		}
		return $return;
	}
	
	function delEkuivalensiDtl($id_ekv,$id_mk_kur_lm,$id_mk_kur_br){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from acc.f_ekuivalensi1_del('$id_ekv','$id_mk_kur_lm','$id_mk_kur_br')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_ekuivalensi1_del'];
		}
		return $return;
	}
}