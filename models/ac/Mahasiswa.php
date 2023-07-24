<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: Mahasiswa - Modeling untuk master mahasiswa
*/
class Mahasiswa
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

	function getMahasiswaByAngkatanProdi($angkatan,$prodi){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$angkatan = $this->to_pg_array($angkatan);
		$prodi = $this->to_pg_array($prodi);
		$stmt=$db->query("select * from acc.f_mahasiswa_fby_angkatan_prodi('$angkatan','$prodi')");
		$data=$stmt->fetchAll();
		return $data;
	}

	function getMahasiswaByAngkatanProdiRangeNim($angkatan,$prodi,$nim1,$nim2){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$angkatan = $this->to_pg_array($angkatan);
		$prodi = $this->to_pg_array($prodi);
		$stmt=$db->query("select * from acc.f_mahasiswa_fby_angkatan_prodi('$angkatan','$prodi') where (nim between '$nim1' and '$nim2') or (nim between '$nim2' and '$nim1')");
		$data=$stmt->fetchAll();
		return $data;
	}

	function getMahasiswaByAngkatanProdiStatus($angkatan,$prodi,$status){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$angkatan = $this->to_pg_array($angkatan);
		$prodi = $this->to_pg_array($prodi);
		$status = $this->to_pg_array($status);
		$stmt=$db->query("select * from acc.f_mahasiswa_fby_angkatan_prodi_status('$angkatan','$prodi','$status')");
		$data=$stmt->fetchAll();
		return $data;
	}
	
	function getMahasiswaByAngkatanProdiStatusDw($angkatan,$prodi,$status,$dw){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$angkatan = $this->to_pg_array($angkatan);
		$prodi = $this->to_pg_array($prodi);
		$status = $this->to_pg_array($status);
		$stmt=$db->query("select * from acc.f_mahasiswa_fby_angkatan_prodi_status('$angkatan','$prodi','$status') where kd_dosen_wali='$dw'");
		$data=$stmt->fetchAll();
		return $data;
	}

	function getMahasiswaById($idMhs){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from acc.f_mahasiswa_fby_id('$idMhs')");
		$data=$stmt->fetchAll();
		return $data;
	}

	function getMahasiswaByNim($nim){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from acc.f_mahasiswa_fby_nim('$nim')");
		$data=$stmt->fetchAll();
		return $data;
	}

	function setMahasiswa($nm,$jk,$tmpLhr,$tglLhr,$agm,$kwn,$alamat,$kota,$idwil,$ayah,$ibu,$j_ayah, $j_ibu,$nik,$email_k,$email_l,$kontak,$nim,$tglMsk,$id_stat_msk,$kd_prodi,$akt,$sks,$pt_asal,$prodi_asal,$id_stat_mhs,$dw){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from acc.f_mahasiswa_ins('$nm','$jk','$tmpLhr','$tglLhr','$agm','$kwn','$alamat','$kota','$idwil','$ayah','$ibu','$j_ayah','$j_ibu','$nik','$email_k','$email_l','$kontak','$nim','$tglMsk','$id_stat_msk','$kd_prodi','$akt','$sks','$pt_asal','$prodi_asal','$id_stat_mhs','$dw')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_mahasiswa_ins'];
		}
		return $return;
	}

	function setMahasiswa2($nm,$jk,$tmpLhr,$tglLhr,$agm,$kwn,$alamat,$kota,$idwil,$ayah,$ibu,$j_ayah, $j_ibu,$nik,$email_k,$email_l,$kontak,$nim,$tglMsk,$id_stat_msk,$kd_prodi,$akt,$sks,$pt_asal,$prodi_asal,$id_stat_mhs,$dw,$nisn,$npwp,$jln,$dusun,$rt,$rw,$kel,$zip,$kps,$trans,$tinggal){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from acc.f_mahasiswa_ins('$nm','$jk','$tmpLhr','$tglLhr','$agm','$kwn','$alamat','$kota','$idwil','$ayah','$ibu','$j_ayah','$j_ibu','$nik','$email_k','$email_l','$kontak','$nim','$tglMsk','$id_stat_msk','$kd_prodi','$akt','$sks','$pt_asal','$prodi_asal','$id_stat_mhs','$dw','$nisn','$npwp','$jln','$dusun','$rt','$rw','$kel','$zip','$kps','$trans','$tinggal')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_mahasiswa_ins'];
		}
		return $return;
	}

	function delMahasiswa($id){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from acc.f_mahasiswa_del('$id')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_mahasiswa_del'];
		}
		return $return;	
	}

	function updMahasiswa($nm,$jk,$tmpLhr,$tglLhr,$agm,$kwn,$alamat,$kota,$idwil,$ayah,$ibu,$j_ayah, $j_ibu,$nik,$email_k,$email_l,$kontak,$nim,$tglMsk,$id_stat_msk,$kd_prodi,$akt,$sks,$pt_asal,$prodi_asal,$dw,$oldNim,$idMhs){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from acc.f_mahasiswa_upd('$nm','$jk','$tmpLhr','$tglLhr','$agm','$kwn','$alamat','$kota','$idwil','$ayah','$ibu','$j_ayah','$j_ibu','$nik','$email_k','$email_l','$kontak','$nim','$tglMsk','$id_stat_msk','$kd_prodi','$akt','$sks','$pt_asal','$prodi_asal','$dw','$oldNim','$idMhs')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_mahasiswa_upd'];
		}
		return $return;
	}

	function updMahasiswa2($nm,$jk,$tmpLhr,$tglLhr,$agm,$kwn,$alamat,$kota,$idwil,$ayah,$ibu,$j_ayah, $j_ibu,$nik,$email_k,$email_l,$kontak,$nim,$tglMsk,$id_stat_msk,$kd_prodi,$akt,$sks,$pt_asal,$prodi_asal,$dw,$oldNim,$idMhs,$nisn,$npwp,$jln,$dusun,$rt,$rw,$kel,$zip,$kps,$trans,$tinggal){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from acc.f_mahasiswa_upd('$nm','$jk','$tmpLhr','$tglLhr','$agm','$kwn','$alamat','$kota','$idwil','$ayah','$ibu','$j_ayah','$j_ibu','$nik','$email_k','$email_l','$kontak','$nim','$tglMsk','$id_stat_msk','$kd_prodi','$akt','$sks','$pt_asal','$prodi_asal','$dw','$oldNim','$idMhs','$nisn','$npwp','$jln','$dusun','$rt','$rw','$kel','$zip','$kps','$trans','$tinggal')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_mahasiswa_upd'];
		}
		return $return;
	}
	
	function updPwdMahasiswa($pwd,$nim){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from acc.f_mahasiswa_upd_pwd('$nim','$pwd')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_mahasiswa_upd_pwd'];
		}
		return $return;
	}
	
	function updPwd2Mahasiswa($pwd,$nim,$old_pwd){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from acc.f_mahasiswa_upd_pwd2('$nim','$pwd','$old_pwd')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_mahasiswa_upd_pwd2'];
		}
		return $return;	
	}
}