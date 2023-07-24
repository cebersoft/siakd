<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: Feeder
*/
class FeederMhsOut
{   
	function setup($url){
        require_once('nusoap/nusoap.php');
        require_once('nusoap/class.wsdlcache.php');
        $client=new nusoap_client($url,true);
        $proxy=$client->getProxy();
        return $proxy;
    }
	
	function setMhsOut($url,$token,$temp_data){
        $proxy = $this->setup($url);
        $updMhs = $proxy->updaterecord($token,'mahasiswa_pt',json_encode($temp_data));
        $result = $updMhs;
        return $result;
    }
    
	function getDataMhsOutByAngktProdi($url,$token,$id_sp,$angkatan,$id_sms) {
        $proxy = $this->setup($url);
        $mulai_smt = $angkatan."1";
        $filter = "p.mulai_smt='".$mulai_smt."' and p.id_sms='".$id_sms."' and p.id_jns_keluar is not null";
        $order = "nipd";
        $result = $proxy->getrecordset($token,'mahasiswa_pt',$filter,$order,1000,0);
        $data = $result['result'];
        return $data;
    }
    
    // SIA Side
    function getMahasiswaOutByAngkatanProdi($angkatan,$prodi){
        // database
        $db=Zend_Registry::get('dbAdapter');
        $stmt=$db->query("select * from feeder.f_mahasiswa_keluar_fby_angkatan_prodi('$angkatan','$prodi')");
        $data=$stmt->fetchAll();
        return $data;
    }
}