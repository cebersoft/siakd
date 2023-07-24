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

    // json
    function runWS($url,$data){
    	$ch=curl_init();
    	curl_setopt($ch, CURLOPT_POST, 1);
    	$headers=array();
    	$headers[]='Content-Type:application/json';
    	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    	$data=json_encode($data);
    	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    	curl_setopt($ch, CURLOPT_URL, $url);
    	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    	$result=curl_exec($ch);
    	curl_close($ch);
    	return $result;
    }

    function getToken($username,$password,$url) {
        $data=array('act'=>'GetToken','username'=>$username,'password'=>$password);
        $result=$this->runWS($url, $data);
        return $result;
    }

     function getProfilPT($token,$kd_pt,$url){
        $filter = "kode_perguruan_tinggi = '".$kd_pt."'";
        $order = "";
        $limit = 1;
        $offset=0;
        $data=array('act'=>'GetProfilPT','token'=>$token,'filter'=>$filter, 'order'=>$order, 'limit'=>$limit, 'offset'=>$offset);
        $result = $this->runWS($url, $data);
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

    function getDict($token,$tabel,$url){
	$data=array('act'=>'GetDictionary','token'=>$token,'table'=>$tabel);
	$result = $this->runWs($url,$data);
	return $result;
    }
}