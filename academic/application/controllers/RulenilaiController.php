<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: Rule Nilai Controller -> Controller untuk modul halaman aturan nilai
*/
class RulenilaiController extends Zend_Controller_Action
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
		Zend_Loader::loadClass('AturanNilai');
		Zend_Loader::loadClass('RangeNilai');
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
		$this->view->active_menu="rulenilai/index";
	}
	
	function indexAction()
	{
		$user = new Menu();
		$menu = "rulenilai/index";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// Title Browser
			$this->view->title = "Aturan Nilai Per Program Studi";
			// navigation
			$this->_helper->navbar(0,0,0,0,0);
			// prodi
			$prodi = new Prodi();
			$this->view->listProdi=$prodi->fetchAll();
		}
	}

	function listAction(){
		$user = new Menu();
		$menu = "rulenilai/index";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// get param
			$kd=$this->_request->get('kd');
			// get data Prodi
			$prodi = new Prodi();
			$getProdi=$prodi->getProdiByKd($kd);
			if($getProdi){
				foreach ($getProdi as $dtProdi) {
					$this->view->kd = $dtProdi['kd_prodi'];
					$this->view->nm = $dtProdi['nm_prodi'];
					$nm_prodi=$dtProdi['nm_prodi'];
					$this->view->jenjang = $dtProdi['id_jenjang_prodi'];
					$jenjang = $dtProdi['nama_jenjang'];
				}
				// get rule nilai
				$ruleNilai = new AturanNilai();
				$this->view->listRuleNilai = $ruleNilai->getAturanNilaiByProdi($kd);
				// Title Browser
				$this->view->title = "Aturan Nilai Program Studi ".$nm_prodi." ( ".$jenjang." )";
			}else{
				$this->view->eksis="f";
				// Title Browser
				$this->view->title = "Aturan Nilai Program Studi";
			}
			
			// navigation
			$this->_helper->navbar('rulenilai',0,0,0,0);
			// get range nilai
			$rangeNilai=new RangeNilai();
			$this->view->listRange = $rangeNilai->fetchAll();
			// periode
			$periode = new Periode();
			$this->view->listPeriode = $periode->fetchAll();
		}
	}
}