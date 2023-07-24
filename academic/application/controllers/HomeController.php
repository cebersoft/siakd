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
		Zend_Loader::loadClass('Mahasiswa');
		Zend_Loader::loadClass('Dosen');
		Zend_Loader::loadClass('Paketkelas');
		Zend_Loader::loadClass('PaketkelasTA');
		Zend_Loader::loadClass('Report');
		Zend_Loader::loadClass('Periode');
		Zend_Loader::loadClass('Prodi');
		Zend_Loader::loadClass('KalenderAkd');
		Zend_Loader::loadClass('Zend_Session');
		Zend_Loader::loadClass('Zend_Layout');
		$auth = Zend_Auth::getInstance();
		$ses_ac = new Zend_Session_Namespace('ses_ac');
		$ses_menu = new Zend_Session_Namespace('ses_menu');
		if (($auth->hasIdentity())and($ses_ac->uname)) {
			$this->view->namauser =Zend_Auth::getInstance()->getIdentity()->nama;
			$this->view->username=Zend_Auth::getInstance()->getIdentity()->username;
			$this->view->kd_pt=$ses_ac->kd_pt;
			$this->view->nm_pt=$ses_ac->nm_pt;
			$this->view->menu=$ses_menu->menu;
		}else{
			$this->_redirect('/');
		}
		// layout
		$this->_helper->layout()->setLayout('main');
	}

	function indexAction()
	{
		// Title Browser
		$this->view->title = "Beranda SI.Akademik";
		// navigation
		$this->_helper->navbar(0,0,0,0,0);
		// get user menu
		$ses_ac = new Zend_Session_Namespace('ses_ac');
		$username=$ses_ac->uname;
		$user = new User();
		$this->view->listMenu=$user->getMenuAcByUname($username);
		// Periode Aktif
		$periode=new Periode();
		$getPeriode=$periode->getPeriodeByStatus(0);
		$kd_periode="";
		foreach ($getPeriode as $dtPeriode) {
			$kd_periode=$dtPeriode['kd_periode'];
			$this->view->perAktif=$dtPeriode['kd_periode'];
			$perAktif=$dtPeriode['kd_periode'];
			$this->view->tglPer=$dtPeriode['tanggal_mulai_fmt']." s/d ".$dtPeriode['tanggal_selesai_fmt'];
		}
		// kalender akademik
		$kalAkd=new KalenderAkd();
		$getKalender=$kalAkd->fetchAll();
		$dataKalender=array();
		$i=0;
		$events = array();
		foreach ($getKalender as $dtKal){
			$e = array();
			$e['title'] = $dtKal['deskripsi'];
			$e['start'] = $dtKal['start_date']." 00:00:00";
			$e['end'] = $dtKal['end_date']." 00:00:00";
			$e['allDay'] = true;
			$e['backgroundColor']= "#f39c12";
          	$e['borderColor']= "#f39c12";
    		array_push($events, $e);
    	}
		$this->view->listKal=json_encode($events);
		// summary report
		// prodi
		$prodi=new Prodi();
		$getProdi=$prodi->fetchAll();
		// mhs
		$mhs = new Mahasiswa();
		$angkatan=array();
		$kd_prodi=array();
		$status=array('A');
		$getMhsAktif=$mhs->getMahasiswaByAngkatanProdiStatus($angkatan, $kd_prodi, $status);
		$this->view->nMhsA=count($getMhsAktif);
		// dosen
		$dsn=new Dosen();
		$getDsnAktif=$dsn->getDosenByStatus(true);
		$this->view->nDsnA=count($getDsnAktif);
		// paket kelas
		$pkls=new Paketkelas();
		$getPkls=$pkls->getPaketKelasByPeriode($kd_periode);
		$nPkls=count($getPkls);
		$pklsTa=new PaketkelasTA();
		$nPklsTa=0;
		foreach ($getProdi as $dtProdi){
			$getPklsTa=$pklsTa->getPaketKelasTAByPeriodeProdi($kd_periode, $dtProdi['kd_prodi']);
			$iPklsTa=count($getPklsTa);
			$nPklsTa=$nPklsTa+$iPklsTa;
		}
		$this->view->nPklsAll=$nPkls+$nPklsTa;
		// kuliah/krs
		$rep=new Report();
		$getTabelData=$rep->getTabel('mhs_kul');
		$arrTabelData=explode("||", $getTabelData);
		// where data
		$whereD[0]['key'] = 'kd_periode';
		$whereD[0]['param'] = array($kd_periode);
		$whereD[1]['key'] = 'approved';
		$whereD[1]['param'] = array(true);
		//--
		$arrKolomD=array('kd_periode');
		$getReportKul= $rep->get($arrTabelData[0], $arrKolomD, $arrKolomD, $arrKolomD,$whereD);
		foreach ($getReportKul as $dtKul){
			$nKul=$dtKul['n'];
		}
		$this->view->nKuliah=$nKul;
	}

	function aksesAction()
	{
		// Title Browser
		$this->view->title = "Halaman Alih SI.Akademik";
		$this->view->desc = "Tidak ada akses untuk user";
		// navigation
		$this->_helper->navbar('home',0,0,0,0);
	}
}