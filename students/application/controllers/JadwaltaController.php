<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: jadwal Ta Controller -> Controller untuk modul jadwal TA
*/
class JadwaltaController extends Zend_Controller_Action
{
	function init()
	{
		$this->initView();
		$this->view->baseUrl = $this->_request->getBaseUrl();
		Zend_Loader::loadClass('KuliahTA');
		Zend_Loader::loadClass('Register');
		Zend_Loader::loadClass('Periode');
		Zend_Loader::loadClass('Paketkelas');
		Zend_Loader::loadClass('PaketkelasTA');
		Zend_Loader::loadClass('Absensi');
		Zend_Loader::loadClass('Profile');
		Zend_Loader::loadClass('User');
		Zend_Loader::loadClass('Zend_Session');
		Zend_Loader::loadClass('Zend_Layout');
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
			$this->nm_pt=$ses_std->nm_pt;
		}else{
			$this->_redirect('/');
		}
		// layout
		$this->_helper->layout()->setLayout('main');
		// nav menu
		$this->view->ta_act="active";
	}

	function indexAction()
	{
		// get param
		$kd_periode=$this->_request->get('id');
		$periode = new Periode();
		if(!$kd_periode){
			// get periode aktif
			$getPeriodeAktif=$periode->getPeriodeByStatus(0);
			if($getPeriodeAktif){
				foreach ($getPeriodeAktif as $dtPeriode) {
					$kd_periode=$dtPeriode['kd_periode'];
				}
			}else{
				$kd_periode="";
			}
		}
		// periode akademik
		$getPeriode = $periode->getPeriodeByKd($kd_periode);
		// navigation
		$this->_helper->navbar(0,0);
		if($getPeriode){
			// Title Browser
			$this->view->title = "Jadwal dan Penguji TA ".$kd_periode;
			$nim=$this->uname;
			// get data krs approved/nilai
			$kuliah = new KuliahTA();
			$getKuliah=$kuliah->getKuliahTAByNimPeriode($nim, $kd_periode);
			// get periode register mhs
			$register=new Register();
			$getRegister=$register->getRegisterByNim($nim);
			$arrPer=array();
			if($getRegister){
				$i=0;
				foreach ($getRegister as $dtRegister){
					$arrPer[$i]['kd_periode']=$dtRegister['kd_periode'];
					$i++;
				}
			}
			$this->view->per=$kd_periode;
			$this->view->listPer=$arrPer;
			$this->view->listKuliah=$getKuliah;
		}else{
			$this->view->eksis="f";
			// Title Browser
			$this->view->title = "Jadwal dan Penguji TA";	
		}
	}
	
}