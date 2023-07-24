<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: Feeder
*/
class FeederKurikulum
{
   function setup($url){
        require_once('nusoap/nusoap.php');
        require_once('nusoap/class.wsdlcache.php');
        $client=new nusoap_client($url,true);
        $proxy=$client->getProxy();
        return $proxy;
    }

    function getDataKurikulum($url,$token){
        $proxy = $this->setup($url);
        $filter = "";
        $temp = $proxy->getrecordset($token,'kurikulum',$filter,'id_kurikulum_sp',100,0);
        $result = $temp['result'];
        return $result;
    }
}