<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: Reporting
*/
class Report
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
	
	function arrToStr($array){
		if(!$array){
			$array=array('');
		}
		$tags = implode(',', $array);
		return $tags;
	}
	
	function get($tabel,$kolom,$groupby,$orderby,$where){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$kolom=$this->arrToStr($kolom);
		$groupby = $this->arrToStr($groupby);
		$orderby = $this->arrToStr($orderby);
		if(count($where)>0){
			$filter=array();
			foreach ($where as $dtWhere){
				//$param="'".$this->arrToStr($dtWhere['param'])."'";
				$param=$this->to_pg_array($dtWhere['param']);
				if($param=='{""}'){
					$param='{}';
				}
				$key=$dtWhere['key'];
				//$filter[]="(".$key."=any(string_to_array(".$param.",',')) or coalesce(array_length(string_to_array(".$param.",','),1),0)=0)";
				$filter[]="(".$key."=any('".$param."') or '{}'='".$param."')";
			}
			$filter = implode(' and ', $filter);
			$stmt=$db->query("select $kolom, count(*) as n from $tabel where $filter group by $groupby order by $orderby");	
		}else{
			$stmt=$db->query("select $kolom, count(*) as n from $tabel group by $groupby order by $orderby");
		}
		$data=$stmt->fetchAll();
		return $data;
	}
	
	function query($tabel,$kolom,$groupby,$orderby,$where){
		$kolom=$this->arrToStr($kolom);
		$groupby = $this->arrToStr($groupby);
		$orderby = $this->arrToStr($orderby);
		if(count($where)>0){
			$filter=array();
			foreach ($where as $dtWhere){
				//$param="'".$this->arrToStr($dtWhere['param'])."'";
				$param=$this->to_pg_array($dtWhere['param']);
				if($param=='{""}'){
					$param='{}';
				}
				$key=$dtWhere['key'];
				//$filter[]="(".$key."=any(string_to_array(".$param.",',')) or coalesce(array_length(string_to_array(".$param.",','),1),0)=0)";
				$filter[]="(".$key."=any('".$param."') or '{}'='".$param."')";
			}
			$filter = implode(' and ', $filter);
			$stmt="select $kolom, count(*) as n from $tabel where $filter group by $groupby order by $orderby";	
		}else{
			$stmt="select $kolom, count(*) as n from $tabel group by $groupby order by $orderby";
		}
		return $stmt;
	}
	
	function getTabel($kd_tabel){
		$tb="";
		$p_key="";
		$p_kol="";
		switch ($kd_tabel) {
			case "mahasiswa":
				$tb="acc.v_mahasiswa";
				$p_key="nim";
				$p_kol="nm_mhs";
			break;
			case "dosen":
				$tb="acc.v_dosen";
				$p_key="kd_dosen";
				$p_kol="nm_dosen";
			break;
			case "kat_dosen":
				$tb="acc.v_kat_dosen";
				$p_key="id_kat_dosen";
				$p_kol="kategori_dosen";
			break;
			case "jab_dosen":
				$tb="ref.v_jabatan_dosen";
				$p_key="id_jab";
				$p_kol="nm_jab";
			break;
			case "angkatan":
				$tb="acc.v_angkatan";
				$p_key="id_angkatan";
				$p_kol="id_angkatan";
			break;
			case "prodi":
				$tb="acc.v_prodi";
				$p_key="kd_prodi";
				$p_kol="nm_prodi";
			break;
			case "periode":
				$tb="acc.v_per_akd";
				$p_key="kd_periode";
				$p_kol="kd_periode";
			break;
			case "sta_mhs":
				$tb="acc.v_stat_mhs";
				$p_key="id_stat_mhs";
				$p_kol="status_mhs";
			break;
			case "stm_mhs":
				$tb="acc.v_stat_masuk";
				$p_key="id_stat_masuk";
				$p_kol="nm_stat_masuk";
			break;
			case "sta_reg":
				$tb="acc.v_status_reg_periode";
				$p_key="kd_status_reg";
				$p_kol="status_reg";
			break;
			case "agm":
				$tb="ref.v_agama";
				$p_key="id_agama";
				$p_kol="nm_agama";
			break;
			case "jk":
				$tb="ref.v_jenis_kelamin";
				$p_key="jenis_kelamin";
				$p_kol="keterangan";
			break;
			case "stat_aktif":
				$tb="ref.stat_aktif";
				$p_key="aktif";
				$p_kol="aktif";
			break;
			case "mhs_reg":
				$tb="acc.v_mhs_reg_periode";
				$p_key="nim";
				$p_kol="kd_status_reg";
			break;
			case "mhs_kul":
				$tb="acc.v_mhs_kuliah_all";
				$p_key="kd_kuliah";
			break;
			case "mhs_kul_ta":
				$tb="acc.v_mhs_kuliah_ta";
				$p_key="kd_kuliah";
			break;
			case "hari":
				$tb="ref.hari";
				$p_key="id_hari";
				$p_kol="nm_hari";
			break;
			case "slot":
				$tb="acc.v_slot";
				$p_key="id_slot";
				$p_kol="range_time";
			break;
			case "jadwal":
				$tb="acc.v_jadwal_kuliah";
			break;
			case "p_kelas":
				$tb="acc.v_paket_kelas";
			break;
			case "p_kelas_ta":
				$tb="acc.v_paket_kelas_ta";
			break;
			case "jns_kelas":
				$tb="acc.v_jns_kelas";
				$p_key="id_jns_kelas";
				$p_kol="jns_kelas";
			break;
			case "kuliah":
				$tb="acc.v_perkuliahan";
			break;
			case "kuliah_abs":
				$tb="acc.v_perkuliahan_absensi";
			break;
			case "absensi":
				$tb="acc.v_absensi";
			break;
			case "stat_hadir":
				$tb="acc.stat_kehadiran";
				$p_key="id_hadir";
				$p_kol="ket";
			break;
			case "ips":
				$tb="acc.v_ips";
			break;
			default:
				$tb="";
				$p_key="";
				$p_kol="";
			break;
		}
		return $tb."||".$p_key."||".$p_kol;
	}
}