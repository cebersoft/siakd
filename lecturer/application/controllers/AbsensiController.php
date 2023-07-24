<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: Absensi Controller -> Controller untuk modul halaman absensi
*/
class AbsensiController extends Zend_Controller_Action
{
	function init()
	{
		$this->initView();
		$this->view->baseUrl = $this->_request->getBaseUrl();
		Zend_Loader::loadClass('Profile');
		Zend_Loader::loadClass('User');
		Zend_Loader::loadClass('Menu');
		Zend_Loader::loadClass('Kbm');
		Zend_Loader::loadClass('Absensi');
		Zend_Loader::loadClass('StatHadir');
		Zend_Loader::loadClass('Mahasiswa');
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
		$this->view->kls_act="active";
	}
	
	function indexAction()
	{
		// get id perkuliahan
		$id_perkuliahan=$this->_request->get('id');
		// Title Browser
		$this->view->title = "Daftar Presensi Mahasiswa";
		// get perkuliahan
		$kbm = new Kbm();
		$getKbm = $kbm->getKbmById($id_perkuliahan);
		if($getKbm){
			// data kbm
			foreach ($getKbm as $dataKbm) {
				$kd_paket=$dataKbm['kd_paket_kelas'];
				$this->view->nm_prodi=$dataKbm['nm_prodi_kur'];
				$this->view->kd_per=$dataKbm['kd_periode'];
				$this->view->nm_kelas=$dataKbm['nm_kelas'];
				$this->view->jns_kelas=$dataKbm['jns_kelas'];
				$this->view->nm_dsn=$dataKbm['nm_dosen'];
				$this->view->nm_mk=$dataKbm['nm_mk'];
				$this->view->kd_mk=$dataKbm['kode_mk'];
				$this->view->hari=$dataKbm['hari'];
				$this->view->tgl=$dataKbm['tgl_kuliah_fmt'];
				$this->view->start=$dataKbm['start_time_fmt'];
				$this->view->end=$dataKbm['end_time_fmt'];
				$this->view->tempat=$dataKbm['tempat'];
			}
			// stat kehadiran
			$statHadir = new StatHadir();
			$this->view->listStatHdr=$statHadir->fetchAll();
			// get data absensi
			$absensi = new Absensi();
			$this->view->listAbsensi=$absensi->getAbsensiByPerkuliahan($id_perkuliahan);
			$this->view->id_perkuliahan=$id_perkuliahan;
			// get data kbm
			$getListKbm=$kbm->getKbmByPaket($kd_paket);
			$this->view->listKbm=$getListKbm;
			// navigation
			$this->_helper->navbar('kbm/index?id='.$kd_paket,0,0,0,0);
		}else{
			$this->view->eksis="f";
			$this->_helper->navbar('kelas',0,0,0,0);
		}
	}

}