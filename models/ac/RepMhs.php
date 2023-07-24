<?php
/*
	Programmer	: Tiar Aristian
	Release		: Agustus 2012
	Module		: Report Mahasiswa (SIA)
*/
class RepMhs
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
	function getRepJk($angkatan,$prodi){
		// database
		$where='';
        	$db=Zend_Registry::get('dbAdapter');
		if(!empty($prodi)){
			$prodi = $this->to_pg_array($prodi);
			$where .= " and kd_prodi = any('$prodi')";
		}
		if(!empty($angkatan)){
			$angkatan = $this->to_pg_array($angkatan);
			$where .= " and id_angkatan = any('$angkatan')";
		}
		$stmt=$db->query("select kd_prodi,id_angkatan, jenis_kelamin, count(nim) as n_mahasiswa from acc.v_mahasiswa 
       		where 1=1  $where
        	group by kd_prodi, id_angkatan, jenis_kelamin
			order by kd_prodi, id_angkatan, jenis_kelamin");
			
		$data=$stmt->fetchAll();
		return $data;
    	}
    	function getRepAgama($angkatan,$prodi){
		// database
		$where='';
		$db=Zend_Registry::get('dbAdapter');
		if(!empty($prodi)){
			$prodi = $this->to_pg_array($prodi);
			$where .= " and kd_prodi = any('$prodi')";
		}
		if(!empty($angkatan)){
			$angkatan = $this->to_pg_array($angkatan);
			$where .= " and id_angkatan = any('$angkatan')";
		}
		$stmt=$db->query("select kd_prodi,id_angkatan, id_agama, count(nim) as n_mahasiswa from acc.v_mahasiswa 
		where 1=1  $where
		group by kd_prodi, id_angkatan, id_agama
        	order by kd_prodi, id_angkatan, id_agama");
		$data=$stmt->fetchAll();
		return $data;
    	}
    
	function getRepStatMhs($angkatan,$prodi){
		// database
		$where='';
		$db=Zend_Registry::get('dbAdapter');
		if(!empty($prodi)){
			$prodi = $this->to_pg_array($prodi);
			$where .= " and kd_prodi = any('$prodi')";
		}
		if(!empty($angkatan)){
			$angkatan = $this->to_pg_array($angkatan);
			$where .= " and id_angkatan = any('$angkatan')";
		}
		$stmt=$db->query("select kd_prodi,id_angkatan, id_stat_mhs, count(nim) as n_mahasiswa from acc.v_mahasiswa 
		where 1=1  $where
		group by kd_prodi, id_angkatan, id_stat_mhs
        	order by kd_prodi, id_angkatan, id_stat_mhs");
		$data=$stmt->fetchAll();
		return $data;
    	}

    	function getRepStatMasuk($angkatan,$prodi){
		// database
		$where='';
		$db=Zend_Registry::get('dbAdapter');
		if(!empty($prodi)){
			$prodi = $this->to_pg_array($prodi);
			$where .= " and kd_prodi = any('$prodi')";
		}
		if(!empty($angkatan)){
			$angkatan = $this->to_pg_array($angkatan);
			$where .= " and id_angkatan = any('$angkatan')";
		}
		$stmt=$db->query("select kd_prodi,id_angkatan, id_stat_masuk, count(nim) as n_mahasiswa from acc.v_mahasiswa 
		where 1=1  $where
		group by kd_prodi, id_angkatan, id_stat_masuk
        	order by kd_prodi, id_angkatan, id_stat_masuk");
		$data=$stmt->fetchAll();
		return $data;
    	}
    
    	function getRepKota($angkatan,$prodi){
        	// database
        	$where='';
        	$db=Zend_Registry::get('dbAdapter');
       		if(!empty($prodi)){
            		$prodi = $this->to_pg_array($prodi);
            		$where .= " and kd_prodi = any('$prodi')";
        	}
        	if(!empty($angkatan)){
            		$angkatan = $this->to_pg_array($angkatan);
            		$where .= " and id_angkatan = any('$angkatan')";
        	}
        	$stmt=$db->query("select a.kd_prodi, a.id_angkatan, b.id_kota, b.kota, b.propinsi, count(nim) as n_mahasiswa from acc.v_mahasiswa a
        		left join ref.v_wlayah b ON a.id_wil=b.id_wil
			where 1=1 $where
			group by a.kd_prodi, a.id_angkatan, b.id_kota, b.kota, b.propinsi
        		order by b.propinsi, b.kota, a.kd_prodi, a.id_angkatan");
        	$data=$stmt->fetchAll();
       		return $data;
    	}
    
    	function getRepProv($angkatan,$prodi){
        	// database
        	$where='';
        	$db=Zend_Registry::get('dbAdapter');
        	if(!empty($prodi)){
            		$prodi = $this->to_pg_array($prodi);
            		$where .= " and kd_prodi = any('$prodi')";
        	}
        	if(!empty($angkatan)){
            		$angkatan = $this->to_pg_array($angkatan);
            		$where .= " and id_angkatan = any('$angkatan')";
        	}
        	$stmt=$db->query("select a.kd_prodi, a.id_angkatan, b.id_propinsi, b.propinsi, count(nim) as n_mahasiswa from acc.v_mahasiswa a
        		left join ref.v_wlayah b ON a.id_wil=b.id_wil
			where 1=1 $where
			group by a.kd_prodi, a.id_angkatan, b.id_propinsi, b.propinsi
        		order by b.propinsi, a.kd_prodi, a.id_angkatan");
        		$data=$stmt->fetchAll();
       		return $data;
    	}
}