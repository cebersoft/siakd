<?php
/*
	Programmer	: Tiar Aristian
	Release		: Agustus 2016
	Module		: Gelombang Mahasiswa Controller -> Controller untuk modul setting gelombang PMB mahasiswa
*/
class GelmhsController extends Zend_Controller_Action
{
	function init()
	{
		$this->initView();
		$this->view->baseUrl = $this->_request->getBaseUrl();
		Zend_Loader::loadClass('Profile');		
		Zend_Loader::loadClass('Angkatan');
		Zend_Loader::loadClass('Prodi');
		Zend_Loader::loadClass('GelombangMsk');
		Zend_Loader::loadClass('Mahasiswa');
		Zend_Loader::loadClass('MhsGelombang');
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
		$this->view->act_gelmhs="active";
		$this->view->act_gel="active open";
	}

	function indexAction()
	{
		// destroy session param
		Zend_Session::namespaceUnset('param_mhsgel');
		// Title Browser
		$this->view->title = "Pengaturan Gelombang Masuk Mahasiswa";
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
		$this->_helper->navbar('gelmhs',0,0,0,0);
		// 	show data
		$param = new Zend_Session_Namespace('param_mhsgel');
		$kd_prodi = $param->prd;
		$id_akt = $param->akt;
		$prodi = new Prodi();
		$getProdi = $prodi->getProdiByKd($kd_prodi);
		if($getProdi){
			foreach ($getProdi as $dtProdi) {
				$nm_prd=$dtProdi['nm_prodi'];
			}
			// Title Browser
			$this->view->title = "Daftar Mahasiswa Angkatan ".$id_akt." Prodi ".$nm_prd;
			$this->view->nm_prd=$nm_prd;
			$this->view->akt=$id_akt;
			// get mahasiswa
			$mhsGel = new MhsGelombang();
			$this->view->listMhsGel=$mhsGel->getMhsGelombangByAktPrdodi($id_akt, $kd_prodi);
		}else{
			$this->view->eksis="f";
			$this->view->title = "Daftar Mahasiswa";
		}
	}
	
	function newAction()
	{
		// Title Browser
		$this->view->title = "Set Gelombang Masuk Mashasiswa";
		// get param
		$nim = $this->_request->get('nim');
		$gelMhs=new MhsGelombang();
		$getMhs=$gelMhs->getMhsGelombangByNim($nim);
		if(!$getMhs){
			$this->view->eksis="f";
			// navigation
			$this->_helper->navbar('gelmhs',0,0,0,0);
		}else{
			$src = $this->_request->get('src');
			if($src=='mhsbi'){
				// navigation
				$this->_helper->navbar('mhsbiaya/list?nim='.$nim,0,0,0,0);
			}else{
				// navigation
				$this->_helper->navbar('gelmhs/list',0,0,0,0);	
			}
			foreach ($getMhs as $dtMhsGel){
				$this->view->nim=$dtMhsGel['nim'];
				$this->view->nm_mhs=$dtMhsGel['nm_mhs'];
				$this->view->id_gel=$dtMhsGel['id_gelombang'];
				$this->view->akt=$dtMhsGel['id_angkatan'];
				$this->view->nm_prd=$dtMhsGel['nm_prodi'];
				$this->view->stat_msk=$dtMhsGel['nm_stat_masuk'];
			}
			// gelombang
			$gelombang = new GelombangMsk();
			$this->view->listGel=$gelombang->fetchAll();
			$this->view->src=$src;
		}
	}
}