<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: Feeder
*/
class WsConfig extends Zend_Db_Table
{
    protected $_name = 'feeder.v_master_ws';
    protected $_primary='live';

    function setup($url){
    	require_once('nusoap/nusoap.php');
		require_once('nusoap/class.wsdlcache.php');
    	$client=new nusoap_client($url,true);
    	$proxy=$client->getProxy();
    	return $proxy;
    }

    function getDataToken($username,$password,$url) {
        $proxy = $this->setup($url);
        $result=$proxy->getToken($username,$password);
        return $result;
    }

     function getDataSp($token,$npsn,$url){
        $proxy = $this->setup($url);
        $filter_sp = "npsn = '".$npsn."'";
        $temp_sp = $proxy->getrecord($token,'satuan_pendidikan',$filter_sp);
        $id_sp = $temp_sp['result']['id_sp'];
        $nm_lemb = $temp_sp['result']['nm_lemb'];
        $result = array ('id_sp'=>$id_sp, 'nm_lemb'=>$nm_lemb);
        return $result;
    }

    function updWsConfig($url,$username,$password,$ws){
        // database
        $db=Zend_Registry::get('dbAdapter');
        $query=$db->query("select * from feeder.f_master_ws_upd('$url','$username','$password','$ws')");
        $isset=$query->fetchAll();
        foreach ($isset as $returnData) {
            $return=$returnData['f_master_ws_upd'];
        }
        return $return;
    }
    
 	function getDictTabel($token,$url,$tabel){
        $proxy = $this->setup($url);
        $filter = "";
        $order = "";
        $result = $proxy->getrecordset($token,$tabel,$filter,$order,1000,0);
        $data = $result['result'];
        return $data;
    }
}