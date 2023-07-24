<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: User - Modeling untuk user sistem
*/
class Menu extends Zend_Db_Table
{
	function getMenuDtlAcc(){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from sys.v_menu_acc_dtl");
		$data=$stmt->fetchAll();
		return $data;
	}
	
	function cekUserMenu($menu){
		Zend_Loader::loadClass('Zend_Session');
		$i=0;
		$ses_menu = new Zend_Session_Namespace('ses_menu');
		$list_menu = $ses_menu->menu;
		if($list_menu){
			foreach($list_menu as $data){
				$arrData = explode('|',$data);
				if ($arrData[0] == $menu){
					$i++;
				}
			}
		}
		if ($i>0){
			$return="T";
		} else {
			$return="F";
		}
		return $return;
	}
}