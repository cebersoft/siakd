<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: Perwalian Controller -> Controller untuk modul halaman perwalian
*/
class PerwalianController extends Zend_Controller_Action
{
	function init()
	{
		$this->initView();
		$this->view->baseUrl = $this->_request->getBaseUrl();
		Zend_Loader::loadClass('Profile');
		Zend_Loader::loadClass('User');
		Zend_Loader::loadClass('Menu');
		Zend_Loader::loadClass('Mahasiswa');
		Zend_Loader::loadClass('Periode');
		Zend_Loader::loadClass('Angkatan');
		Zend_Loader::loadClass('Prodi');
		Zend_Loader::loadClass('Pkrs');
		Zend_Loader::loadClass('Perwalian');
		Zend_Loader::loadClass('KalenderAkd');
		Zend_Loader::loadClass('Register');
		Zend_Loader::loadClass('Kuliah');
		Zend_Loader::loadClass('KuliahTA');
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
			$this->nm_dsn=Zend_Auth::getInstance()->getIdentity()->nm_dosen;
		}else{
			$this->_redirect('/');
		}
		// layout
		$this->_helper->layout()->setLayout('main');
		// nav menu
		$this->view->wali_act="active";
	}
	
	function indexAction(){
		// Title Browser
		$this->view->title = "Daftar Periode Akademik Registrasi Akademik Mahasiswa";
		// destroy session param
		Zend_Session::namespaceUnset('param_dsn_reg');
		// navigation
		$this->_helper->navbar(0,0);
		$periode = new Periode();
		$this->view->listPeriode = $periode->fetchAll();
		$angkatan = new Angkatan();
		$this->view->listAngkt = $angkatan->fetchAll();
		$prodi = new Prodi();
		$this->view->listProdi= $prodi->fetchAll();
		// kalender akademik
		$kd_aktivitas='101';
		$kAkd=new KalenderAkd();
		$getKalender=$kAkd->getKalenderAkdByAktivitas($kd_aktivitas);
		$this->view->listKalender=$getKalender;
	}
	
	function listAction(){
		// show data
		$param = new Zend_Session_Namespace('param_dsn_reg');
		$kd_prodi = $param->prd;
		$kd_periode = $param->per;
		$id_angkatan = $param->akt;
		// get data prodi
		$nm_prd="";
		$nm_jenis="";
		$prodi = new Prodi();
		$listProdi=$prodi->fetchAll();
		foreach ($listProdi as $dataPrd) {
			if($kd_prodi==$dataPrd['kd_prodi']){
				$nm_prd=$dataPrd['nm_prodi'];
			}
		}
		// Title Browser
		$this->view->title = "Daftar Registrasi Akademik ".$nm_prd." (".$id_angkatan.") - ".$kd_periode;
		$this->view->kd_periode=$kd_periode;
		$this->view->id_akt=$id_angkatan;
		$this->view->nm_prd=$nm_prd;
		// navigation
		$this->_helper->navbar('perwalian',0);
		// get data 
		$register = new Register();
		$getRegister = $register->getRegisterByPeriodeAngkatanProdi($kd_periode,$id_angkatan,$kd_prodi);
		
		$this->view->listRegister = $getRegister;
	}

	function newAction(){
		// Title Browser
		$this->view->title = "Perwalian";
		// navigation
		$src = $this->_request->get('src');
		if($src=='home'){
			$this->_helper->navbar('home',0);	
		}else{
			$this->_helper->navbar('perwalian/list',0);
		}
		// get data 
		$nim = $this->_request->get('nim');
		$kd_periode=$this->_request->get('per');
		// get register
		$register = new Register();
		$getRegister = $register->getRegisterByNimPeriode($nim,$kd_periode);
		if($getRegister){
			foreach ($getRegister as $dtReg) {
				$this->view->nim=$dtReg['nim'];
				$this->view->idmhs=$dtReg['id_mhs'];
				$this->view->nm_mhs=$dtReg['nm_mhs'];
				$this->view->akt=$dtReg['id_angkatan'];
				$this->view->kd_prd=$dtReg['kd_prodi'];
				$this->view->prd=$dtReg['nm_prodi'];
				$this->view->kd_dw=$dtReg['kd_dosen_wali'];
				$kd_dw=$dtReg['kd_dosen_wali'];
				$this->view->dw=$dtReg['nm_dosen_wali'];
				$kd_prodi=$dtReg['kd_prodi'];
				$this->view->per=$dtReg['kd_periode'];
				$krs=$dtReg['krs'];
				$nim=$dtReg['nim'];
				$nm_mhs=$dtReg['nm_mhs'];
				$akt=$dtReg['id_angkatan'];
				$prd=$dtReg['nm_prodi'];
				$per=$dtReg['kd_periode'];
				$dw=$dtReg['nm_dosen_wali'];
			}
			if($krs=='f'){
				// Title Browser
				$this->view->title = "KRS Mahasiswa";
				$this->view->eksis="f";	
			}else{
				if($kd_dw!=$this->kd_dsn){
					// Title Browser
					$this->view->title = "KRS Mahasiswa";
					$this->view->eksis="f";
				}else{
					// Title Browser
					$this->view->title = "KRS Mahasiswa ".$nm_mhs;
					// get data KRS
					$kuliah = new Kuliah();
					$getKuliah = $kuliah->getKuliahByNimPeriode($nim,$kd_periode);
					$this->view->listKuliah=$getKuliah;
					$kuliahTA = new KuliahTA();
					$getKuliahTA = $kuliahTA->getKuliahTAByNimPeriode($nim,$kd_periode);
					$this->view->listKuliahTA=$getKuliahTA;
					// cek periode dan tanggal di kalender
					$tgl = date('Y-m-d');
					$periode=new Periode();
					$getPeriode=$periode->getPeriodeByTgl($tgl);
					$kd_periode_now="";
					if($getPeriode){
						foreach ($getPeriode as $dtPeriode){
							$kd_periode_now=$dtPeriode['kd_periode'];
						}
					}else{
						$getPeriodeAktif=$periode->getPeriodeByStatus(0);
						foreach ($getPeriodeAktif as $dtPeriode) {
							$kd_periode_now=$dtPeriode['kd_periode'];;
						}
					}
					if($kd_periode_now!=$kd_periode){
						$this->view->allowReg=-1;	
					}else{
						// cek kalender
						$kd_aktivitas='103';
						// jadwal krs/her registrasi
						$kalender=new KalenderAkd();
						$getKalender=$kalender->getKalenderAkdByPeriodeAktivitas($kd_periode, $kd_aktivitas);
						$this->view->allowReg="";
						if ($getKalender){
							// cek tanggal
							foreach ($getKalender as $dtKalender) {
								$startDate=$dtKalender['start_date'];
								$endDate=$dtKalender['end_date'];
							}
							if(($tgl>=$startDate)and($tgl<=$endDate)){
								$this->view->allowReg=1;
							}else{
								$this->view->allowReg=-1;
							}
						}else{
							$this->view->allowReg=0;
						}
					}
					// get data perwalian
					$perwalian = new Perwalian();
					$getPerwalian=$perwalian->getPerwalianFeedByPeriodeNim($kd_periode, $nim);
					$this->view->listPerwalian=$getPerwalian;
				}
			}
		}else{
			// Title Browser
			$this->view->title = "KRS Mahasiswa";
			$this->view->eksis="f";
		}
	}

	function new2Action()
	{		
		$tgl = date('Y-m-d');
		// get data
		$nim = $this->_request->get('nim');
		$kd_periode=$this->_request->get('per');
		$this->view->per=$kd_periode;
		$kd_aktivitas='103'; // perwalian
		// jadwal pkrs
		$kalender=new KalenderAkd();
		$getKalender=$kalender->getKalenderAkdByPeriodeAktivitas($kd_periode, $kd_aktivitas);
		$this->view->allowReg="";
		// get register
		$register = new Register();
		$getRegister = $register->getRegisterByNimPeriode($nim,$kd_periode);
		if($getRegister){
			foreach ($getRegister as $dtReg) {
				$this->view->nim=$dtReg['nim'];
				$this->view->nm_mhs=$dtReg['nm_mhs'];
				$this->view->akt=$dtReg['id_angkatan'];
				$this->view->kd_prd=$dtReg['kd_prodi'];
				$this->view->prd=$dtReg['nm_prodi'];
				$this->view->dw=$dtReg['nm_dosen_wali'];
				$kd_prodi=$dtReg['kd_prodi'];
				$this->view->per=$dtReg['kd_periode'];
				$krs=$dtReg['krs'];
				$nim=$dtReg['nim'];
				$nm_mhs=$dtReg['nm_mhs'];
				$akt=$dtReg['id_angkatan'];
				$prd=$dtReg['nm_prodi'];
				$per=$dtReg['kd_periode'];
				$dw=$dtReg['nm_dosen_wali'];
			}
			if($krs=='f'){
				// Title Browser
				$this->view->title = "Form PKRS Mahasiswa";
				$this->view->eksis="f";
				$this->_helper->navbar('perwalian',0);
			}else{
				// Title Browser
				$this->view->title = "Form PKRS MAHASISWA ".$nm_mhs;
				// navigation
				$this->_helper->navbar('perwalian/list',0);
				$kuliah = new Kuliah();
				$getKuliah = $kuliah->getKuliahByNimPeriode($nim,$kd_periode);
				$this->view->listKuliah=$getKuliah;
				$kuliahTA = new KuliahTA();
				$getKuliahTA = $kuliahTA->getKuliahTAByNimPeriode($nim,$kd_periode);
				$this->view->listKuliahTA=$getKuliahTA;
				$this->view->per=$kd_periode;
				// list pkrs
				$pkrs = new Pkrs();
				$getPkrs=$pkrs->getPkrsByNimPeriode($nim, $kd_periode);
				$getPkrsTA=$pkrs->getPkrsTAByNimPeriode($nim, $kd_periode);
				$this->view->listPkrs=$getPkrs;
				$this->view->listPkrsTA=$getPkrsTA;
			}
		}else{
			$this->view->eksis="f";
			// Title Browser
			$this->view->title = "Form PKRS Mahasiswa";
		}
		if ($getKalender){
			// cek tanggal
			foreach ($getKalender as $dtKalender) {
				$startDate=$dtKalender['start_date'];
				$endDate=$dtKalender['end_date'];
			}
			if(($tgl>=$startDate)and($tgl<=$endDate)){
				$this->view->allowReg=1;
			}else{
				$this->view->allowReg=-1;
			}
		}else{
			$this->view->allowReg=0;
		}
	}
}