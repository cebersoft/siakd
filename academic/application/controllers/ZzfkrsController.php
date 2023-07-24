<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: Feeder KRS Controller -> Controller untuk modul halaman KRS-Feeder
*/
class ZzfkrsController extends Zend_Controller_Action
{
	function init()
	{
		$this->initView();
		$this->view->baseUrl = $this->_request->getBaseUrl();
		Zend_Loader::loadClass('Zend_Session');
		Zend_Loader::loadClass('Zend_Soap_Client');
		Zend_Loader::loadClass('Prodi');
		Zend_Loader::loadClass('Angkatan');
		Zend_Loader::loadClass('Periode');
		Zend_Loader::loadClass('FeederMhs');
		Zend_Loader::loadClass('FeederMk');
		Zend_Loader::loadClass('FeederKls');
		Zend_Loader::loadClass('FeederKrs');
		Zend_Loader::loadClass('FeederProdi');
		Zend_Loader::loadClass('ConfigKurikulum');
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
		$this->view->active_menu="zzfkls/index";
	}
	
	function indexAction()
	{
		$user = new Menu();
		$menu = "zzfkrs/index";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			$ses_feeder = new Zend_Session_Namespace('ses_feeder');
			if(!$ses_feeder->token){
				$this->_redirect('zzfconfig');
			}else{
				// Title Browser
				$this->view->title = "Sinkronisasi Data KRS - Feeder";
				// layout
				$this->_helper->layout()->setLayout('main');
				// control navigasi back
				$this->view->disabled_back="disabled='disabled'";
				// destroy session param
				Zend_Session::namespaceUnset('param_fkrs');
				// get data prodi
				$prodi = new Prodi();
				$listProdi = $prodi->fetchAll();
				$this->view->listProdi=$listProdi;
				// get data angkatan
				$akt = new Angkatan();
				$this->view->listAkt=$akt->fetchAll();
				// get data periode
				$periode = new Periode();
				$listPeriode = $periode->fetchAll();
				$this->view->listPeriode=$listPeriode;
				// navigation
				$this->_helper->navbar(0,0,0,0,0);	
			}
		}
	}
	
	function listAction() 
	{
		$user = new Menu();
		$menu = "zzfkrs/index";
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
				// Title Browser
				$this->view->title = "Sinkronisasi Data KRS";
				// navigation
				$this->_helper->navbar('zzfkls/list',0,0,0,0);
				// show data
				$nm_kls=$this->_request->get('nm');
				$kd_mk=$this->_request->get('mk');
				$id_smt=$this->_request->get('sm');
				$prd=$this->_request->get('prd');
				$opt = $this->_request->get('o');
				$kls = $this->_request->get('kls');
				// option view
				$this->view->nm_kls=$nm_kls;
				$this->view->kd_mk=$kd_mk;
				$this->view->smt=$id_smt;
				$this->view->prd=$prd;
				$this->view->opt=$opt;
				$this->view->kls=$kls;
				// get data KRS
				$feederProdi = new FeederProdi();
				$getProdi = $feederProdi->getProdi($token, "kode_program_studi='$prd'", "", 1, 0, $url);
				$resProdi=json_decode($getProdi,true);
				if (!$resProdi['data']){
					$this->view->eksis="f";
				}else{
					// KRS
					$feederKrs = new FeederKrs();
					// feeder side
					foreach ($resProdi['data'] as $dataProdi) {
						$id_prodi=$dataProdi['id_prodi'];
						$nm_prodi = $dataProdi['nama_program_studi'];
						$kd_prodi = $dataProdi['kode_program_studi'];
					}
					$feederKls=new FeederKls();
					$getKlsFeeder = $feederKls->getDetailKelasKuliah($token, "id_kelas_kuliah='$kls'","", 1, 0, $url);
					$getKlsFeeder = json_decode($getKlsFeeder,true);
					if(!$getKlsFeeder['data']){
						$this->view->eksis="f";
					}else{
						// get data KRS
						$getKrsFeeder = $feederKrs->getDetailNilaiKelasKuliah($token, "id_kelas_kuliah='$kls'", "", 1000, 0, $url);
						$getKrsFeeder = json_decode($getKrsFeeder,true);
						$this->view->listKrsFeeder=$getKrsFeeder;
					}
					// sia side
					$getKrsSIA = $feederKrs->getKrsByKlsMkProdiSmt($nm_kls, $kd_mk, $kd_prodi, $id_smt);
					$this->view->listKrsSIA=$getKrsSIA;
					$this->view->id_kls=$kls;
					$this->view->nm_prd=$nm_prodi;
					$this->view->nm_mk=$kd_mk;
					$this->view->title = "Sinkronisasi Data KRS";
				}
			}
		}
	}
}