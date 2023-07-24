<?php
/*
	Programmer	: Tiar Aristian
	Release		: Agustus 2016
	Module		: master gelombang Controller -> Controller untuk modul master gelombang masuk
*/
class GelmasterController extends Zend_Controller_Action
{
	function init()
	{
		$this->initView();
		$this->view->baseUrl = $this->_request->getBaseUrl();
		Zend_Loader::loadClass('Profile');		
		Zend_Loader::loadClass('GelombangMsk');
		Zend_Loader::loadClass('Zend_Session');
		Zend_Loader::loadClass('Zend_Layout');
		$auth = Zend_Auth::getInstance();
		$ses_fin = new Zend_Session_Namespace('ses_fin');
		if (($auth->hasIdentity())and($ses_fin->uname)) {
			$this->view->namauser =Zend_Auth::getInstance()->getIdentity()->nama;
			$this->view->kd_pt=$ses_fin->kd_pt;
			$this->view->nm_pt=$ses_fin->nm_pt;
		}else{
			$this->_redirect('/');
		}
		// layout
		$this->_helper->layout()->setLayout('main');
		// menu nav
		$this->view->act_gel="active";
		$this->view->act_gelmt="active open";
	}

	function indexAction()
	{
		// Title Browser
		$this->view->title = "Daftar Gelombang Masuk Mahasiswa";
		// navigation
		$this->_helper->navbar(0,0,'gelmaster/new',0,0);
		$gelombang=new GelombangMsk();
		$getGelombang=$gelombang->fetchAll();
		$this->view->listGelombang=$getGelombang;
	}
	
	function newAction() {
		// Title Browser
		$this->view->title = "Input Master Gelombang Masuk Mahasiswa";
		// navigation
		$this->_helper->navbar(0,'gelmaster',0,0,0);
	}
	
	function editAction(){
		// Title Browser
		$this->view->title = "Edit Gelombang Masuk Mahasiswa";
		// navigation
		$this->_helper->navbar('gelmaster',0,0,0,0);
		// get param
		$id=$this->_request->get('id');
		$gelombang=new GelombangMsk();
		$getGel=$gelombang->getGelombangById($id);
		if($getGel){
			foreach ($getGel as $dtGel){
				$this->view->id=$dtGel['id_gelombang'];
				$this->view->nm=$dtGel['nm_gelombang'];
				$this->view->urutan=$dtGel['urutan'];
			}
		}else{
			$this->view->eksis="f";
		}
	}
}