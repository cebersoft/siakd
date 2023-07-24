<?php
/*
	Programmer	: Tiar Aristian
	Release		: Agustus 2016
	Module		: verify TA Controller -> Controller untuk modul Verifikasi TA
*/
class VerifytaController extends Zend_Controller_Action
{
	function init()
	{
		$this->initView();
		$this->view->baseUrl = $this->_request->getBaseUrl();
		Zend_Loader::loadClass('Profile');
		Zend_Loader::loadClass('Periode');
		Zend_Loader::loadClass('Prodi');
		Zend_Loader::loadClass('KelasTA');
		Zend_Loader::loadClass('PaketkelasTA');
		Zend_Loader::loadClass('PrpUjianTa');
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
		$this->view->act_verifyta="active";
		$this->view->act_ta="active open";
	}

	function indexAction()
	{
		// Title Browser
		$this->view->title = "Verifikasi TA (Keuangan)";
		// navigation
		$this->_helper->navbar(0,0,0,0,0);
		// destroy session param
		Zend_Session::namespaceUnset('param_verifyta');
		// get data prodi
		$prodi = new Prodi();
		$this->view->listProdi=$prodi->fetchAll();
		// get data periode
		$periode = new Periode();
		$this->view->listPeriode=$periode->fetchAll();
		$getPerAktif=$periode->getPeriodeByStatus(0);
		foreach ($getPerAktif as $dtPerAktif) {
			$per_aktif=$dtPerAktif['kd_periode'];
		}
		$this->view->per_aktif=$per_aktif;
	}
	
	function listAction()
	{
		// show data
		$param = new Zend_Session_Namespace('param_verifyta');
		$kd_prodi = $param->prd;
		$kd_periode = $param->per;
		$prodi = new Prodi();
		$getProdi = $prodi->getProdiByKd($kd_prodi);
		$periode = new Periode();
		$getPeriode = $periode->getPeriodeByKd($kd_periode);
		if(($getPeriode)and($getProdi)){
			foreach ($getProdi as $dtProdi) {
				$nm_prd=$dtProdi['nm_prodi'];
			}
			// Title Browser
			$this->view->title = "Verifikasi TA (Keuangan) Periode ".$kd_periode." Prodi ".$nm_prd;
			// paket kelas
			$paketkelasta=new PaketkelasTA();
			$this->view->listPaketTA=$paketkelasta->getPaketKelasTAByPeriodeProdi($kd_periode,$kd_prodi);
		}else{
			$this->view->eksis="f";
			// Title Browser
			$this->view->title = "Verifikasi TA (Keuangan)";	
		}
		// navigation
		$this->_helper->navbar('verifyta',0,0,0,0);
	}

	function detilAction(){
		// Title Browser
		$this->view->title = "Verifikasi TA (Keuangan) - Daftar Pengajuan";
		// get kd paket kelas
		$kd_paket=$this->_request->get('id');
		$this->view->kd_paket=$kd_paket;
		$paketkelasta = new PaketkelasTA();
		$getPaketKelasTA=$paketkelasta->getPaketKelasTAByKd($kd_paket);
		if($getPaketKelasTA){
			foreach ($getPaketKelasTA as $dtPaket) {
				$kd_periode=$dtPaket['kd_periode'];
				$kd_prodi=$dtPaket['kd_prodi_kur'];
				$kdKelas = $dtPaket['kd_kelas'];
				$this->view->kd_kelas = $dtPaket['kd_kelas'];
				$this->view->nm_prodi=$dtPaket['nm_prodi_kur'];
				$this->view->kd_per=$dtPaket['kd_periode'];
				$this->view->nm_kelas=$dtPaket['nm_kelas'];
				$this->view->jns_kelas=$dtPaket['jns_kelas'];
				$this->view->nm_dsn=$dtPaket['nm_dosen'];
				$this->view->nm_mk=$dtPaket['nm_mk'];
				$this->view->kd_mk=$dtPaket['kode_mk'];
				$this->view->sks=$dtPaket['sks_tm']+$dtPaket['sks_prak']+$dtPaket['sks_prak_lap']+$dtPaket['sks_sim'];
			}
			$prp = new PrpUjianTa();
			$getPrp = $prp->getPrpApproverByPaketKelas($kd_paket);
			$this->view->listPrp=$getPrp;
			// navigation
			$this->_helper->navbar('verifyta/list',0,0,0,0);
		}else{
			$this->view->eksis="f";
			// navigation
			$this->_helper->navbar('verifyta',0,0,0,0);
		}
	}

}
