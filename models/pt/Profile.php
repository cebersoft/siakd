<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: Profile - Modeling untuk data profil perguruan tinggi
*/
class Profile extends Zend_Db_Table
{
    protected $_name = 'pt.pt_profile';
	protected $_primary='kode_pt';
	
	function updPt($newKd,$nm,$alamat,$kota,$visi,$misi,$nickname,$email,$web,$telp,$fax,$oldKd){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from pt.f_pt_profile_upd('$newKd','$nm','$alamat','$kota','$visi','$misi','$nickname','$email','$web','$telp','$fax','$oldKd')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_pt_profile_upd'];
		}
		return $return;
	}
}