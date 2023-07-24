<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: Home Controller -> Controller untuk modul home
*/
class HomeController extends Zend_Controller_Action
{
	function init()
	{
		$this->initView();
		$this->view->baseUrl = $this->_request->getBaseUrl();
		Zend_Loader::loadClass('Profile');
		Zend_Loader::loadClass('User');
		Zend_Loader::loadClass('Mahasiswa');
		Zend_Loader::loadClass('Periode');
		Zend_Loader::loadClass('KalenderAkd');
		Zend_Loader::loadClass('Perwalian');
		Zend_Loader::loadClass('SurveyMhs');
		Zend_Loader::loadClass('AnnouncementMhs');
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
		}else{
			$this->_redirect('/');
		}
		// layout
		$this->_helper->layout()->setLayout('main');
		// navigation
		$this->_helper->navbar(0,0);
	}

	function indexAction()
	{
		// Title Browser
		$this->view->title = "Beranda Portal Mahasiswa";
		// nav menu
		$this->view->home_act="active";
		// get data 
		$nim=$this->uname;
		// get profile pt
		$profil = new Profile();
		$getProfil = $profil->fetchAll();
		foreach ($getProfil as $dtProf){
			$this->view->nm_pt=$dtProf['nama_pt'];
			$this->view->kd_pt=$dtProf['kode_pt'];
			$this->view->alamat=$dtProf['alamat'].", ".$dtProf['kota'];
			$this->view->web=$dtProf['web'];
			$this->view->email=$dtProf['email'];
			$this->view->telp=$dtProf['telpon'];
			$this->view->fax=$dtProf['fax'];
			$this->view->visi=$dtProf['visi'];
			$this->view->misi=$dtProf['misi'];
			$this->view->nick=$dtProf['nickname'];
		}

		// dosen wali
		$mhs=new Mahasiswa();
		$getMhs=$mhs->getMahasiswaByNim($nim);
		foreach ($getMhs as $dtMhs){
			$kd_prodi=$dtMhs['kd_prodi'];
			$this->view->kd_prodi=$kd_prodi;
			$id_angkatan=$dtMhs['id_angkatan'];
			$this->view->id_angkatan=$id_angkatan;
			$kd_dosen_wali=$dtMhs['kd_dosen_wali'];
			$this->view->kd_dw=$dtMhs['kd_dosen_wali'];
		}
		// pengumuman
		$ancMhs=new AnnouncementMhs();
		$this->view->listNewsFeed=$ancMhs->getAnnouncementMhsFilterRunning($id_angkatan,$kd_prodi,$kd_dosen_wali);
		// inbox perwalian
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
		$this->view->per=$kd_periode;
		// get data perwalian
		$perwalian = new Perwalian();
		$getPerwalian=$perwalian->getPerwalianFeedByPeriodeNim($kd_periode, $nim);
		$this->view->listPerwalian=$getPerwalian;
		
		// kalender akademik
		$kalAkd=new KalenderAkd();
		$getKalender=$kalAkd->getKalenderAkdByPeriode($kd_periode);
		$this->view->listKalender=$getKalender;
		// survey
		$this->view->nim=$nim;
		$id_survey="xxxx";
		$surveyMhs=new SurveyMhs();
		$getSurveyHdr=$surveyMhs->getSurveyById($id_survey);
		if($getSurveyHdr){
			foreach ($getSurveyHdr as $dtSurv){
				$this->view->id_surv=$dtSurv['id_survey'];
				$this->view->titleSurv=$dtSurv['nm_survey'];
				$this->view->quest=$dtSurv['question'];
			}
			$getSurveyDtl=$surveyMhs->getSurveyDtlByNim($nim);
			if(!$getSurveyDtl){
				$this->view->survey="y";
			}else{
				$this->view->survey="t";
			}
		}
	}

	function aksesAction()
	{
		// Title Browser
		$this->view->title = "Halaman Alih Portal Mahasiswa";
	}

	function keuanganAction()
	{
		// Title Browser
		$this->view->title = "Halaman Alih Portal Mahasiswa";
	}
}