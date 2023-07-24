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
	
   function setup($url){
        require_once('nusoap/nusoap.php');
        require_once('nusoap/class.wsdlcache.php');
        $client=new nusoap_client($url,true);
        $proxy=$client->getProxy();
        return $proxy;
    }

    function getDataProdi($url,$token,$id_sp,$kode_prodi){
        $proxy = $this->setup($url);
        $filter_sms = "kode_prodi='".$kode_prodi."' AND id_sp='".$id_sp."'";
        $temp_sms = $proxy->getrecord($token,'sms',$filter_sms);
        $result = $temp_sms;
        return $result;
    }

    function getDataMhsByAngktProdi($url,$token,$id_sp,$angkatan,$id_sms) {
        $proxy = $this->setup($url);
        $mulai_smt = $angkatan."1";
        $filter = "p.mulai_smt='".$mulai_smt."' and p.id_sms='".$id_sms."'";
        $order = "nipd";
        $result = $proxy->getrecordset($token,'mahasiswa_pt',$filter,$order,1000,0);
        $data = $result['result'];
        return $data;
    }

    function getDataMhsByIdent($url,$token,$id_sp,$nama,$tmpLahir,$tglLahir,$ibuKdg){
        $proxy = $this->setup($url);
        $filter_pd = "(trim(upper(nm_pd))='trim(upper(".$nama."))') AND trim((upper(tmpt_lahir))='trim(upper(".$tmpLahir."))') AND (tgl_lahir='".$tglLahir."') AND trim((upper(nm_ibu_kandung))='trim(upper(".$ibuKdg."))') AND (p.id_sp='".$id_sp."')";
        $filter_pd = "(trim(upper(nm_pd))='trim(upper(".$nama."))') AND trim((upper(tmpt_lahir))='trim(upper(".$tmpLahir."))') AND (tgl_lahir='".$tglLahir."') AND (p.id_sp='".$id_sp."')";
        $result = $proxy->getrecord($token,'mahasiswa',$filter_pd);
        $data = $result['result'];
        return $data;
    }
    
	function getDataMhsByIdpd($url,$token,$id_sp,$id_pd){
        $proxy = $this->setup($url);
        $id_pd = $this->to_pg_array($id_pd);
        $filter_pd = "id_pd=any('".$id_pd."')";
        $result = $proxy->getrecordset($token,'mahasiswa',$filter_pd,'id_pd',1000,0);
        $data = $result['result'];
        return $data;
    }

    function getDataMhsPTByNimSms($url,$token,$nim,$id_sms){
        $proxy = $this->setup($url);
        $filter_regpd = "p.nipd LIKE '%".$nim."%' AND p.id_sms='".$id_sms."'";
        $result = $proxy->getrecordset($token,'mahasiswa_pt',$filter_regpd,'nipd',1,0);
        $data=$result['result'];
        return $data;
    }
    
	function getDataMhsPTByIdReg($url,$token,$id_reg_pd){
        $proxy = $this->setup($url);
        $filter_regpd = "p.id_reg_pd='".$id_reg_pd."'";
        $result = $proxy->getrecordset($token,'mahasiswa_pt',$filter_regpd,'nipd',1,0);
        $data=$result['result'];
        return $data;
    }

    function setMhs($url,$token,$temp_data){
        $proxy = $this->setup($url);
        $setMhs = $proxy->insertrecord($token,'mahasiswa',json_encode($temp_data));
        $result = $setMhs;
        return $result;
    }

    function setMhsPT($url,$token,$temp_data){
        $proxy = $this->setup($url);
        $setMhsPT = $proxy->insertrecord($token,'mahasiswa_pt',json_encode($temp_data));
        $result = $setMhsPT;
        return $result;
    }
    
	function updMhs($url,$token,$temp_data){
        $proxy = $this->setup($url);
        $updMhs = $proxy->updaterecord($token,'mahasiswa',json_encode($temp_data));
        $result = $updMhs;
        return $result;
    }
    
	function updMhsPT($url,$token,$temp_data){
        $proxy = $this->setup($url);
        $updMhs = $proxy->updaterecord($token,'mahasiswa_pt',json_encode($temp_data));
        $result = $updMhs;
        return $result;
    }
    
	function getListMhsFeeder($url,$token,$where,$order,$limit,$offset){
        $proxy = $this->setup($url);
        $temp = $proxy->GetListMahasiswa($token,$where,$order,$limit,$offset);
        $result = $temp['result'];
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