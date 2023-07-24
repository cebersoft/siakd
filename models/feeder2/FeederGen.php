<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: Feeder
*/
class FeederGen
{
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
	
	function getWilayah($token,$filter,$order,$limit,$offset,$url){
		$data=array('act'=>'GetWilayah','token'=>$token,'filter'=>$filter, 'order'=>$order, 'limit'=>$limit, 'offset'=>$offset);
		$result = $this->runWS($url, $data);
		return $result;
	}
	
	function getKebutuhanKhusus($token,$filter,$order,$limit,$offset,$url){
		$data=array('act'=>'GetKebutuhanKhusus','token'=>$token,'filter'=>$filter, 'order'=>$order, 'limit'=>$limit, 'offset'=>$offset);
		$result = $this->runWS($url, $data);
		return $result;
	}
	
	function getAgama($token,$filter,$order,$limit,$offset,$url){
		$data=array('act'=>'GetAgama','token'=>$token,'filter'=>$filter, 'order'=>$order, 'limit'=>$limit, 'offset'=>$offset);
		$result = $this->runWS($url, $data);
		return $result;
	}
	
	function getDictionary($token,$filter,$order,$limit,$offset,$url){
		$data=array('act'=>'GetDictionary','token'=>$token,'filter'=>$filter, 'order'=>$order, 'limit'=>$limit, 'offset'=>$offset);
		$result = $this->runWS($url, $data);
		return $result;
	}
	
	function getSkalaNilai($token,$filter,$order,$limit,$offset,$url){
		$data=array('act'=>'GetListSkalaNilaiProdi','token'=>$token,'filter'=>$filter, 'order'=>$order, 'limit'=>$limit, 'offset'=>$offset);
		$result = $this->runWS($url, $data);
		return $result;
	}
}