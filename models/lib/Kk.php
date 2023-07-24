<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: KK - Modeling untuk data KK
*/
class Kk 
{
	function getKk(){
		// database
		$db=Zend_Registry::get('dbAdapter2');
		$stmt=$db->query("select * from ac.stfi_vlib_kk");
		$data=$stmt->fetchAll();
		return $data;
	}
}