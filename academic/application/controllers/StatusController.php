<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: Status Controller -> Controller untuk modul halaman status2 mahasiswa
*/
class StatusController extends Zend_Controller_Action
{
	function init()
	{
		$this->initView();
		$this->view->baseUrl = $this->_request->getBaseUrl();
		Zend_Loader::loadClass('Profile');
		Zend_Loader::loadClass('User');
		Zend_Loader::loadClass('Menu');
		Zend_Loader::loadClass('StatMasuk');
		Zend_Loader::loadClass('StatMhs');
		Zend_Loader::loadClass('StatReg');
		Zend_Loader::loadClass('JnsMasuk');
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
		$this->view->active_tree="00";
		$this->view->active_menu="status/index";
	}
	
	function indexAction()
	{
		$user = new Menu();
		$menu = "status/index";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// Title Browser
			$this->view->title = "Data Status Mahasiswa";
			// navigation
			$this->_helper->navbar(0,0,0,0,0);
			$statMsk = new StatMasuk();
			// tab
			$tab=$this->_request->get('tab');
			$this->view->tab_1="active";
			if($tab=='3'){
				$this->view->tab_3="active";
				$this->view->tab_1="";
			}
			// data
			$this->view->listStatMsk=$statMsk->fetchAll();
			$statMhs = new StatMhs();
			$this->view->listStatMhs=$statMhs->fetchAll();
			$statReg=new StatReg();
			$this->view->listStatReg=$statReg->fetchAll();
			$jnsMsk = new JnsMasuk();
			$this->view->listJnsMsk=$jnsMsk->fetchAll();
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