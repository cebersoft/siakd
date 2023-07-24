<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: Dosbim Controller -> Controller untuk modul halaman dosen pembimbing
*/
class DosbimController extends Zend_Controller_Action
{
	function init()
	{
		$this->initView();
		$this->view->baseUrl = $this->_request->getBaseUrl();
		Zend_Loader::loadClass('Profile');
		Zend_Loader::loadClass('User');
		Zend_Loader::loadClass('Menu');
		Zend_Loader::loadClass('Periode');
		Zend_Loader::loadClass('Prodi');
		Zend_Loader::loadClass('Dosbim');
		Zend_Loader::loadClass('Dosji');
		Zend_Loader::loadClass('KuliahTA');
		Zend_Loader::loadClass('Dosen');
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
		$this->view->active_tree="09";
		$this->view->active_menu="dosbim/index";
	}
	
	function indexAction(){
		$user = new Menu();
		$menu = "dosbim/index";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// Title Browser
			$this->view->title = "Daftar Dosen Pembimbing dan Penguji";
			// navigation
			$this->_helper->navbar(0,0,0,0,0);
			// destroy session param
			Zend_Session::namespaceUnset('param_dsbm');
			Zend_Session::namespaceUnset('param_dsbm2');
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
	}

	function listAction(){
		$user = new Menu();
		$menu = "dosbim/index";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// show data
			$param = new Zend_Session_Namespace('param_dsbm');
			$kd_prodi = $param->prd;
			$kd_periode = $param->per;
			// get data prodi
			$nm_prd="";
			$prodi = new Prodi();
			$listProdi=$prodi->fetchAll();
			foreach ($listProdi as $dataPrd) {
				if($kd_prodi==$dataPrd['kd_prodi']){
					$nm_prd=$dataPrd['nm_prodi'];
					$kd_prd=$dataPrd['kd_prodi'];
				}
			}
			// Title Browser
			$this->view->title = "Daftar Dosen Pembimbing Prodi ".$nm_prd." Periode ".$kd_periode;
			$this->view->kd_periode=$kd_periode;
			$this->view->kd_prodi=$kd_prd;
			$this->view->nm_prodi=$nm_prd;
			// navigation
			$this->_helper->navbar('dosbim',0,0,0,0);
			// get data 
			$dosbim = new Dosbim();
			$getDosbim = $dosbim->getDosbimByPeriode($kd_periode,$kd_prodi);
			$this->view->listDosbim= $getDosbim;
		}
	}

	function list2Action(){
		$user = new Menu();
		$menu = "dosbim/index";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// show data
			$param = new Zend_Session_Namespace('param_dsbm2');
			$kd_prodi = $param->prd;
			$kd_periode = $param->per;
			// get data prodi
			$nm_prd="";
			$prodi = new Prodi();
			$listProdi=$prodi->fetchAll();
			foreach ($listProdi as $dataPrd) {
				if($kd_prodi==$dataPrd['kd_prodi']){
					$nm_prd=$dataPrd['nm_prodi'];
					$kd_prd=$dataPrd['kd_prodi'];
				}
			}
			// Title Browser
			$this->view->title = "Daftar Dosen Penguji Prodi ".$nm_prd." Periode ".$kd_periode;
			$this->view->kd_periode=$kd_periode;
			$this->view->kd_prodi=$kd_prd;
			$this->view->nm_prodi=$nm_prd;
			// navigation
			$this->_helper->navbar('dosbim',0,0,0,0);
			// get data 
			$dosji = new Dosji();
			$getDosji = $dosji->getDosjiByPeriode($kd_periode,$kd_prodi);
			$this->view->listDosji= $getDosji;
		}
	}

	function detilAction(){
		$user = new Menu();
		$menu = "dosbim/index";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			$kd_dosen=$this->_request->get('kd');
			$kd_periode=$this->_request->get('per');
			$kd_prodi=$this->_request->get('prd');
			$dosen=new Dosen();
			$getDosen=$dosen->getDosenByKd($kd_dosen);
			$nm_dosen="";
			foreach($getDosen as $dtDosen){
				$nm_dosen=$dtDosen['nm_dosen'];
			}
			// Title Browser
			$this->view->title = "Daftar Mahasiswa Dibimbing ".$nm_dosen." Periode ".$kd_periode;
			// navigation
			$this->_helper->navbar('dosbim/list',0,0,0,0);
			// get data 
			$kuliahTa = new KuliahTA();
			$getKuliahTa = $kuliahTa->getKuliahTAByDosbimPeriodeProdi($kd_dosen,$kd_periode,$kd_prodi);
			$this->view->listKuliahTa= $getKuliahTa;
		}
	}

	function detil2Action(){
		$user = new Menu();
		$menu = "dosbim/index";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			$kd_dosen=$this->_request->get('kd');
			$kd_periode=$this->_request->get('per');
			$kd_prodi=$this->_request->get('prd');
			$dosen=new Dosen();
			$getDosen=$dosen->getDosenByKd($kd_dosen);
			$nm_dosen="";
			foreach($getDosen as $dtDosen){
				$nm_dosen=$dtDosen['nm_dosen'];
			}
			// Title Browser
			$this->view->title = "Daftar Mahasiswa Diuji ".$nm_dosen." Periode ".$kd_periode;
			// navigation
			$this->_helper->navbar('dosbim/list2',0,0,0,0);
			// get data 
			$kuliahTa = new KuliahTA();
			$getKuliahTa = $kuliahTa->getKuliahTAByDosjiPeriodeProdi($kd_dosen,$kd_periode,$kd_prodi);
			$this->view->listKuliahTa= $getKuliahTa;
		}
	}
}