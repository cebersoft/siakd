<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: Feeder Kelas Controller -> Controller untuk modul halaman kelas-feeder
*/
class ZZfklsController extends Zend_Controller_Action
{
	function init()
	{
		$this->initView();
		$this->view->baseUrl = $this->_request->getBaseUrl();
		Zend_Loader::loadClass('Zend_Session');
		Zend_Loader::loadClass('Zend_Soap_Client');
		Zend_Loader::loadClass('Prodi');
		Zend_Loader::loadClass('Periode');
		Zend_Loader::loadClass('FeederMk');
		Zend_Loader::loadClass('FeederKls');
		Zend_Loader::loadClass('FeederKrs');
		Zend_Loader::loadClass('FeederProdi');
		Zend_Loader::loadClass('ConfigKurikulum');
		$auth = Zend_Auth::getInstance();
		$ses_ac = new Zend_Session_Namespace('ses_ac');
		$ses_menu = new Zend_Session_Namespace('ses_menu');
		if (($auth->hasIdentity())and($ses_ac->uname)) {
			$this->view->namauser =Zend_Auth::getInstance()->getIdentity()->nama;
			$this->view->username=Zend_Auth::getInstance()->getIdentity()->username;
			$this->view->kd_pt=$ses_ac->kd_pt;
			$this->view->nm_pt=$ses_ac->nm_pt;
			$this->view->menu=$ses_menu->menu;
		}else{
			$this->_redirect('/');
		}
		$ses_feeder = new Zend_Session_Namespace('ses_feeder');
		if(!$ses_feeder->token){
			$this->_redirect('zzfconfig');
		}
		// treeview
		$this->view->active_tree="13";
		$this->view->active_menu="zzfkls/index";
	}
	
	function indexAction()
	{
		// Title Browser
		$this->view->title = "Sinkronisasi Data Kelas - Feeder";
		// layout
		$this->_helper->layout()->setLayout('main');
		// control navigasi back
		$this->view->disabled_back="disabled='disabled'";
		// destroy session param
		Zend_Session::namespaceUnset('param_fkls');
		// get data prodi
		$prodi = new Prodi();
		$listProdi = $prodi->fetchAll();
		$this->view->listProdi=$listProdi;
		// get data periode
		$per = new Periode();
		$this->view->listPeriode=$per->fetchAll();
		// navigation
		$this->_helper->navbar(0,0,0,0,0);
	}
	
	function listAction(){
		// token
		$ses_feeder = new Zend_Session_Namespace('ses_feeder');
		$token=$ses_feeder->token;
		$id_sp=$ses_feeder->id_sp;
		$url=$ses_feeder->url;
		// layout
		$this->_helper->layout()->setLayout('second');
		// navigation
		$this->_helper->navbar('zzfkls',0,0,0,0);
		// show data
		$param = new Zend_Session_Namespace('param_fkls');
		$per = $param->per;
		$prd = $param->prd;
		$opt=$param->opt;
		// option view
		$this->view->opt=$opt;
		// get data prodi
		$feederProdi = new FeederProdi();
		$getProdi = $feederProdi->getProdi($token, "kode_program_studi='$prd'", "", 1, 0, $url);
		$resProdi=json_decode($getProdi,true);
		if (!$resProdi['data']){
			$this->view->eksis="f";
			// Title Browser
			$this->view->title = "Sinkronisasi Data Kelas";
		}else{
			foreach ($resProdi['data'] as $dataProdi) {
				$id_prodi=$dataProdi['id_prodi'];
				$nm_prodi = $dataProdi['nama_program_studi'];
				$kd_prodi=$dataProdi['kode_program_studi'];
			}
			// Title Browser
			$this->view->title = "Sinkronisasi Data Kelas Periode ".$per." Program Studi ".$nm_prodi;
			$this->view->prd=$kd_prodi;
			// get data kelas from SIA
			$klsFeeder = new FeederKls();
			$this->view->listkelasSIA  = $klsFeeder->getPaketKelasByPeriodeProdi($per, $kd_prodi);
			// cacah periode
			$arrPeriode = explode('/', $per);
			$smt=explode('-', $arrPeriode[0]);
			if($arrPeriode[1]=='GASAL'){
				$s='1';
			}elseif ($arrPeriode[1]=='GENAP'){
				$s='2';
			}else {
				$s='3';
			}
			$id_smt=$smt[0].$s;
			$this->view->smt=$id_smt;
			$this->view->id_prodi=$id_prodi;
			$getKlsFeeder=$klsFeeder->getListKelasKuliahNilai($token, "id_sms='$id_prodi' and id_smt='$id_smt'", "nama_mata_kuliah, id_kelas_kuliah", 500, 0, $url);
			$resKlsFeeder=json_decode($getKlsFeeder,true);
			// kurikulum feeder
			$id_kur="";
			$ckur = new ConfigKurikulum();
			$getKurikulum=$ckur->getDataKurikulumByProdiPeriode($prd, $per);
			if($getKurikulum){
				foreach ($getKurikulum as $dtKur) {
					$id_kur=$dtKur['id_kurikulum_sp'];
				}
			}
			$this->view->id_kur=$id_kur;
			$dataKlsFeeder=array();
			$i=0;
			foreach ($resKlsFeeder['data'] as $dtKlsFeeder){
				$dataKlsFeeder[$i]['id_matkul']=$dtKlsFeeder['id_matkul'];
				$dataKlsFeeder[$i]['kode_mata_kuliah']=$dtKlsFeeder['kode_mata_kuliah'];
				$dataKlsFeeder[$i]['nama_mata_kuliah']=$dtKlsFeeder['nama_mata_kuliah'];
				$dataKlsFeeder[$i]['id_kelas_kuliah']=$dtKlsFeeder['id_kelas_kuliah'];
				$dataKlsFeeder[$i]['nama_kelas_kuliah']=$dtKlsFeeder['nama_kelas_kuliah'];
				$dataKlsFeeder[$i]['sks']=$dtKlsFeeder['sks_mata_kuliah'];
				$dataKlsFeeder[$i]['jumlah_mahasiswa']=$dtKlsFeeder['jumlah_mahasiswa_krs'];
				$dataKlsFeeder[$i]['jumlah_mahasiswa_nilai']=$dtKlsFeeder['jumlah_mahasiswa_dapat_nilai'];
				$i++;
			}
			//$dataKlsFeeder=array_values(array_unique($dataKlsFeeder)); // <--- di php 5.3 ke atas tidak perlu
			$dataKlsFeeder = array_values(array_map("unserialize", array_unique(array_map("serialize", $dataKlsFeeder))));
			$this->view->listKelasFeeder=$dataKlsFeeder;
		}
	}
}