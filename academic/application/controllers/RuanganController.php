<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: Ruangan Controller -> Controller untuk modul halaman ruangan
*/
class RuanganController extends Zend_Controller_Action
{
	function init()
	{
		$this->initView();
		$this->view->baseUrl = $this->_request->getBaseUrl();
		Zend_Loader::loadClass('Profile');
		Zend_Loader::loadClass('User');
		Zend_Loader::loadClass('Menu');
		Zend_Loader::loadClass('Ruangan');
		Zend_Loader::loadClass('KatRuangan');
		Zend_Loader::loadClass('Zend_Session');
		Zend_Loader::loadClass('Zend_Layout');
		Zend_Loader::loadClass('Validation');
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
		// layout
		$this->_helper->layout()->setLayout('main');
		// treeview
		$this->view->active_tree="03";
		$this->view->active_menu="ruangan/index";
	}
	
	function indexAction()
	{
		$user = new Menu();
		$menu = "ruangan/index";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// Title Browser
			$this->view->title = "Daftar Ruangan";
			// navigation
			$this->_helper->navbar(0,0,'ruangan/new',0,0);
			// destroy session param
			Zend_Session::namespaceUnset('param_room');
			// kategori ruangan
			$katRuangan = new KatRuangan();
			$this->view->listKatRuangan=$katRuangan->fetchAll();
		}
	}

	function listAction(){
		$user = new Menu();
		$menu = "ruangan/index";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// show data
			$param = new Zend_Session_Namespace('param_room');
			$kat_room = $param->kat_room;
			// kategori ruangan
			$katRuangan = new KatRuangan();
			$listKatRuangan=$katRuangan->fetchAll();
			// if have param get
			$kat_get=$this->_request->get('kat');
			if($kat_get){
				$kat_room = $kat_get;
				$katRoom="";
				foreach ($listKatRuangan as $dataKat) {
					if($kat_room==$dataKat['id_kat_ruangan']){
						$katRoom=$dataKat['kat_ruangan'].", ".$katRoom;
					}
				}
				if($katRoom){
					$this->view->katRoom=$katRoom;
				}else{
					$this->view->eksis="f";
				}
			}else{
				if(!$kat_room){
					$this->view->katRoom="SEMUA";
				}else{
					$v_katroom="";
					foreach ($listKatRuangan as $dataKat) {
						foreach ($kat_room as $dt) {
							if($dt==$dataKat['id_kat_ruangan']){
								$v_katroom=$dataKat['kat_ruangan'].", ".$v_katroom;
							}
						}
					}
					$this->view->katRoom=$v_katroom;
				}
			}
			// Title Browser
			$this->view->title = "Daftar Ruangan";
			// navigation
			$this->_helper->navbar('ruangan',0,'ruangan/new',0,0);
			// get data 
			$ruangan = new Ruangan();
			$getRuangan = $ruangan->getRuanganByKat($kat_room);
			$this->view->listRuangan = $getRuangan;
		}
	}

	function newAction(){
		$user = new Menu();
		$menu = "ruangan/new";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// Title Browser
			$this->view->title = "Input Data Ruangan";
			// navigation
			$this->_helper->navbar(0,'ruangan',0,0,0);
			// kategori ruangan
			$katRuangan = new KatRuangan();
			$this->view->listKatRuangan=$katRuangan->fetchAll();
		}
	}

	function editAction(){
		$user = new Menu();
		$menu = "ruangan/edit";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// get param
			$kd=$this->_request->get('kd');
			// get data ruangan
			$ruangan = new Ruangan();
			$getRuangan=$ruangan->getRuanganByKd($kd);
			if($getRuangan){
				foreach ($getRuangan as $dtRuangan) {
					$this->view->kd = $dtRuangan['kd_ruangan'];
					$this->view->nm = $dtRuangan['nm_ruangan'];
					$nm_ruangan=$dtRuangan['nm_ruangan'];
					$this->view->kpsts = $dtRuangan['kapasitas'];
					$this->view->kpsts_u = $dtRuangan['kapasitas_ujian'];
					$this->view->id_kat = $dtRuangan['id_kat_ruangan'];
				}
				// Title Browser
				$this->view->title = "Edit Data Ruangan ".$nm_ruangan;
			}else{
				$this->view->eksis="f";
				// Title Browser
				$this->view->title = "Edit Data Ruangan";
			}
			
			// navigation
			$this->_helper->navbar('ruangan/list',0,0,0,0);
			// kategori ruangan
			$katRuangan = new KatRuangan();
			$this->view->listKatRuangan=$katRuangan->fetchAll();
		}
	}
}