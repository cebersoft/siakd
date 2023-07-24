<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: Jenis Kelamin - Modeling untuk master jenis kelamin
*/
class JenisKelamin extends Zend_Db_Table
{
    	function getAll(){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from ref.v_jenis_kelamin");
		$data=$stmt->fetchAll();
		return $data;
	}
}