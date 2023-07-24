<?php
class ProdiCapaianKkni extends Zend_Db_Table
{
    protected $_name = 'alm.v_prodi_capaian_kkni';
    protected $_primary='id_prodi_capaian_kkni';
    
    function getProdiCapaianKkniBySkpiLabel($id_prodi_skpi_label){
        // database
        $db=Zend_Registry::get('dbAdapter');
        $stmt=$db->query("select * from alm.f_prodi_capaian_kkni_fby_skpilabel('$id_prodi_skpi_label')");
        $data=$stmt->fetchAll();
        return $data;
    }

    function getProdiCapaianKkniById($id_prodi_capaian_kkni){
        // database
        $db=Zend_Registry::get('dbAdapter');
        $stmt=$db->query("select * from alm.f_prodi_capaian_kkni_fby_id('$id_prodi_capaian_kkni')");
        $data=$stmt->fetchAll();
        return $data;
    }
    
    function setProdiCapaianKkni($id_prodi_skpi_label,$urutan,$is_numbered,$keterangan_id,$keterangan_en){
        // database
        $db=Zend_Registry::get('dbAdapter');
        $query=$db->query("select * from alm.f_prodi_capaian_kkni_ins('$id_prodi_skpi_label',$urutan,'$is_numbered','$keterangan_id','$keterangan_en')");
        $isset=$query->fetchAll();
        foreach ($isset as $returnData) {
            $return=$returnData['f_prodi_capaian_kkni_ins'];
        }
        return $return;
    }
    
    function updProdiCapaianKkni($id_prodi_skpi_label,$urutan,$is_numbered,$keterangan_id,$keterangan_en,$id_prodi_capaian_kkni){
        // database
        $db=Zend_Registry::get('dbAdapter');
        $query=$db->query("select * from alm.f_prodi_capaian_kkni_upd('$id_prodi_skpi_label',$urutan,'$is_numbered','$keterangan_id','$keterangan_en','$id_prodi_capaian_kkni')");
        $isset=$query->fetchAll();
        foreach ($isset as $returnData) {
            $return=$returnData['f_prodi_capaian_kkni_upd'];
        }
        return $return;
    }
    
    function delProdiCapaianKkni($id_prodi_capaian_kkni){
        // database
        $db=Zend_Registry::get('dbAdapter');
        $query=$db->query("select * from alm.f_prodi_capaian_kkni_del('$id_prodi_capaian_kkni')");
        $isset=$query->fetchAll();
        foreach ($isset as $returnData) {
            $return=$returnData['f_prodi_capaian_kkni_del'];
        }
        return $return;
    }
}