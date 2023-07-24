<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: Feeder alumni Controller -> Controller untuk modul halaman alumni-feeder
*/
class ZzfalmController extends Zend_Controller_Action
{
	function init()
	{
		$this->initView();
		$this->view->baseUrl = $this->_request->getBaseUrl();
		Zend_Loader::loadClass('Zend_Session');
		Zend_Loader::loadClass('Zend_Soap_Client');
		Zend_Loader::loadClass('Mahasiswa');
		Zend_Loader::loadClass('Prodi');
		Zend_Loader::loadClass('Angkatan');
		Zend_Loader::loadClass('FeederMhsOut');
		Zend_Loader::loadClass('FeederMhs');
		Zend_Loader::loadClass('FeederProdi');
		Zend_Loader::loadClass('Menu');
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
		$this->view->active_menu="zzfalm/index";
	}
	
	function indexAction()
	{
		$user = new Menu();
		$menu = "zzfalm/index";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			$ses_feeder = new Zend_Session_Namespace('ses_feeder');
			if(!$ses_feeder->token){
				$this->_redirect('zzfconfig');
			}else{
				// Title Browser
				$this->view->title = "Sinkronisasi Data Mahasiswa Keluar - Feeder";
				// layout
				$this->_helper->layout()->setLayout('main');
				// control navigasi back
				$this->view->disabled_back="disabled='disabled'";
				// destroy session param
				Zend_Session::namespaceUnset('param_falm');
				// get data prodi
				$prodi = new Prodi();
				$listProdi = $prodi->fetchAll();
				$this->view->listProdi=$listProdi;
				// get data angkatan
				$akt = new Angkatan();
				$this->view->listAkt=$akt->fetchAll();
				// navigation
				$this->_helper->navbar(0,0,0,0,0);	
			}
		}
	}

	function listAction(){
		$user = new Menu();
		$menu = "zzfalm/index";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			$ses_feeder = new Zend_Session_Namespace('ses_feeder');
			if(!$ses_feeder->token){
				$this->_redirect('zzfconfig');
			}else{
				// token
				$token=$ses_feeder->token;
				$id_sp=$ses_feeder->id_sp;
				$url=$ses_feeder->url;
				// layout
				$this->_helper->layout()->setLayout('second');
				// navigation
				$this->_helper->navbar('zzfalm',0,0,0,0);
				// show data
				$param = new Zend_Session_Namespace('param_falm');
				$akt = $param->akt;
				$prd = $param->prd;
				$opt=$param->opt;
				// option view
				$this->view->opt=$opt;
				// get data prodi
				$feederProdi = new FeederProdi();
				$getProdi = $feederProdi->getProdi($token, "kode_program_studi='$prd'", "", 1, 0, $url);
				$resProdi = json_decode($getProdi,true);
				if (!$resProdi['data']){
					$this->view->eksis="f";
					// Title Browser
					$this->view->title = "Sinkronisasi Data Mahasiswa Keluar";
				}else{
					$id_prodi="";
					$nm_prodi="";
					$kd_prodi="";
					foreach ($resProdi['data'] as $dataProdi) {
						$id_prodi=$dataProdi['id_prodi'];
						$nm_prodi = $dataProdi['nama_program_studi'];
						$kd_prodi=$dataProdi['kode_program_studi'];
					}
					// Title Browser
					$this->view->title = "Sinkronisasi Data Mahasiswa Keluar Angkatan ".$akt." Program Studi ".$nm_prodi;
					$this->view->prd=$kd_prodi;
					// get data mahasiswa
					$mhsFeederOut = new FeederMhsOut();
					$mhsFeeder = new FeederMhs();
					$this->view->listMhsSIA  = $mhsFeederOut->getMahasiswaOutByAngkatanProdi($akt, $prd);
					$getDtMhsOutFeeder = $mhsFeederOut->getListMahasiswaLulusDO($token, "id_prodi='$id_prodi' and angkatan='$akt'", "", 500, 0, $url);
					$resDtMhsOutFeeder = json_decode($getDtMhsOutFeeder,true);
					$this->view->listMhsFeeder=$resDtMhsOutFeeder['data'];
				}
			}
		}
	}
}