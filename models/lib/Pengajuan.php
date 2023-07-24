<?php
/*
	Programmer	: Tiar Aristian
	Release		: Agustus 2012
	Module		: Pengajuan - Modeling untuk pengajuan
*/
class Pengajuan
{
	
	function getPengajuanByTanggal($tglAwal,$tglAkhir){
		// database
		$db=Zend_Registry::get('dbAdapter2');
		if (($tglAwal=='')and($tglAkhir=='')){
			$stmt=$db->query("select * from stfi_flib_pengajuan_buku_filter_by_tanggal_range(null,null)");
		}else{
			$stmt=$db->query("select * from stfi_flib_pengajuan_buku_filter_by_tanggal_range('$tglAwal','$tglAkhir')");
		}
		$data=$stmt->fetchAll();
		return $data;		
	}

	function getPengajuanByDosen($kd_dosen){
		// database
		$db=Zend_Registry::get('dbAdapter2');
		$stmt=$db->query("select * from stfi_flib_pengajuan_buku_filter_by_dosen('$kd_dosen')");
		$data=$stmt->fetchAll();
		return $data;		
	}

	
	function setPengajuan($tgl,$dsn,$kk,$jdl,$sub,$pgr,$pnb,$thn,$ed,$kat,$ex){
		// database
		$db=Zend_Registry::get('dbAdapter2');
		$stmt=$db->query("select * from stfi_flib_pengajuan_buku_insert('$tgl','$dsn','$kk','$jdl','$sub','$pgr','$pnb','$thn','$ed','$kat',$ex)");
		$set = $stmt->fetchAll();
		foreach ($set as $dataReturn){
			$return=$dataReturn['stfi_flib_pengajuan_buku_insert'];
		}
		return $return;		
	}
	
	function delPengajuan($id){
		// database
		$db=Zend_Registry::get('dbAdapter2');
		$stmt=$db->query("select * from stfi_flib_pengajuan_buku_delete('$id')");
		$set = $stmt->fetchAll();
		foreach ($set as $dataReturn){
			$return=$dataReturn['stfi_flib_pengajuan_buku_delete'];
		}
		return $return;
	}
}