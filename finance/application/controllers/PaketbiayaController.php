<?php
/*
	Programmer	: Tiar Aristian
	Release		: Agustus 2016
	Module		: Paket biaya Controller -> Controller untuk modul paket biaya
*/
class PaketbiayaController extends Zend_Controller_Action
{
	function init()
	{
		$this->initView();
		$this->view->baseUrl = $this->_request->getBaseUrl();
		Zend_Loader::loadClass('Profile');
		Zend_Loader::loadClass('PaketBiaya');
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
		$this->view->act_pkt="active";
		$this->view->act_setbiaya="active open";
	}

	function indexAction()
	{
		// Title Browser
		$this->view->title = "Daftar Paket Biaya Mahasiswa";
		// navigation
		$this->_helper->navbar(0,0,'paketbiaya/new',0,0);
		$paketBiaya=new PaketBiaya();
		$getPaketBiaya=$paketBiaya->fetchAll();
		$this->view->listPaketBiaya=$getPaketBiaya;
	}
	
	function newAction() {
		// Title Browser
		$this->view->title = "Input Paket Biaya Mahasiswa";
		// navigation
		$this->_helper->navbar(0,'paketbiaya',0,0,0);
	}
	
	function editAction(){
		// Title Browser
		$this->view->title = "Edit Paket Biaya Mahasiswa";
		// navigation
		$this->_helper->navbar('paketbiaya',0,0,0,0);
		// get param
		$id=$this->_request->get('id');
		$pktBiaya=new PaketBiaya();
		$getPkt=$pktBiaya->getPaketBiayaById($id);
		if($getPkt){
			foreach ($getPkt as $dtPkt){
				$this->view->id=$dtPkt['id_paket'];
				$this->view->nm=$dtPkt['nm_paket'];
			}	
		}else{
			$this->view->eksis="f";
		}
	}
}