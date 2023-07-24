<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: Feeder
*/
class ConfigKurikulum 
{
   function setup($url){
        require_once('nusoap/nusoap.php');
        require_once('nusoap/class.wsdlcache.php');
        $client=new nusoap_client($url,true);
        $proxy=$client->getProxy();
        return $proxy;
    }

    function getDataKurikulumProdi(){
        // database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from feeder.v_kurikulum_prodi");
		$data=$stmt->fetchAll();
        return $data;
    }
    
	function setKurikulumProdi($kd_prodi,$kd_periode,$id_kur){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from feeder.f_kurikulum_periode_ins('$kd_prodi','$kd_periode','$id_kur')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_kurikulum_periode_ins'];
		}
		return $return;
	}
	
	function delKurikulumProdi($kd_prodi,$kd_periode){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from feeder.f_kurikulum_periode_del('$kd_prodi','$kd_periode')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_kurikulum_periode_del'];
		}
		return $return;
	}
	
	function getDataKurikulumByProdiPeriode($kd_prodi,$kd_periode){
        // database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from feeder.v_kurikulum_prodi where kd_prodi='$kd_prodi' and kd_periode='$kd_periode' limit 1");
		$data=$stmt->fetchAll();
        return $data;
    }
}