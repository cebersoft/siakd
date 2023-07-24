<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: Feeder
*/
class FeederMk
{
   function setup($url){
        require_once('nusoap/nusoap.php');
        require_once('nusoap/class.wsdlcache.php');
        $client=new nusoap_client($url,true);
        $proxy=$client->getProxy();
        return $proxy;
    }

    function getDataMk($url,$token,$kd_mk){
        $proxy = $this->setup($url);
        $filter_mk = "kode_mk like '%".$kd_mk."%'";
        $temp_mk = $proxy->getrecordset($token,'mata_kuliah',$filter_mk,'id_mk',2000,0);
        $result = $temp_mk['result'];
        return $result;
    }
    
    function getDataMkKurikulum($url,$token,$id_mk,$id_kur){
        $proxy = $this->setup($url);
        $filter_mk = "p.id_mk='$id_mk' and p.id_kurikulum_sp='$id_kur'";
        $temp_mk = $proxy->getrecordset($token,'mata_kuliah_kurikulum',$filter_mk,'id_mk',1,0);
        $result = $temp_mk['result'];
        return $result;
    }    
    
}