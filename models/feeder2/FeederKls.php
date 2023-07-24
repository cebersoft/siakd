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
	
	function getListKelasKuliah($token,$filter,$order,$limit,$offset,$url){
		$data=array('act'=>'GetListKelasKuliah','token'=>$token,'filter'=>$filter, 'order'=>$order, 'limit'=>$limit, 'offset'=>$offset);
		$result = $this->runWS($url, $data);
		return $result;
	}

	function getListKelasKuliahNilai($token,$filter,$order,$limit,$offset,$url){
		$data=array('act'=>'GetListNilaiPerkuliahanKelas','token'=>$token,'filter'=>$filter, 'order'=>$order, 'limit'=>$limit, 'offset'=>$offset);
		$result = $this->runWS($url, $data);
		return $result;
	}
	
	function getDetailKelasKuliah($token,$filter,$order,$limit,$offset,$url){
		$data=array('act'=>'GetDetailKelasKuliah','token'=>$token,'filter'=>$filter, 'order'=>$order, 'limit'=>$limit, 'offset'=>$offset);
		$result = $this->runWS($url, $data);
		return $result;
	}

	function setKelasKuliah($url,$token,$data){
		$data=array('act'=>'InsertKelasKuliah','token'=>$token,'record'=>$data);
		$result = $this->runWS($url, $data);
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