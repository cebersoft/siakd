<?php
class ProdiCapaianPtMajor extends Zend_Db_Table
{
    protected $_name = 'alm.v_prodi_capaian_pt_major';
    protected $_primary='id_prodi_capaian_pt_major';
    
    function getProdiCapaianPtMajorBySkpiLabel($id_prodi_skpi_label){
        // database
        $db=Zend_Registry::get('dbAdapter');
        $stmt=$db->query("select * from alm.f_prodi_capaian_pt_major_fby_skpilabel('$id_prodi_skpi_label')");
        $data=$stmt->fetchAll();
        return $data;
    }

    function getProdiCapaianPtMajorById($id_prodi_capaian_pt_major){
        // database
        $db=Zend_Registry::get('dbAdapter');
        $stmt=$db->query("select * from alm.f_prodi_capaian_pt_major_fby_id('$id_prodi_capaian_pt_major')");
        $data=$stmt->fetchAll();
        return $data;
    }
    
    function setProdiCapaianPtMajor($id_prodi_skpi_label,$urutan,$is_numbered,$keterangan_id,$keterangan_en){
        // database
        $db=Zend_Registry::get('dbAdapter');
        $query=$db->query("select * from alm.f_prodi_capaian_pt_major_ins('$id_prodi_skpi_label',$urutan,'$is_numbered','$keterangan_id','$keterangan_en')");
        $isset=$query->fetchAll();
        foreach ($isset as $returnData) {
            $return=$returnData['f_prodi_capaian_pt_major_ins'];
        }
        return $return;
    }
    
    function updProdiCapaianPtMajor($id_prodi_skpi_label,$urutan,$is_numbered,$keterangan_id,$keterangan_en,$id_prodi_capaian_pt_major){
        // database
        $db=Zend_Registry::get('dbAdapter');
        $query=$db->query("select * from alm.f_prodi_capaian_pt_major_upd('$id_prodi_skpi_label',$urutan,'$is_numbered','$keterangan_id','$keterangan_en','$id_prodi_capaian_pt_major')");
        $isset=$query->fetchAll();
        foreach ($isset as $returnData) {
            $return=$returnData['f_prodi_capaian_pt_major_upd'];
        }
        return $return;
    }
    
    function delProdiCapaianPtMajor($id_prodi_capaian_pt_major){
        // database
        $db=Zend_Registry::get('dbAdapter');
        $query=$db->query("select * from alm.f_prodi_capaian_pt_major_del('$id_prodi_capaian_pt_major')");
        $isset=$query->fetchAll();
        foreach ($isset as $returnData) {
            $return=$returnData['f_prodi_capaian_pt_major_del'];
        }
        return $return;
    }
}