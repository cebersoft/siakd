<?php
/*
	Programmer	: Tiar Aristian
	Release		: Agustus 2016
	Module		: Biaya interval Controller -> Controller untuk modul biaya Interval
*/
class BiayataController extends Zend_Controller_Action
{
	function init()
	{
		$this->initView();
		$this->view->baseUrl = $this->_request->getBaseUrl();
		Zend_Loader::loadClass('Profile');
		Zend_Loader::loadClass('Prodi');
		Zend_Loader::loadClass('Angkatan');
		Zend_Loader::loadClass('Periode');
		Zend_Loader::loadClass('ParamBiaya');
		Zend_Loader::loadClass('PaketBiaya');
		Zend_Loader::loadClass('KompBiaya');
		Zend_Loader::loadClass('FormulaBiayaTA');
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
		$this->view->act_biayata="active";
		$this->view->act_setbiaya="active open";
	}

	function indexAction()
	{
		// Title Browser
		$this->view->title = "Daftar Biaya Interval Per Prodi dan Angkatan";
		// navigation
		$this->_helper->navbar(0,0,0,0,0);
		// destroy session param
		Zend_Session::namespaceUnset('param_biaya');
		// get data prodi
		$prodi = new Prodi();
		$this->view->listProdi=$prodi->fetchAll();
		// get data angkatan
		$angkatan = new Angkatan();
		$this->view->listAngkt=$angkatan->fetchAll();
	}
	
	function listAction()
	{
		// show data
		$param = new Zend_Session_Namespace('param_biayata');
		$kd_prodi = $param->prd;
		$id_akt = $param->akt;
		$prodi = new Prodi();
		$getProdi = $prodi->getProdiByKd($kd_prodi);
		$angkatan = new Angkatan();
		$getAkt = $angkatan->fetchAll();
		if(($getAkt)and($getProdi)){
			foreach ($getProdi as $dtProdi) {
				$nm_prd=$dtProdi['nm_prodi'];
			}
			// Title Browser
			$this->view->title = "Komponen Biaya Interval Angkatan ".$id_akt." Prodi ".$nm_prd;
			// throw param
			$this->view->akt=$id_akt;
			$this->view->prd=$kd_prodi;
			// komponen biaya
			$kompBiaya = new KompBiaya();
			$stat="t";
			$getKompBiaya=$kompBiaya->getKompBiayaByStat($stat);
			$this->view->listKompBiaya=$getKompBiaya;
			// periode akademik
			$periode = new Periode();
			$this->view->listPeriode=$periode->fetchAll();
			// paket biaya
			$paketBiaya = new PaketBiaya();
			$getPaket=$paketBiaya->getPaketBiayaByStat($stat);
			$this->view->listPaketBiaya=$getPaket;
			// param biaya
			$paramBiaya = new ParamBiaya();
			$this->view->listParam=$paramBiaya->fetchAll();
			// formula biaya
			$formBiayaTA = new FormulaBiayaTA();
			$getFormTa=$formBiayaTA->getFormulaBiayaTAByAktProdi($id_akt, $kd_prodi);
			$this->view->listFormTA=$getFormTa;
		}else{
			$this->view->eksis="f";
			// Title Browser
			$this->view->title = "Daftar Biaya Interval";
		}
		// navigation
		$this->_helper->navbar('biaya',0,0,0,0);
	}
}