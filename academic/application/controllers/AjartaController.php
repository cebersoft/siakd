<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: Ajar TA Controller -> Controller untuk modul halaman ajar TA
*/
class AjartaController extends Zend_Controller_Action
{
	function init()
	{
		$this->initView();
		$this->view->baseUrl = $this->_request->getBaseUrl();
		Zend_Loader::loadClass('Profile');
		Zend_Loader::loadClass('User');
		Zend_Loader::loadClass('Menu');
		Zend_Loader::loadClass('DosenTA');
		Zend_Loader::loadClass('Prodi');
		Zend_Loader::loadClass('AjarTA');
		Zend_Loader::loadClass('MatkulKurikulum');
		Zend_Loader::loadClass('MatkulTaApp');
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
		$this->view->active_tree="09";
		$this->view->active_menu="ajarta/index";
	}
	
	function indexAction(){
		$user = new Menu();
		$menu = "ajarta/index";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// Title Browser
			$this->view->title = "Daftar Mata Kuliah TA Tiap Prodi dan Kurikulum";
			// navigation
			$this->_helper->navbar(0,0,0,0,0);
			// get data prodi
			$prodi = new Prodi();
			$this->view->listProdi=$prodi->fetchAll();
			// get data dosen TA
			$dosenTA = new DosenTA();
			$this->view->listDosenTA=$dosenTA->fetchAll();
			// get data ajar
			$ajarTA = new AjarTA();
			$getAjarTA = $ajarTA->fetchAll();
			$this->view->listAjar = $getAjarTA;
			$arr_prd = array();
			foreach ($getAjarTA as $dtAjar) {
				$arr_prd[] = array('kd_prd' => $dtAjar['kd_prodi_kur'], 'nm_prd'=>  $dtAjar['nm_prodi_kur']);
			}
			$this->view->arr_prd=array_unique($arr_prd);
		}
	}

	function approvertaAction(){
		$user = new Menu();
		$menu = "ajarta/index";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// navigation
			$this->_helper->navbar('ajarta',0,0,0,0);
			// get id mk kur
			$id_mk_kur=$this->_request->get('id');
			$this->view->id_mk_kur=$id_mk_kur;
			$mkKur=new MatkulKurikulum();
			$getMkKur=$mkKur->getMatkulKurikulumById($id_mk_kur);
			if($getMkKur){
				$nm_mk="";
				foreach ($getMkKur as $dtMk){
					$nm_mk=$dtMk['nm_mk'];
				}
				// Title Browser
				$this->view->title = "Verifikator Administrasi TA Mata Kuliah : ".$nm_mk;
				// get data
				$mkApp=new MatkulTaApp();
				$this->view->listMkApp=$mkApp->getAppByMkKur($id_mk_kur);
			}else{
				$this->view->eksis="f";
				// Title Browser
				$this->view->title = "Verifikator Administrasi TA";
			}
		}
	}
}