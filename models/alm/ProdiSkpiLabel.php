<?php
class ProdiSkpiLabel extends Zend_Db_Table
{
    protected $_name = 'alm.v_prodi_skpi_label';
    protected $_primary='id_prodi_skpi_label';
    
    function getProdiSkpiLabelByKd($kode_prodi){
        // database
        $db=Zend_Registry::get('dbAdapter');
        $stmt=$db->query("select * from alm.f_prodi_skpi_label_fby_kd('$kode_prodi')");
        $data=$stmt->fetchAll();
        return $data;
    }

    function getProdiSkpiLabelById($id_prodi_skpi_label){
        // database
        $db=Zend_Registry::get('dbAdapter');
        $stmt=$db->query("select * from alm.f_prodi_skpi_label_fby_id('$id_prodi_skpi_label')");
        $data=$stmt->fetchAll();
        return $data;
    }
    
    function setProdiSkpiLabel($kode_prodi,$capaian_kkni_id,$capaian_kkni_en,$capaian_asosiasi_id,$capaian_asosiasi_en,$capaian_pt_major_id,$capaian_pt_major_en,$capaian_pt_minor_id,$capaian_pt_minor_en,$capaian_pt_other_id,$capaian_pt_other_en){
        // database
        $db=Zend_Registry::get('dbAdapter');
        $query=$db->query("select * from alm.f_prodi_skpi_label_ins('$kode_prodi','$capaian_kkni_id','$capaian_kkni_en','$capaian_asosiasi_id','$capaian_asosiasi_en','$capaian_pt_major_id','$capaian_pt_major_en','$capaian_pt_minor_id','$capaian_pt_minor_en','$capaian_pt_other_id','$capaian_pt_other_en')");
        $isset=$query->fetchAll();
        foreach ($isset as $returnData) {
            $return=$returnData['f_prodi_skpi_label_ins'];
        }
        return $return;
    }
    
    function updProdiSkpiLabel($kode_prodi,$capaian_kkni_id,$capaian_kkni_en,$capaian_asosiasi_id,$capaian_asosiasi_en,$capaian_pt_major_id,$capaian_pt_major_en,$capaian_pt_minor_id,$capaian_pt_minor_en,$capaian_pt_other_id,$capaian_pt_other_en,$id_prodi_skpi_label){
        // database
        $db=Zend_Registry::get('dbAdapter');
        $query=$db->query("select * from alm.f_prodi_skpi_label_upd('$kode_prodi','$capaian_kkni_id','$capaian_kkni_en','$capaian_asosiasi_id','$capaian_asosiasi_en','$capaian_pt_major_id','$capaian_pt_major_en','$capaian_pt_minor_id','$capaian_pt_minor_en','$capaian_pt_other_id','$capaian_pt_other_en','$id_prodi_skpi_label')");
        $isset=$query->fetchAll();
        foreach ($isset as $returnData) {
            $return=$returnData['f_prodi_skpi_label_upd'];
        }
        return $return;
    }
    
    function delProdiSkpiLabel($kode_prodi){
        // database
        $db=Zend_Registry::get('dbAdapter');
        $query=$db->query("select * from alm.f_prodi_skpi_label_del('$kode_prodi')");
        $isset=$query->fetchAll();
        foreach ($isset as $returnData) {
            $return=$returnData['f_prodi_skpi_label_del'];
        }
        return $return;
    }
}