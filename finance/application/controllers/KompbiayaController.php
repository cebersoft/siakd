<?php
/*
	Programmer	: Tiar Aristian
	Release		: Agustus 2016
	Module		: Komponen biaya Controller -> Controller untuk modul komponen biaya
*/
class KompbiayaController extends Zend_Controller_Action
{
	function init()
	{
		$this->initView();
		$this->view->baseUrl = $this->_request->getBaseUrl();
		Zend_Loader::loadClass('Profile');
		Zend_Loader::loadClass('KompBiaya');
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
		$this->view->act_kom="active";
		$this->view->act_setbiaya="active open";
	}

	function indexAction()
	{
		// Title Browser
		$this->view->title = "Daftar Komponen Biaya Mahasiswa";
		// navigation
		$this->_helper->navbar(0,0,'kompbiaya/new',0,0);
		$kompBiaya=new KompBiaya();
		$getKompBiaya=$kompBiaya->fetchAll();
		$this->view->listKompBiaya=$getKompBiaya;
	}
	
	function newAction() {
		// Title Browser
		$this->view->title = "Input Komponen Biaya Mahasiswa";
		// navigation
		$this->_helper->navbar(0,'kompbiaya',0,0,0);
	}
	
	function editAction(){
		// Title Browser
		$this->view->title = "Edit Komponen Biaya Mahasiswa";
		// navigation
		$this->_helper->navbar('kompbiaya',0,0,0,0);
		// get param
		$id=$this->_request->get('id');
		$kompBiaya=new KompBiaya();
		$getKomp=$kompBiaya->getKompBiayaById($id);
		if($getKomp){
			foreach ($getKomp as $dtKomp){
				$this->view->id=$dtKomp['id_komp'];
				$this->view->nm=$dtKomp['nm_komp'];
			}	
		}else{
			$this->view->eksis="f";
		}
	}
}