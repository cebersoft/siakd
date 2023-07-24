<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: Periode Controller -> Controller untuk modul halaman periode akademik
*/
class PeriodeController extends Zend_Controller_Action
{
	function init()
	{
		$this->initView();
		$this->view->baseUrl = $this->_request->getBaseUrl();
		Zend_Loader::loadClass('Profile');
		Zend_Loader::loadClass('User');
		Zend_Loader::loadClass('Menu');
		Zend_Loader::loadClass('Periode');
		Zend_Loader::loadClass('StatPeriode');
		Zend_Loader::loadClass('SmtPeriode');
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
		$menu = "periode/index";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// Title Browser
			$this->view->title = "Daftar Periode Akademik";
			// navigation
			$this->_helper->navbar(0,0,'periode/new',0,0);
			$periode = new Periode();
			$this->view->listPeriode = $periode->fetchAll();
		}
	}

	function newAction()
	{
		$user = new Menu();
		$menu = "periode/new";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// Title Browser
			$this->view->title = "Input Periode Akademik";
			// navigation
			$this->_helper->navbar(0,'periode',0,0,0);
			$statperiode = new StatPeriode();
			$this->view->listStatPeriode = $statperiode->fetchAll();
			$smtperiode = new SmtPeriode();
			$this->view->listSmtPeriode = $smtperiode->fetchAll();
		}
	}

	function editAction()
	{
		$user = new Menu();
		$menu = "periode/edit";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// get
			$kd=$this->_request->get('id');
			$periode = new Periode();
			$getPeriode = $periode->getPeriodeByKd($kd);
			if($getPeriode){
				foreach ($getPeriode as $dataPeriode) {
					$tgl_awal = $dataPeriode['tanggal_mulai_fmt'];
					$tgl_akhir = $dataPeriode['tanggal_selesai_fmt'];
					$this->view->kd_per = $dataPeriode['kd_periode'];
				}
				$this->view->rangeTgl = $tgl_awal." - ".$tgl_akhir;
				// Title Browser
				$this->view->title = "Edit Periode Akademik ".$kd;
			}else{
				$this->view->eksis="f";
				// Title Browser
				$this->view->title = "Edit Periode Akademik";
			}
			// navigation
			$this->_helper->navbar('periode',0,0,0,0);
		}
	}
}