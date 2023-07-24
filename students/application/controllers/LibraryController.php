<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: Library Controller -> Controller untuk modul perpustakaan
*/
class LibraryController extends Zend_Controller_Action
{
	function init()
	{
		$this->initView();
		$this->view->baseUrl = $this->_request->getBaseUrl();
		Zend_Loader::loadClass('Profile');
		Zend_Loader::loadClass('User');
		Zend_Loader::loadClass('Zend_Session');
		Zend_Loader::loadClass('Zend_Layout');
		$auth = Zend_Auth::getInstance();
		$ses_std = new Zend_Session_Namespace('ses_std');
		if (($auth->hasIdentity())and($ses_std->uname)) {
			$this->view->namamhs =Zend_Auth::getInstance()->getIdentity()->nm_mhs;
			$this->view->nim=Zend_Auth::getInstance()->getIdentity()->nim;
			$this->view->idmhs=Zend_Auth::getInstance()->getIdentity()->id_mhs;
			$this->view->kd_pt=$ses_std->kd_pt;
			$this->view->nm_pt=$ses_std->nm_pt;
		}else{
			$this->_redirect('/');
		}
		// layout
		$this->_helper->layout()->setLayout('main');
		// navigation
		$this->_helper->navbar(0,0);
		// nav menu
		$this->view->lib_act="active";
	}

	function indexAction()
	{
		// Title Browser
		$this->view->title = "Digital Library";
	}

	function catalogAction()
	{
		// Title Browser
		$this->view->title = "Katalog Perpustakaan";
	}
	
	function jurnalAction(){
		// Title Browser
		$this->view->title = "Link Jurnal";

	}
}