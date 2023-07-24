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
   function setup($url){
        require_once('nusoap/nusoap.php');
        require_once('nusoap/class.wsdlcache.php');
        $client=new nusoap_client($url,true);
        $proxy=$client->getProxy();
        return $proxy;
    }
    
	function getAkmByAktSmt($url,$token,$akt,$id_smt) {
        $proxy = $this->setup($url);
        $mulai_smt=$akt."1";
        $filter = "mulai_smt='$mulai_smt' and p.id_smt='$id_smt'";
        $order = "id_smt";
        $result = $proxy->getrecordset($token,'kuliah_mahasiswa',$filter,$order,50000,0);
        $data = $result['result'];
        return $data;
    }
    
	function setAkm($url,$token,$temp_data){
        $proxy = $this->setup($url);
        $setAkm = $proxy->insertrecord($token,'kuliah_mahasiswa',json_encode($temp_data));
        $result = $setAkm;
        return $result;
    }
    
	function updAkm($url,$token,$temp_data){
        $proxy = $this->setup($url);
        $updAkm = $proxy->updaterecord($token,'kuliah_mahasiswa',json_encode($temp_data));
        $result = $updAkm;
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