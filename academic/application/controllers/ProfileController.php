<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: Profile Controller -> Controller untuk modul halaman profil user
*/
class ProfileController extends Zend_Controller_Action
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
			$this->uname=$ses_ac->uname;
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
	}
	
	function indexAction()
	{
		// get id
		$username = $this->uname;
		// Title Browser
		$this->view->title = "Profil User";
		// navigation
		$this->_helper->navbar(0,0,0,0,0);
		$user_acc=new User();
		$getUser=$user_acc->getUserAcByUname($username);
		if($getUser){
			foreach ($getUser as $dtUser){
				$this->view->uname=$dtUser['username'];
				$this->view->nm=$dtUser['nama'];
				$this->view->email=$dtUser['email'];
				$this->view->pwd=$dtUser['pwd'];
			}
		}else{
			$this->view->eksis="f";
		}
	}
	
	function aksesAction(){
		// Title Browser
		$this->view->title = "Akses User";
		// navigation
		$this->_helper->navbar(0,0,0,0,0);
		// get user menu
		$ses_ac = new Zend_Session_Namespace('ses_ac');
		$username=$ses_ac->uname;
		$user = new User();
		$this->view->listMenu=$user->getMenuAcByUname($username);
	}
}