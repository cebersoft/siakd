<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: Feeder
*/
class FeederKrs
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
    
	function getPesertaKelasKuliah($token,$filter,$order,$limit,$offset,$url){
		$data=array('act'=>'GetPesertaKelasKuliah','token'=>$token,'filter'=>$filter, 'order'=>$order, 'limit'=>$limit, 'offset'=>$offset);
		$result = $this->runWS($url, $data);
		return $result;
	}
	
	function getDetailNilaiKelasKuliah($token,$filter,$order,$limit,$offset,$url){
		$data=array('act'=>'GetDetailNilaiPerkuliahanKelas','token'=>$token,'filter'=>$filter, 'order'=>$order, 'limit'=>$limit, 'offset'=>$offset);
		$result = $this->runWS($url, $data);
		return $result;
	}
    
	function setKrs($url,$token,$temp_data){
		$data=array('act'=>'InsertPesertaKelasKuliah','token'=>$token,'record'=>$temp_data);
		$result = $this->runWS($url, $data);
		return $result;
    }
    
    // update nilai
	function setNlm($url,$token,$key,$temp_data){
		$data=array('act'=>'UpdateNilaiPerkuliahanKelas','token'=>$token,'key'=>$key,'record'=>$temp_data);
		$result = $this->runWS($url, $data);
		return $result;
    }
    
	// 	SIA Side
    function getKrsByKlsMkProdiSmt($nm_kls,$kd_mk,$kd_prodi,$id_smt){
        // database
        $db=Zend_Registry::get('dbAdapter');
        $stmt=$db->query("select * from feeder.v_mhs_kuliah where id_nm_kelas='$nm_kls' and kode_mk='$kd_mk' and kd_prodi='$kd_prodi' and id_smt='$id_smt'");
        $data=$stmt->fetchAll();
        return $data;
    }
}