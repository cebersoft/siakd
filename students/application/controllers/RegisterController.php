<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: Register Controller -> Controller untuk modul halaman regstrasi
*/
class RegisterController extends Zend_Controller_Action
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
		Zend_Loader::loadClass('Register');
		Zend_Loader::loadClass('StatReg');
		Zend_Loader::loadClass('KalenderAkd');
		Zend_Loader::loadClass('Zend_Session');
		Zend_Loader::loadClass('Zend_Layout');
		Zend_Loader::loadClass('Validation');
		//--- keuangan
		Zend_Loader::loadClass('PaketBiaya');
		Zend_Loader::loadClass('Biaya');
		Zend_Loader::loadClass('FormulaBiaya');
		Zend_Loader::loadClass('FormulaBiayaTA');
		Zend_Loader::loadClass('Mahasiswa');
		Zend_Loader::loadClass('MhsGelombang');
		Zend_Loader::loadClass('MhsRegPeriode');
		Zend_Loader::loadClass('MhsBiayaPeriode');
		Zend_Loader::loadClass('Sumbangan');
		Zend_Loader::loadClass('Konversi');
		Zend_Loader::loadClass('Bayar');
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
			$nim=Zend_Auth::getInstance()->getIdentity()->nim;
		}else{
			$this->_redirect('/');
		}
		// cek status keuangan
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
		// get data biaya periodik mahasiswa
		$mhsBiayaPer=new MhsBiayaPeriode();
		$getBiayaPeriode=$mhsBiayaPer->getMhsBiayaPeriodeByNim($nim);
		$totBiaya=0;
		$i=1;
		foreach ($getBiayaPeriode as $dtMhsReg){
			if($dtMhsReg['kd_periode']!=$kd_periode){
				$totBiaya=$totBiaya+$dtMhsReg['tot_biaya']-$dtMhsReg['tot_kompensasi'];
			}
		}
		// get data pembayaran
		$bayar = new Bayar();
		$listBayar=$bayar->getBayarDtlByNim($nim);
		$totBayarPer=0;
		foreach ($listBayar as $dtBayar){
			if(($dtBayar['id_term']=='1')or($dtBayar['id_term']=='2')){
				if($dtBayar['kd_periode_alocate']!=$kd_periode){
					$totBayarPer=$totBayarPer+$dtBayar['nominal_alocate'];
				}
			}
		}
		$tunggakan=$totBiaya-$totBayarPer;
		if($tunggakan>0){
			$this->_redirect('/home/keuangan');
		}
		$this->totBayarPer=$totBayarPer;
		$this->totBiaya=$totBiaya;
		$this->tunggakan=$tunggakan;
		// layout
		$this->_helper->layout()->setLayout('main');
		// nav menu
		$this->view->krs_act="active";
	}

	function indexAction(){
		// Title Browser
		$this->view->title = "Her-Registrasi Akademik";
		// navigation
		$this->_helper->navbar(0,0);
		// get data 
		$nim=$this->uname;
		// stat reg
		$statReg = new StatReg();
		$this->view->listStatReg = $statReg->fetchAll();
		// register
		$register = new Register();
		$getRegister = $register->getRegisterByNim($nim);
		$this->view->listRegister = $getRegister;
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
		$kd_aktivitas='101';
		// jadwal krs/her registrasi
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
	}
}