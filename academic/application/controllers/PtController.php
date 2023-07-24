<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: PT Controller -> Controller untuk modul profil PT
*/
class PtController extends Zend_Controller_Action
{
	function init()
	{
		$this->initView();
		$this->view->baseUrl = $this->_request->getBaseUrl();
		Zend_Loader::loadClass('Profile');
		Zend_Loader::loadClass('User');
		Zend_Loader::loadClass('Menu');
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
		// treeview
		$this->view->active_tree="00";
		$this->view->active_menu="pt/index";
	}

	function indexAction()
	{
		$user = new Menu();
		$menu = "pt/index";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// Title Browser
			$this->view->title = "Profil Perguruan Tinggi";
			// navigation
			$this->_helper->navbar(0,0,0,0,0);
			// get profile pt
			$profil = new Profile();
			$getProfil = $profil->fetchAll();
			foreach ($getProfil as $dtProf){
				$this->view->nm_pt=$dtProf['nama_pt'];
				$this->view->kd_pt=$dtProf['kode_pt'];
				$this->view->alamat=$dtProf['alamat'].", ".$dtProf['kota'];
				$this->view->web=$dtProf['web'];
				$this->view->email=$dtProf['email'];
				$this->view->telp=$dtProf['telpon'];
				$this->view->fax=$dtProf['fax'];
				$this->view->visi=$dtProf['visi'];
				$this->view->misi=$dtProf['misi'];
				$this->view->nick=$dtProf['nickname'];
			}
		}
	}
	
	function editAction()
	{
		$user = new Menu();
		$menu = "pt/edit";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// Title Browser
			$this->view->title = "Edit Profil Perguruan Tinggi";
			// navigation
			$this->_helper->navbar('pt',0,0,0,0);
			// get profile pt
			$profil = new Profile();
			$getProfil = $profil->fetchAll();
			foreach ($getProfil as $dtProf){
				$this->view->nm_pt=$dtProf['nama_pt'];
				$this->view->kd_pt=$dtProf['kode_pt'];
				$this->view->alamat=$dtProf['alamat'];
				$this->view->kota=$dtProf['kota'];
				$this->view->web=$dtProf['web'];
				$this->view->email=$dtProf['email'];
				$this->view->telp=$dtProf['telpon'];
				$this->view->fax=$dtProf['fax'];
				$this->view->visi=$dtProf['visi'];
				$this->view->misi=$dtProf['misi'];
				$this->view->nick=$dtProf['nickname'];
			}
		}
	}
}