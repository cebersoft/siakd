<?php
class ProdiInfo0 extends Zend_Db_Table
{
    protected $_name = 'acc.v_prodi_info0';
    protected $_primary='kode_prodi';
    
    function getProdiInfo0ByKd($kode_prodi){
        // database
        $db=Zend_Registry::get('dbAdapter');
        $stmt=$db->query("select * from acc.f_prodi_info0_fby_kd('$kode_prodi')");
        $data=$stmt->fetchAll();
        return $data;
    }
    
    function setProdiInfo0($kode_prodi,$gelar_id,$gelar_en,$jenis_pend_id,$jenis_pend_en,$req_pend_id,$req_pend_en,$bahasa_id,$bahasa_en,$lanjut_id,$lanjut_en){
        // database
        $db=Zend_Registry::get('dbAdapter');
        $query=$db->query("select * from acc.f_prodi_info0_ins('$kode_prodi','$gelar_id','$gelar_en','$jenis_pend_id','$jenis_pend_en','$req_pend_id','$req_pend_en','$bahasa_id','$bahasa_en','$lanjut_id','$lanjut_en')");
        $isset=$query->fetchAll();
        foreach ($isset as $returnData) {
            $return=$returnData['f_prodi_info0_ins'];
        }
        return $return;
    }
    
    function updProdiInfo0($kode_prodi,$gelar_id,$gelar_en,$jenis_pend_id,$jenis_pend_en,$req_pend_id,$req_pend_en,$bahasa_id,$bahasa_en,$lanjut_id,$lanjut_en,$_prodi_info0){
        // database
        $db=Zend_Registry::get('dbAdapter');
        $query=$db->query("select * from acc.f_prodi_info0_upd('$kode_prodi','$gelar_id','$gelar_en','$jenis_pend_id','$jenis_pend_en','$req_pend_id','$req_pend_en','$bahasa_id','$bahasa_en','$lanjut_id','$lanjut_en','$_prodi_info0')");
        $isset=$query->fetchAll();
        foreach ($isset as $returnData) {
            $return=$returnData['f_prodi_info0_upd'];
        }
        return $return;
    }
    
    function delProdiInfo0($kode_prodi){
        // database
        $db=Zend_Registry::get('dbAdapter');
        $query=$db->query("select * from acc.f_prodi_info0_del('$kode_prodi')");
        $isset=$query->fetchAll();
        foreach ($isset as $returnData) {
            $return=$returnData['f_prodi_info0_del'];
        }
        return $return;
    }
}