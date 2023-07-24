<?php
/*
	Programmer	: Tiar Aristian
	Release		: Agustus 2016
	Module		: Bayar - Modeling untuk pembayaran
*/
class Bayar extends Zend_Db_Table
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
	
	function getBayarByNoTrans($noTrans){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from fin.f_bayar_by_notrans('$noTrans')");
		$data=$stmt->fetchAll();
		return $data;
	}
	
	function getBayarByNim($nim){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from fin.f_bayar_by_nim('$nim')");
		$data=$stmt->fetchAll();
		return $data;
	}
	
	function getBayarByTgl($tgl1,$tgl2){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from fin.f_bayar_by_tanggal('$tgl1','$tgl2')");
		$data=$stmt->fetchAll();
		return $data;
	}
	
	function getBayarDtlByTgl($tgl1,$tgl2){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from fin.f_bayar_detil_by_tanggal('$tgl1','$tgl2')");
		$data=$stmt->fetchAll();
		return $data;
	}
	
	function getBayarDtlByAktProdiTgl($akt,$prd,$tgl1,$tgl2){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$akt = $this->to_pg_array($akt);
		$prd = $this->to_pg_array($prd);
		$stmt=$db->query("select * from fin.f_bayar_detil_by_angkatan_prodi_tanggal('$akt','$prd','$tgl1','$tgl2')");
		$data=$stmt->fetchAll();
		return $data;
	}
	
	function getBayarDtlByPeriodeTerm($kd_periode,$term){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from fin.f_bayar_detil_by_periode_term('$kd_periode','$term')");
		$data=$stmt->fetchAll();
		return $data;
	}

	function getBayarDtlByNim($nim){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from fin.f_bayar_detil_by_nim('$nim')");
		$data=$stmt->fetchAll();
		return $data;
	}
	
	function setBayar($nim,$tgl,$nominal,$via,$bank,$bukti,$term,$kd_periode,$id_komp,$status){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from fin.f_bayar_ins('$nim','$tgl',$nominal,'$via','$bank','$bukti','$term','$kd_periode','$id_komp',$status)");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_bayar_ins'];
		}
		return $return;
	}
	
	function updBayar($nim,$tgl,$nominal,$via,$bank,$bukti,$term,$kd_periode,$id_komp,$status,$notrans){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from fin.f_bayar_upd('$nim','$tgl',$nominal,'$via','$bank','$bukti','$term','$kd_periode','$id_komp',$status,'$notrans')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_bayar_upd'];
		}
		return $return;
	}
	
	function delBayar($noTrans){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from fin.f_bayar_del('$noTrans')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_bayar_del'];
		}
		return $return;
	}
	
	function realocateBayar($nim){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from fin.f_bayar_realocate('$nim')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_bayar_realocate'];
		}
		return $return;
	}

	function terbilang($angka){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from fin.f_terbilang_ina('$angka')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_terbilang_ina'];
		}
		return $return;
	}
}