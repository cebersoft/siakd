<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: Mata Kuliah - Modeling untuk master Mata Kuliah
*/
class AnnouncementMhs extends Zend_Db_Table
{
    protected $_name = 'acv.v_announcement_mhs';
	protected $_primary='id_announcement_mhs';

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
    function queryAnnouncementMhs($id_announcement_kategori,$tgl1,$tgl2){
        // database
        $db=Zend_Registry::get('dbAdapter');
        $id_announcement_kategori=$this->to_pg_array($id_announcement_kategori);
        if(($tgl1=='')or($tgl2=='')){
            $stmt=$db->query("select * from  acv.f_announcement_mhs_query('$id_announcement_kategori','01-01-01','01-01-01')");
        }else{
            $stmt=$db->query("select * from  acv.f_announcement_mhs_query('$id_announcement_kategori','$tgl1','$tgl2')");
        }
        $data=$stmt->fetchAll();
        return $data;
    }

	function getAnnouncementMhsFilterRunning($id_angkatan,$kd_prodi,$kd_dosen){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from acv.f_announcement_mhs_all_filter('$id_angkatan','$kd_prodi','$kd_dosen') where date_expired > now()");
		$data=$stmt->fetchAll();
		return $data;
	}

	function getAnnouncementMhsById($id_announcement_mhs){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from acv.f_announcement_mhs_fby_id('$id_announcement_mhs')");
		$data=$stmt->fetchAll();
		return $data;
	}

	function setAnnouncementMhs($judul,$konten,$id_announcement_kategori,$status,$date_expired,$username_created){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from acv.f_announcement_mhs_ins('$judul','$konten','$id_announcement_kategori',$status,'$date_expired','$username_created')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_announcement_mhs_ins'];
		}
		return $return;
	}

	function delAnnouncementMhs($id_announcement_mhs){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from acv.f_announcement_mhs_del('$id_announcement_mhs')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_announcement_mhs_del'];
		}
		return $return;
	}

	function updAnnouncementMhs($judul,$konten,$id_announcement_kategori,$status,$date_expired,$username_edited,$id_announcement_mhs){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from acv.f_announcement_mhs_upd('$judul','$konten','$id_announcement_kategori',$status,'$date_expired','$username_edited','$id_announcement_mhs')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_announcement_mhs_upd'];
		}
		return $return;
	}
}