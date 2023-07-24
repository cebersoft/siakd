<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: PKRS TA Controller -> Controller untuk modul halaman PKRS TA
*/
class PkrstaController extends Zend_Controller_Action
{
	function init()
	{
		$this->initView();
		$this->view->baseUrl = $this->_request->getBaseUrl();
		Zend_Loader::loadClass('Profile');
		Zend_Loader::loadClass('User');
		Zend_Loader::loadClass('Menu');
		Zend_Loader::loadClass('Angkatan');
		Zend_Loader::loadClass('Prodi');
		Zend_Loader::loadClass('Mahasiswa');
		Zend_Loader::loadClass('Periode');
		Zend_Loader::loadClass('PaketkelasTA');
		Zend_Loader::loadClass('KuliahTA');
		Zend_Loader::loadClass('Register');
		Zend_Loader::loadClass('StatReg');
		Zend_Loader::loadClass('Zend_Session');
		Zend_Loader::loadClass('Zend_Layout');
		Zend_Loader::loadClass('Validation');
		$auth = Zend_Auth::getInstance();
		$ses_std = new Zend_Session_Namespace('ses_std');
		if (($auth->hasIdentity())and($ses_std->uname)) {
			$this->view->namamhs =Zend_Auth::getInstance()->getIdentity()->nm_mhs;
			$this->view->nim=Zend_Auth::getInstance()->getIdentity()->nim;
			$this->view->idmhs=Zend_Auth::getInstance()->getIdentity()->id_mhs;
			$this->view->kd_pt=$ses_std->kd_pt;
			$this->view->nm_pt=$ses_std->nm_pt;
			// global var
			$this->uname=Zend_Auth::getInstance()->getIdentity()->nim;
			$this->id=Zend_Auth::getInstance()->getIdentity()->id_mhs;
		}else{
			$this->_redirect('/');
		}
		// layout
		$this->_helper->layout()->setLayout('main');
		// nav menu
		$this->view->krs_act="active";
	}
	
	function indexAction()
	{
		// get nim periode
		$nim = $this->uname;
		$kd_periode = $this->_request->get('per');
		// get register
		$register = new Register();
		$getRegister = $register->getRegisterByNimPeriode($nim,$kd_periode);
		if($getRegister){
			foreach ($getRegister as $dtReg) {
				$this->view->nim=$dtReg['nim'];
				$this->view->nm_mhs=$dtReg['nm_mhs'];
				$this->view->akt=$dtReg['id_angkatan'];
				$this->view->prd=$dtReg['nm_prodi'];
				$this->view->dw=$dtReg['nm_dosen_wali'];
				$kd_prodi=$dtReg['kd_prodi'];
				$this->view->per=$dtReg['kd_periode'];
				$krs=$dtReg['krs'];
				$nm_mhs=$dtReg['nm_mhs'];
			}
			if($krs=='f'){
				// Title Browser
				$this->view->title = "PKRS Penambahan Mata Kuliah TA";
				$this->view->eksis="f";
			}else{
				// Title Browser
				$this->view->title = "PKRS Penambahan Mata Kuliah TA ".$nm_mhs;
				$paketkelasta = new PaketkelasTA();
				$this->view->listPaketkelasTA= $paketkelasta->getPaketKelasTAByPeriodeProdi($kd_periode,$kd_prodi);
				$kuliahTA = new KuliahTA();
				$getKuliahTA = $kuliahTA->getKuliahTAByNimPeriode($nim,$kd_periode);
				$this->view->listKuliahTA=$getKuliahTA;
				// periode akad aktif + periode TA sebelumnya
				$periode = new Periode();
				$this->view->listPerAktif = $periode->getPeriodeByStatus(0);
				$this->view->listPeriodeTA = $kuliahTA->getKuliahTAByNim($nim);
				// navigation
				$this->_helper->navbar("pkrs",0);
			}
		}else{
			// Title Browser
			$this->view->title = "PKRS Penambahan Mata Kuliah TA";
			$this->view->eksis="f";
			// navigation
			$this->_helper->navbar("register",0);
		}
	}
}