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
   function setup($url){
        require_once('nusoap/nusoap.php');
        require_once('nusoap/class.wsdlcache.php');
        $client=new nusoap_client($url,true);
        $proxy=$client->getProxy();
        return $proxy;
    }
    
	function getKrsByIdKls($url,$token,$id_kls) {
        $proxy = $this->setup($url);
        $id_kls = $this->to_pg_array($id_kls);
        $filter = "p.id_kls=any('".$id_kls."')";
        $order = "id_kls";
        $result = $proxy->getrecordset($token,'nilai',$filter,$order,50000,0);
        $data = $result['result'];
        return $data;
    }
    
	function setKrs($url,$token,$temp_data){
        $proxy = $this->setup($url);
        $setKrs = $proxy->insertrecord($token,'nilai',json_encode($temp_data));
        $result = $setKrs;
        return $result;
    }
    
    // hapus krs
	function delKrs($url,$token,$record){
        $proxy = $this->setup($url);
        $delKrs = $proxy->deleterecordset($token,'nilai',json_encode($record));
        $result = $delKrs;
        return $result;
    }
    
    // update nilai
	function setNlm($url,$token,$temp_data){
        $proxy = $this->setup($url);
        $setNlm = $proxy->updaterecord($token,'nilai',json_encode($temp_data));
        $result = $setNlm;
        return $result;
    }
    
	function setNlmMass($url,$token,$temp_data){
        $proxy = $this->setup($url);
        $setNlm = $proxy->updaterecordset($token,'nilai',json_encode($temp_data));
        $result = $setNlm;
        return $result;
    }
    
	function getListNilaiFeeder($url,$token,$where,$order,$limit,$offset){
        $proxy = $this->setup($url);
        $temp = $proxy->GetListNilai($token,$where,$order,$limit,$offset);
        $result = $temp['result'];
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