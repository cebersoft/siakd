<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: Feeder
*/
class FeederKls 
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
    
 	function getKelasKuliahBySmtSms($url,$token,$id_smt,$id_sms) {
        $proxy = $this->setup($url);
        $filter = "p.id_smt='".$id_smt."' and p.id_sms='".$id_sms."'";
        $order = "id_kls";
        $result = $proxy->getrecordset($token,'kelas_kuliah',$filter,$order,1000,0);
        $data = $result['result'];
        return $data;
    }
    
    function getKelasKuliahBySmtSmsMkNm($url,$token,$id_smt,$id_sms,$id_mk,$nm_kls) {
    	$proxy = $this->setup($url);
        $filter = "p.id_smt='".$id_smt."' and p.id_sms='".$id_sms."' and p.id_mk='".$id_mk."' and p.nm_kls='".$nm_kls."'";
        $order = "id_kls";
        $result = $proxy->getrecordset($token,'kelas_kuliah',$filter,$order,1,0);
        $data = $result['result'];
        return $data;
    }
    
	function setKelasKuliah($url,$token,$temp_data){
        $proxy = $this->setup($url);
        $setKelasKuliah = $proxy->insertrecord($token,'kelas_kuliah',json_encode($temp_data));
        $result = $setKelasKuliah;
        return $result;
    }
    
	// hapus krs
	function delKlsKuliah($url,$token,$record){
        $proxy = $this->setup($url);
        $delKrs = $proxy->deleterecordset($token,'kelas_kuliah',json_encode($record));
        $result = $delKrs;
        return $result;
    }
    
	function getListKelasFeeder($url,$token,$where,$order,$limit,$offset){
        $proxy = $this->setup($url);
        $temp = $proxy->GetListKelasKuliah($token,$where,$order,$limit,$offset);
        $result = $temp['result'];
        return $result;
    }
    
	// SIA Side
    function getPaketKelasByPeriodeProdi($periode,$prodi){
        // database
        $db=Zend_Registry::get('dbAdapter');
        $stmt=$db->query("SELECT id_nm_kelas, kode_mk, nm_mk, sks_tm, sks_prak, sks_prak_lap, sks_sim, id_smt, sum(n_mhs_kuliah) as n_mhs_kuliah, count(kd_dosen) as n_dosen FROM (select * from feeder.f_paket_kelas_fby_periode_prodi('$periode','$prodi')) a group by id_nm_kelas, id_smt, kode_mk, sks_tm, sks_prak, sks_prak_lap, sks_sim, nm_mk order by kode_mk, id_nm_kelas");
        $data=$stmt->fetchAll();
        return $data;
    }
    
	function getPaketKelasByKd($kd_paket){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$kd_paket = $this->to_pg_array($kd_paket);
		$stmt=$db->query("select * from feeder.f_paket_kelas_fby_kd('$kd_paket')");
		$data=$stmt->fetchAll();
		return $data;
	}
}