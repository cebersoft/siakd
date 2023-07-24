<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: Kurikulum Controller -> Controller untuk modul halaman kurikulum
*/
class KurikulumController extends Zend_Controller_Action
{
	function init()
	{
		$this->initView();
		$this->view->baseUrl = $this->_request->getBaseUrl();
		Zend_Loader::loadClass('Profile');
		Zend_Loader::loadClass('User');
		Zend_Loader::loadClass('Menu');
		Zend_Loader::loadClass('Prodi');
		Zend_Loader::loadClass('Periode');
		Zend_Loader::loadClass('Kurikulum');
		Zend_Loader::loadClass('MatkulKurikulum');
		Zend_Loader::loadClass('KatMatkul');
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
		$this->view->active_menu="kurikulum/index";
	}
	
	function indexAction()
	{
		$user = new Menu();
		$menu = "kurikulum/index";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// Title Browser
			$this->view->title = "Daftar Kurikulum";
			// navigation
			$this->_helper->navbar(0,0,'kurikulum/new',0,0);
			// destroy session param
			Zend_Session::namespaceUnset('param_kur');
			// get data prodi
			$prodi = new Prodi();
			$this->view->listProdi=$prodi->fetchAll();
		}
	}

	function listAction(){
		$user = new Menu();
		$menu = "kurikulum/index";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// show data
			$param = new Zend_Session_Namespace('param_kur');
			$prd = $param->prd;
			// Title Browser
			$this->view->title = "Daftar Kurikulum";
			// navigation
			$this->_helper->navbar('kurikulum',0,'kurikulum/new',0,0);
			// get data 
			$kurikulum = new Kurikulum();
			$getKur = $kurikulum->getKurByProdi($prd);
			$this->view->listKur = $getKur;
			// get data prodi
			$prod = new Prodi();
			$listProdi=$prod->fetchAll();
			if(!$prd){
				$this->view->prd="SEMUA";
			}else{
				$v_prd="";
				foreach ($listProdi as $dataPrd) {
					foreach ($prd as $dt) {
						if($dt==$dataPrd['kd_prodi']){
							$v_prd=$dataPrd['nm_prodi'].", ".$v_prd;
						}
					}
				}
				$this->view->prd=$v_prd;
			}
		}
	}

	function newAction(){
		$user = new Menu();
		$menu = "kurikulum/new";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// navigation
			$this->_helper->navbar(0,'kurikulum',0,0,0);	
			// Title Browser
			$this->view->title = "Input Kurikulum Baru";
			$prod = new Prodi();
			$this->view->listProdi=$prod->fetchAll();
			$periode = new Periode();
			$this->view->listPeriode = $periode->fetchAll();
		}
	}

	function editAction(){
		$user = new Menu();
		$menu = "kurikulum/edit";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// get kd
			$id=$this->_request->get('id');
			// ref
			$prod = new Prodi();
			$this->view->listProdi=$prod->fetchAll();
			$periode = new Periode();
			$this->view->listPeriode = $periode->fetchAll();
			// get data from data base
			$kur=new Kurikulum();
			$getKur=$kur->getKurById($id);
			$i=0;
			foreach ($getKur as $data_kur) {
				$this->view->id_kurikulum=$data_kur['id_kurikulum'];
				$this->view->kd_kurikulum=$data_kur['kd_kurikulum'];
				$this->view->nm_kurikulum=$data_kur['nm_kurikulum'];
				$this->view->kd_prodi=$data_kur['kd_prodi'];
				$this->view->kd_periode=$data_kur['kd_periode_berlaku'];
				$this->view->smt_normal=$data_kur['smt_normal'];
				$this->view->sks_l=$data_kur['sks_lulus'];
				$this->view->sks_w=$data_kur['sks_wajib'];
				$this->view->sks_p=$data_kur['sks_pilihan'];
				$nm_kurikulum=$data_kur['nm_kurikulum'];
				$i++;
			}
			// navigation
			$this->_helper->navbar('kurikulum/list',0,0,0,0);
			// Title Browser
			if ($i>0){
				$this->view->title = "Edit Data Kurikulum : ".$nm_kurikulum;
			}else{
				$this->view->title = "Edit Data Kurikulum";
				$this->view->eksis ='f';
			}
		}
	}

	function detilAction(){
		$user = new Menu();
		$menu = "kurikulum/detil";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// get kd
			$id=$this->_request->get('id');
			// kat matkul
			$katMatkul = new KatMatkul();
			$this->view->listKatMatkul=$katMatkul->fetchAll();
			// get data from data base
			$kur=new Kurikulum();
			$getKur=$kur->getKurById($id);
			$i=0;
			foreach ($getKur as $data_kur) {
				$this->view->id_kurikulum=$data_kur['id_kurikulum'];
				$this->view->kd_kurikulum=$data_kur['kd_kurikulum'];
				$this->view->nm_kurikulum=$data_kur['nm_kurikulum'];
				$this->view->nm_prodi=$data_kur['nm_prodi'];
				$this->view->kd_periode=$data_kur['kd_periode_berlaku'];
				$this->view->smt_normal=$data_kur['smt_normal'];
				$this->view->sks_l=$data_kur['sks_lulus'];
				$this->view->sks_w=$data_kur['sks_wajib'];
				$this->view->sks_p=$data_kur['sks_pilihan'];
				$nm_kurikulum=$data_kur['nm_kurikulum'];
				$i++;
			}
			// navigation
			$this->_helper->navbar('kurikulum/list',0,0,0,0);
			// Title Browser
			if ($i>0){
				$this->view->title = "Detil Kurikulum : ".$nm_kurikulum;
				// get data mata kuliah
				$matkulKur = new MatkulKurikulum();
				$getMatkulKur = $matkulKur->getMatkulByKurikulum($id);
				$this->view->listMatkulKur=$getMatkulKur;
			}else{
				$this->view->title = "Detil Kurikulum";
				$this->view->eksis ='f';
			}
		}
	}
}