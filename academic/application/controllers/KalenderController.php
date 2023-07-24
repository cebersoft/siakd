<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: Kalender Controller -> Controller untuk modul halaman kalender
*/
class KalenderController extends Zend_Controller_Action
{
	function init()
	{
		$this->initView();
		$this->view->baseUrl = $this->_request->getBaseUrl();
		Zend_Loader::loadClass('Profile');
		Zend_Loader::loadClass('User');
		Zend_Loader::loadClass('Menu');
		Zend_Loader::loadClass('Periode');
		Zend_Loader::loadClass('AktivitasAkd');
		Zend_Loader::loadClass('KalenderAkd');
		Zend_Loader::loadClass('Zend_Session');
		Zend_Loader::loadClass('Zend_Layout');
		Zend_Loader::loadClass('Validation');
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
		// treeview
		$this->view->active_tree="02";
		$this->view->active_menu="periode/index";
	}
	
	function indexAction()
	{
		$user = new Menu();
		$menu = "kalender/index";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// get id
			$kd_periode=$this->_request->get('id');
			$periode = new Periode();
			$getPeriode = $periode->getPeriodeByKd($kd_periode);
			if($getPeriode){
				// Title Browser
				$this->view->title = "Daftar Kalender Akademik ".$kd_periode;
				// master aktv
				$aktAkd = new AktivitasAkd();
				$this->view->listAktAkd = $aktAkd->fetchAll();
				// get data kalender
				$kalender=new KalenderAkd();
				$getKalender=$kalender->getKalenderAkdByPeriode($kd_periode);
				$this->view->listKalender=$getKalender;
				$this->view->per=$kd_periode;
			}else{
				$this->view->eksis="f";
				// Title Browser
				$this->view->title = "Daftar Kalender Akademik";	
			}
			
			// navigation
			$this->_helper->navbar('periode',0,0,0,0);
		}
	}
	
	function editAction()
	{
		$user = new Menu();
		$menu = "kalender/edit";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// get param
			$kd_periode=$this->_request->get('per');
			$kd_aktivitas=$this->_request->get('akt');
			$kalender = new KalenderAkd();
			$getKalender = $kalender->getKalenderAkdByPeriodeAktivitas($kd_periode, $kd_aktivitas);
			if($getKalender){
				// Title Browser
				$this->view->title = "Edit Kalender Akademik ".$kd_periode;
				// get data kalender
				foreach ($getKalender as $dtKal) {
					$this->view->startdate=$dtKal['start_date_fmt'];
					$this->view->enddate=$dtKal['end_date_fmt'];
					$this->view->desk_akt=$dtKal['deskripsi'];
				}
				$this->view->per=$kd_periode;
				$this->view->kd_akt=$kd_aktivitas;
			}else{
				$this->view->eksis="f";
				// Title Browser
				$this->view->title = "Edit Kalender Akademik";	
			}
			// navigation
			$this->_helper->navbar('kalender/index?id='.$kd_periode,0,0,0,0);
		}
	}
}