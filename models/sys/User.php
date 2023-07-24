<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: User - Modeling untuk user sistem
*/
class User extends Zend_Db_Table
{
	protected $_name = 'sys.user_acc';
	protected $_primary='username';
	
	function getUserAcByUname($username){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from sys.f_user_acc_by_username('$username')");
		$data=$stmt->fetchAll();
		return $data;
	}
	
	function getMenuAcByUname($username){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from sys.f_akses_acc_by_username('$username')");
		$data=$stmt->fetchAll();
		return $data;
	}
	
	function getMenuAcByUnameNot($username){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from sys.v_menu_acc_dtl where id not in (select id from sys.f_akses_acc_by_username('$username'))");
		$data=$stmt->fetchAll();
		return $data;
	}
	
	function getProdiByUname($username){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from sys.f_user_acc_prodi_by_username('$username')");
		$data=$stmt->fetchAll();
		return $data;
	}
	
	function setUserAcc($username,$pwd,$nama,$superadmin,$email){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from sys.f_user_acc_ins('$username','$pwd','$nama','$superadmin','$email')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_user_acc_ins'];
		}
		return $return;
	}
	
	function delUserAcc($username){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from sys.f_user_acc_del('$username')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_user_acc_del'];
		}
		return $return;
	}
	
	function updUserAcc($username,$nama,$email){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from sys.f_user_acc_upd('$username','$nama','$email')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_user_acc_upd'];
		}
		return $return;
	}
	
	function updPasswordUserAcc($username,$pwd){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from sys.f_user_acc_upd_pwd('$username','$pwd')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_user_acc_upd_pwd'];
		}
		return $return;
	}
	
	function setAksesAcc($username,$id_menu){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from sys.f_akses_acc_ins('$username','$id_menu')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_akses_acc_ins'];
		}
		return $return;
	}
	
	function delAksesAcc($username,$id_menu){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from sys.f_akses_acc_del('$username','$id_menu')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_akses_acc_del'];
		}
		return $return;
	}
}