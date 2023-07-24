<?php
/*
	Programmer	: Tiar Aristian
	Release		: Agustus 2016
	Module		: User Controller -> Controller untuk modul master user
*/
class UserController extends Zend_Controller_Action
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
		if (($auth->hasIdentity())and($ses_fin->uname)and($ses_fin->super=='t')) {
			$this->view->namauser =Zend_Auth::getInstance()->getIdentity()->nama;
			$this->view->kd_pt=$ses_fin->kd_pt;
			$this->view->nm_pt=$ses_fin->nm_pt;
		}else{
			$this->_redirect('/');
		}
		// layout
		$this->_helper->layout()->setLayout('main');
		// menu nav
		$this->view->act_tools="active";
		$this->view->act_user="active open";
	}

	function indexAction()
	{
		// Title Browser
		$this->view->title = "Daftar User Sistem Informasi";
		// navigation
		$this->_helper->navbar(0,0,'user/new',0,0);
		$user=new UserFin();
		$getUser=$user->fetchAll();
		$this->view->listUser=$getUser;
	}
	
	function newAction() {
		// Title Browser
		$this->view->title = "Input User Sistem Informasi";
		// navigation
		$this->_helper->navbar(0,'user',0,0,0);
	}
	
	function editAction(){
		// Title Browser
		$this->view->title = "Edit User Sistem Informasi";
		// navigation
		$this->_helper->navbar('user',0,0,0,0);
		// get param
		$username=$this->_request->get('id');
		$userfin=new UserFin();
		$getUser=$userfin->getUserFinByUname($username);
		if($getUser){
			foreach ($getUser as $dtUser){
				$this->view->uname=$dtUser['username'];
				$this->view->nm=$dtUser['nama'];
				$this->view->pwd=$dtUser['pwd'];
				$this->view->superadm=$dtUser['superadmin'];
				$this->view->email=$dtUser['email'];
			}
		}else{
			$this->view->eksis="f";
		}
	}
	
	function passwordAction(){
		// Title Browser
		$this->view->title = "Edit Password Sistem Informasi";
		// navigation
		$this->_helper->navbar('user',0,0,0,0);
		// get param
		$username=$this->_request->get('id');
		$userfin=new UserFin();
		$getUser=$userfin->getUserFinByUname($username);
		if($getUser){
			foreach ($getUser as $dtUser){
				$this->view->uname=$dtUser['username'];
				$this->view->nm=$dtUser['nama'];
			}
		}else{
			$this->view->eksis="f";
		}
	}
}