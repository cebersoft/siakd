<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: Ekuivalensi Controller -> Controller untuk modul halaman ekuivalensi
*/
class EkuivalensiController extends Zend_Controller_Action
{
	function init()
	{
		$this->initView();
		$this->view->baseUrl = $this->_request->getBaseUrl();
		Zend_Loader::loadClass('Profile');
		Zend_Loader::loadClass('User');
		Zend_Loader::loadClass('Menu');
		Zend_Loader::loadClass('Prodi');
		Zend_Loader::loadClass('Ekuivalensi');
		Zend_Loader::loadClass('MatkulKurikulum');
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
		$this->view->active_tree="02";
		$this->view->active_menu="ekuivalensi/index";
	}
	
	function indexAction()
	{
		$user = new Menu();
		$menu = "ekuivalensi/index";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// Title Browser
			$this->view->title = "Daftar Ekuivalensi Kurikulum";
			// navigation
			$this->_helper->navbar(0,0,'ekuivalensi/new',0,0);
			$ekuivalensi = new Ekuivalensi();
			$this->view->listEkuivalensi=$ekuivalensi->fetchAll();
		}
	}
	
	function newAction() {
		$user = new Menu();
		$menu = "ekuivalensi/new";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// Title Browser
			$this->view->title = "Input Ekuivalensi Kurikulum";
			// navigation
			$this->_helper->navbar(0,'ekuivalensi',0,0,0);
			// prodi
			$prodi = new Prodi();
			$this->view->listProdi=$prodi->fetchAll();
		}
	}
	
	function detilAction() {
		$user = new Menu();
		$menu = "ekuivalensi/detil";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// get kd
			$id=$this->_request->get('id');
			// Title Browser
			$this->view->title = "Detil Ekuivalensi-Transformasi Mata Kuliah";
			// navigation
			$this->_helper->navbar('ekuivalensi',0,'ekuivalensi/new',0,0);
			// get data ekuivalensi
			$ekuivalensi = new Ekuivalensi();
			$getEkuivalensiHdr=$ekuivalensi->getEkuivalensiHdrById($id);
			if($getEkuivalensiHdr){
				foreach ($getEkuivalensiHdr as $dtEkv){
					$this->view->id_ekv=$dtEkv['id_ekuivalensi'];
					$this->view->nm_prodi=$dtEkv['nm_prodi_kur_lm'];
					$this->view->id_kur_lm=$dtEkv['id_kurikulum_lama'];
					$this->view->kd_kur_lm=$dtEkv['kd_kurikulum_lama'];
					$this->view->nm_kur_lm=$dtEkv['nm_kurikulum_lama'];
					$this->view->per_lama=$dtEkv['kd_periode_berlaku_lama'];
					$this->view->id_kur_br=$dtEkv['id_kurikulum_baru'];
					$this->view->kd_kur_br=$dtEkv['kd_kurikulum_baru'];
					$this->view->nm_kur_br=$dtEkv['nm_kurikulum_baru'];
					$this->view->per_baru=$dtEkv['kd_periode_berlaku_baru'];
				}
				// get data ekuiv detil
				$this->view->listDtlEkv=$ekuivalensi->getEkuivalensiDtlById($id);
			}else{
				$this->view->eksis="f";
			}		
		}
	}
}