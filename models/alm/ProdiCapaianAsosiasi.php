<?php
class ProdiCapaianAsosiasi extends Zend_Db_Table
{
    protected $_name = 'alm.v_prodi_capaian_asosiasi';
    protected $_primary='id_prodi_capaian_asosiasi';
    
    function getProdiCapaianAsosiasiBySkpiLabel($id_prodi_skpi_label){
        // database
        $db=Zend_Registry::get('dbAdapter');
        $stmt=$db->query("select * from alm.f_prodi_capaian_asosiasi_fby_skpilabel('$id_prodi_skpi_label')");
        $data=$stmt->fetchAll();
        return $data;
    }

    function getProdiCapaianAsosiasiById($id_prodi_capaian_asosiasi){
        // database
        $db=Zend_Registry::get('dbAdapter');
        $stmt=$db->query("select * from alm.f_prodi_capaian_asosiasi_fby_id('$id_prodi_capaian_asosiasi')");
        $data=$stmt->fetchAll();
        return $data;
    }
    
    function setProdiCapaianAsosiasi($id_prodi_skpi_label,$urutan,$is_numbered,$keterangan_id,$keterangan_en){
        // database
        $db=Zend_Registry::get('dbAdapter');
        $query=$db->query("select * from alm.f_prodi_capaian_asosiasi_ins('$id_prodi_skpi_label',$urutan,'$is_numbered','$keterangan_id','$keterangan_en')");
        $isset=$query->fetchAll();
        foreach ($isset as $returnData) {
            $return=$returnData['f_prodi_capaian_asosiasi_ins'];
        }
        return $return;
    }
    
    function updProdiCapaianAsosiasi($id_prodi_skpi_label,$urutan,$is_numbered,$keterangan_id,$keterangan_en,$id_prodi_capaian_asosiasi){
        // database
        $db=Zend_Registry::get('dbAdapter');
        $query=$db->query("select * from alm.f_prodi_capaian_asosiasi_upd('$id_prodi_skpi_label',$urutan,'$is_numbered','$keterangan_id','$keterangan_en','$id_prodi_capaian_asosiasi')");
        $isset=$query->fetchAll();
        foreach ($isset as $returnData) {
            $return=$returnData['f_prodi_capaian_asosiasi_upd'];
        }
        return $return;
    }
    
    function delProdiCapaianAsosiasi($id_prodi_capaian_asosiasi){
        // database
        $db=Zend_Registry::get('dbAdapter');
        $query=$db->query("select * from alm.f_prodi_capaian_asosiasi_del('$id_prodi_capaian_asosiasi')");
        $isset=$query->fetchAll();
        foreach ($isset as $returnData) {
            $return=$returnData['f_prodi_capaian_asosiasi_del'];
        }
        return $return;
    }
}