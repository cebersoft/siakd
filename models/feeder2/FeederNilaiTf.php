<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: Feeder
*/
class FeederNilaiTf
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

	    
    function getNilaiTransferPendidikanMahasiswa($token,$filter,$order,$limit,$offset,$url){
	$data=array('act'=>'GetNilaiTransferPendidikanMahasiswa','token'=>$token,'filter'=>$filter, 'order'=>$order, 'limit'=>$limit, 'offset'=>$offset);
	$result = $this->runWS($url, $data);
	return $result;
    }
    
    // SIA Side
    function getMahasiswaPindByAngkatanProdi($angkatan,$prodi){
        // database
        $db=Zend_Registry::get('dbAdapter');
        $stmt=$db->query("select * from feeder.f_mahasiswa_konversi_fby_angkatan_prodi('$angkatan','$prodi')");
        $data=$stmt->fetchAll();
        return $data;
    }
    
    function getDataNilaiTfByNim($nim) {
    	// database
    	$db=Zend_Registry::get('dbAdapter');
    	$stmt=$db->query("select * from feeder.v_nilai_transfer where nim='$nim'");
    	$data=$stmt->fetchAll();
    	return $data;
    }

    function getDataNilaiTfByAngkatanProdi($akt,$prd) {
    	// database
    	$db=Zend_Registry::get('dbAdapter');
    	$stmt=$db->query("select * from feeder.v_nilai_transfer where id_angkatan='$akt' and kd_prodi='$prd'");
    	$data=$stmt->fetchAll();
    	return $data;
    }

}