<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: User Akses Controller -> Controller untuk modul halaman user akses
*/
class UseraksesController extends Zend_Controller_Action
{
	function init()
	{
		$this->initView();
		$this->view->baseUrl = $this->_request->getBaseUrl();
		Zend_Loader::loadClass('Profile');
		Zend_Loader::loadClass('User');
		Zend_Loader::loadClass('Menu');
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
		$this->view->active_tree="12";
		$this->view->active_menu="user/index";
	}
	
	function indexAction()
	{
		$user = new Menu();
		$menu = "user/index";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// get id
			$username=$this->_request->get('id');
			// Title Browser
			$this->view->title = "Daftar Akses User Akademik";
			// navigation
			$this->_helper->navbar('user',0,0,0,0);
			// get user & menu
			$user_acc = new User();
			$getUser = $user_acc->getUserAcByUname($username);
			if($getUser){
				foreach ($getUser as $dtUser){
					$this->view->uname=$dtUser['username'];
					$this->view->nm=$dtUser['nama'];
					$this->view->email=$dtUser['email'];
					if ($dtUser['superadmin']=='t'){
						$this->view->superadmin="YA";
					}else{
						$this->view->superadmin="TIDAK";
					}
				}
				$this->view->listMenu=$user_acc->getMenuAcByUname($username);	
			}else{
				$this->view->eksis="f";
			}
		}
	}
	
	function newAction() {
		$user = new Menu();
		$menu = "user/new";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// Title Browser
			$this->view->title = "Input User Akademik";
			// navigation
			$this->_helper->navbar(0,'user',0,0,0);
		}
	}
	
	function editAction() {
		$user = new Menu();
		$menu = "user/edit";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// get id
			$username = $this->_request->get('id');
			// Title Browser
			$this->view->title = "Edit User Akademik";
			// navigation
			$this->_helper->navbar('user',0,0,0,0);
			$user_acc=new User();
			$getUser=$user_acc->getUserAcByUname($username);
			if($getUser){
				foreach ($getUser as $dtUser){
					$this->view->uname=$dtUser['username'];
					$this->view->nm=$dtUser['nama'];
					$this->view->email=$dtUser['email'];	
				}
			}else{
				$this->view->eksis="f";
			}
		}
	}
}