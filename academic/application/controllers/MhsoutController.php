<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: mahasiswa out Controller -> Controller untuk modul halaman mahasiswa out
*/
class MhsoutController extends Zend_Controller_Action
{
	function init()
	{
		$this->initView();
		$this->view->baseUrl = $this->_request->getBaseUrl();
		Zend_Loader::loadClass('Profile');
		Zend_Loader::loadClass('User');
		Zend_Loader::loadClass('Menu');
		Zend_Loader::loadClass('JnsKeluar');
		Zend_Loader::loadClass('MhsOut');
		Zend_Loader::loadClass('Angkatan');
		Zend_Loader::loadClass('Prodi');
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
		$this->view->active_tree="10";
		$this->view->active_menu="mhsout/index";
	}
	
	function indexAction()
	{
		$user = new Menu();
		$menu = "mhsout/index";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// Title Browser
			$this->view->title = "Daftar Mahasiswa Keluar";
			// navigation
			$this->_helper->navbar(0,0,'mhsout/new',0,0);
			// destroy session param
			Zend_Session::namespaceUnset('param_mout');
			// get data angkatan
			$akt = new Angkatan();
			$this->view->listAkt=$akt->fetchAll();
			// get data prodi
			$prod = new Prodi();
			$this->view->listProdi=$prod->fetchAll();
		}
	}

	function listAction(){
		$user = new Menu();
		$menu = "mhsout/index";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// show data
			$param = new Zend_Session_Namespace('param_mout');
			$akt = $param->akt;
			$prd = $param->prd;
			$startdate = $param->startdate;
			$enddate = $param->enddate;
			// get data angkatan
			$angkatan = new Angkatan();
			$listAkt=$angkatan->fetchAll();
			if(!$akt){
				$this->view->akt="SEMUA";
			}else{
				$angk="";
				foreach ($listAkt as $dataAkt) {
					foreach ($akt as $dt) {
						if($dt==$dataAkt['id_angkatan']){
							$angk=$dataAkt['id_angkatan'].", ".$angk;
						}
					}
				}
				$this->view->akt=$angk;
			}
			// get data prodi
			$prod = new Prodi();
			$listProdi=$prod->fetchAll();
			if(!$prd){
				$this->view->prd="SEMUA";
			}else{
				$prod="";
				foreach ($listProdi as $dataPrd) {
					foreach ($prd as $dt) {
						if($dt==$dataPrd['kd_prodi']){
							$prod=$dataPrd['nm_prodi'].", ".$prod;
						}
					}
				}
				$this->view->prd=$prod;
			}
			$this->view->startdate=$startdate;
			$this->view->enddate=$enddate;
			// Title Browser
			$this->view->title = "Daftar Mahasiswa Keluar";
			// navigation
			$this->_helper->navbar('mhsout',0,'mhsout/new',0,0);
			// get data 
			$mhsout = new MhsOut();
			$this->view->listMhsOut = $mhsout->getMhsOutByAngkatanProdiTanggal($akt, $prd, $startdate, $enddate);
		}
	}

	function newAction(){
		$user = new Menu();
		$menu = "mhsout/new";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// Title Browser
			$this->view->title = "Input Mahasiswa Keluar";
			// navigation
			$this->_helper->navbar(0,'mhsout',0,0,0);
			// get data angkatan
			$akt = new Angkatan();
			$this->view->listAkt=$akt->fetchAll();
			// get data prodi
			$prod = new Prodi();
			$this->view->listProdi=$prod->fetchAll();
			// jns keluar
			$jnsKeluar=new JnsKeluar();
			$this->view->listJnsKeluar=$jnsKeluar->fetchAll();
		}
	}

	function editAction(){
		$user = new Menu();
		$menu = "mhsout/edit";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// get id
			$nim = $this->_request->get('nim');
			// get data 
			$mhsOut = new MhsOut();
			$getMhsOut = $mhsOut->getMhsOutByNim($nim);
			if($getMhsOut){
				foreach ($getMhsOut as $dtMhsOut) {
					$nm_mhs=$dtMhsOut['nm_mhs'];
					$this->view->akt=$dtMhsOut['id_angkatan'];
					$this->view->prd=$dtMhsOut['kd_prodi'];
					$this->view->nim=$dtMhsOut['nim'];
					$this->view->nm_mhs=$dtMhsOut['nm_mhs'];
					$this->view->jns_kel=$dtMhsOut['id_jns_keluar'];
					$this->view->tglout=$dtMhsOut['tgl_keluar_fmt'];
				}
				// Title Browser
				$this->view->title = "Edit Mahasiswa Keluar ".$nm_mhs;
				// get data angkatan
				$akt = new Angkatan();
				$this->view->listAkt=$akt->fetchAll();
				// get data prodi
				$prod = new Prodi();
				$this->view->listProdi=$prod->fetchAll();
				// jns keluar
				$jnsKeluar=new JnsKeluar();
				$this->view->listJnsKeluar=$jnsKeluar->fetchAll();
			}else{
				$this->view->eksis="f";
				// Title Browser
				$this->view->title = "Edit Mahasiswa Keluar";
			}
			// navigation
			$this->_helper->navbar('mhsout/list',0,0,0,0);
		}
	}
}