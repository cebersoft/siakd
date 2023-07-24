<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: Feeder
*/
class FeederAkm
{
	function to_pg_array($set) {
	    settype($set, 'array'); 
	    $result = array();
	    foreach ($set as $t) {
	        if (is_array($t)) {
	            $result[] = to_pg_array($t);
	        } else {
	            $t = str_replace('"', '\\"', $t); 
	            if (! is_numeric($t)) 
	                $t = '"' . $t . '"';
	            $result[] = $t;
	        }
	    }
	    return '{' . implode(",", $result) . '}';
	}
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
    
	function getIdRegisterMahasiswa($token,$filter,$limit,$ofsset,$url){
		$data=array('act'=>'GetListRiwayatPendidikanMahasiswa','token'=>$token,'filter'=>$filter, 'order'=>$order, 'limit'=>$limit, 'offset'=>$offset);
		$result = $this->runWS($url, $data);
		return $result;

	}
	function getDetailPerkuliahan($token,$filter,$order,$limit,$offset,$url){
		$data=array('act'=>'GetListPerkuliahanMahasiswa','token'=>$token,'filter'=>$filter, 'order'=>$order, 'limit'=>$limit, 'offset'=>$offset);
		$result = $this->runWS($url, $data);
		return $result;
	}
	
	function setPerkuliahan($url,$token,$data){
		$data=array('act'=>'InsertPerkuliahanMahasiswa','token'=>$token,'record'=>$data);
		$result = $this->runWS($url, $data);
		return $result;
	}
	
	function updPerkuliahan($url,$token,$key,$data){
		$data=array('act'=>'UpdatePerkuliahanMahasiswa','token'=>$token,'key'=>$key,'record'=>$data);
		$result = $this->runWS($url, $data);
		return $result;
	}
    
	// 	SIA Side
    function getRegByAktProdiPeriode($id_akt,$kd_prd,$kd_periode){
        // database
        $db=Zend_Registry::get('dbAdapter');
        $stmt=$db->query("select * from feeder.f_mhs_reg_periode_fby_angkatan_prodi_periode('$id_akt','$kd_prd','$kd_periode')");
        $data=$stmt->fetchAll();
        return $data;
    }
}