<?php
class ProdiCapaianPtOther extends Zend_Db_Table
{
    protected $_name = 'alm.v_prodi_capaian_pt_other';
    protected $_primary='id_prodi_capaian_pt_other';
    
    function getProdiCapaianPtOtherBySkpiLabel($id_prodi_skpi_label){
        // database
        $db=Zend_Registry::get('dbAdapter');
        $stmt=$db->query("select * from alm.f_prodi_capaian_pt_other_fby_skpilabel('$id_prodi_skpi_label')");
        $data=$stmt->fetchAll();
        return $data;
    }

    function getProdiCapaianPtOtherById($id_prodi_capaian_pt_other){
        // database
        $db=Zend_Registry::get('dbAdapter');
        $stmt=$db->query("select * from alm.f_prodi_capaian_pt_other_fby_id('$id_prodi_capaian_pt_other')");
        $data=$stmt->fetchAll();
        return $data;
    }
    
    function setProdiCapaianPtOther($id_prodi_skpi_label,$urutan,$is_numbered,$keterangan_id,$keterangan_en){
        // database
        $db=Zend_Registry::get('dbAdapter');
        $query=$db->query("select * from alm.f_prodi_capaian_pt_other_ins('$id_prodi_skpi_label',$urutan,'$is_numbered','$keterangan_id','$keterangan_en')");
        $isset=$query->fetchAll();
        foreach ($isset as $returnData) {
            $return=$returnData['f_prodi_capaian_pt_other_ins'];
        }
        return $return;
    }
    
    function updProdiCapaianPtOther($id_prodi_skpi_label,$urutan,$is_numbered,$keterangan_id,$keterangan_en,$id_prodi_capaian_pt_other){
        // database
        $db=Zend_Registry::get('dbAdapter');
        $query=$db->query("select * from alm.f_prodi_capaian_pt_other_upd('$id_prodi_skpi_label',$urutan,'$is_numbered','$keterangan_id','$keterangan_en','$id_prodi_capaian_pt_other')");
        $isset=$query->fetchAll();
        foreach ($isset as $returnData) {
            $return=$returnData['f_prodi_capaian_pt_other_upd'];
        }
        return $return;
    }
    
    function delProdiCapaianPtOther($id_prodi_capaian_pt_other){
        // database
        $db=Zend_Registry::get('dbAdapter');
        $query=$db->query("select * from alm.f_prodi_capaian_pt_other_del('$id_prodi_capaian_pt_other')");
        $isset=$query->fetchAll();
        foreach ($isset as $returnData) {
            $return=$returnData['f_prodi_capaian_pt_other_del'];
        }
        return $return;
    }
}