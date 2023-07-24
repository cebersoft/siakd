<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: Home Controller -> Controller untuk modul home
*/
class HomeController extends Zend_Controller_Action
{
	function init()
	{
		$this->initView();
		$this->view->baseUrl = $this->_request->getBaseUrl();
		Zend_Loader::loadClass('Profile');
		Zend_Loader::loadClass('User');
		Zend_Loader::loadClass('Periode');
		Zend_Loader::loadClass('KalenderAkd');
		Zend_Loader::loadClass('Perwalian');
		Zend_Loader::loadClass('Zend_Session');
		Zend_Loader::loadClass('Zend_Layout');
		$auth = Zend_Auth::getInstance();
		$ses_lec = new Zend_Session_Namespace('ses_lec');
		if (($auth->hasIdentity())and($ses_lec->uname)) {
			$this->view->namadsn =Zend_Auth::getInstance()->getIdentity()->nm_dosen;
			$this->view->kddsn=Zend_Auth::getInstance()->getIdentity()->kd_dosen;
			$this->view->kd_pt=$ses_lec->kd_pt;
			$this->view->nm_pt=$ses_lec->nm_pt;
			// global var
			$this->kd_dsn=Zend_Auth::getInstance()->getIdentity()->kd_dosen;
		}else{
			$this->_redirect('/');
		}
		// layout
		$this->_helper->layout()->setLayout('main');
		// navigation
		$this->_helper->navbar(0,0);
	}

	function indexAction()
	{
		// Title Browser
		$this->view->title = "Beranda Portal Dosen";
		// nav menu
		$this->view->home_act="active";
		// get profile pt
		$profil = new Profile();
		$getProfil = $profil->fetchAll();
		foreach ($getProfil as $dtProf){
			$this->view->nm_pt=$dtProf['nama_pt'];
			$this->view->kd_pt=$dtProf['kode_pt'];
			$this->view->alamat=$dtProf['alamat'].", ".$dtProf['kota'];
			$this->view->web=$dtProf['web'];
			$this->view->email=$dtProf['email'];
			$this->view->telp=$dtProf['telpon'];
			$this->view->fax=$dtProf['fax'];
			$this->view->visi=$dtProf['visi'];
			$this->view->misi=$dtProf['misi'];
			$this->view->nick=$dtProf['nickname'];
		}

		// get periode from tgl now
		$tgl = date('Y-m-d');
		$kd_periode="";
		$periode = new Periode();
		$getPeriode=$periode->getPeriodeByTgl($tgl);
		if($getPeriode){
			foreach ($getPeriode as $dtPeriode){
				$kd_periode=$dtPeriode['kd_periode'];
			}
		}else{
			$getPeriodeAktif=$periode->getPeriodeByStatus(0);
			foreach ($getPeriodeAktif as $dtPeriode) {
				$kd_periode=$dtPeriode['kd_periode'];;
			}
		}
		$this->view->per=$kd_periode;
		
		// kalender akademik
		$kalAkd=new KalenderAkd();
		$getKalender=$kalAkd->getKalenderAkdByPeriode($kd_periode);
		$this->view->listKalender=$getKalender;

		// get data perwalian
		$perwalian = new Perwalian();
		$getPerwalian=$perwalian->getPerwalianFeedByPeriodeDw($kd_periode, $this->kd_dsn);
		$this->view->listPerwalian=$getPerwalian;
	}

	function aksesAction()
	{
		// Title Browser
		$this->view->title = "Halaman Alih Portal Dosen";
	}
}