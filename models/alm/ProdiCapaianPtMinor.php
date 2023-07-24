<?php
class ProdiCapaianPtMinor extends Zend_Db_Table
{
    protected $_name = 'alm.v_prodi_capaian_pt_minor';
    protected $_primary='id_prodi_capaian_pt_minor';
    
    function getProdiCapaianPtMinorBySkpiLabel($id_prodi_skpi_label){
        // database
        $db=Zend_Registry::get('dbAdapter');
        $stmt=$db->query("select * from alm.f_prodi_capaian_pt_minor_fby_skpilabel('$id_prodi_skpi_label')");
        $data=$stmt->fetchAll();
        return $data;
    }

    function getProdiCapaianPtMinorById($id_prodi_capaian_pt_minor){
        // database
        $db=Zend_Registry::get('dbAdapter');
        $stmt=$db->query("select * from alm.f_prodi_capaian_pt_minor_fby_id('$id_prodi_capaian_pt_minor')");
        $data=$stmt->fetchAll();
        return $data;
    }
    
    function setProdiCapaianPtMinor($id_prodi_skpi_label,$urutan,$is_numbered,$keterangan_id,$keterangan_en){
        // database
        $db=Zend_Registry::get('dbAdapter');
        $query=$db->query("select * from alm.f_prodi_capaian_pt_minor_ins('$id_prodi_skpi_label',$urutan,'$is_numbered','$keterangan_id','$keterangan_en')");
        $isset=$query->fetchAll();
        foreach ($isset as $returnData) {
            $return=$returnData['f_prodi_capaian_pt_minor_ins'];
        }
        return $return;
    }
    
    function updProdiCapaianPtMinor($id_prodi_skpi_label,$urutan,$is_numbered,$keterangan_id,$keterangan_en,$id_prodi_capaian_pt_minor){
        // database
        $db=Zend_Registry::get('dbAdapter');
        $query=$db->query("select * from alm.f_prodi_capaian_pt_minor_upd('$id_prodi_skpi_label',$urutan,'$is_numbered','$keterangan_id','$keterangan_en','$id_prodi_capaian_pt_minor')");
        $isset=$query->fetchAll();
        foreach ($isset as $returnData) {
            $return=$returnData['f_prodi_capaian_pt_minor_upd'];
        }
        return $return;
    }
    
    function delProdiCapaianPtMinor($id_prodi_capaian_pt_minor){
        // database
        $db=Zend_Registry::get('dbAdapter');
        $query=$db->query("select * from alm.f_prodi_capaian_pt_minor_del('$id_prodi_capaian_pt_minor')");
        $isset=$query->fetchAll();
        foreach ($isset as $returnData) {
            $return=$returnData['f_prodi_capaian_pt_minor_del'];
        }
        return $return;
    }
}