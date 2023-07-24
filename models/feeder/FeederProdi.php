<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: Feeder
*/
class FeederProdi 
{
   function setup($url){
        require_once('nusoap/nusoap.php');
        require_once('nusoap/class.wsdlcache.php');
        $client=new nusoap_client($url,true);
        $proxy=$client->getProxy();
        return $proxy;
    }

    function getDataProdi($url,$token,$id_sp,$kode_prodi){
        $proxy = $this->setup($url);
        $filter_sms = "kode_prodi='".$kode_prodi."' AND id_sp='".$id_sp."'";
        $temp_sms = $proxy->getrecordset($token,'sms',$filter_sms,'id_sms',1,0);
        $result = $temp_sms['result'];
        return $result;
    }
    
	function getDataSMS($url,$token,$id_sp,$id_sms){
        $proxy = $this->setup($url);
        $filter_sms = "id_sms='".$id_sms."' AND id_sp='".$id_sp."'";
        $temp_sms = $proxy->getrecordset($token,'sms',$filter_sms,'id_sms',1,0);
        $result = $temp_sms['result'];
        return $result;
    }
}