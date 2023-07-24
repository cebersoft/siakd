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
		$ses_lec = new Zend_Session_Namespace('ses_lec');
		if (($auth->hasIdentity())and($ses_lec->uname)) {
			$this->view->namadsn =Zend_Auth::getInstance()->getIdentity()->nm_dosen;
			$this->view->kddsn=Zend_Auth::getInstance()->getIdentity()->kd_dosen;
			$this->view->kd_pt=$ses_lec->kd_pt;
			$this->view->nm_pt=$ses_lec->nm_pt;
			// global var
			$this->kd_dsn=Zend_Auth::getInstance()->getIdentity()->kd_dosen;
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
			// periode
			$periode=new Periode();
			$getPeriode=$periode->fetchAll();
			$this->view->listPeriode=$getPeriode;
			$this->view->per=$kd_periode;
			// prodi
			$prodi=new Prodi();
			$getProdi=$prodi->fetchAll();
			$this->view->listProdi=$getProdi;
		}else{
			$this->view->eksis="f";
			// Title Browser
			$this->view->title = "Judul Tugas Akhir";
		}
	}

	function listAction(){
		// navigation
		$this->_helper->navbar('judulta',0);
		// show data
		$param = new Zend_Session_Namespace('param_dsn_judulta');
		$kd_prodi = $param->prd;
		$kd_periode = $param->per;
		$kd_dsn=$this->kd_dsn;
		$st=$this->_request->get('st');
		$this->view->st=$st;
		if($st==1){
			$this->view->btn1="disabled='disabled'";
			$this->view->stat="Disetujui";
		}elseif($st==-1){
			$this->view->btn2="disabled='disabled'";
			$this->view->stat="Ditolak";
		}else{
			$this->view->btn0="disabled='disabled'";
			$this->view->stat="Diajukan";
		}
		// Title Browser
		$this->view->title = "Judul Tugas Akhir";
		$this->view->per=$kd_periode;
		$prodi=new Prodi();
		$getProdi=$prodi->getProdiByKd($kd_prodi);
		$nm_prodi="";
		foreach($getProdi as $dtProdi){
			$nm_prodi=$dtProdi['nm_prodi'];
		}
		$this->view->nm_prodi=$nm_prodi;
		// get data
		$judulTa=new JudulTA();
		$getJudulTa=$judulTa->getJudulTAByPembimbingPeriodeProdi($kd_dsn,$kd_periode,$kd_prodi);
		$this->view->listJudulTa=$getJudulTa;
	}
}