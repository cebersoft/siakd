<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: Nama kelas Controller -> Controller untuk modul halaman nama kelas
*/
class NmkelasController extends Zend_Controller_Action
{
	function init()
	{
		$this->initView();
		$this->view->baseUrl = $this->_request->getBaseUrl();
		Zend_Loader::loadClass('Profile');
		Zend_Loader::loadClass('User');
		Zend_Loader::loadClass('Menu');;
		Zend_Loader::loadClass('Nmkelas');
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
		$this->view->active_menu="nmkelas/index";
	}
	
	function indexAction()
	{
		$user = new Menu();
		$menu = "nmkelas/index";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// Title Browser
			$this->view->title = "Nama Paket Kelas";
			// navigation
			$this->_helper->navbar(0,0,0,0,0);
			// prodi
			$nmKelas = new Nmkelas();
			$this->view->listNmKls=$nmKelas->fetchAll();
		}
	}
}