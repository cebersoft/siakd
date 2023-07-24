<?php
/*
	Programmer	: Tiar Aristian
	Release		: Agustus 2016
	Module		: kalender Controller -> Controller untuk modul kalender
*/
class KalenderController extends Zend_Controller_Action
{
	function init()
	{
		$this->initView();
		$this->view->baseUrl = $this->_request->getBaseUrl();
		Zend_Loader::loadClass('Profile');
		Zend_Loader::loadClass('AktivitasFin');
		Zend_Loader::loadClass('KalenderFin');
		Zend_Loader::loadClass('Periode');
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
		$this->view->act_mst="active";
		$this->view->act_kal="active open";
	}

	function indexAction()
	{
		// Title Browser
		$this->view->title = "Daftar Periode Akademik";
		// navigation
		$this->_helper->navbar(0,0,0,0,0);
		$periode=new Periode();
		$getPeriode=$periode->fetchAll();
		$this->view->listPeriode=$getPeriode;
	}

	function detilAction()
	{
		// get id
		$kd_periode=$this->_request->get('id');
		$periode = new Periode();
		$getPeriode = $periode->getPeriodeByKd($kd_periode);
		if($getPeriode){
			// Title Browser
			$this->view->title = "Daftar Kalender Keuangan ".$kd_periode;
			// master aktv
			$aktFin = new AktivitasFin();
			$this->view->listAktFin = $aktFin->fetchAll();
			// get data kalender
			$kalender=new KalenderFin();
			$getKalender=$kalender->getKalenderFinByPeriode($kd_periode);
			$this->view->listKalender=$getKalender;
			$this->view->per=$kd_periode;
		}else{
			$this->view->eksis="f";
			// Title Browser
			$this->view->title = "Daftar Kalender Keuangan";	
		}	
		// navigation
		$this->_helper->navbar('kalender',0,0,0,0);
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