<?php
/*
	Programmer	: Tiar Aristian
	Release		: Agustus 2016
	Module		: Biaya Controller -> Controller untuk modul biaya
*/
class BiayaController extends Zend_Controller_Action
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
		$this->view->act_biaya="active";
		$this->view->act_setbiaya="active open";
	}

	function indexAction()
	{
		// Title Browser
		$this->view->title = "Daftar Biaya Periodik Per Prodi dan Angkatan";
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
		$param = new Zend_Session_Namespace('param_biaya');
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
			$this->view->title = "Daftar Biaya Angkatan ".$id_akt." Prodi ".$nm_prd;
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
			// komponen biaya
			$kompBiaya = new KompBiaya();
			$stat="t";
			$getKompBiaya=$kompBiaya->getKompBiayaByStat($stat);
			$this->view->listKompBiaya=$getKompBiaya;
			// paket biaya
			$paketBiaya = new PaketBiaya();
			$getPaket=$paketBiaya->getPaketBiayaByStat($stat);
			$this->view->listPaketBiaya=$getPaket;
			// biaya
			$biaya = new Biaya();
			$getBiaya = $biaya->getBiayaByAktProdi($id_akt, $kd_prodi);
			$this->view->listBiaya=$getBiaya;
		}else{
			$this->view->eksis="f";
			// Title Browser
			$this->view->title = "Daftar Biaya";	
		}
		// navigation
		$this->_helper->navbar('biaya',0,0,0,0);
	}
	
	function detilAction(){
		// Title Browser
		$this->view->title = "Pengaturan Biaya Mahasiswa Per Semester";
		// get param
		$akt=$this->_request->get('akt');
		$prd=$this->_request->get('prd');
		$sm=$this->_request->get('sm');
		$gel=$this->_request->get('gel');
		$kom=$this->_request->get('kom');
		// get biaya
		$biaya = new Biaya();
		$getBiaya=$biaya->getBiayaById($akt, $prd, $sm, $gel, $kom);
		if($getBiaya){
			// navigation
			$this->_helper->navbar('biaya/list',0,0,0,0);
			// get data
			foreach ($getBiaya as $dtBiaya) {
				$this->view->prd=$dtBiaya['nm_prodi'];
				$this->view->kdprd=$dtBiaya['kd_prodi'];
				$this->view->akt=$dtBiaya['id_angkatan'];
				$this->view->sm=$dtBiaya['nm_stat_masuk'];
				$this->view->idsm=$dtBiaya['id_stat_masuk'];
				$this->view->gel=$dtBiaya['nm_gelombang'];
				$this->view->idgel=$dtBiaya['id_gelombang'];
				$this->view->kom=$dtBiaya['nm_komp'];
				$this->view->idkom=$dtBiaya['id_komp'];
				$this->view->pkt=$dtBiaya['nm_paket'];
				$this->view->idpkt=$dtBiaya['id_paket'];
				$this->view->nom=$dtBiaya['nominal'];
			}
			// stat reg
			$statReg = new StatReg();
			$getStatReg=$statReg->fetchAll();
			$this->view->listStatReg=$getStatReg;
			// rule biaya
			$rule = new RuleBiaya();
			$this->view->listRule=$rule->fetchAll();
			// param biaya
			$prmBiaya = new ParamBiaya();
			$this->view->listParam=$prmBiaya->fetchAll();
			// get list formula
			$formula = new FormulaBiaya();
			$getFormula=$formula->getFormulaBiayaByBiaya($akt, $prd, $sm, $gel, $kom);
			$this->view->listFormula=$getFormula;
			// tahun keu
			$thn=new TahunKeu();
			$this->view->listThn=$thn->fetchAll();
			// smt
			$smtPer = new SmtPeriode();
			$this->view->listSmtPer=$smtPer->fetchAll();
		}else{
			$this->view->eksis="f";
			// navigation
			$this->_helper->navbar('biaya',0,0,0,0);
		}
	}
	
	function editAction(){
		// Title Browser
		$this->view->title = "Edit Biaya Mahasiswa";
		// get param
		$akt=$this->_request->get('akt');
		$prd=$this->_request->get('prd');
		$sm=$this->_request->get('sm');
		$gel=$this->_request->get('gel');
		$kom=$this->_request->get('kom');
		// get biaya
		$biaya = new Biaya();
		$getBiaya=$biaya->getBiayaById($akt, $prd, $sm, $gel, $kom);
		if($getBiaya){
			// navigation
			$this->_helper->navbar('biaya/list',0,0,0,0);
			// get data
			foreach ($getBiaya as $dtBiaya) {
				$this->view->prd=$dtBiaya['nm_prodi'];
				$this->view->kdprd=$dtBiaya['kd_prodi'];
				$this->view->akt=$dtBiaya['id_angkatan'];
				$this->view->sm=$dtBiaya['nm_stat_masuk'];
				$this->view->idsm=$dtBiaya['id_stat_masuk'];
				$this->view->gel=$dtBiaya['nm_gelombang'];
				$this->view->idgel=$dtBiaya['id_gelombang'];
				$this->view->kom=$dtBiaya['nm_komp'];
				$this->view->idkom=$dtBiaya['id_komp'];
				$this->view->pkt=$dtBiaya['nm_paket'];
				$this->view->idpkt=$dtBiaya['id_paket'];
				$this->view->nom=$dtBiaya['nominal'];
			}
			// paket biaya
			$stat='t';
			$paketBiaya = new PaketBiaya();
			$getPaket=$paketBiaya->getPaketBiayaByStat($stat);
			$this->view->listPaketBiaya=$getPaket;
		}else{
			$this->view->eksis="f";
			// navigation
			$this->_helper->navbar('biaya',0,0,0,0);
		}
	}
}