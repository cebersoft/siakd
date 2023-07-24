<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: User - Modeling untuk user sistem
*/
class UserFin extends Zend_Db_Table
{
	protected $_name = 'sys.user_fin';
	protected $_primary='username';
	
	function getUserFinByUname($username){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from sys.f_user_fin_by_username('$username')");
		$data=$stmt->fetchAll();
		return $data;
	}
	
	function setUserFin($username,$pwd,$nama,$superadmin,$email){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from sys.f_user_fin_ins('$username','$pwd','$nama','$superadmin','$email')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_user_fin_ins'];
		}
		return $return;
	}
	
	function delUserFin($username){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from sys.f_user_fin_del('$username')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_user_fin_del'];
		}
		return $return;
	}
	
	function updUserFin($username,$nama,$email){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from sys.f_user_fin_upd('$username','$nama','$email')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_user_fin_upd'];
		}
		return $return;
	}
	
	function updPasswordUserFin($username,$pwd){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from sys.f_user_fin_upd_pwd('$username','$pwd')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_user_fin_upd_pwd'];
		}
		return $return;
	}
}