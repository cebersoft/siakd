<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: Prp Ujian TA Controller -> Controller untuk modul halaman pengajuan ujian ta
*/
class PrpujiantaController extends Zend_Controller_Action
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
		Zend_Loader::loadClass('PrpUjianTa');
		Zend_Loader::loadClass('KelompokKeilmuan');
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
		$this->view->id=$kd_periode;
		// periode akademik
		$getPeriode = $periode->getPeriodeByKd($kd_periode);
		// navigation
		$this->_helper->navbar(0,0);
		if($getPeriode){
			// Title Browser
			$this->view->title = "Pengajuan Seminar/Sidang Periode ".$kd_periode;
			$kd_dsn=$this->kd_dsn;
			// get periode
			$this->view->listPer=$periode->fetchAll();
			$this->view->per=$kd_periode;
			$this->view->kd_dsn=$kd_dsn;
			// get data prp
			$prp=new PrpUjianTa();
			$this->view->listPrp=$prp->getPrpByPeriodePemb($kd_periode,$kd_dsn);
		}else{
			$this->view->eksis="f";
			// Title Browser
			$this->view->title = "Pengajuan Seminar/Sidang";
		}
	}

	function detilAction(){
		// layout
		$this->_helper->layout()->setLayout('main');
		// get param
		$id=$this->_request->get('id');
		$prp=new PrpUjianTa();
		$getPrp=$prp->getPrpById($id);
		if($getPrp){
			$nm_mk="";
			$per="";
			foreach($getPrp as $dt){
				$nm_mk=$dt['nm_mk'];
				$per=$dt['kd_periode'];
			}
			// Title Browser
			$this->view->title = "Detil Verifikasi ".$nm_mk." Periode ".$per;
			$this->view->listDetil=$prp->getPrpApproverByPrp($id);
			// navigation
			$this->_helper->navbar('prpujianta/index?id='.$per,0);
		}else{
			$this->view->eksis="f";
			// Title Browser
			$this->view->title = "Detil Verifikasi";
			// navigation
			$this->_helper->navbar('prpujianta',0);
		}
	}
}