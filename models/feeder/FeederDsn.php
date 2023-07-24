<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: Feeder
*/
class FeederDsn 
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
    
    function getDsnByIdptk($url,$token,$id_ptk) {
    	$proxy = $this->setup($url);
        $filter = "p.id_ptk = '$id_ptk'";
        $order = "id_ptk";
        $result = $proxy->getrecordset($token,'dosen',$filter,$order,5000,0);
        $data = $result['result'];
        return $data;
    }
    
	function getDsnByNidn($url,$token,$nidn) {
    	$proxy = $this->setup($url);
        $filter = "p.nidn = '$nidn'";
        $order = "id_sdm";
        $result = $proxy->getrecordset($token,'dosen',$filter,$order,1,0);
        $data = $result['result'];
        return $data;
    }
    
	function getPenugasanDsn($url,$token,$id_sp,$thn_ajaran){
    	$proxy = $this->setup($url);
        $filter = "p.id_sp='$id_sp' and p.id_thn_ajaran::text like '%$thn_ajaran%'";
        $order = "id_reg_ptk";
        $result = $proxy->getrecordset($token,'dosen_pt',$filter,$order,50000,0);
        $data = $result['result'];
        return $data;
    }
    
    function getPenugasanDsnByThnIdsdm($url,$token,$id_sp,$thn,$id_sdm){
    	$proxy = $this->setup($url);
        $filter = "p.id_thn_ajaran=$thn and p.id_sp='$id_sp' and p.id_sdm='$id_sdm'";
        $order = "id_reg_ptk";
        $result = $proxy->getrecordset($token,'dosen_pt',$filter,$order,1,0);
        $data = $result['result'];
        return $data;
    }
    
	function getKlsDsnByIdKls($url,$token,$id_kls) {
        $proxy = $this->setup($url);
        $id_kls = $this->to_pg_array($id_kls);
        $filter = "p.id_kls=any('".$id_kls."')";
        $order = "id_kls";
        $result = $proxy->getrecordset($token,'ajar_dosen',$filter,$order,50000,0);
        $data = $result['result'];
        return $data;
    }
    
	function getListPenugasanDosenFeeder($url,$token,$where,$order,$limit,$offset){
        $proxy = $this->setup($url);
        $temp = $proxy->GetListPenugasanDosen($token,$where,$order,$limit,$offset);
        $result = $temp['result'];
        return $result;
    }
    
	function setAjarDsn($url,$token,$temp_data){
        $proxy = $this->setup($url);
        $setAjarDsn = $proxy->insertrecord($token,'ajar_dosen',json_encode($temp_data));
        $result = $setAjarDsn;
        return $result;
    }
    
	// 	hapus ajar dosen
	function delAjarDsn($url,$token,$record){
        $proxy = $this->setup($url);
        $delKrs = $proxy->deleterecordset($token,'ajar_dosen',json_encode($record));
        $result = $delKrs;
        return $result;
    }
    
    // update ajar dosen
    function updAjarDsn($url,$token,$record){
    	$proxy = $this->setup($url);
        $updAjar = $proxy->updaterecord($token,'ajar_dosen',json_encode($record));
        $result = $updAjar;
        return $result;	
    }
    
	// SIA Side
    function getKelasDosenByPeriodeProdi($periode,$prodi){
        // database
        $db=Zend_Registry::get('dbAdapter');
        $stmt=$db->query("select * from feeder.f_paket_kelas_fby_periode_prodi('$periode','$prodi') order by kode_mk,id_nm_kelas");
        $data=$stmt->fetchAll();
        return $data;
    }
}