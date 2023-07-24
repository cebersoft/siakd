<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: Kurikulum Controller -> Controller untuk modul kurikulum
*/
class KurikulumController extends Zend_Controller_Action
{
	function init()
	{
		$this->initView();
		$this->view->baseUrl = $this->_request->getBaseUrl();
		Zend_Loader::loadClass('Profile');
		Zend_Loader::loadClass('User');
		Zend_Loader::loadClass('Mahasiswa');
		Zend_Loader::loadClass('Periode');
		Zend_Loader::loadClass('Prodi');
		Zend_Loader::loadClass('Kurikulum');
		Zend_Loader::loadClass('MatkulKurikulum');
		Zend_Loader::loadClass('Zend_Session');
		Zend_Loader::loadClass('Zend_Layout');
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
		// navigation
		$this->_helper->navbar(0,0);
		// nav menu
		$this->view->kur_act="active";
	}

	function indexAction()
	{
		// Title Browser
		$this->view->title = "Kurikulum";
		// get periode from tgl now
		$tgl = date('Y-m-d');
		$kd_periode="";
		$periode = new Periode();
		$getPeriode=$periode->getPeriodeByTgl($tgl);
		if($getPeriode){
			foreach ($getPeriode as $dtPeriode){
				$kd_periode=$dtPeriode['kd_periode'];
			}
		}else{
			$getPeriodeAktif=$periode->getPeriodeByStatus(0);
			foreach ($getPeriodeAktif as $dtPeriode) {
				$kd_periode=$dtPeriode['kd_periode'];;
			}
		}
		// get Prodi
		$prodi=new Prodi();
		$kurikulum=new Kurikulum();
		$getProdi=$prodi->fetchAll();
		foreach ($getProdi as $dtProdi){
			$kd_prodi=$dtProdi['kd_prodi'];
		}
		$getKurikulum=$kurikulum->getKurByProdiPeriode('48201', $kd_periode);
		$id_kur="";
		if($getKurikulum){
			foreach ($getKurikulum as $dtKur) {
				$id_kur=$dtKur['id_kurikulum'];
				$this->view->smt_normal=$dtKur['smt_normal'];
				$this->view->kd_kurikulum=$dtKur['kd_kurikulum'];
				$this->view->nm_kurikulum=$dtKur['nm_kurikulum'];
				$this->view->nm_prodi=$dtKur['nm_prodi'];
				$this->view->kd_periode=$dtKur['kd_periode_berlaku'];
				$this->view->smt_normal=$dtKur['smt_normal'];
				$this->view->sks_l=$dtKur['sks_lulus'];
				$this->view->sks_w=$dtKur['sks_wajib'];
				$this->view->sks_p=$dtKur['sks_pilihan'];
			}
			// get matkul
			$mkKur=new MatkulKurikulum();
			$getMk=$mkKur->getMatkulByKurikulum($id_kur);
			$this->view->listMatkulKur=$getMk;
		}else{
			$this->view->eksis="f";
		}
		$this->view->title="Kurikulum";
	}
}