<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: Paket Kelas Controller -> Controller untuk modul halaman paket kelas
*/
class PaketkelasController extends Zend_Controller_Action
{
	function init()
	{
		$this->initView();
		$this->view->baseUrl = $this->_request->getBaseUrl();
		Zend_Loader::loadClass('Profile');
		Zend_Loader::loadClass('User');
		Zend_Loader::loadClass('Menu');
		Zend_Loader::loadClass('Kelas');
		Zend_Loader::loadClass('Paketkelas');
		Zend_Loader::loadClass('Nmkelas');
		Zend_Loader::loadClass('TimTeaching');
		Zend_Loader::loadClass('Zend_Session');
		Zend_Loader::loadClass('Zend_Layout');
		Zend_Loader::loadClass('Validation');
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
		$this->view->kls_act="active";
	}

	function indexAction()
	{
		// get param
		$kd_kls=$this->_request->get('kd');
		// navigation
		$this->_helper->navbar('kelas/list',0);
		// get data kelas dan paket kelas
		$kelas = new Kelas();
		$getKelas = $kelas->getKelasByKd($kd_kls);
		if($getKelas){
			foreach ($getKelas as $data_kls) {
				$this->view->kd_kls=$data_kls['kd_kelas'];
				$this->view->kd_dsn=$data_kls['kd_dosen'];
				$kd_dsn=$data_kls['kd_dosen'];
				$this->view->nm_dsn=$data_kls['nm_dosen'];
				$this->view->kd_mk=$data_kls['kode_mk'];
				$this->view->nm_mk=$data_kls['nm_mk'];
				$this->view->kd_per=$data_kls['kd_periode'];
				$this->view->nm_prodi=$data_kls['nm_prodi_kur'];
				$this->view->jns_kelas=$data_kls['jns_kelas'];
				$this->view->sks=($data_kls['sks_tm']+$data_kls['sks_prak']+$data_kls['sks_prak_lap']+$data_kls['sks_sim']);
				$nm_mk=$data_kls['nm_mk'];
				$nm_dsn=$data_kls['nm_dosen'];
				$nm_prodi=$data_kls['nm_prodi_kur'];
				$timTeaching=new TimTeaching();
				$getKelasTt=$timTeaching->getTimTeachingByKelas($kd_kls);
				$found=0;
				if ($getKelasTt){
					foreach($getKelasTt as $tt){
						if($tt['kd_dosen']==$this->kd_dsn){
							$found=$found+1;
						}
					}
				}
				if(($kd_dsn==$this->kd_dsn)or($found>0)){
					// title
					$this->view->title="Daftar Paket Kelas ".$nm_mk." - ".$nm_prodi;
					$paketkelas = new Paketkelas();
					$getPaket = $paketkelas->getPaketKelasByKelas($kd_kls);
					$this->view->listPaket = $getPaket;	
				}else{
					$this->view->eksis="f";
					// title
					$this->view->title="Daftar Paket Kelas";
				}
			}
		}else{
			$this->view->eksis="f";
			// title
			$this->view->title="Daftar Paket Kelas";
		}
	}
}