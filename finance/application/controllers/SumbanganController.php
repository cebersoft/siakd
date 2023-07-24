<?php
/*
	Programmer	: Tiar Aristian
	Release		: Agustus 2016
	Module		: Sumbangan Mahasiswa Controller -> Controller untuk modul sumbangan mahasiswa
*/
class SumbanganController extends Zend_Controller_Action
{
	function init()
	{
		$this->initView();
		$this->view->baseUrl = $this->_request->getBaseUrl();
		Zend_Loader::loadClass('Profile');
		Zend_Loader::loadClass('Angkatan');
		Zend_Loader::loadClass('Prodi');
		Zend_Loader::loadClass('MhsGelombang');
		Zend_Loader::loadClass('KompBiaya');
		Zend_Loader::loadClass('Periode');
		Zend_Loader::loadClass('Sumbangan');
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
		$this->view->act_sumb="active";
		$this->view->act_setbiaya="active open";
	}

	function indexAction()
	{
		// destroy session param
		Zend_Session::namespaceUnset('param_mhsgel');
		// Title Browser
		$this->view->title = "Pengaturan Besaran Sumbangan Mahasiswa";
		// navigation
		$this->_helper->navbar(0,0,0,0,0);
		// angkatan
		$akt=new Angkatan();
		$this->view->listAkt=$akt->fetchAll();
		// prodi
		$prodi = new Prodi();
		$this->view->listProdi = $prodi->fetchAll();
	}
	
	function listAction()
	{
		// navigation
		$this->_helper->navbar('sumbangan',0,0,0,0);
		// 	show data
		$param = new Zend_Session_Namespace('param_sumb');
		$kd_prodi = $param->prd;
		$id_akt = $param->akt;
		$prodi = new Prodi();
		$getProdi = $prodi->getProdiByKd($kd_prodi);
		if($getProdi){
			foreach ($getProdi as $dtProdi) {
				$nm_prd=$dtProdi['nm_prodi'];
			}
			// Title Browser
			$this->view->title = "Daftar Besaran Sumbangan Mahasiswa Angkatan ".$id_akt." Prodi ".$nm_prd;
			$this->view->nm_prd=$nm_prd;
			$this->view->akt=$id_akt;
			// get mahasiswa
			$mhsSumb = new Sumbangan();
			$this->view->listMhsSumb=$mhsSumb->getSumbanganByAktProdi($id_akt, $kd_prodi);
		}else{
			$this->view->eksis="f";
			$this->view->title = "Daftar Besaran Sumbangan Mahasiswa";
		}
	}
	
	function newAction()
	{
		// Title Browser
		$this->view->title = "Set Besaran Sumbangan Mashasiswa";
		// get param
		$nim = $this->_request->get('nim');
		$mhs=new MhsGelombang();
		$mhsSumb=new Sumbangan();
		$getMhs=$mhs->getMhsGelombangByNim($nim);
		if(!$getMhs){
			$this->view->eksis="f";
			// navigation
			$this->_helper->navbar('sumbangan',0,0,0,0);
		}else{
			// navigation
			$this->_helper->navbar('sumbangan/list',0,0,0,0);
			foreach ($getMhs as $dtMhs){
				$this->view->nim=$dtMhs['nim'];
				$this->view->nm_mhs=$dtMhs['nm_mhs'];
				$this->view->nm_gel=$dtMhs['nm_gelombang'];
				$this->view->akt=$dtMhs['id_angkatan'];
				$this->view->nm_prd=$dtMhs['nm_prodi'];
				$this->view->stat_msk=$dtMhs['nm_stat_masuk'];
			}
			// data sumbangan detil
			$this->view->listSumbDtl=$mhsSumb->getSumbanganDtlByNim($nim);
			// komponen & periode
			$komp=new KompBiaya();
			$this->view->listKomp=$komp->fetchAll();
			$periode=new Periode();
			$this->view->listPer=$periode->fetchAll();
		}
	}
}