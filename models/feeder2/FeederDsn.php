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
	
	function GetDosen($token,$filter,$order,$limit,$offset,$url){
		$data=array('act'=>'GetListDosen','token'=>$token,'filter'=>$filter, 'order'=>$order, 'limit'=>$limit, 'offset'=>$offset);
		$result = $this->runWS($url, $data);
		return $result;
	}
	
	function GetDetilPenugasanDosen($token,$filter,$order,$limit,$offset,$url){
		$data=array('act'=>'GetDetailPenugasanDosen','token'=>$token,'filter'=>$filter, 'order'=>$order, 'limit'=>$limit, 'offset'=>$offset);
		$result = $this->runWS($url, $data);
		return $result;
	}
	
	function GetDosenKelas($token,$filter,$order,$limit,$offset,$url){
		$data=array('act'=>'GetDosenPengajarKelasKuliah','token'=>$token,'filter'=>$filter, 'order'=>$order, 'limit'=>$limit, 'offset'=>$offset);
		$result = $this->runWS($url, $data);
		return $result;
	}

	function setDosenKelas($url,$token,$data){
		$data=array('act'=>'InsertDosenPengajarKelasKuliah','token'=>$token,'record'=>$data);
		$result = $this->runWS($url, $data);
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

	function getKelasDosenTimTeachingByPeriodeProdi($periode,$prodi){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from feeder.f_paket_kelas_tim_teaching_fby_periode_prodi('$periode','$prodi') order by kode_mk,id_nm_kelas");
		$data=$stmt->fetchAll();
		return $data;
	}
}