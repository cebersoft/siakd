<?php
/*
	Programmer	: Tiar Aristian
	Release		: Agustus 2016
	Module		: Bank Controller -> Controller untuk modul master bank
*/
class BankController extends Zend_Controller_Action
{
	function init()
	{
		$this->initView();
		$this->view->baseUrl = $this->_request->getBaseUrl();
		Zend_Loader::loadClass('Profile');
		Zend_Loader::loadClass('Bank');
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
		$this->view->act_bank="active";
		$this->view->act_mst="active open";
	}

	function indexAction()
	{
		// Title Browser
		$this->view->title = "Daftar Bank";
		// navigation
		$this->_helper->navbar(0,0,'bank/new',0,0);
		$bank=new Bank();
		$getBank=$bank->fetchAll();
		$this->view->listBank=$getBank;
	}
	
	function newAction() {
		// Title Browser
		$this->view->title = "Input Master Bank";
		// navigation
		$this->_helper->navbar(0,'bank',0,0,0);
	}
	
	function editAction(){
		// Title Browser
		$this->view->title = "Edit Data Bank";
		// navigation
		$this->_helper->navbar('bank',0,0,0,0);
		// get param
		$id=$this->_request->get('id');
		$bank=new Bank();
		$getBank=$bank->getBankById($id);
		if($getBank){
			foreach ($getBank as $dt){
				$this->view->id=$dt['id_bank'];
				$this->view->nm=$dt['nm_bank'];
				$this->view->rek=$dt['no_rek'];
				$this->view->akun=$dt['nm_akun'];
			}
		}else{
			$this->view->eksis="f";
		}
	}
}