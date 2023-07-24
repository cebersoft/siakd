<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: Perwalian - Modeling untuk perwalian
*/
class Perwalian extends Zend_Db_Table
{
	
	function getPerwalianFeedByPeriodeNim($kd_periode,$nim){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from acc.f_perwalian_feed_fby_periode_nim('$kd_periode','$nim')");
		$data=$stmt->fetchAll();
		return $data;
	}
	
	function setPerwalianFeed($asal,$kd_periode,$nim,$isi,$sender,$receiver){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from acc.f_perwalian_feed_ins('$asal','$kd_periode','$nim','$isi','$sender','$receiver')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_perwalian_feed_ins'];
		}
		return $return;
	}

	function getPerwalianFeedByPeriodeDw($kd_periode,$kd_dw){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from acc.f_perwalian_feed_fby_periode_dw('$kd_periode','$kd_dw')");
		$data=$stmt->fetchAll();
		return $data;
	}
}