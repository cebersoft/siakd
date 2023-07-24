<?php
/*
	Programmer	: Tiar Aristian
	Release		: Agustus 2016
	Module		: Edit formula Biaya Controller -> Controller untuk modul edit urutan formula biaya
*/
class FbiayaController extends Zend_Controller_Action
{
	function init()
	{
		$this->initView();
		$this->view->baseUrl = $this->_request->getBaseUrl();
		Zend_Loader::loadClass('Profile');
		Zend_Loader::loadClass('Prodi');
		Zend_Loader::loadClass('Angkatan');
		Zend_Loader::loadClass('StatMasuk');
		Zend_Loader::loadClass('StatReg');
		Zend_Loader::loadClass('GelombangMsk');
		Zend_Loader::loadClass('TahunKeu');
		Zend_Loader::loadClass('SmtPeriode');
		Zend_Loader::loadClass('RuleBiaya');
		Zend_Loader::loadClass('ParamBiaya');
		Zend_Loader::loadClass('PaketBiaya');
		Zend_Loader::loadClass('KompBiaya');
		Zend_Loader::loadClass('Biaya');
		Zend_Loader::loadClass('FormulaBiaya');
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
		$this->view->act_obiaya="active";
		$this->view->act_setbiaya="active open";
	}

	function indexAction()
	{
		// Title Browser
		$this->view->title = "Urutan Biaya Periodik Per Prodi dan Angkatan";
		// navigation
		$this->_helper->navbar(0,0,0,0,0);
		// destroy session param
		Zend_Session::namespaceUnset('param_fbiaya');
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
		$param = new Zend_Session_Namespace('param_fbiaya');
		$kd_prodi = $param->prd;
		$id_akt = $param->akt;
		$frm = $param->frm;
		$prodi = new Prodi();
		$getProdi = $prodi->getProdiByKd($kd_prodi);
		$angkatan = new Angkatan();
		$getAkt = $angkatan->fetchAll();
		if(($getAkt)and($getProdi)){
			foreach ($getProdi as $dtProdi) {
				$nm_prd=$dtProdi['nm_prodi'];
			}
			// Title Browser
			$this->view->title = "Daftar Periode Biaya Angkatan ".$id_akt." Prodi ".$nm_prd;
			// initial tab
			$tab=$this->_request->get('tab');
			if(!$tab){
				$this->view->nTab="1";
			}else{
				$this->view->nTab=$tab;
			}
			// throw param
			$this->view->akt=$id_akt;
			$this->view->prd=$kd_prodi;
			// status masuk
			$statMsk = new StatMasuk();
			$getStatMsk=$statMsk->fetchAll();
			$this->view->listStatMsk=$getStatMsk;
			// gelombang masuk
			$gelMsk= new GelombangMsk();
			$getGelMsk=$gelMsk->fetchAll();
			$this->view->listGel=$getGelMsk;
			// grup formula biaya
			$fbiaya = new FormulaBiaya();
			$getFBiayaGrup = $fbiaya->getFormulaBiayaGrupByAktPrd($id_akt, $kd_prodi);
			$this->view->listFBiayaGrup=$getFBiayaGrup;
		}else{
			$this->view->eksis="f";
			// Title Browser
			$this->view->title = "Daftar Periode Biaya";	
		}
		// navigation
		$this->_helper->navbar('fbiaya',0,0,0,0);
	}
	
	function detilAction(){
		// Title Browser
		$this->view->title = "Pengaturan Urutan Biaya Mahasiswa Per Semester";
		// get param
		$akt=$this->_request->get('akt');
		$prd=$this->_request->get('prd');
		$sm=$this->_request->get('sm');
		$gel=$this->_request->get('gel');
		$per=$this->_request->get('per');
		$reg=$this->_request->get('reg');
		// navigation
		$this->_helper->navbar('fbiaya/list',0,0,0,0);
		// get list formula
		$formula = new FormulaBiaya();
		$getFormula=$formula->getFormulaBiayaByPeriode($akt, $prd, $sm, $gel, $reg, $per);
		if($getFormula){
			$this->view->listFormula=$getFormula;
			foreach ($getFormula as $dtBiaya) {
				$this->view->prd=$dtBiaya['nm_prodi'];
				$this->view->kdprd=$dtBiaya['kd_prodi'];
				$this->view->akt=$dtBiaya['id_angkatan'];
				$this->view->sm=$dtBiaya['nm_stat_masuk'];
				$this->view->idsm=$dtBiaya['id_stat_masuk'];
				$this->view->gel=$dtBiaya['nm_gelombang'];
				$this->view->idgel=$dtBiaya['id_gelombang'];
				$this->view->stat=$dtBiaya['status_reg']." (".$dtBiaya['status_mhs'].")";
				$this->view->per=$dtBiaya['kd_periode'];
				$this->view->thn=$dtBiaya['tahun_keu'];
				$this->view->smt=$dtBiaya['id_smt'];
				$this->view->reg=$dtBiaya['kd_status_reg'];
			}
		}else{
			$this->view->eksis="f";
		}
		
	}
}