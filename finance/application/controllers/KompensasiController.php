<?php
/*
	Programmer	: Tiar Aristian
	Release		: Agustus 2016
	Module		: Kompensasi Controller -> Controller untuk modul kompensasi biaya
*/
class KompensasiController extends Zend_Controller_Action
{
	function init()
	{
		$this->initView();
		$this->view->baseUrl = $this->_request->getBaseUrl();
		Zend_Loader::loadClass('Profile');
		Zend_Loader::loadClass('KompBiaya');
		Zend_Loader::loadClass('RuleBiaya');
		Zend_Loader::loadClass('ParamBiaya');
		Zend_Loader::loadClass('Kompensasi');
		Zend_Loader::loadClass('MhsRegPeriode');
		Zend_Loader::loadClass('Zend_Session');
		Zend_Loader::loadClass('Zend_Layout');
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
		$this->view->act_detbiaya="active";
		$this->view->act_mhsbiaya="active open";
	}

	function indexAction()
	{
		$nim=$this->_request->get('nim');
		$per=$this->_request->get('per');
		// Title Browser
		$this->view->title = "Daftar Kompensasi/Pengurangan Biaya Mahasiswa";
		// get registrasi
		$mhsReg=new MhsRegPeriode();
		$getMhsReg=$mhsReg->getMhsRegPeriodeByNimPeriode($nim, $per);
		if(!$getMhsReg){
			$this->view->eksis="f";
			// navigation
			$this->_helper->navbar('mhsbiaya',0,0,0,0);
		}else{
			// navigation
			$this->_helper->navbar('mhsbiaya/list?nim='.$nim,0,0,0,0);
			foreach ($getMhsReg as $dtMhsReg) {
				$nm_mhs=$dtMhsReg['nm_mhs'];
				$this->view->nm=$nm_mhs;
				$this->view->nim=$nim;
				$this->view->akt=$dtMhsReg['id_angkatan'];
				$this->view->per=$per;
				$this->view->nm_prd=$dtMhsReg['nm_prodi'];
				$this->view->stat_msk=$dtMhsReg['nm_stat_masuk'];
				$this->view->nm_gel=$dtMhsReg['nm_gelombang'];
			}
			$kompensasi = new Kompensasi();
			$this->view->listKompensasi=$kompensasi->getKompensasiByNimPeriode($nim, $per);
			// komponen biaya
			$kompBiaya = new KompBiaya();
			$stat="t";
			$getKompBiaya=$kompBiaya->getKompBiayaByStat($stat);
			$this->view->listKompBiaya=$getKompBiaya;
			// rule biaya
			$rule = new RuleBiaya();
			$this->view->listRule=$rule->fetchAll();
			// param biaya
			$prmBiaya = new ParamBiaya();
			$this->view->listParam=$prmBiaya->fetchAll();
		}
		
	}
}