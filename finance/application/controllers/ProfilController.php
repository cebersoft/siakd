<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: Profil Controller -> Controller untuk modul profil
*/
class ProfilController extends Zend_Controller_Action
{
	function init()
	{
		$this->initView();
		$this->view->baseUrl = $this->_request->getBaseUrl();
		Zend_Loader::loadClass('Profile');
		Zend_Loader::loadClass('UserFin');
		Zend_Loader::loadClass('Zend_Session');
		Zend_Loader::loadClass('Zend_Layout');
		$auth = Zend_Auth::getInstance();
		$ses_fin = new Zend_Session_Namespace('ses_fin');
		if (($auth->hasIdentity())and($ses_fin->uname)) {
			$this->view->namauser =Zend_Auth::getInstance()->getIdentity()->nama;
			$this->view->kd_pt=$ses_fin->kd_pt;
			$this->view->nm_pt=$ses_fin->nm_pt;
			// var global
			$this->username = $ses_fin->uname;
		}else{
			$this->_redirect('/');
		}
		// layout
		$this->_helper->layout()->setLayout('main');
		// navigation
		$this->_helper->navbar(0,0,0,0,0);
	}

	function indexAction()
	{
		// Title Browser
		$this->view->title = "Profil Pengguna";
		$user = new UserFin();
		$username = $this->username;
		$getUser=$user->getUserFinByUname($username);
		foreach ($getUser as $dtUser){
			$this->view->username=$username;
			$this->view->nama=$dtUser['nama'];
			$this->view->email=$dtUser['email'];
			$this->view->pwd=$dtUser['pwd'];
		}
	}
}