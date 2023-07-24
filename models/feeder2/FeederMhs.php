<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: Feeder
*/
class FeederMhs 
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

    function getListMahasiswa($token,$filter,$order,$limit,$offset,$url){
		$data=array('act'=>'GetListMahasiswa','token'=>$token,'filter'=>$filter, 'order'=>$order, 'limit'=>$limit, 'offset'=>$offset);
		$result = $this->runWS($url, $data);
		return $result;
    }

    function getBiodataMahasiswa($token,$filter,$order,$limit,$offset,$url){
    	$data=array('act'=>'GetBiodataMahasiswa','token'=>$token,'filter'=>$filter, 'order'=>$order, 'limit'=>$limit, 'offset'=>$offset);
    	$result = $this->runWS($url, $data);
    	return $result;
    }
    
    function getListRiwayatPendidikanMahasiswa($token,$filter,$order,$limit,$offset,$url){
    	$data=array('act'=>'GetListRiwayatPendidikanMahasiswa','token'=>$token,'filter'=>$filter, 'order'=>$order, 'limit'=>$limit, 'offset'=>$offset);
    	$result = $this->runWS($url, $data);
    	return $result;
    }

    
   function setMhs($url,$token,$temp_data){
    	$data=array('act'=>'InsertBiodataMahasiswa','token'=>$token,'record'=>$temp_data);
    	$result=$this->runWS($url, $data);
        return $result;
    }
    
    function setMhsPT($url,$token,$temp_data){
    	$data=array('act'=>'InsertRiwayatPendidikanMahasiswa','token'=>$token,'record'=>$temp_data);
    	$result=$this->runWS($url, $data);
    	return $result;
    }
    
	function updMhs($url,$token,$key,$record){
		$data=array('act'=>'UpdateBiodataMahasiswa','token' =>$token,'key'=>$key, 'record'=>$record);
		$result=$this->runWS($url, $data);
		return $result;
    }
    
	function updMhsPT($url,$token,$key,$record){
		$data=array('act'=>'UpdateRiwayatPendidikanMahasiswa','token' =>$token,'key'=>$key,'record'=>$record);
		$result=$this->runWS($url, $data);
		return $result;
    }
    
    // SIA Side
    function getMahasiswaByAngkatanProdi($angkatan,$prodi){
        // database
        $db=Zend_Registry::get('dbAdapter');
        $stmt=$db->query("select * from feeder.f_mahasiswa_fby_angkatan_prodi('$angkatan','$prodi')");
        $data=$stmt->fetchAll();
        return $data;
    }
    
	function getMahasiswaByNim($nim){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$nim = $this->to_pg_array($nim);
		$stmt=$db->query("select * from feeder.f_mahasiswa_fby_nim('$nim')");
		$data=$stmt->fetchAll();
		return $data;
	}
}