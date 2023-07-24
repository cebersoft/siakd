<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: Perwalian Controller -> Controller untuk modul halaman perwalian
*/
class PerwalianController extends Zend_Controller_Action
{
	function init()
	{
		$this->initView();
		$this->view->baseUrl = $this->_request->getBaseUrl();
		Zend_Loader::loadClass('Profile');
		Zend_Loader::loadClass('User');
		Zend_Loader::loadClass('Menu');
		Zend_Loader::loadClass('Mahasiswa');
		Zend_Loader::loadClass('Periode');
		Zend_Loader::loadClass('Perwalian');
		Zend_Loader::loadClass('KalenderAkd');
		Zend_Loader::loadClass('Zend_Session');
		Zend_Loader::loadClass('Zend_Layout');
		Zend_Loader::loadClass('Validation');
		$auth = Zend_Auth::getInstance();
		$ses_std = new Zend_Session_Namespace('ses_std');
		if (($auth->hasIdentity())and($ses_std->uname)) {
			$this->view->namamhs =Zend_Auth::getInstance()->getIdentity()->nm_mhs;
			$this->view->nim=Zend_Auth::getInstance()->getIdentity()->nim;
			$this->view->idmhs=Zend_Auth::getInstance()->getIdentity()->id_mhs;
			$this->view->kd_pt=$ses_std->kd_pt;
			$this->view->nm_pt=$ses_std->nm_pt;
			// global var
			$this->uname=Zend_Auth::getInstance()->getIdentity()->nim;
			$this->id=Zend_Auth::getInstance()->getIdentity()->id_mhs;
		}else{
			$this->_redirect('/');
		}
		// layout
		$this->_helper->layout()->setLayout('main');
		// nav menu
		$this->view->krs_act="active";
	}

	function indexAction(){
		// Title Browser
		$this->view->title = "Feedback Perwalian";
		// navigation
		$this->_helper->navbar(0,0);
		// get data 
		$nim=$this->uname;
		// get data mhs
		$mhs=new Mahasiswa();
		$getMhs=$mhs->getMahasiswaByNim($nim);
		foreach ($getMhs as $dtMhs){
			$this->view->nm_mhs=$dtMhs['nm_mhs'];
			$this->view->dw=$dtMhs['nm_dosen_wali'];
			$this->view->kd_dw=$dtMhs['kd_dosen_wali'];
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
		$kd_aktivitas='103';
		// jadwal krs/her registrasi/perwalian
		$kalender=new KalenderAkd();
		$getKalender=$kalender->getKalenderAkdByPeriodeAktivitas($kd_periode, $kd_aktivitas);
		$this->view->allowReg="";
		if ($getKalender){
			// cek tanggal
			foreach ($getKalender as $dtKalender) {
				$startDate=$dtKalender['start_date'];
				$endDate=$dtKalender['end_date'];
			}
			if(($tgl>=$startDate)and($tgl<=$endDate)){
				$this->view->allowReg=1;
			}else{
				$this->view->allowReg=-1;
			}
		}else{
			$this->view->allowReg=0;
		}
		// get data perwalian
		$perwalian = new Perwalian();
		$getPerwalian=$perwalian->getPerwalianFeedByPeriodeNim($kd_periode, $nim);
		$this->view->listPerwalian=$getPerwalian;
	}
}