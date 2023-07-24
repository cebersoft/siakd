<?php
/*
	Programmer	: Tiar Aristian
	Release		: Agustus 2016
	Module		: Bank - Modeling untuk bank
*/
class Bank extends Zend_Db_Table
{	
	protected $_name = 'fin.v_bank';
	protected $_primary='id_bank';
	
	function getBankAktif(){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from fin.v_bank where status='t'");
		$data=$stmt->fetchAll();
		return $data;
	}
	
	function getBankById($idBank){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from fin.f_bank_by_id('$idBank')");
		$data=$stmt->fetchAll();
		return $data;
	}
	
	function setBank($nmBank,$rek,$akun){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from fin.f_bank_ins('$nmBank','$rek','$akun')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_bank_ins'];
		}
		return $return;
	}
	
	function delBank($idBank){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from fin.f_bank_del('$idBank')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_bank_del'];
		}
		return $return;
	}
	
	function updBank($nmBank,$rek,$akun,$idBank){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from fin.f_bank_upd('$nmBank','$rek','$akun','$idBank')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_bank_upd'];
		}
		return $return;
	}
	
	function updStatBank($stat,$idBank){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from fin.f_bank_updstat('$stat','$idBank')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_bank_updstat'];
		}
		return $return;
	}
}