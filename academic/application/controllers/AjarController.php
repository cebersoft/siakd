<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: Ajar Controller -> Controller untuk modul halaman ajar dosen
*/
class AjarController extends Zend_Controller_Action
{
	function init()
	{
		$this->initView();
		$this->view->baseUrl = $this->_request->getBaseUrl();
		Zend_Loader::loadClass('Profile');
		Zend_Loader::loadClass('User');
		Zend_Loader::loadClass('Menu');
		Zend_Loader::loadClass('Dosen');
		Zend_Loader::loadClass('Prodi');
		Zend_Loader::loadClass('Ajar');
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
		$this->view->active_tree="01";
		$this->view->active_menu="dosen/index";
	}
	
	function indexAction(){
		$user = new Menu();
		$menu = "ajar/index";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// get data dosen
			$kd_dsn=$this->_request->get('kd');
			$this->view->kd_dsn=$kd_dsn;
			$dosen = new Dosen();
			$getDosen = $dosen->getDosenByKd($kd_dsn);
			if($getDosen){
				foreach ($getDosen as $data) {
					$nm_dosen=$data['nm_dosen'];
				}
				// Title Browser
				$this->view->title = "Daftar Ajar Dosen ".$nm_dosen;
			}else{
				// Title Browser
				$this->view->title = "Daftar Ajar Dosen";
				$this->view->eksis="f";
			}
			// navigation
			$this->_helper->navbar('dosen/list',0,0,0,0);
			// get data prodi
			$prodi = new Prodi();
			$this->view->listProdi=$prodi->fetchAll();
			// get data ajar
			$ajar = new Ajar();
			$getAjar = $ajar->getAjarByDosen($kd_dsn);
			$this->view->listAjar = $getAjar;
			$arr_prd = array();
			foreach ($getAjar as $dtAjar) {
				$arr_prd[] = array('kd_prd' => $dtAjar['kd_prodi_kur'], 'nm_prd'=>  $dtAjar['nm_prodi_kur']);
			}
			$this->view->arr_prd=array_unique($arr_prd);
		}
	}
}