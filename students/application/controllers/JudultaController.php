<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: Judul TA Controller -> Controller untuk modul halaman judul ta
*/
class JudultaController extends Zend_Controller_Action
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
		Zend_Loader::loadClass('Register');
		Zend_Loader::loadClass('KuliahTA');
		Zend_Loader::loadClass('JudulTA');
		Zend_Loader::loadClass('Zend_Session');
		Zend_Loader::loadClass('Zend_Layout');
		Zend_Loader::loadClass('Validation');
		Zend_Loader::loadClass('PHPExcel');
		Zend_Loader::loadClass('PHPExcel_Cell_AdvancedValueBinder');
		Zend_Loader::loadClass('PHPExcel_IOFactory');
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
		$this->view->ta_act="active";
	}
	
	function indexAction(){
		// layout
		$this->_helper->layout()->setLayout('main');
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
			$this->view->title = "Judul Tugas Akhir ".$kd_periode;
			$nim=$this->uname;
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
			$this->view->listPer=$arrPer;
			$this->view->per=$kd_periode;
			// get data nilai ta
			$kuliahTa=new KuliahTA();
			$getKuliahTa=$kuliahTa->getKuliahTAByNimPeriode($this->uname,$kd_periode);
			$this->view->listKuliahTa=$getKuliahTa;
		}else{
			$this->view->eksis="f";
			// Title Browser
			$this->view->title = "Judul Tugas Akhir";
		}
	}

	function newAction(){
		// layout
		$this->_helper->layout()->setLayout('main');
		// get param
		$kd_kuliah=$this->_request->get('id');
		$kuliahTa=new KuliahTA();
		$getKuliahTa=$kuliahTa->getKuliahTAByKd($kd_kuliah);
		// navigation
		$this->_helper->navbar('judulta',0);
		if($getKuliahTa){
			$nm_mk="";
			foreach($getKuliahTa as $dtKuliahTa){
				$nm_mk=$dtKuliahTa['nm_mk'];
			}
			// Title Browser
			$this->view->title = "Pengajuan Judul Tugas Akhir Mata Kuliah ".$nm_mk;
			$this->view->kd_kuliah=$kd_kuliah;
			$this->view->nm_mk=$nm_mk;
			$judulTa=new JudulTA();
			$this->view->listJudul=$judulTa->getJudulTAByKdKuliah($kd_kuliah);
		}else{
			$this->view->eksis="f";
			// Title Browser
			$this->view->title = "Pengajuan Judul Tugas Akhir";
		}
	}
}