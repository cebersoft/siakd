<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: Wilayah - Modeling untuk master wilayah
*/
class Wilayah extends Zend_Db_Table
{
    protected $_name = 'ref.v_wilayah';
	protected $_primary='id_wil';
	
	function getWilayahById($id_wil){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from ref.v_wlayah where id_wil='$id_wil' limit 1");
		$data=$stmt->fetchAll();
		return $data;
	}
	
	function getWilayahByLevel($id_level){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from ref.v_wlayah where id_level_wil=$id_level");
		$data=$stmt->fetchAll();
		return $data;
	}
}