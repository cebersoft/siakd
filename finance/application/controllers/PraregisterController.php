<?php 
	// bismillah
	/*
	 Programmer	: Tiar Aristian
	 Release	: September 2016
	 Module		: Pra register Controller -> Controller untuk modul pra registrasi
	 */
class PraregisterController extends Zend_Controller_Action
{
	function init()
	{
		$this->initView();
		$this->view->baseUrl = $this->_request->getBaseUrl();
		Zend_Loader::loadClass('Profile');
		Zend_Loader::loadClass('Angkatan');
		Zend_Loader::loadClass('Prodi');
		Zend_Loader::loadClass('Periode');
		Zend_Loader::loadClass('Praregister');
		Zend_Loader::loadClass('Zend_Session');
		Zend_Loader::loadClass('Zend_Layout');
		Zend_Loader::loadClass('PHPExcel');
		Zend_Loader::loadClass('PHPExcel_Cell_AdvancedValueBinder');
		Zend_Loader::loadClass('PHPExcel_IOFactory');
		$auth = Zend_Auth::getInstance();
		$ses_fin = new Zend_Session_Namespace('ses_fin');
		if (($auth->hasIdentity())and($ses_fin->uname)) {
			$this->view->namauser =Zend_Auth::getInstance()->getIdentity()->nama;
			$this->view->kd_pt=$ses_fin->kd_pt;
			$this->view->nm_pt=$ses_fin->nm_pt;
		}else{
			$this->_redirect('/');
		}
		// layout
		$this->_helper->layout()->setLayout('main');
		// menu nav
		$this->view->act_reg="active open";
	}
		
	function indexAction(){
		// Title Browser
		$this->view->title = "Pra Registrasi Mahasiswa";
		// navigation
		$this->_helper->navbar(0,0,0,0,0);
		// angkatan
		$akt=new Angkatan();
		$this->view->listAkt=$akt->fetchAll();
		// prodi
		$prodi = new Prodi();
		$this->view->listProdi = $prodi->fetchAll();
		// periode akademik
		$periode = new Periode();
		$this->view->listPeriode=$periode->fetchAll();
		// menu nav
		$this->view->act_prareg="active";
	}
	
	function showAction(){
		// menu nav
		$this->view->act_prareg="active";
		// get param
		$param = new Zend_Session_Namespace('param_praregister');
		$prd=$param->prd;
		$akt=$param->akt;
		$per=$param->per;
		if(!$per){
			$this->view->eksis="f";
			// navigation
			$this->_helper->navbar(0,0,0,0,0);
		}else{
			// navigation
			$this->_helper->navbar('praregister',0,0,0,0);
			// title
			$this->view->title="Pra Registrasi Periode ".$per;
			// get data mhs pra reg
			$mhsReg=new Praregister();
			$getMhsReg=$mhsReg->getPraRegisterByPeriodeAngkatanProdi($per, $akt, $prd);
			$this->view->listMhsReg=$getMhsReg;
		}
	}
	
}