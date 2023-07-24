<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: Matkul Controller -> Controller untuk modul halaman mata kuliah
*/
class MatkulController extends Zend_Controller_Action
{
	function init()
	{
		$this->initView();
		$this->view->baseUrl = $this->_request->getBaseUrl();
		Zend_Loader::loadClass('Profile');
		Zend_Loader::loadClass('User');
		Zend_Loader::loadClass('Menu');
		Zend_Loader::loadClass('Prodi');
		Zend_Loader::loadClass('Matkul');
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
		$this->view->active_menu="matkul/index";
	}
	
	function indexAction()
	{
		$user = new Menu();
		$menu = "matkul/index";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// Title Browser
			$this->view->title = "Daftar Mata Kuliah";
			// navigation
			$this->_helper->navbar(0,0,'matkul/new',0,0);
			// destroy session param
			Zend_Session::namespaceUnset('param_mk');
			// get data prodi
			$prodi = new Prodi();
			$this->view->listProdi=$prodi->fetchAll();
		}
	}

	function listAction(){
		$user = new Menu();
		$menu = "matkul/index";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// show data
			$param = new Zend_Session_Namespace('param_mk');
			$prd = $param->prd;
			// get data prodi
			$prodi = new Prodi();
			$listProdi=$prodi->fetchAll();
			if(!$prd){
				$this->view->prd="SEMUA";
			}else{
				$prod="";
				foreach ($listProdi as $dataPrd) {
					foreach ($prd as $dt) {
						if($dt==$dataPrd['kd_prodi']){
							$prod=$dataPrd['nm_prodi'].", ".$prod;
						}
					}
				}
				$this->view->prd=$prod;
			}
			// Title Browser
			$this->view->title = "Daftar Mata Kuliah";
			// navigation
			$this->_helper->navbar('matkul',0,0,0,0);
			// get data 
			$matkul = new Matkul();
			$getMk = $matkul->getMatkulByProdi($prd);
			$this->view->listMk = $getMk;
		}
	}

	function newAction(){
		$user = new Menu();
		$menu = "matkul/new";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// navigation
			$this->_helper->navbar(0,'matkul',0,0,0);
			// Title Browser
			$this->view->title = "Input Mata Kuliah Baru";
			// get data prodi
			$prodi = new Prodi();
			$this->view->listProdi=$prodi->fetchAll();
		}
	}

	function editAction(){
		$user = new Menu();
		$menu = "matkul/edit";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// navigation
			$this->_helper->navbar('matkul/list',0,0,0,0);
			// get data matkul
			$id=$this->_request->get('id');
			$matkul =  new Matkul();
			$getMatkul = $matkul->getMatkulById($id);
			if($getMatkul){
				foreach ($getMatkul as $dataMk) {
					$nmMk=$dataMk['nm_mk'];
					$this->view->id_mk=$dataMk['id_mk'];
					$this->view->nm_mk=$dataMk['nm_mk'];
					$this->view->kd_prodi=$dataMk['kd_prodi'];
				}
				// Title Browser
				$this->view->title = "Edit Mata Kuliah ".$nmMk;
			}else{
				$this->view->eksis="f";
				// Title Browser
				$this->view->title = "Edit Mata Kuliah";
			}
			// get data prodi
			$prodi = new Prodi();
			$this->view->listProdi=$prodi->fetchAll();
		}
	}
}