<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: Ruangan Controller -> Controller untuk modul halaman ruangan
*/
class RangenilaiController extends Zend_Controller_Action
{
	function init()
	{
		$this->initView();
		$this->view->baseUrl = $this->_request->getBaseUrl();
		Zend_Loader::loadClass('Profile');
		Zend_Loader::loadClass('User');
		Zend_Loader::loadClass('Menu');
		Zend_Loader::loadClass('RangeNilai');
		Zend_Loader::loadClass('Indeks');
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
		$this->view->active_tree="03";
		$this->view->active_menu="rangenilai/index";
	}
	
	function indexAction()
	{
		$user = new Menu();
		$menu = "rangenilai/index";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// Title Browser
			$this->view->title = "Daftar Range Nilai";
			// navigation
			$this->_helper->navbar(0,0,'rangenilai/new',0,0);
			//list range nilai
			$rangeNilai = new RangeNilai();
			$this->view->listRangeNilai=$rangeNilai->fetchAll();
		}
	}

	function newAction(){
		$user = new Menu();
		$menu = "rangenilai/new";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// Title Browser
			$this->view->title = "Input Data Range Nilai";
			// navigation
			$this->_helper->navbar(0,'rangenilai',0,0,0);
			// indeks
			$indeks = new Indeks();
			$this->view->listInd=$indeks->fetchAll();
		}
	}

	function editAction(){
		$user = new Menu();
		$menu = "rangenilai/edit";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// get param
			$id_range=$this->_request->get('id');
			// get data range
			$rangeNilai = new RangeNilai();
			$getRange=$rangeNilai->getRangeNilaiHDRById($id_range);
			if($getRange){
				foreach ($getRange as $dtRange) {
					$this->view->id = $dtRange['id_range_hdr'];
					$this->view->nm = $dtRange['nama_range'];
					$nm_range=$dtRange['nama_range'];
					$this->view->ind_t = $dtRange['id_index_tunda'];
				}
				// Title Browser
				$this->view->title = "Edit Data Range Nilai ".$nm_range;
			}else{
				$this->view->eksis="f";
				// Title Browser
				$this->view->title = "Edit Data Range Nilai";
			}
			// navigation
			$this->_helper->navbar('rangenilai',0,0,0,0);
			// indeks
			$indeks = new Indeks();
			$this->view->listInd=$indeks->fetchAll();
		}
	}
	
	function detilAction(){
		$user = new Menu();
		$menu = "rangenilai/new";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// get param
			$id_range=$this->_request->get('id');
			// get data range
			$rangeNilai = new RangeNilai();
			$getRange=$rangeNilai->getRangeNilaiHDRById($id_range);
			if($getRange){
				foreach ($getRange as $dtRange) {
					$this->view->id = $dtRange['id_range_hdr'];
					$this->view->nm = $dtRange['nama_range'];
					$nm_range=$dtRange['nama_range'];
					$this->view->ind_t = $dtRange['indeks_tunda'];
				}
				$getDtlRange = $rangeNilai->getRangeNilaiDTLByIdHdr($id_range);
				$this->view->listRangeDtl=$getDtlRange;
				// Title Browser
				$this->view->title = "Indeks Nilai Angka Range ".$nm_range;
			}else{
				$this->view->eksis="f";
				// Title Browser
				$this->view->title = "Indeks Nilai Angka";
			}
			// navigation
			$this->_helper->navbar('rangenilai',0,0,0,0);
			// indeks
			$indeks = new Indeks();
			$this->view->listInd=$indeks->fetchAll();
		}
	}
}